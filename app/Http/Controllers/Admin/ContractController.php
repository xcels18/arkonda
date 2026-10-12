<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\Opd;
use App\Services\ContractDocumentService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display list of contracts
     */
    public function index(Request $request)
    {
        $query = Contract::with(['uploader', 'opd']);

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

        // Filter by OPD
        if ($request->filled('opd_id')) {
            $query->filterOpd($request->opd_id);
        }

        // Filter by penyedia
        if ($request->filled('penyedia')) {
            $query->filterPenyedia($request->penyedia);
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $years = Contract::distinct()->pluck('tahun_anggaran')->sort()->reverse();
        $jenisOptions = Contract::distinct()->whereNotNull('jenis_kontrak')->pluck('jenis_kontrak');
        $opds = Opd::orderBy('nama')->get();
        $penyediaOptions = Contract::distinct()->whereNotNull('nama_penyedia')->pluck('nama_penyedia');

        return view('admin.contracts.index', compact('contracts', 'years', 'jenisOptions', 'opds', 'penyediaOptions'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $opds = Opd::orderBy('nama')->get();

        return view('admin.contracts.create', compact('opds'));
    }

    /**
     * Store new contract
     */
    public function store(Request $request)
    {
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
            'opd_id' => ['nullable', 'exists:opds,id'],
            'tahun_anggaran' => ['required', 'integer', 'min:2000', 'max:2100'],
            'keterangan' => ['nullable', 'string'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf', 'max:30720'], // Max 30MB
        ]);

        $contract = new Contract([
            'nomor_kontrak' => $validated['nomor_kontrak'],
            'nama_kontrak' => $validated['nama_kontrak'],
            'nama_penyedia' => $validated['nama_penyedia'],
            'nilai_kontrak' => $validated['nilai_kontrak'],
            'tanggal_kontrak' => $validated['tanggal_kontrak'],
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'jenis_kontrak' => $validated['jenis_kontrak'] ?? null,
            'bidang' => $validated['bidang'] ?? 'Aset',
            'sumber_dana' => $validated['sumber_dana'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'opd_id' => $validated['opd_id'] ?? null,
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        app(ContractDocumentService::class)->save($contract, $request->file('dokumen'));

        AuditLog::log('create', "Admin membuat kontrak baru: {$contract->nomor_kontrak}", $contract);

        if ($request->expectsJson()) {
            $request->session()->flash('success', 'Kontrak berhasil ditambahkan.');

            return response()->json(['redirect' => route('admin.contracts.index')], 201);
        }

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Kontrak berhasil ditambahkan.');
    }

    /**
     * Show contract details
     */
    public function show(Contract $contract)
    {
        return view('admin.contracts.show', compact('contract'));
    }

    /**
     * Show edit form
     */
    public function edit(Contract $contract)
    {
        $opds = Opd::orderBy('nama')->get();

        return view('admin.contracts.edit', compact('contract', 'opds'));
    }

    /**
     * Update contract
     */
    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'nomor_kontrak' => ['required', 'string', 'max:100', 'unique:contracts,nomor_kontrak,'.$contract->id],
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
            'opd_id' => ['nullable', 'exists:opds,id'],
            'tahun_anggaran' => ['required', 'integer', 'min:2000', 'max:2100'],
            'keterangan' => ['nullable', 'string'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf', 'max:30720'], // Max 30MB
        ]);

        $oldValues = $contract->toArray();

        $contract->fill([
            'nomor_kontrak' => $validated['nomor_kontrak'],
            'nama_kontrak' => $validated['nama_kontrak'],
            'nama_penyedia' => $validated['nama_penyedia'],
            'nilai_kontrak' => $validated['nilai_kontrak'],
            'tanggal_kontrak' => $validated['tanggal_kontrak'],
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'jenis_kontrak' => $validated['jenis_kontrak'] ?? null,
            'bidang' => $validated['bidang'] ?? 'Aset',
            'sumber_dana' => $validated['sumber_dana'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'opd_id' => $validated['opd_id'] ?? null,
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        app(ContractDocumentService::class)->save($contract, $request->file('dokumen'));

        AuditLog::log('update', "Admin mengupdate kontrak: {$contract->nomor_kontrak}", $contract, $oldValues);

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Kontrak berhasil diperbarui.');
    }

    /**
     * Delete contract
     */
    public function destroy(Contract $contract)
    {
        app(ContractDocumentService::class)->destroy($contract);

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Kontrak berhasil dihapus.');
    }

    /**
     * Validate contract number (AJAX endpoint)
     */
    public function validateContractNumber(Request $request)
    {
        $nomorKontrak = $request->input('nomor_kontrak');
        $contractId = $request->input('contract_id');

        if (! $nomorKontrak) {
            return response()->json([
                'valid' => false,
                'message' => 'Nomor kontrak tidak boleh kosong.',
            ]);
        }

        $query = Contract::where('nomor_kontrak', $nomorKontrak);

        if ($contractId) {
            $query->where('id', '!=', $contractId);
        }

        $exists = $query->exists();

        if ($exists) {
            $contract = $query->first();

            return response()->json([
                'valid' => false,
                'message' => 'Nomor kontrak sudah terdaftar.',
                'existing' => [
                    'nama_kontrak' => $contract->nama_kontrak,
                    'nama_penyedia' => $contract->nama_penyedia,
                    'tahun_anggaran' => $contract->tahun_anggaran,
                    'opd' => $contract->opd?->singkatan ?? '-',
                ],
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Nomor kontrak tersedia.',
        ]);
    }
}
