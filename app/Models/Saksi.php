<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_id',
        'participant_id',
        'jabatan',
        'nama_pengurus',
        'foto',
        'ttd',
        'urutan',
        'is_active'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
    
    public function monthlyPeriods()
    {
        return $this->belongsToMany(MonthlyPeriod::class, 'monthly_period_saksi');
    }

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan', 'asc');
    }
}
