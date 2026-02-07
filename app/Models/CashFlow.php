<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    protected $fillable = [
        'group_id',
        'monthly_period_id',
        'month_key',
        'month_name',
        'previous_balance',
        'monthly_installments',
        'total_bids',
        'admin_fees',
        'prizes_given',
        'remaining_cash',
        'status'
    ];

    protected $casts = [
        'previous_balance' => 'decimal:2',
        'monthly_installments' => 'decimal:2',
        'total_bids' => 'decimal:2',
        'admin_fees' => 'decimal:2',
        'prizes_given' => 'decimal:2',
        'remaining_cash' => 'decimal:2',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function monthlyPeriod()
    {
        return $this->belongsTo(MonthlyPeriod::class);
    }

    /**
     * Update remaining cash based on current values
     */
    public function updateRemainingCash()
    {
        $this->remaining_cash = $this->previous_balance + $this->monthly_installments + $this->total_bids - $this->admin_fees - $this->prizes_given;
        $this->save();
    }

    /**
     * Get total incoming cash
     */
    public function getTotalIncomingAttribute()
    {
        return $this->previous_balance + $this->monthly_installments + $this->total_bids;
    }

    /**
     * Get total outgoing cash
     */
    public function getTotalOutgoingAttribute()
    {
        return $this->admin_fees + $this->prizes_given;
    }

    /**
     * Get net cash flow for the month
     */
    public function getNetCashFlowAttribute()
    {
        return $this->monthly_installments + $this->total_bids - $this->admin_fees - $this->prizes_given;
    }
}
