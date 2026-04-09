<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anak extends Model
{
    protected $table = 'anak';
    protected $primaryKey = 'nik_anak';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['nik_anak', 'nik_orang_tua', 'nama_anak', 'tgl_lahir', 'jenis_kelamin'];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'nik_orang_tua', 'nik_orang_tua');
    }

    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'nik_anak', 'nik_anak')->orderBy('tgl_pemeriksaan', 'desc');
    }

    public function imunisasi()
    {
        return $this->hasMany(Imunisasi::class, 'nik_anak', 'nik_anak')->orderBy('tgl_pemberian', 'desc');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'nik_anak', 'nik_anak');
    }

    // Helper: hitung umur dalam bulan
    public function getUmurBulanAttribute(): int
    {
        return (int) $this->tgl_lahir->diffInMonths(now());
    }
}