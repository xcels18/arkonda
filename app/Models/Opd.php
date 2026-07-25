<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opd extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'singkatan',
    ];

    // ==================== Relations ====================

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    // ==================== Helpers ====================

    public function getDisplayName(): string
    {
        return $this->singkatan ? "{$this->singkatan} - {$this->nama}" : $this->nama;
    }
}

