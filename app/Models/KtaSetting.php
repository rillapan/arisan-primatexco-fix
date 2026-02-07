<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KtaSetting extends Model
{
    protected $fillable = [
        'header_title',
        'moto',
        'logo',
        'signature_name',
        'signature_image',
        'vision',
        'mission',
    ];
}
