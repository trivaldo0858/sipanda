<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // Relationships
    public function bidan()
    {
        return $this->hasOne(Bidan::class, 'id_user', 'id_user');
    }

    public function kader()
    {
        return $this->hasOne(Kader::class, 'id_user', 'id_user');
    }

    public function orangTua()
    {
        return $this->hasOne(OrangTua::class, 'id_user', 'id_user');
    }

    public function akunGoogle()
    {
        return $this->hasOne(AkunGoogle::class, 'id_user', 'id_user');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_user', 'id_user');
    }

    // Helpers
    public function isBidan(): bool { return $this->role === 'Bidan'; }
    public function isKader(): bool { return $this->role === 'Kader'; }
    public function isOrangTua(): bool { return $this->role === 'OrangTua'; }
}