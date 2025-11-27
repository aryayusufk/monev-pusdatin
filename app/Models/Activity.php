<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $fillable = [
    'bidang_id',
    'kegiatan',
    'tindak_lanjut',
    'kendala',
    'percepatan',
    'proyeksi_kedepan',
    'occurred_at',
    'year',
    'month',
    'quarter',
    'iso_week',
    'status',
    'is_selected_quarterly',
    'selected_quarterly_scope',
    'selected_quarterly_by',
    'selected_quarterly_at',
    'created_by',
    'updated_by',
];

    protected $casts = [
        'occurred_at'            => 'datetime',
        'is_selected_quarterly'  => 'boolean',
        'selected_quarterly_at'  => 'datetime',
    ];

    /* ========= RELASI ========= */

    public function bidang()
    {
        // Laporan milik satu bidang
        return $this->belongsTo(\App\Models\Bidang::class);
    }

    public function ikus()
    {
        // Laporan bisa dikaitkan ke banyak IKU (pivot activity_iku)
        return $this->belongsToMany(\App\Models\Iku::class, 'activity_iku')
            ->withPivot('contribution');
    }

    public function files()
    {
        // Data pendukung (PDF, dll.) yang diupload user
        return $this->hasMany(ActivityFile::class);
    }

    public function notes()
    {
        // Catatan monev, dll.
        return $this->hasMany(\App\Models\ActivityNote::class);
    }

    public function audits()
    {
        // Log aktivitas (create, update, seleksi triwulan, dsb.)
        return $this->hasMany(\App\Models\ActivityAudit::class);
    }

    public function selectedBy()
    {
        // User yang menyeleksi untuk laporan triwulan
        return $this->belongsTo(\App\Models\User::class, 'selected_quarterly_by');
    }

    /* ========= SCOPES FILTER PERIOD & IKU ========= */

    public function scopeOfYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeOfMonth($query, int $month)
    {
        return $query->where('month', $month);
    }

    public function scopeOfQuarter($query, int $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    public function scopeOfWeek($query, int $isoWeek)
    {
        return $query->where('iso_week', $isoWeek);
    }

    public function scopeByIku($query, int $ikuId)
    {
        // Filter kegiatan yang terkait dengan IKU tertentu
        return $query->whereHas('ikus', function ($q) use ($ikuId) {
            $q->where('id', $ikuId);
        });
    }

    public function scopeSelectedQuarterly($query, bool $selected = true)
    {
        return $query->where('is_selected_quarterly', $selected);
    }
}
