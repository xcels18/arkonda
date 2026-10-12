<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Contract;
use App\Services\ContractDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    /**
     * Display list of contracts for regular users
     * Users can only see contracts from their own OPD
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Contract::with('opd')->visibleTo($user);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by year
        if ($request->filled('tahun')) {
            $query->filterTahun($request->tahun);
        }

        // Filter by type
        if ($request->filled('jenis')) {
            $query->filterJenis($request->jenis);
        }

        // Filter by penyedia
        if ($request->filled('penyedia')) {
            $query->filterPenyedia($request->penyedia);
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options (scoped to user's OPD for non-admin)
        $contractScope = Contract::visibleTo($user);

        $years = (clone $contractScope)->distinct()->pluck('tahun_anggaran')->sort()->reverse();
        $jenisOptions = (clone $contractScope)->distinct()->whereNotNull('jenis_kontrak')->pluck('jenis_kontrak');
        $penyediaOptions = (clone $contractScope)->distinct()->whereNotNull('nama_penyedia')->pluck('nama_penyedia');

        // User's OPD info
        $userOpd = $user->opd;

        return view('contracts.index', compact('contracts', 'years', 'jenisOptions', 'penyediaOptions', 'userOpd'));
    }

    /**
     * Show create form for user's OPD
     */
    public function create()
    {
        $user = auth()->user();

        // User must have OPD assigned
        if (! $user->opd_id) {
            return redirect()->route('contracts.index')
                ->with('error', 'Anda belum terdaftar di OPD manapun. Hubungi administrator.');
        }

        $userOpd = $user->opd;

        return view('contracts.create', compact('userOpd'));
    }

    /**
     * Store new contract for user's OPD
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // User must have OPD assigned
        if (! $user->opd_id) {
            return redirect()->route('contracts.index')
                ->with('error', 'Anda belum terdaftar di OPD manapun. Hubungi administrator.');
        }

        $validated = $request->validate([
            'nomor_kontrak' => ['required', 'string', 'max:100', 'unique:contracts'],
            'nama_kontrak' => ['required', 'string', 'max:255'],
            'nama_penyedia' => ['required', 'string', 'max:255'],
            'nilai_kontrak' => ['required', 'numeric', 'min:0'],
            'tanggal_kontrak' => ['required', 'date'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'jenis_kontrak' => ['required', 'string', 'in:'.implode(',', array_keys(Contract::JENIS_OPTIONS))],
            'bidang' => ['nullable', 'string', 'max:100'],
            'sumber_dana' => ['nullable', 'string', 'in:'.implode(',', array_keys(Contract::SUMBER_DANA_OPTIONS))],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'min:2000', 'max:2100'],
            'keterangan' => ['nullable', 'string'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf', 'max:30720'], // Max 30MB
        ]);

        // Create contract with user's OPD
        $contract = new Contract([
            'nomor_kontrak' => $validated['nomor_kontrak'],
            'nama_kontrak' => $validated['nama_kontrak'],
            'nama_penyedia' => $validated['nama_penyedia'],
            'nilai_kontrak' => $validated['nilai_kontrak'],
            'tanggal_kontrak' => $validated['tanggal_kontrak'],
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'jenis_kontrak' => $validated['jenis_kontrak'],
            'bidang' => $validated['bidang'] ?? 'Aset',
            'sumber_dana' => $validated['sumber_dana'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'opd_id' => $user->opd_id, // Always use user's OPD
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        app(ContractDocumentService::class)->save($contract, $request->file('dokumen'));

        AuditLog::log('create', "User membuat kontrak baru: {$contract->nomor_kontrak}", $contract);

        return redirect()->route('contracts.index')
            ->with('success', 'Kontrak berhasil ditambahkan.');
    }

    /**
     * Show contract details
     * Users can only view contracts from their own OPD
     */
    public function show(Contract $contract)
    {
        $user = auth()->user();

        // Check if user can access this contract (must be from same OPD or admin)
        if (! $contract->isAccessibleBy($user)) {
            abort(403, 'Anda tidak memiliki akses ke kontrak ini.');
        }

        return view('contracts.show', compact('contract'));
    }

    /**
     * Check if user can access this contract
     */
    private function checkAccess(Contract $contract): void
    {
        $user = auth()->user();
        if (! $contract->isAccessibleBy($user)) {
            abort(403, 'Anda tidak memiliki akses ke kontrak ini.');
        }
    }

    /**
     * Download contract document
     */
    public function download(Contract $contract)
    {
        // Check access
        $this->checkAccess($contract);

        // Check if contract has file
        if (! $contract->hasFile()) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'Dokumen kontrak tidak tersedia.');
        }

        // Build full file path
        $fullPath = Storage::disk('contracts')->path(preg_replace('/^contracts\//', '', $contract->file_path));

        // Check if file exists in storage
        if (! file_exists($fullPath)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'File tidak ditemukan di server.');
        }

        // Log download activity
        AuditLog::logDownload($contract, auth()->user());

        // Return file for download
        return response()->download($fullPath, $contract->file_name, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Preview/View contract document in browser
     */
    public function preview(Contract $contract)
    {
        // Check access
        $this->checkAccess($contract);

        // Check if contract has file
        if (! $contract->hasFile()) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'Dokumen kontrak tidak tersedia.');
        }

        // Build full file path
        $fullPath = Storage::disk('contracts')->path(preg_replace('/^contracts\//', '', $contract->file_path));

        // Check if file exists in storage
        if (! file_exists($fullPath)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'File tidak ditemukan di server.');
        }

        // Return file for inline viewing
        return response()->file($fullPath, ['Content-Type' => 'application/pdf']);
    }

    /** Validate uniqueness without disclosing another OPD's contract data. */
    public function validateContractNumber(Request $request)
    {
        $validated = $request->validate([
            'nomor_kontrak' => ['required', 'string', 'max:100'],
        ]);

        abort_unless(auth()->user()->isAdmin() || auth()->user()->opd_id !== null, 403);
        $exists = Contract::where('nomor_kontrak', $validated['nomor_kontrak'])->exists();

        return response()->json([
            'valid' => ! $exists,
            'message' => $exists ? 'Nomor kontrak sudah terdaftar.' : 'Nomor kontrak tersedia.',
        ]);
    }
}
