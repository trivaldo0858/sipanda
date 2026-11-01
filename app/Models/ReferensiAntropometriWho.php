<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferensiAntropometriWho extends Model
{
    protected $table = 'referensi_antropometri_who';

    public $timestamps = false;

    protected $fillable = [
        'kode_referensi',
        'indikator',
        'jenis_kelamin',
        'umur_hari',
        'nilai_ukur_cm',
        'jenis_ukuran',
        'l',
        'm',
        's',
        'sumber_file',
    ];

    protected $casts = [
        'umur_hari' => 'integer',
        'nilai_ukur_cm' => 'float',
        'l' => 'float',
        'm' => 'float',
        's' => 'float',
    ];
}
