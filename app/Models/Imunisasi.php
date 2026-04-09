<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imunisasi extends Model
{
    protected $table = 'imunisasi';
    protected $primaryKey = 'id_imunisasi';

    protected $fillable = ['nik_anak', 'nip_bidan', 'id_vaksin', 'tgl_pemberian'];

    protected $casts = [
        'tgl_pemberian' => 'date',
    ];

    public function anak()
    {
        return $this->belongsTo(Anak::class, 'nik_anak', 'nik_anak');
    }

    public function bidan()
    {
        return $this->belongsTo(Bidan::class, 'nip_bidan', 'nip');
    }

    public function jenisVaksin()
    {
        return $this->belongsTo(JenisVaksin::class, 'id_vaksin', 'id_vaksin');
    }
}