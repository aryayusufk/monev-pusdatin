<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
    ];

    public function users()
    {
        // Satu bidang punya banyak user (user_bidang, monev_bidang, dll.)
        return $this->hasMany(\App\Models\User::class);
    }

    public function activities()
    {
        // Satu bidang punya banyak laporan kegiatan
        return $this->hasMany(\App\Models\Activity::class);
    }

    public function ikus()
    {
        return $this->belongsToMany(
            Iku::class,
            'iku_bidang',
            'bidang_id',
            'iku_id'
        );
    }

    public function scopeWithIku($query)
    {
        return $query->whereHas('ikus');
    }

    public function hasIku(int $ikuId): bool
    {
        return $this->ikus()->where('ikus.id', $ikuId)->exists();
    }
}
