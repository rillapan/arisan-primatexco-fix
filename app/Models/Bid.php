<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_period_id',
        'participant_id',
        'bid_amount',
        'status',
        'bid_time',
        'is_permanent'
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2',
        'bid_time' => 'datetime',
        'is_permanent' => 'boolean'
    ];

    public function monthlyPeriod()
    {
        return $this->belongsTo(MonthlyPeriod::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function winner()
    {
        return $this->hasOne(Winner::class);
    }
}
