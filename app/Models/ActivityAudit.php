<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
        'action',       // misal: 'create', 'update', 'select_quarterly'
        'payload',      // json ringkas perubahan
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function activity()
    {
        return $this->belongsTo(\App\Models\Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
