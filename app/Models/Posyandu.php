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
        'latitude',
        'longitude',
    ];

    protected $appends = ['total_balita'];

    // ── Relasi ────────────────────────────────────────────────────────

    // Bidan terhubung ke posyandu via pengguna.id_posyandu
    public function bidan()
    {
        return $this->hasManyThrough(
            Bidan::class,
            Pengguna::class,
            'id_posyandu',
            'id_user',
            'id_posyandu',
            'id_user'
        );
    }

    public function penggunaBidan()
    {
        return $this->hasMany(Pengguna::class, 'id_posyandu', 'id_posyandu')
            ->where('role', 'Bidan');
    }

    public function anak()
    {
        return $this->hasMany(
            \App\Models\Anak::class,
            'id_posyandu',
            'id_posyandu'
        );
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
        return $this->hasOne(
            Pengguna::class,
            'id_posyandu',
            'id_posyandu'
        )->where('role', 'Posyandu');
    }

    // ── Accessor ──────────────────────────────────────────────────────

    public function getTotalBalitaAttribute(): int
    {
        return $this->anak()->count();
    }

    public function getTotalBidanAttribute(): int
    {
        return $this->penggunaBidan()->count();
    }
}