<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'email',
        'role',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'SuperAdmin'; // Pastikan 'SuperAdmin' sesuai dengan isi kolom role di database
    }
}