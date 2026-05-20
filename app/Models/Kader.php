<?php
// app/Models/Kader.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kader extends Model
{
    protected $table      = 'kader';
    protected $primaryKey = 'id_kader';

    protected $fillable = ['id_posyandu', 'nama_kader', 'no_telp'];

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'id_posyandu', 'id_posyandu');
    }
}