<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPosyandu extends Model
{
    protected $table = 'jadwal_posyandu';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = ['id_kader', 'tgl_kegiatan', 'lokasi', 'agenda'];

    protected $casts = [
        'tgl_kegiatan' => 'date',
    ];

    public function kader()
    {
        return $this->belongsTo(Kader::class, 'id_kader', 'id_kader');
    }

    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'id_jadwal', 'id_jadwal');
    }
}