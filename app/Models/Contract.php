<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    // Jenis Kontrak Options
    public const JENIS_KONTRAK = 'Kontrak';

    public const JENIS_SPK = 'SPK';

    public const JENIS_OPTIONS = [
        self::JENIS_KONTRAK => 'Kontrak',
        self::JENIS_SPK => 'SPK',
    ];

    // Sumber Dana Options
    public const SUMBER_DANA_OPTIONS = [
        'APBD' => 'APBD',
        'APBN' => 'APBN',
        'DAK' => 'DAK',
        'DAU' => 'DAU',
        'OTSUS' => 'OTSUS',
        'Hibah' => 'Hibah',
        'Lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'nomor_kontrak',
        'nama_kontrak',
        'nama_penyedia',
        'nilai_kontrak',
        'tanggal_kontrak',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_kontrak',
        'bidang',
        'sumber_dana',
        'lokasi',
        'opd_id',
        'tahun_anggaran',
        'keterangan',
        'file_path',
        'file_name',
        'file_size',
        'uploaded_by',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kontrak' => 'date',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'uploaded_at' => 'datetime',
            'nilai_kontrak' => 'decimal:2',
        ];
    }

    // ==================== Relations ====================

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'model_id')
            ->where('model_type', self::class);
    }

    // ==================== Helpers ====================

    public function hasFile(): bool
    {
        return ! empty($this->file_path);
    }

    public function getFileSizeFormatted(): string
    {
        if (! $this->file_size) {
            return '-';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    public function getNilaiKontrakFormatted(): string
    {
        return 'Rp '.number_format($this->nilai_kontrak, 0, ',', '.');
    }

    public function isAccessibleBy(User $user): bool
    {
        return $user->isAdmin() || ($user->opd_id !== null
            && $this->opd_id !== null
            && (int) $this->opd_id === (int) $user->opd_id);
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $user->opd_id === null
            ? $query->whereRaw('1 = 0')
            : $query->where('opd_id', $user->opd_id);
    }

    // ==================== Scopes ====================

    public function scopeSearch($query, ?string $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nomor_kontrak', 'like', "%{$search}%")
                ->orWhere('nama_kontrak', 'like', "%{$search}%")
                ->orWhere('nama_penyedia', 'like', "%{$search}%");
        });
    }

    public function scopeFilterTahun($query, $tahun)
    {
        if (! $tahun) {
            return $query;
        }

        return $query->where('tahun_anggaran', $tahun);
    }

    public function scopeFilterJenis($query, $jenis)
    {
        if (! $jenis) {
            return $query;
        }

        return $query->where('jenis_kontrak', $jenis);
    }

    public function scopeFilterOpd($query, $opdId)
    {
        if (! $opdId) {
            return $query;
        }

        return $query->where('opd_id', $opdId);
    }

    public function scopeFilterPenyedia($query, $penyedia)
    {
        if (! $penyedia) {
            return $query;
        }

        return $query->where('nama_penyedia', 'like', "%{$penyedia}%");
    }
}
