<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth; // ⬅️ TAMBAH INI

class SystemLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function write(string $action, string $description): void
    {
        // ⬅️ GANTI ini:
        // $user = auth()->user();

        // ⬇️ MENJADI:
        $user = Auth::user();

        self::create([
            'user_id'    => $user?->id,
            'action'     => $action,
            'description'=> $description,
        ]);
    }
}
