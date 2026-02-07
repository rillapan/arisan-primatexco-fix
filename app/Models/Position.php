<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['name', 'description'];

    public function saksis()
    {
        return $this->hasMany(Saksi::class);
    }

    public function managements()
    {
        return $this->hasMany(Management::class);
    }
}
