<?php

namespace App\Exports;

use App\Models\Contract;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContractsExport extends DefaultValueBinder implements FromQuery, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function bindValue(Cell $cell, $value): bool
    {
        // Only the row number, amount, and budget year are numeric columns.
        if (in_array($cell->getColumn(), ['A', 'G', 'M'], true) && is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);

            return true;
        }

        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function query()
    {
        $query = Contract::with(['opd', 'uploader']);

        if (! empty($this->filters['tahun'])) {
            $query->where('tahun_anggaran', $this->filters['tahun']);
        }

        if (! empty($this->filters['opd_id'])) {
            $query->where('opd_id', $this->filters['opd_id']);
        }

        if (! empty($this->filters['jenis'])) {
            $query->where('jenis_kontrak', $this->filters['jenis']);
        }

        if (! empty($this->filters['sumber_dana'])) {
            $query->where('sumber_dana', $this->filters['sumber_dana']);
        }

        return $query->orderBy('tahun_anggaran', 'desc')->orderBy('tanggal_kontrak', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Kontrak',
            'Nama Kontrak',
            'OPD',
            'Jenis',
            'Nama Penyedia',
            'Nilai Kontrak',
            'Sumber Dana',
            'Lokasi',
            'Tanggal Kontrak',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Tahun Anggaran',
            'Bidang',
            'Keterangan',
            'File Dokumen',
        ];
    }

    public function map($contract): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $contract->nomor_kontrak,
            $this->formatText($contract->nama_kontrak),
            $contract->opd?->singkatan ?? '-',
            $contract->jenis_kontrak ?? '-',
            $contract->nama_penyedia,
            $contract->nilai_kontrak,
            $contract->sumber_dana ?? '-',
            $contract->lokasi ?? '-',
            $contract->tanggal_kontrak?->format('d/m/Y') ?? '-',
            $contract->tanggal_mulai?->format('d/m/Y') ?? '-',
            $contract->tanggal_selesai?->format('d/m/Y') ?? '-',
            $contract->tahun_anggaran,
            $contract->bidang ?? '-',
            $contract->keterangan ?? '-',
            $contract->hasFile() ? 'Ada' : 'Tidak Ada',
        ];
    }

    /**
     * Normalize text casing to Title Case for consistent display in export.
     */
    private function formatText(?string $text): string
    {
        if (empty($text)) {
            return '-';
        }

        return Str::title(Str::lower($text));
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Rekap Kontrak';
    }
}
