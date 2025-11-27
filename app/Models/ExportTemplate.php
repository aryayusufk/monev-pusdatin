<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'period_type', // week, month, quarter, year
        'scope',       // global, bidang
        'file_path',   // path template asli
    ];

    public function exports()
    {
        return $this->hasMany(\App\Models\ReportExport::class, 'template_id');
    }
}
