<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_period_id',
        'participant_id',
        'bid_id',
        'main_prize',
        'bid_amount',
        'final_prize',
        'needs_draw',
        'draw_time',
        'notes'
    ];

    protected $casts = [
        'main_prize' => 'decimal:2',
        'bid_amount' => 'decimal:2',
        'final_prize' => 'decimal:2',
        'needs_draw' => 'boolean',
        'draw_time' => 'datetime'
    ];

    public function monthlyPeriod()
    {
        return $this->belongsTo(MonthlyPeriod::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }
}
