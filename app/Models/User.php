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
}
