# Keamanan data dan operasional

## Provisioning administrator

`AdminUserSeeder` memerlukan `BOOTSTRAP_ADMIN_EMAIL` dan `BOOTSTRAP_ADMIN_PASSWORD`
(minimal 12 karakter) dari konfigurasi lingkungan. Tidak ada akun atau kata sandi
bawaan dan tidak ada pengguna demo yang dibuat. Seeder tidak menimpa akun yang
sudah ada maupun menaikkan role pengguna dengan email yang sama. Hapus variabel
bootstrap setelah provisioning, lalu bangun ulang cache konfigurasi jika digunakan.
Akun bawaan dari deployment lama harus diperiksa dan passwordnya direset melalui
pengelolaan pengguna; mengubah seeder tidak mengubah password akun yang sudah ada.

## Sesi dan reset kata sandi

Login dan registrasi menyimpan pembanding hash password di sesi. Middleware
`AuthenticateSession` memeriksanya pada request berikutnya, termasuk cookie ingat
saya. Reset password merotasi remember token. Dengan driver sesi database, record
sesi pengguna juga dihapus. Sesi yang telah melewati middleware akan ditolak setelah
password berubah. Saat pertama menerapkan perbaikan ini pada deployment lama,
invalidasikan sesi lama yang belum memiliki pembanding hash, sesuai driver sesi
yang digunakan (misalnya bersihkan penyimpanan sesi aplikasi saat maintenance).

## Penghapusan kontrak dan pemulihan

Sebelum menghapus record, aplikasi menyalin PDF ke `.deleted/<uuid>/<filename>` di
**disk privat contracts**. Penghapusan record dan pencatatan audit berada dalam satu
transaksi. Kegagalan transaksi mempertahankan record dan PDF asal. Jika pembersihan
file asal gagal sesudah commit, salinan arsip tetap tersedia dan kegagalan dicatat.

Audit `delete` menyimpan metadata kontrak sebelum penghapusan (`old_values`) dan
lokasi salinan pemulihan (`new_values.archived_document`). Salinan ini tidak
memiliki route publik dan tidak ikut unduhan ZIP. Belum ada antarmuka pemulihan;
operator berwenang perlu memulihkan metadata dan menyalin file dari arsip privat
ke `old_values.file_path` melalui prosedur maintenance, memeriksa benturan ID dan
nomor kontrak, serta memastikan OPD dan identitas uploader masih valid. Catat audit
pemulihan. Sertakan database audit dan seluruh disk contracts (termasuk `.deleted`)
dalam backup. Jangan menghapus arsip sebelum kebijakan retensi disepakati.

## Ekspor dan audit

Binder ekspor memperlakukan kolom teks sebagai string, termasuk teks yang diawali
`=`. Hanya nomor urut, nilai kontrak, dan tahun anggaran yang numerik. Perubahan
role dan pembatalan verifikasi mencatat pelaku, target, waktu, serta nilai sebelum
dan sesudah, dalam transaksi yang sama dengan perubahan data. Reset password juga
dicatat tanpa menyimpan password atau remember token dalam audit.
