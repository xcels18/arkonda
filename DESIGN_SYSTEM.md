# Panduan Desain Aplikasi Inspektorat Kabupaten Puncak Jaya

Versi 1.0 · 3 Oktober 2026

Dokumen ini merangkum tema visual e-Konsul agar aplikasi lain milik Inspektorat tampil seragam. Gunakan aturan inti di bawah untuk antarmuka web; sesuaikan nama aplikasi, menu, isi, dan alur dengan fungsi aplikasi baru. Rujukan implementasi saat dokumen dibuat adalah `resources/css/app.css`, `resources/views/layouts/app.blade.php`, serta templat cetak di `resources/views/reports/`.

## Karakter visual

Tenang, resmi, mudah dibaca, dan hemat ruang. Latar abu sangat muda menonjolkan kartu putih. Biru tua menjadi warna identitas dan tindakan utama; hijau lembut menandai progres positif. Gunakan satu judul utama per halaman, informasi ringkas di bagian atas, lalu detail dalam kartu atau tabel. Hindari dekorasi besar yang mengurangi ruang kerja.

## Identitas dan logo

- Nama instansi: **Inspektorat Kabupaten Puncak Jaya**.
- Logo Inspektorat (`public/images/logo-inspektorat.png`) dipakai pada halaman masuk/identifikasi. Tempatkan logo di header, tepat di sebelah kiri logo/identitas produk e-Konsul, proporsional, tanpa dipotong atau diberi efek.
- Lambang Kabupaten Puncak Jaya (`public/images/logo-puncak-jaya.png`) dipakai pada kop dokumen resmi, di kiri teks “PEMERINTAH KABUPATEN PUNCAK JAYA / INSPEKTORAT”. Pertahankan rasio asli dan ruang kosong di sekelilingnya.
- Teks alamat kop: **Jl. Drs. P.A.Coem, No.01, Mulia, Puncak Jaya**. Periksa kembali alamat jika dipakai oleh unit lain.
- Pada navigasi aplikasi, identitas produk boleh berupa nama dan monogram sederhana; jangan menjadikan logo resmi sebagai pola latar.

## Token warna

| Peran | Nilai | Pemakaian |
| --- | --- | --- |
| Biru identitas | `#1E3A5F` | Sidebar, tombol utama, tautan penting |
| Biru hover | `#2D517B` | Tombol utama saat diarahkan |
| Teks utama | `#24364A` | Isi dan judul |
| Teks sekunder | `#7B8796` | Keterangan dan metadata |
| Latar halaman | `#F8FAFC` | Bidang di belakang kartu |
| Permukaan | `#FFFFFF` | Kartu, form, tabel, modal |
| Garis | `#E2E8EF` | Batas kartu dan pemisah |
| Hijau aksen | `#439575` | Titik status aktif dan aksen positif |
| Fokus | `#759CCC` | Outline elemen yang mendapat fokus |
| Sesi aktif | `#153D47` | Banner ruang konsultasi berjalan |
| Menunggu | `#FCF3DF` / `#A17C2C` | Latar/teks badge |
| Diproses | `#EAF1FC` / `#587FAF` | Latar/teks badge |
| Selesai | `#E9F4EC` / `#4A8B65` | Latar/teks badge |
| Dibatalkan | `#FBECEE` / `#B56971` | Latar/teks badge |

Jangan mengandalkan warna saja untuk menyampaikan status. Tulis label status secara eksplisit.

## Tipografi

Antarmuka menggunakan **Inter**, kemudian **Avenir Next**, lalu font sans-serif sistem. Ukuran dasar 14 px. Judul halaman sekitar 27–30 px, judul kartu 15–19 px, teks isi 12–14 px, dan metadata 10–11 px. Bobot judul 600–700; teks isi normal. Tinggi baris isi sekitar 1,5–1,7. Gunakan huruf kapital dengan jarak huruf yang renggang hanya untuk label kecil seperti “RUANG PELAYANAN” atau “MASTER DATA”.

Dokumen cetak menggunakan **Bookman Old Style** dengan fallback Bookman/serif. Isi 10 pt; judul dokumen 11 pt; nama instansi pada kop 14–16 pt. Font harus tersedia pada perangkat pencetak agar hasil persis sama; jika tidak tersedia, fallback serif akan dipakai.

## Tata letak dan jarak

- Area petugas: sidebar tetap selebar 245 px di desktop, topbar 78 px, konten maksimal 1500 px dengan padding 38 px vertikal dan 42 px horizontal.
- Area publik: header dan konten memakai lebar maksimal sekitar 1180–1280 px. Form tunggal maksimal 800 px dan berada di tengah.
- Kartu: latar putih, garis 1 px, radius 10 px, padding sekitar 20–25 px, jarak antarkartu 20–24 px.
- Tabel rekap: padat dan mudah dipindai; judul kolom kecil, baris diberi garis tipis. Form tambah/ubah data master dibuka dalam modal agar daftar tetap menjadi fokus.
- Grid dashboard: empat kartu statistik di desktop, dua di layar kecil. Detail konsultasi: kolom utama yang lebih lebar untuk isi, kolom samping untuk identitas.
- Pada lebar di bawah 760 px, sidebar berubah menjadi navigasi ringkas; grid utama menjadi satu kolom. Jangan biarkan tabel atau tombol memotong layar; beri scroll horizontal pada tabel bila perlu.

