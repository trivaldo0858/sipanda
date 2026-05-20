<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anak extends Model
{
    protected $table      = 'anak';
    protected $primaryKey = 'nik_anak';
    protected $keyType    = 'string';
    public $incrementing  = false;

    protected $fillable = [
        'nik_anak',
        'nik_orang_tua',
        'id_posyandu',
        'nama_anak',
        'tgl_lahir',
        'jenis_kelamin',
        'nama_ayah',
    ];

    protected $casts = ['tgl_lahir' => 'date'];

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'nik_orang_tua', 'nik_orang_tua');
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'id_posyandu', 'id_posyandu');
    }

    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'nik_anak', 'nik_anak')
                    ->orderBy('tgl_periksa', 'desc');
    }

    public function imunisasi()
    {
        return $this->hasMany(Imunisasi::class, 'nik_anak', 'nik_anak')
                    ->orderBy('tgl_pemberian', 'desc');
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

    // Helper: format umur
    public function getUmurFormatAttribute(): string
    {
        $bulanTotal = $this->umur_bulan;
        $tahun      = intdiv($bulanTotal, 12);
        $bulan      = $bulanTotal % 12;

        if ($tahun === 0) return "$bulan Bulan";
        if ($bulan === 0) return "$tahun Tahun";
        return "$tahun Tahun $bulan Bulan";
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}