<?php
// app/Models/Kader.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kader extends Model
{
    protected $table      = 'kader';
    protected $primaryKey = 'id_kader';

    protected $fillable = ['id_user', 'nama_kader', 'wilayah', 'id_posyandu'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_user', 'id_user');
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'id_posyandu', 'id_posyandu');
    }

    public function jadwalPosyandu()
    {
        return $this->hasMany(JadwalPosyandu::class, 'id_kader', 'id_kader');
    }

    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'id_kader', 'id_kader');
    }
}