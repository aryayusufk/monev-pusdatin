<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iku extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'tahun',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tahun'     => 'integer',
    ];

    public function bidangs()
    {
        // IKU melibatkan banyak bidang (pivot iku_bidang)
        return $this->belongsToMany(\App\Models\Bidang::class, 'iku_bidang');
    }

    public function activities()
    {
        // Banyak kegiatan berkontribusi ke IKU ini
        return $this->belongsToMany(\App\Models\Activity::class, 'activity_iku')
            ->withPivot('contribution');
    }

    // (Opsional) scope untuk filter hanya IKU aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
