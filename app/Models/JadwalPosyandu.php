<?php
// app/Models/JadwalPosyandu.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JadwalPosyandu extends Model
{
    protected $table      = 'jadwal_posyandu';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = ['id_posyandu', 'tgl_kegiatan', 'lokasi', 'agenda'];

    protected $casts = ['tgl_kegiatan' => 'date'];

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'id_posyandu', 'id_posyandu');
    }

    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'id_jadwal', 'id_jadwal');
    }

    public function isUpcoming(): bool
    {
        return $this->tgl_kegiatan->isFuture();
    }
}