<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Management extends Model
{
    protected $table = 'managements';

    protected $fillable = [
        'position_id',
        'nama_lengkap',
        'foto_profil',
        'ttd',
        'jabatan',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
