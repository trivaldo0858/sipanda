<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tua';
    protected $primaryKey = 'nik_orang_tua';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['nik_orang_tua', 'id_user', 'nama_ibu', 'alamat'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_user', 'id_user');
    }

    public function anak()
    {
        return $this->hasMany(Anak::class, 'nik_orang_tua', 'nik_orang_tua');
    }
}