# Konfigurasi Upload File 500MB

Aplikasi ini sudah dikonfigurasi untuk mendukung upload file hingga 500MB.

## Konfigurasi yang Sudah Diterapkan

### 1. Laravel Validation
- File: `app/Http/Controllers/Admin/ContractController.php`
- File: `app/Http/Controllers/ContractController.php`
- Max size: 512000 KB (500 MB)

### 2. Apache Configuration
- File: `public/.htaccess`
- Settings:
  - upload_max_filesize = 500M
  - post_max_size = 500M
  - max_execution_time = 300
  - max_input_time = 300

### 3. PHP-FPM Configuration (jika menggunakan PHP-FPM)
- File: `public/.user.ini`
- Settings sama seperti di atas

## Jika Upload Masih Gagal

Jika masih ada masalah saat upload file besar, lakukan langkah berikut:

### Untuk Server dengan akses php.ini:

Edit file `php.ini` dan ubah nilai berikut:

```ini
upload_max_filesize = 500M
post_max_size = 500M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

Kemudian restart web server:
- Apache: `sudo systemctl restart apache2` atau `sudo service apache2 restart`
- Nginx + PHP-FPM: `sudo systemctl restart php-fpm` dan `sudo systemctl restart nginx`

### Untuk Nginx:

Tambahkan di file konfigurasi nginx (biasanya di `/etc/nginx/nginx.conf` atau di site config):

```nginx
client_max_body_size 500M;
```

Kemudian restart nginx: `sudo systemctl restart nginx`

### Verifikasi Konfigurasi

Untuk memverifikasi konfigurasi PHP yang aktif:

```bash
php -i | grep -i "upload_max_filesize"
php -i | grep -i "post_max_size"
```

Atau buat file PHP dengan isi:
```php
<?php phpinfo(); ?>
```
Dan akses melalui browser untuk melihat semua konfigurasi PHP.
