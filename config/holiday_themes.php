<?php

/**
 * Konfigurasi tema hari besar/nasional untuk halaman login.
 *
 * Setiap tema aktif otomatis pada rentang tanggal (format 'MM-DD') yang
 * ditentukan, mengikuti tahun berjalan (tidak perlu update tahun setiap kali).
 * Jika ada dua tema yang tanggalnya beririsan, tema pertama yang cocok
 * (urutan array ini) yang akan dipakai.
 *
 * Untuk menambah tema baru, cukup tambahkan entri array baru di sini -
 * tidak perlu mengubah kode Blade/Controller.
 */

return [

    'enabled' => env('HOLIDAY_THEME_ENABLED', true),

    'themes' => [

        [
            'key' => 'kemerdekaan',
            'name' => 'Hari Kemerdekaan RI',
            // Ditampilkan mulai 1 Agustus s.d 20 Agustus setiap tahun
            'start' => '08-01',
            'end' => '08-20',
            'badge_text_template' => 'Dirgahayu Republik Indonesia ke-:tahun',
            'colors' => [
                'primary' => '#DC2626',   // merah
                'primary_dark' => '#991B1B',
                'accent' => '#FFFFFF',    // putih
                'gradient_from' => '#B91C1C',
                'gradient_via' => '#DC2626',
                'gradient_to' => '#7F1D1D',
            ],
            'icon' => 'flag',
        ],

        [
            'key' => 'natal',
            'name' => 'Hari Natal',
            'start' => '12-20',
            'end' => '12-26',
            'badge_text_template' => 'Selamat Hari Natal',
            'colors' => [
                'primary' => '#B91C1C',
                'primary_dark' => '#7F1D1D',
                'accent' => '#15803D',
                'gradient_from' => '#166534',
                'gradient_via' => '#15803D',
                'gradient_to' => '#7F1D1D',
            ],
            'icon' => 'star',
        ],

        [
            'key' => 'tahun_baru',
            'name' => 'Tahun Baru',
            'start' => '12-30',
            'end' => '01-02',
            'badge_text_template' => 'Selamat Tahun Baru :tahun',
            'colors' => [
                'primary' => '#7C3AED',
                'primary_dark' => '#5B21B6',
                'accent' => '#FBBF24',
                'gradient_from' => '#4C1D95',
                'gradient_via' => '#7C3AED',
                'gradient_to' => '#1E1B4B',
            ],
            'icon' => 'sparkles',
        ],

    ],

];
