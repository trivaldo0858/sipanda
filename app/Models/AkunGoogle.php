<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkunGoogle extends Model
{
    protected $table = 'akun_google';
    protected $primaryKey = 'google_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['google_id', 'id_user', 'email_google'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_user', 'id_user');
    }
}