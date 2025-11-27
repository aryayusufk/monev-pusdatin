<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityFile extends Model
{
    protected $fillable = [
        'activity_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
