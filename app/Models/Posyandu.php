<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    protected $table = 'posyandu';
    protected $primaryKey = 'id_posyandu';

    protected $fillable = [
        'nama_posyandu',
        'kecamatan',
        'desa_kelurahan',
        'alamat',
        'kabupaten_kota',
    ];

    // ── Relasi ────────────────────────────────────────────────────────

    public function kader()
    {
        return $this->hasMany(Kader::class, 'id_posyandu', 'id_posyandu');
    }

    public function bidan()
    {
        return $this->hasMany(Bidan::class, 'id_posyandu', 'id_posyandu');
    }

    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'id_posyandu', 'id_posyandu');
    }

    // Pengguna yang punya akses ke posyandu ini (many-to-many) ← BARU
    public function penggunaAkses()
    {
        return $this->belongsToMany(
            Pengguna::class,
            'pengguna_posyandu',
            'id_posyandu',
            'id_user'
        )->withTimestamps();
    }

    public function getTotalBalitaAttribute(): int
    {
        return Anak::whereHas('orangTua.pengguna', function ($q) {
            $q->where('id_posyandu', $this->id_posyandu);
        })->count();
    }
}