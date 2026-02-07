<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'saksi_id',
        'period_name',
        'period_start',
        'period_end',
        'bid_deadline',
        'previous_cash_balance',
        'total_installments',
        'total_amount',
        'shu_amount',
        'available_funds',
        'remaining_cash',
        'motor_slots',
        'notes',
        'status'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'bid_deadline' => 'datetime',
        'previous_cash_balance' => 'decimal:2',
        'total_installments' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shu_amount' => 'decimal:2',
        'available_funds' => 'decimal:2',
        'remaining_cash' => 'decimal:2'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function saksi()
    {
        return $this->belongsTo(Saksi::class);
    }

    public function saksis()
    {
        return $this->belongsToMany(Saksi::class, 'monthly_period_saksi');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function winners()
    {
        return $this->hasMany(Winner::class);
    }

    public function documentations()
    {
        return $this->hasMany(Documentation::class);
    }

    public function highestBids()
    {
        $maxBid = $this->bids()->max('bid_amount');
        return $this->bids()->where('bid_amount', $maxBid);
    }

    public function calculateWinnerCount()
    {
        return $this->previous_cash_balance < $this->group->main_prize ? 1 : 2;
    }

    public function calculateAvailableFunds()
    {
        $total = $this->previous_cash_balance + $this->total_installments;
        $winnerCount = $this->calculateWinnerCount();
        $shu = 500000 * $winnerCount; // Fixed SHU of 500,000 per winner
        return $total - $shu;
    }

    public function getCalculationBreakdown()
    {
        $winnerCount = $this->calculateWinnerCount();
        $participantCount = $this->group->participants()->where('is_active', true)->count();
        $monthlyInstallmentAmount = (float)$this->group->monthly_installment;
        $totalInstallments = $participantCount * $monthlyInstallmentAmount;
        $total = (float)$this->previous_cash_balance + $totalInstallments;
        $shuPerWinner = (float)($this->group->shu ?? 500000);
        $shu = $shuPerWinner * $winnerCount;
        $availableFunds = $total - $shu;
        
        return [
            'previous_cash_balance' => (float)$this->previous_cash_balance,
            'total_installments' => $totalInstallments,
            'total_amount' => $total,
            'winner_count' => $winnerCount,
            'shu_amount' => $shu,
            'available_funds' => $availableFunds,
            'main_prize' => (float)$this->group->main_prize
        ];
    }

    public function calculateRemainingCash($totalBidAmount)
    {
        $breakdown = $this->getCalculationBreakdown();
        $winnerCount = $breakdown['winner_count'];
        $mainPrize = $breakdown['main_prize'];
        
        if ($winnerCount === 1) {
            // 1 winner: final_prize = main_prize - bid_amount
            $finalPrize = $mainPrize - $totalBidAmount;
            $totalPrizes = $finalPrize;
        } else {
            // 2 winners: final_prize = main_prize - bid_amount (per winner)
            $finalPrizePerWinner = $mainPrize - $totalBidAmount;
            $totalPrizes = $finalPrizePerWinner * $winnerCount;
        }
        
        return $breakdown['available_funds'] - $totalPrizes;
    }
}
