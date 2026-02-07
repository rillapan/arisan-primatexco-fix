<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     * Set to false to allow custom ID assignment
     */
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'max_participants',
        'monthly_installment',
        'main_prize',
        'shu',
        'min_bid',
        'max_bid',
        'is_active',
        'is_registration_active'
    ];

    protected $casts = [
        'monthly_installment' => 'decimal:2',
        'main_prize' => 'decimal:2',
        'shu' => 'decimal:2',
        'min_bid' => 'decimal:2',
        'max_bid' => 'decimal:2',
        'is_active' => 'boolean',
        'is_registration_active' => 'boolean'
    ];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function monthlyPeriods()
    {
        return $this->hasMany(MonthlyPeriod::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function bids()
    {
        return $this->hasManyThrough(Bid::class, Participant::class);
    }

    public function winners()
    {
        return $this->hasManyThrough(Winner::class, Participant::class);
    }

    public function currentPeriod()
    {
        return $this->monthlyPeriods()
            ->where('status', '!=', 'completed')
            ->orderBy('period_start', 'desc')
            ->first();
    }
}
