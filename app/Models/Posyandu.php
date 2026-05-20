<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    protected $table      = 'posyandu';
    protected $primaryKey = 'id_posyandu';

    protected $fillable = [
        'nama_posyandu',
        'kecamatan',
        'desa_kelurahan',
        'alamat',
        'kabupaten_kota',
        'password_kader',
    ];

    protected $hidden = ['password_kader'];

    // ── Relasi ────────────────────────────────────────────────────────

    // Bidan terhubung ke posyandu via pengguna.id_posyandu
    public function bidan()
    {
        return $this->hasMany(Bidan::class, 'id_posyandu', 'id_posyandu');
    }

    // Kader tidak punya id_posyandu langsung —
    // terhubung via pengguna.id_posyandu
    public function penggunaKader()
    {
        return $this->hasMany(Pengguna::class, 'id_posyandu', 'id_posyandu')
                    ->where('role', 'Kader');
    }

    public function penggunaBidan()
    {
        return $this->hasMany(Pengguna::class, 'id_posyandu', 'id_posyandu')
                    ->where('role', 'Bidan');
    }

    public function anak()
    {
        return $this->hasMany(Anak::class, 'id_posyandu', 'id_posyandu');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalPosyandu::class, 'id_posyandu', 'id_posyandu');
    }

    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'id_posyandu', 'id_posyandu');
    }

    public function penggunaAkses()
    {
        return $this->belongsToMany(
            Pengguna::class,
            'pengguna_posyandu',
            'id_posyandu',
            'id_user'
        )->withTimestamps();
    }

    // ── Accessor ──────────────────────────────────────────────────────

    public function getTotalBalitaAttribute(): int
    {
        return $this->anak()->count();
    }

    public function getTotalKaderAttribute(): int
    {
        return $this->penggunaKader()->count();
    }

    public function getTotalBidanAttribute(): int
    {
        return $this->penggunaBidan()->count();
    }
}