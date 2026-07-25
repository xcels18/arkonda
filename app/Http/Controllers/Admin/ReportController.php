<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Opd;
use App\Exports\ContractsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display report page with filters
     */
    public function index(Request $request)
    {
        // Get filter options
        $years = Contract::distinct()
            ->orderBy('tahun_anggaran', 'desc')
            ->pluck('tahun_anggaran');
        
        $opds = Opd::orderBy('nama')->get();

        // Get summary statistics
        $query = Contract::query();

        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }

        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_kontrak', $request->jenis);
        }

        $stats = [
            'total_kontrak' => (clone $query)->count(),
            'total_nilai' => (clone $query)->sum('nilai_kontrak'),
            'kontrak_count' => (clone $query)->where('jenis_kontrak', Contract::JENIS_KONTRAK)->count(),
            'spk_count' => (clone $query)->where('jenis_kontrak', Contract::JENIS_SPK)->count(),
            'dengan_dokumen' => (clone $query)->whereNotNull('file_path')->count(),
            'tanpa_dokumen' => (clone $query)->whereNull('file_path')->count(),
        ];

        // Get contracts for preview (limited)
        $contracts = (clone $query)
            ->with(['opd'])
            ->orderBy('tahun_anggaran', 'desc')
            ->orderBy('tanggal_kontrak', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', compact('years', 'opds', 'stats', 'contracts'));
    }

    /**
     * Export contracts to Excel
     */
    public function export(Request $request)
    {
        $filters = [
            'tahun' => $request->tahun,
            'opd_id' => $request->opd_id,
            'jenis' => $request->jenis,
        ];

        $filename = 'Rekap_Kontrak';
        
        if ($request->filled('tahun')) {
            $filename .= '_' . $request->tahun;
        }
        
        if ($request->filled('opd_id')) {
            $opd = Opd::find($request->opd_id);
            if ($opd) {
                $filename .= '_' . $opd->singkatan;
            }
        }
        
        if ($request->filled('jenis')) {
            $filename .= '_' . $request->jenis;
        }
        
        $filename .= '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new ContractsExport($filters), $filename);
    }
}

