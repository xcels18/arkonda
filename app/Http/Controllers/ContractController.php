<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\Opd;
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
        $query = Contract::with('opd');

        // Filter by user's OPD (non-admin users can only see their OPD's contracts)
        if (!$user->isAdmin() && $user->opd_id) {
            $query->where('opd_id', $user->opd_id);
        }

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
        $contractScope = Contract::query();
        if (!$user->isAdmin() && $user->opd_id) {
            $contractScope->where('opd_id', $user->opd_id);
        }

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
        if (!$user->opd_id) {
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
        if (!$user->opd_id) {
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
            'jenis_kontrak' => ['required', 'string', 'in:' . implode(',', array_keys(Contract::JENIS_OPTIONS))],
            'bidang' => ['nullable', 'string', 'max:100'],
            'sumber_dana' => ['nullable', 'string', 'in:' . implode(',', array_keys(Contract::SUMBER_DANA_OPTIONS))],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'min:2000', 'max:2100'],
            'keterangan' => ['nullable', 'string'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf', 'max:30720'], // Max 30MB
        ]);

        // Create contract with user's OPD
        $contract = Contract::create([
            'nomor_kontrak' => $validated['nomor_kontrak'],
            'nama_kontrak' => $validated['nama_kontrak'],
            'nama_penyedia' => $validated['nama_penyedia'],
            'nilai_kontrak' => $validated['nilai_kontrak'],
            'tanggal_kontrak' => $validated['tanggal_kontrak'],
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'jenis_kontrak' => $validated['jenis_kontrak'],
            'bidang' => $validated['bidang'] ?? null,
            'sumber_dana' => $validated['sumber_dana'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'opd_id' => $user->opd_id, // Always use user's OPD
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Handle file upload
        if ($request->hasFile('dokumen')) {
            $this->uploadDocument($contract, $request->file('dokumen'));
        }

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
        if (!$user->isAdmin() && $user->opd_id && $contract->opd_id !== $user->opd_id) {
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
        if (!$user->isAdmin() && $user->opd_id && $contract->opd_id !== $user->opd_id) {
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
        if (!$contract->hasFile()) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'Dokumen kontrak tidak tersedia.');
        }

        // Build full file path
        $fullPath = storage_path('app/' . $contract->file_path);

        // Check if file exists in storage
        if (!file_exists($fullPath)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'File tidak ditemukan di server.');
        }

        // Log download activity
        AuditLog::logDownload($contract, auth()->user());

        // Return file for download
        return response()->download($fullPath, $contract->file_name, [
            'Content-Type' => 'application/pdf'
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
        if (!$contract->hasFile()) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'Dokumen kontrak tidak tersedia.');
        }

        // Build full file path
        $fullPath = storage_path('app/' . $contract->file_path);

        // Check if file exists in storage
        if (!file_exists($fullPath)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'File tidak ditemukan di server.');
        }

        // Return file for inline viewing
        return response()->file($fullPath, ['Content-Type' => 'application/pdf']);
    }

    /**
     * Upload document to secure storage
     */
    private function uploadDocument(Contract $contract, $file): void
    {
        // Delete old file if exists
        if ($contract->hasFile()) {
            $this->deleteDocument($contract);
        }

        // Generate unique filename: OPD_Jenis_NamaKontrak_Tahun.pdf
        $fileName = $this->generateFileName($contract);

        // Store in contracts disk (storage/app/contracts)
        $file->storeAs('', $fileName, 'contracts');

        $contract->update([
            'file_path' => 'contracts/' . $fileName,
            'file_name' => $fileName,
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now(),
        ]);

        AuditLog::logUpload($contract, auth()->user());
    }

    /**
     * Generate unique filename based on contract data
     * Format: OPD_Jenis_NamaKontrak_Tahun.pdf
     */
    private function generateFileName(Contract $contract): string
    {
        // Get OPD singkatan or 'TANPA-OPD'
        $opd = $contract->opd?->singkatan ?? 'TANPA-OPD';

        // Get jenis kontrak or 'UMUM'
        $jenis = $contract->jenis_kontrak ?? 'UMUM';

        // Get nama kontrak (truncate if too long)
        $nama = $contract->nama_kontrak;

        // Get tahun
        $tahun = $contract->tahun_anggaran;

        // Sanitize each part (remove special characters, replace spaces with dash)
        $opd = $this->sanitizeFileName($opd);
        $jenis = $this->sanitizeFileName($jenis);
        $nama = $this->sanitizeFileName($nama);

        // Truncate nama if too long (max 50 chars)
        if (strlen($nama) > 50) {
            $nama = substr($nama, 0, 50);
        }

        // Build filename
        $baseName = "{$opd}_{$jenis}_{$nama}_{$tahun}";

        // Add unique suffix to prevent duplicates
        $uniqueSuffix = '_' . substr(md5($contract->id . time()), 0, 8);

        return $baseName . $uniqueSuffix . '.pdf';
    }

    /**
     * Sanitize string for use in filename
     */
    private function sanitizeFileName(string $string): string
    {
        // Replace spaces with dash
        $string = str_replace(' ', '-', $string);

        // Remove special characters except dash and underscore
        $string = preg_replace('/[^A-Za-z0-9\-_]/', '', $string);

        // Remove multiple dashes
        $string = preg_replace('/-+/', '-', $string);

        // Trim dashes from start and end
        $string = trim($string, '-');

        return $string ?: 'UNKNOWN';
    }

    /**
     * Delete document from storage
     */
    private function deleteDocument(Contract $contract): void
    {
        if ($contract->file_path) {
            $fullPath = storage_path('app/' . $contract->file_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}

