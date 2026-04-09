<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'nip_bidan',
        'jenis_laporan',
        'periode_awal',
        'periode_akhir',
        'tgl_cetak',
    ];

    protected $casts = [
        'periode_awal'  => 'date',
        'periode_akhir' => 'date',
        'tgl_cetak'     => 'date',
    ];

    public function bidan()
    {
        return $this->belongsTo(Bidan::class, 'nip_bidan', 'nip');
    }
}