<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'jenis_kontrak' => ['required', 'string', 'in:' . implode(',', array_keys(Contract::JENIS_OPTIONS))],
            'bidang' => ['nullable', 'string', 'max:100'],
            'sumber_dana' => ['nullable', 'string', 'in:' . implode(',', array_keys(Contract::SUMBER_DANA_OPTIONS))],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'opd_id' => ['nullable', 'exists:opds,id'],
            'tahun_anggaran' => ['required', 'integer', 'min:2000', 'max:2100'],
            'keterangan' => ['nullable', 'string'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf', 'max:512000'], // Max 500MB
        ]);

        $contract = Contract::create([
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

        // Handle file upload
        if ($request->hasFile('dokumen')) {
            $this->uploadDocument($contract, $request->file('dokumen'));
        }

        AuditLog::log('create', "Admin membuat kontrak baru: {$contract->nomor_kontrak}", $contract);

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
            'nomor_kontrak' => ['required', 'string', 'max:100', 'unique:contracts,nomor_kontrak,' . $contract->id],
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
            'opd_id' => ['nullable', 'exists:opds,id'],
            'tahun_anggaran' => ['required', 'integer', 'min:2000', 'max:2100'],
            'keterangan' => ['nullable', 'string'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf', 'max:512000'],
        ]);

        $oldValues = $contract->toArray();

        $contract->update([
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

        // Handle file upload
        if ($request->hasFile('dokumen')) {
            $this->uploadDocument($contract, $request->file('dokumen'));
        }

        AuditLog::log('update', "Admin mengupdate kontrak: {$contract->nomor_kontrak}", $contract, $oldValues);

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Kontrak berhasil diperbarui.');
    }

    /**
     * Delete contract
     */
    public function destroy(Contract $contract)
    {
        // Delete associated file if exists
        if ($contract->hasFile()) {
            $this->deleteDocument($contract);
        }

        $contractNumber = $contract->nomor_kontrak;
        $contract->delete();

        AuditLog::log('delete', "Admin menghapus kontrak: {$contractNumber}");

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Kontrak berhasil dihapus.');
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
     * Validate contract number (AJAX endpoint)
     */
    public function validateContractNumber(Request $request)
    {
        $nomorKontrak = $request->input('nomor_kontrak');
        $contractId = $request->input('contract_id');

        if (!$nomorKontrak) {
            return response()->json([
                'valid' => false,
                'message' => 'Nomor kontrak tidak boleh kosong.'
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
                    'opd' => $contract->opd?->singkatan ?? '-'
                ]
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Nomor kontrak tersedia.'
        ]);
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

