<?php
// app/Models/Bidan.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bidan extends Model
{
    protected $table      = 'bidan';
    protected $primaryKey = 'nip';
    protected $keyType    = 'string';
    public $incrementing  = false;

    protected $fillable = ['nip', 'id_user', 'id_posyandu', 'nama_bidan', 'no_telp'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_user', 'id_user');
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'id_posyandu', 'id_posyandu');
    }

    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'nip_bidan', 'nip');
    }

    public function imunisasi()
    {
        return $this->hasMany(Imunisasi::class, 'nip_bidan', 'nip');
    }
}