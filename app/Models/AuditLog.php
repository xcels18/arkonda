<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // ==================== Relations ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== Static Logging Methods ====================

    public static function log(
        string $action,
        ?string $description = null,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    public static function logLogin(User $user): self
    {
        return self::log('login', "User {$user->name} logged in", $user);
    }

    public static function logLogout(User $user): self
    {
        return self::log('logout', "User {$user->name} logged out", $user);
    }

    public static function logUpload(Contract $contract, User $user): self
    {
        return self::log(
            'upload',
            "User {$user->name} uploaded document for contract {$contract->nomor_kontrak}",
            $contract
        );
    }

    public static function logDownload(Contract $contract, User $user): self
    {
        return self::log(
            'download',
            "User {$user->name} downloaded document for contract {$contract->nomor_kontrak}",
            $contract
        );
    }

    public static function logVerifyUser(User $verifiedUser, User $admin): self
    {
        return self::log(
            'verify_user',
            "Admin {$admin->name} verified user {$verifiedUser->name}",
            $verifiedUser
        );
    }

    // ==================== Scopes ====================

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ==================== Helpers ====================

    public function getActionLabel(): string
    {
        return match ($this->action) {
            'login' => 'Login',
            'logout' => 'Logout',
            'upload' => 'Upload Dokumen',
            'download' => 'Download Dokumen',
            'create' => 'Buat Data',
            'update' => 'Edit Data',
            'delete' => 'Hapus Data',
            'verify_user' => 'Verifikasi User',
            'unverify_user' => 'Batalkan Verifikasi User',
            'change_role' => 'Ubah Role User',
            'reset_password' => 'Reset Kata Sandi',
            default => ucfirst($this->action),
        };
    }

    public function getActionColor(): string
    {
        return match ($this->action) {
            'login' => 'text-green-600',
            'logout' => 'text-gray-600',
            'upload' => 'text-blue-600',
            'download' => 'text-indigo-600',
            'create' => 'text-green-600',
            'update' => 'text-yellow-600',
            'delete' => 'text-red-600',
            'verify_user' => 'text-purple-600',
            default => 'text-gray-600',
        };
    }
}
