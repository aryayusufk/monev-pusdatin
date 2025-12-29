<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Bidang;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'password',
        'bidang_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function bidang()
    {
        return $this->belongsTo(\App\Models\Bidang::class);
    }

    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class)->withTimestamps();
    }

    public function hasRole(string|array $names): bool
    {
        $names = is_array($names) ? $names : [$names];

        if ($this->relationLoaded('roles')) {
            return $this->roles->contains(fn($r) => in_array($r->name, $names, true));
        }

        return $this->roles()->whereIn('name', $names)->exists();
    }

    public function defaultBidangReportUrl(): string
    {
        // Jika user tidak punya akses menu Laporan Bidang, fallback aman
        if (!$this->can('reports.bidang.view')) {
            return route('activities.index');
        }

        // Role berbasis 1 bidang
        if ($this->hasRole(['user_bidang', 'monev_bidang'])) {
            $bidangId = (int) $this->bidang_id;

            return route('reports.bidang.index', [
                'bidang_id' => $bidangId,
            ]);
        }

        // Role lintas bidang: pilih bidang "paling atas" (samakan dengan dropdown kamu: orderBy('nama'))
        $firstBidangId = Bidang::query()->orderBy('nama')->value('id');

        if (!$firstBidangId) {
            return route('activities.index');
        }

        return route('reports.bidang.index', [
            'bidang_id' => (int) $firstBidangId,
        ]);
    }
}
