<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_type',
        'year',
        'month',
        'quarter',
        'iso_week',
        'scope',
        'bidang_id',
        'template_id',
        'status',     // processing, ready, failed
        'file_path',
        'requested_by',
    ];

    protected $casts = [
        'year'   => 'integer',
        'month'  => 'integer',
        'quarter'=> 'integer',
        'iso_week'=> 'integer',
    ];

    public function bidang()
    {
        return $this->belongsTo(\App\Models\Bidang::class);
    }

    public function template()
    {
        return $this->belongsTo(\App\Models\ExportTemplate::class, 'template_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }
}
