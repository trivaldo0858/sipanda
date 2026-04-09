<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    protected $table = 'pemeriksaan';
    protected $primaryKey = 'id_pemeriksaan';

    protected $fillable = [
        'nik_anak',
        'id_kader',
        'nip_bidan',
        'id_jadwal',
        'tgl_pemeriksaan',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'keluhan',
    ];

    protected $casts = [
        'tgl_pemeriksaan' => 'date',
        'berat_badan'     => 'float',
        'tinggi_badan'    => 'float',
        'lingkar_kepala'  => 'float',
    ];

    public function anak()
    {
        return $this->belongsTo(Anak::class, 'nik_anak', 'nik_anak');
    }

    public function kader()
    {
        return $this->belongsTo(Kader::class, 'id_kader', 'id_kader');
    }

    public function bidan()
    {
        return $this->belongsTo(Bidan::class, 'nip_bidan', 'nip');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPosyandu::class, 'id_jadwal', 'id_jadwal');
    }
}