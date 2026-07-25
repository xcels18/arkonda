<?php

namespace App\Services;

use Carbon\Carbon;

class HolidayThemeService
{
    /**
     * Resolve tema hari besar yang aktif untuk tanggal saat ini.
     * Mengembalikan null jika tidak ada tema aktif atau fitur dimatikan.
     */
    public static function active(?Carbon $now = null): ?array
    {
        if (! config('holiday_themes.enabled', true)) {
            return null;
        }

        $now = $now ?? Carbon::now();
        $themes = config('holiday_themes.themes', []);

        foreach ($themes as $theme) {
            if (self::isDateInRange($now, $theme['start'], $theme['end'])) {
                return self::withComputedText($theme, $now);
            }
        }

        return null;
    }

    /**
     * Cek apakah tanggal `$now` berada dalam rentang `MM-DD` s.d `MM-DD`.
     * Mendukung rentang yang melewati pergantian tahun (contoh: 12-30 s.d 01-02).
     */
    private static function isDateInRange(Carbon $now, string $start, string $end): bool
    {
        $year = $now->year;

        $startDate = Carbon::createFromFormat('Y-m-d', "{$year}-{$start}")->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', "{$year}-{$end}")->endOfDay();

        if ($endDate->lessThan($startDate)) {
            // Rentang melewati pergantian tahun, misal 12-30 s.d 01-02:
            // aktif jika >= tanggal mulai tahun ini, ATAU <= tanggal akhir tahun ini
            // (yang sebenarnya kelanjutan dari rentang yang dimulai tahun lalu).
            return $now->greaterThanOrEqualTo($startDate) || $now->lessThanOrEqualTo($endDate);
        }

        return $now->betweenIncluded($startDate, $endDate);
    }

    /**
     * Sisipkan teks badge dengan variabel terhitung, contoh :tahun untuk
     * kemerdekaan otomatis dihitung dari tahun berjalan dikurangi 1945.
     */
    private static function withComputedText(array $theme, Carbon $now): array
    {
        $replacements = [
            ':tahun' => $now->year,
        ];

        if ($theme['key'] === 'kemerdekaan') {
            $replacements[':tahun'] = $now->year - 1945;
        }

        $theme['badge_text'] = strtr($theme['badge_text_template'] ?? '', $replacements);

        return $theme;
    }
}
