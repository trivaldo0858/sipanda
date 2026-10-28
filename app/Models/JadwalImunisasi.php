<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalImunisasi extends Model
{
    protected $table = 'jadwal_imunisasi';

    protected $primaryKey = 'id_jadwal_imunisasi';

    protected $fillable = [
        'id_vaksin',
        'urutan_dosis',
        'usia_hari',
        'usia_bulan',
        'khusus_wilayah_endemis',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'urutan_dosis' => 'integer',
            'usia_hari' => 'integer',
            'usia_bulan' => 'integer',
            'khusus_wilayah_endemis' => 'boolean',
        ];
    }

    public function jenisVaksin(): BelongsTo
    {
        return $this->belongsTo(
            JenisVaksin::class,
            'id_vaksin',
            'id_vaksin'
        );
    }
}
