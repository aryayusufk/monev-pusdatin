<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        // User milik satu bidang (kecuali superadmin/tim monev tertentu bisa null)
        return $this->belongsTo(\App\Models\Bidang::class);
    }

    public function roles()
    {
        // User bisa punya banyak role (super_admin, tim_monev, user_bidang, dst.)
        return $this->belongsToMany(\App\Models\Role::class)->withTimestamps();
    }

    public function hasRole(string $name): bool
    {
        // Dipakai di policy dan blade: $user->hasRole('tim_monev')
        return $this->roles()->where('name', $name)->exists();
    }
}
