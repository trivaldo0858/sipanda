<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'id_user',
        'nik_anak',
        'pesan',
        'tgl_kirim',
        'status',
        'jenis_notif',
    ];

    protected $casts = [
        'tgl_kirim' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_user', 'id_user');
    }

    public function anak()
    {
        return $this->belongsTo(Anak::class, 'nik_anak', 'nik_anak');
    }
}