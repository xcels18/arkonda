<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opds = [
            ['kode' => '01', 'nama' => 'Dinas Pendidikan', 'singkatan' => 'Disdik'],
            ['kode' => '02', 'nama' => 'Dinas Kesehatan', 'singkatan' => 'Dinkes'],
            ['kode' => '03', 'nama' => 'Dinas Pekerjaan Umum dan Penataan Ruang', 'singkatan' => 'DPUPR'],
            ['kode' => '04', 'nama' => 'Dinas Perumahan dan Kawasan Permukiman', 'singkatan' => 'Disperkim'],
            ['kode' => '05', 'nama' => 'Satuan Polisi Pamong Praja', 'singkatan' => 'Satpol PP'],
            ['kode' => '06', 'nama' => 'Badan Penanggulangan Bencana Daerah', 'singkatan' => 'BPBD'],
            ['kode' => '07', 'nama' => 'Dinas Sosial', 'singkatan' => 'Dinsos'],
            ['kode' => '08', 'nama' => 'Dinas Tenaga Kerja', 'singkatan' => 'Disnaker'],
            ['kode' => '09', 'nama' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak', 'singkatan' => 'DP3A'],
            ['kode' => '10', 'nama' => 'Dinas Ketahanan Pangan', 'singkatan' => 'DKP'],
            ['kode' => '11', 'nama' => 'Dinas Lingkungan Hidup', 'singkatan' => 'DLH'],
            ['kode' => '12', 'nama' => 'Dinas Kependudukan dan Pencatatan Sipil', 'singkatan' => 'Disdukcapil'],
            ['kode' => '13', 'nama' => 'Dinas Pemberdayaan Masyarakat dan Desa', 'singkatan' => 'DPMD'],
            ['kode' => '14', 'nama' => 'Dinas Pengendalian Penduduk dan Keluarga Berencana', 'singkatan' => 'DPPKB'],
            ['kode' => '15', 'nama' => 'Dinas Perhubungan', 'singkatan' => 'Dishub'],
            ['kode' => '16', 'nama' => 'Dinas Komunikasi dan Informatika', 'singkatan' => 'Diskominfo'],
            ['kode' => '17', 'nama' => 'Dinas Koperasi dan UKM', 'singkatan' => 'Diskop UKM'],
            ['kode' => '18', 'nama' => 'Dinas Penanaman Modal dan PTSP', 'singkatan' => 'DPMPTSP'],
            ['kode' => '19', 'nama' => 'Dinas Kepemudaan dan Olahraga', 'singkatan' => 'Dispora'],
            ['kode' => '20', 'nama' => 'Dinas Kebudayaan dan Pariwisata', 'singkatan' => 'Disbudpar'],
            ['kode' => '21', 'nama' => 'Dinas Perpustakaan dan Kearsipan', 'singkatan' => 'Dispusip'],
            ['kode' => '22', 'nama' => 'Dinas Pertanian', 'singkatan' => 'Distan'],
            ['kode' => '23', 'nama' => 'Dinas Perikanan', 'singkatan' => 'Diskan'],
            ['kode' => '24', 'nama' => 'Dinas Perdagangan dan Perindustrian', 'singkatan' => 'Disperdagin'],
            ['kode' => '25', 'nama' => 'Sekretariat Daerah', 'singkatan' => 'Setda'],
            ['kode' => '26', 'nama' => 'Sekretariat DPRD', 'singkatan' => 'Set DPRD'],
            ['kode' => '27', 'nama' => 'Badan Perencanaan Pembangunan Daerah', 'singkatan' => 'Bappeda'],
            ['kode' => '28', 'nama' => 'Badan Pengelolaan Keuangan dan Aset Daerah', 'singkatan' => 'BPKAD'],
            ['kode' => '29', 'nama' => 'Badan Pendapatan Daerah', 'singkatan' => 'Bapenda'],
            ['kode' => '30', 'nama' => 'Badan Kepegawaian Daerah', 'singkatan' => 'BKD'],
            ['kode' => '31', 'nama' => 'Inspektorat', 'singkatan' => 'Inspektorat'],
            ['kode' => '32', 'nama' => 'Kecamatan', 'singkatan' => 'Kec'],
            ['kode' => '33', 'nama' => 'Kelurahan', 'singkatan' => 'Kel'],
        ];

        foreach ($opds as $opd) {
            Opd::create($opd);
        }
    }
}

