<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- penting

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes; // <-- jika kamu pakai soft deletes

    protected $fillable = [
        'name','email','password','bidang_id','is_active'
    ];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Import modelnya biar IDE happy:
    // use App\Models\Bidang; use App\Models\Role;  (pakai FQCN di bawah, jadi tidak wajib)

    public function bidang()
    {
        return $this->belongsTo(\App\Models\Bidang::class);
    }

    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class)->withTimestamps();
    }

    public function hasRole(string $name): bool
    {
        return $this->roles()->where('name', $name)->exists();
    }
}
