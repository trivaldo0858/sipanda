<?php
// ============================================================
// File: app/Models/JenisVaksin.php
// ============================================================
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JenisVaksin extends Model
{
    protected $table = 'jenis_vaksin';
    protected $primaryKey = 'id_vaksin';
    protected $fillable = ['nama_vaksin', 'deskripsi'];

    public function imunisasi()
    {
        return $this->hasMany(Imunisasi::class, 'id_vaksin', 'id_vaksin');
    }
}