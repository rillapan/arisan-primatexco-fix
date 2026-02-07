<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Participant extends Authenticatable
{
    use HasFactory;

    /**
     * The field used for authentication.
     */
    protected $username = 'lottery_number';

    protected $fillable = [
        'group_id',
        'lottery_number',
        'name',
        'nik',
        'department',
        'shift',
        'monthly_installment',
        'has_won',
        'won_at',
        'password',
        'is_active',
        'is_password_changed',
        'photo',
        'registration_status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'monthly_installment' => 'decimal:2',
        'has_won' => 'boolean',
        'won_at' => 'datetime',
        'is_active' => 'boolean',
        'is_password_changed' => 'boolean',
        'password' => 'hashed'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function winner()
    {
        return $this->hasOne(Winner::class);
    }

    public function getCurrentMonthBid()
    {
        return $this->bids()
            ->whereHas('monthlyPeriod', function($query) {
                $query->where('status', '!=', 'completed');
            })
            ->first();
    }
}
