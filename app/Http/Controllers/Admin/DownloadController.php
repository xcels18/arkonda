<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DownloadController extends Controller
{
    /**
     * Show download page with filters
     */
    public function index(Request $request)
    {
        $query = Contract::with('opd')->whereNotNull('file_path');

        // Filter by year
        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }

        // Filter by OPD
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis_kontrak', $request->jenis);
        }

        // Filter by sumber dana
        if ($request->filled('sumber_dana')) {
            $query->where('sumber_dana', $request->sumber_dana);
        }

        $contracts = $query->orderBy('opd_id')->orderBy('jenis_kontrak')->orderBy('nama_kontrak')->get();

        // Group contracts by OPD and Jenis for preview
        $grouped = $contracts->groupBy(function ($contract) {
            $opdName = $contract->opd?->singkatan ?? $contract->opd?->nama ?? 'Tanpa-OPD';

            return $this->sanitizeFolderName($opdName);
        })->map(function ($opdContracts) {
            return $opdContracts->groupBy(function ($contract) {
                return $this->sanitizeFolderName($contract->jenis_kontrak ?? 'Lainnya');
            });
        });

        // Get filter options
        $years = Contract::distinct()->pluck('tahun_anggaran')->sort()->reverse();
        $opds = Opd::orderBy('nama')->get();

        // Count files
        $totalFiles = $contracts->count();
        $totalSize = $contracts->sum('file_size');

        return view('admin.downloads.index', compact('grouped', 'years', 'opds', 'totalFiles', 'totalSize'));
    }

    /**
     * Download contracts as ZIP
     */
    public function download(Request $request)
    {
        $request->validate([
            'selection_mode' => ['nullable', 'in:selected'],
            'contract_ids' => ['required_if:selection_mode,selected', 'nullable', 'array', 'min:1'],
            'contract_ids.*' => ['integer', 'distinct', 'exists:contracts,id'],
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
            'opd_id' => ['nullable', 'integer', 'exists:opds,id'],
            'jenis' => ['nullable', 'in:'.implode(',', array_keys(Contract::JENIS_OPTIONS))],
            'sumber_dana' => ['nullable', 'in:'.implode(',', array_keys(Contract::SUMBER_DANA_OPTIONS))],
        ]);

        $query = Contract::with('opd')->whereNotNull('file_path');

        // Filter by selected contract IDs (checklist)
        if ($request->filled('contract_ids')) {
            $query->whereIn('id', $request->contract_ids);
        } else {
            // Filter by year
            if ($request->filled('tahun')) {
                $query->where('tahun_anggaran', $request->tahun);
            }

            // Filter by OPD
            if ($request->filled('opd_id')) {
                $query->where('opd_id', $request->opd_id);
            }

            // Filter by jenis
            if ($request->filled('jenis')) {
                $query->where('jenis_kontrak', $request->jenis);
            }

            // Filter by sumber dana
            if ($request->filled('sumber_dana')) {
                $query->where('sumber_dana', $request->sumber_dana);
            }
        }

        $contracts = $query->get();

        if ($contracts->isEmpty()) {
            return back()->with('error', 'Tidak ada file kontrak yang dapat didownload.');
        }

        // Create ZIP file
        $zipFileName = 'Kontrak_'.date('Y-m-d_His').'.zip';
        $zipPath = storage_path('app/temp/'.Str::uuid().'.zip');

        // Ensure temp directory exists
        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        $addedFiles = 0;
        foreach ($contracts as $contract) {
            // Remove 'contracts/' prefix if exists (to avoid double path)
            $relativePath = preg_replace('/^contracts\//', '', $contract->file_path);
            $filePath = Storage::disk('contracts')->path($relativePath);

            if (file_exists($filePath)) {
                // Create folder structure: OPD/Jenis/filename.pdf
                $opdName = $contract->opd?->singkatan ?? $contract->opd?->nama ?? 'Tanpa-OPD';
                $jenisKontrak = $contract->jenis_kontrak ?? 'Lainnya';
                $fileName = $this->generateFileName($contract);

                $folderPath = $this->sanitizeFolderName($opdName).'/'.
                              $this->sanitizeFolderName($jenisKontrak).'/'.
                              $fileName;

                if (! $zip->addFile($filePath, $folderPath)) {
                    $zip->close();
                    @unlink($zipPath);

                    return back()->with('error', 'Gagal menambahkan dokumen ke ZIP. Silakan coba kembali.');
                }
                $addedFiles++;
            }
        }

        if (! $zip->close()) {
            @unlink($zipPath);

            return back()->with('error', 'Gagal menyimpan file ZIP. Silakan coba kembali.');
        }

        if ($addedFiles === 0) {
            @unlink($zipPath);

            return back()->with('error', 'Tidak ada file yang ditemukan di server.');
        }

        AuditLog::log('download', "Admin mendownload {$addedFiles} file kontrak dalam ZIP");

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Generate filename for contract
     */
    private function generateFileName(Contract $contract): string
    {
        $nama = $contract->nama_kontrak;
        $penyedia = $contract->nama_penyedia;
        $tahun = $contract->tahun_anggaran;

        // Sanitize and limit length (keep spaces, remove special chars)
        $nama = preg_replace('/[^a-zA-Z0-9\s]/', '', $nama);
        $nama = preg_replace('/\s+/', ' ', $nama); // normalize multiple spaces
        $nama = trim(substr($nama, 0, 50));

        $penyedia = preg_replace('/[^a-zA-Z0-9\s]/', '', $penyedia);
        $penyedia = preg_replace('/\s+/', ' ', $penyedia); // normalize multiple spaces
        $penyedia = trim(substr($penyedia, 0, 50));

        return $nama.'_'.$penyedia.'_'.$tahun.'_'.$contract->id.'.pdf';
    }

    /**
     * Sanitize folder/file name
     */
    private function sanitizeFolderName(string $name): string
    {
        $name = preg_replace('/[^a-zA-Z0-9\s]/', '', $name);
        $name = preg_replace('/\s+/', ' ', $name); // normalize multiple spaces

        return trim($name) ?: 'Unknown';
    }
}