## Komponen antarmuka

| Komponen | Aturan |
| --- | --- |
| Tombol utama | Biru identitas, teks putih, radius 7 px, padding 12 × 18 px. Gunakan untuk satu tindakan utama per area. |
| Tombol sekunder | Putih dengan garis abu dan teks biru keabu-abuan. Gunakan untuk kembali, cetak, atau tindakan tambahan. |
| Input | Tinggi nyaman disentuh, padding 13 × 14 px, radius 7 px, garis `#D9E1E9`; label di atas bidang. |
| Fokus | Outline jelas `#759CCC` dengan jarak dari elemen; pertahankan navigasi keyboard. |
| Badge | Label ringkas dengan warna latar lembut sesuai status. Status harus terbaca tanpa bergantung pada warna. |
| Kartu data | Heading, isi, lalu tindakan. Gunakan pembatas halus; hindari kartu bersarang berlebihan. |
| Modal | Maksimal sekitar 520 px, radius 12 px, padding 20–24 px, tinggi dibatasi viewport, konten dapat digulir. |
| Notifikasi | Pesan sukses/error dekat awal konten; teks menyebut tindakan dan hasilnya. |
| Keadaan kosong | Jelaskan kondisi dan langkah berikutnya tanpa menyalahkan pengguna. |

Status layanan memakai istilah konsisten: **Menunggu**, **Sedang dilayani**, **Selesai**, **Selesai dan dinilai**, **Dibatalkan**. Pada sesi berjalan, tampilkan badge “Proses layanan berlangsung”, waktu mulai, dan timer `jam:menit:detik` yang dihitung dari waktu server. Timer adalah informasi sesi, bukan sumber kebenaran untuk durasi yang disimpan.

## Pola halaman

1. **Halaman publik/login:** identitas instansi di atas form, satu pesan utama, keterangan singkat, satu tindakan utama. Logo Inspektorat ditampilkan di sini.
2. **Dashboard petugas:** judul dan ringkasan statistik di atas, antrean atau aktivitas utama di kolom utama, informasi pendukung di samping.
3. **Daftar/master data:** daftar dan pencarian langsung terlihat. Penambahan dan pengubahan memakai modal; status sukses/error tampil dekat konten.
4. **Detail layanan:** nomor dan status di kepala halaman; waktu dan metrik ringkas; isi konsultasi di kolom utama; identitas pemohon dan APIP di samping.
5. **Ruang konsultasi aktif:** banner gelap, badge status, timer, permasalahan, area catatan, tindak lanjut, lalu tinjauan sebelum menyelesaikan.
6. **Dokumen resmi:** kop, judul, nomor, isi terstruktur, ruang tanda tangan, dan keterangan pencetakan.

## Dokumen cetak

Gunakan **A4 portrait (210 × 297 mm)**. TOR memakai margin 15 mm atas/bawah dan 18 mm kiri/kanan; laporan hasil konsultasi memakai 18 mm atas/bawah dan 20 mm kiri/kanan. Kop terdiri dari lambang Kabupaten Puncak Jaya di kiri dan nama pemerintah/instansi di tengah area yang tersisa, dengan garis ganda di bawah. Logo sekitar 18 × 23 mm dan `object-fit: contain`. Saat mencetak, sembunyikan tombol dan toolbar, hilangkan bayangan halaman, pertahankan ukuran isi 10 pt, dan usahakan judul serta tanda tangan tidak terpotong antarhalaman. Sarankan skala cetak 100% tanpa header/footer browser.

## Aksesibilitas dan perilaku

- Gunakan HTML semantik: satu `<h1>`, heading berurutan, label untuk setiap input, dan teks alternatif pada logo.
- Beri label pada tombol yang menyebut tindakan, misalnya “Ajukan konsultasi” atau “Simpan pembatalan”.
- Tampilkan kesalahan validasi di dekat bidang yang relevan dan tetap validasi di server.
- Status aktif, proses selesai, dan pembatalan harus memiliki teks yang jelas.
- Tombol yang belum dapat dipakai harus tampak nonaktif dan memiliki alasan singkat di dekatnya.
- Pada lebar sempit, prioritaskan isi utama dan tindakan; informasi pendukung boleh turun ke bawah.

## Paket awal untuk aplikasi lain

Salin token berikut ke stylesheet aplikasi baru dan gunakan komponen di atas sebagai pola. Nilai ini adalah titik awal; jangan menyalin alur bisnis e-Konsul bila aplikasi baru memiliki proses berbeda.

```css
:root {
  --font-ui: Inter, "Avenir Next", ui-sans-serif, system-ui, sans-serif;
  --color-brand: #1e3a5f;
  --color-brand-hover: #2d517b;
  --color-text: #24364a;
  --color-muted: #7b8796;
  --color-page: #f8fafc;
  --color-surface: #ffffff;
  --color-border: #e2e8ef;
  --color-focus: #759ccc;
  --radius-control: 7px;
  --radius-card: 10px;
}
```

Sumber aset di proyek ini: `public/images/logo-inspektorat.png` dan `public/images/logo-puncak-jaya.png`. Pastikan penggunaan lambang dan logo pada aplikasi lain sesuai kewenangan instansi.
