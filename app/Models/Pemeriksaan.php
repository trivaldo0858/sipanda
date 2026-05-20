<?php
// app/Models/Pemeriksaan.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    protected $table      = 'pemeriksaan';
    protected $primaryKey = 'id_periksa';

    protected $fillable = [
        'nik_anak', 'id_posyandu', 'nip_bidan', 'id_jadwal',
        'tgl_periksa', 'berat_badan', 'tinggi_badan', 'lingkar_kepala',
        'keluhan', 'status_validasi', 'catatan_validasi',
    ];

    protected $casts = [
        'tgl_periksa'  => 'date',
        'berat_badan'  => 'float',
        'tinggi_badan' => 'float',
        'lingkar_kepala' => 'float',
    ];

    public function anak()
    {
        return $this->belongsTo(Anak::class, 'nik_anak', 'nik_anak');
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'id_posyandu', 'id_posyandu');
    }

    public function bidan()
    {
        return $this->belongsTo(Bidan::class, 'nip_bidan', 'nip');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPosyandu::class, 'id_jadwal', 'id_jadwal');
    }

    public function scopeMenungguValidasi($query)
    {
        return $query->where('status_validasi', 'Menunggu');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status_validasi', 'Disetujui');
    }
}