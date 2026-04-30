<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table      = 'pengguna';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'password',
        'role',
        'id_posyandu',
        'id_posyandu_aktif', // ← BARU
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['password' => 'hashed'];

    // ── Relasi ────────────────────────────────────────────────────────

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

    // Posyandu utama (dari saat akun dibuat)
    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'id_posyandu', 'id_posyandu');
    }

    // Posyandu aktif saat ini
    public function posyanduAktif()
    {
        return $this->belongsTo(Posyandu::class, 'id_posyandu_aktif', 'id_posyandu');
    }

    // Semua posyandu yang bisa diakses (many-to-many) ← BARU
    public function posyanduList()
    {
        return $this->belongsToMany(
            Posyandu::class,
            'pengguna_posyandu',
            'id_user',
            'id_posyandu'
        )->withTimestamps();
    }

    // ── Role Helpers ──────────────────────────────────────────────────

    public function isSuperAdmin(): bool { return $this->role === 'SuperAdmin'; }
    public function isBidan(): bool      { return $this->role === 'Bidan'; }
    public function isKader(): bool      { return $this->role === 'Kader'; }
    public function isOrangTua(): bool   { return $this->role === 'OrangTua'; }

    // ── Helper: ambil id_posyandu yang sedang aktif ───────────────────
    // Prioritas: posyandu_aktif > posyandu utama
    public function getPosyanduAktifId(): ?int
    {
        return $this->id_posyandu_aktif ?? $this->id_posyandu;
    }
}