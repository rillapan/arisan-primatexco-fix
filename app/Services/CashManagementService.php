<?php

namespace App\Services;

use App\Models\MonthlyPeriod;
use App\Models\Bid;
use App\Models\Winner;
use App\Models\Group;
use App\Models\CashFlow;
use Illuminate\Support\Collection;

class CashManagementService
{
    private Group $group;
    
    // Constants from the calculation system
    const MONTHLY_INSTALLMENT = 175000;
    const PARTICIPANT_COUNT = 90;
    const ADMIN_FEE_PER_WINNER = 500000;
    const MAIN_PRIZE = 17500000;
    const MINIMUM_BID = 2250000;
    
    public function __construct(Group $group)
    {
        $this->group = $group;
    }
    
    /**
     * Calculate monthly cash flow breakdown
     */
    public function calculateMonthlyCashFlow(MonthlyPeriod $period): array
    {
        $totalInstallments = self::PARTICIPANT_COUNT * self::MONTHLY_INSTALLMENT;
        $winnerCount = $this->determineWinnerCount($period);
        $adminFee = self::ADMIN_FEE_PER_WINNER * $winnerCount;
        
        $totalIncoming = $period->previous_cash_balance + $totalInstallments;
        $availableFunds = $totalIncoming - $adminFee;
        
        return [
            'previous_cash_balance' => $period->previous_cash_balance,
            'total_installments' => $totalInstallments,
            'total_incoming' => $totalIncoming,
            'winner_count' => $winnerCount,
            'admin_fee' => $adminFee,
            'available_funds' => $availableFunds,
            'main_prize' => self::MAIN_PRIZE,
            'minimum_bid' => self::MINIMUM_BID
        ];
    }
    
    /**
     * Determine winner count based on accumulated cash
     */
    public function determineWinnerCount(MonthlyPeriod $period): int
    {
        return $period->previous_cash_balance >= self::MAIN_PRIZE ? 2 : 1;
    }
    
    /**
     * Calculate remaining cash after prize distribution
     */
    public function calculateRemainingCash(MonthlyPeriod $period, float $totalBidAmount): float
    {
        $cashFlow = $this->calculateMonthlyCashFlow($period);
        $winnerCount = $cashFlow['winner_count'];
        
        if ($winnerCount === 1) {
            $finalPrize = self::MAIN_PRIZE - $totalBidAmount;
            $totalPrizes = $finalPrize;
        } else {
            $finalPrizePerWinner = self::MAIN_PRIZE - $totalBidAmount;
            $totalPrizes = $finalPrizePerWinner * $winnerCount;
        }
        
        return $cashFlow['available_funds'] - $totalPrizes;
    }
    
    /**
     * Project cash accumulation for future periods
     */
    public function projectCashAccumulation(MonthlyPeriod $currentPeriod, int $monthsAhead = 6): array
    {
        $projections = [];
        $currentBalance = $currentPeriod->previous_cash_balance;
        
        for ($i = 1; $i <= $monthsAhead; $i++) {
            $monthlyNet = self::PARTICIPANT_COUNT * self::MONTHLY_INSTALLMENT - self::ADMIN_FEE_PER_WINNER;
            $projectedBalance = $currentBalance + $monthlyNet;
            
            $projections[] = [
                'month' => $i,
                'projected_balance' => $projectedBalance,
                'can_have_two_winners' => $projectedBalance >= self::MAIN_PRIZE,
                'monthly_net' => $monthlyNet
            ];
            
            $currentBalance = $projectedBalance;
        }
        
        return $projections;
    }
    
    /**
     * Get comprehensive cash flow analysis for a period
     */
    public function getCashFlowAnalysis(MonthlyPeriod $period): array
    {
        $cashFlow = $this->calculateMonthlyCashFlow($period);
        $winners = $period->winners()->with(['participant', 'bid'])->get();
        $bids = $period->bids()->with('participant')->get();
        
        $totalBidAmount = $bids->sum('bid_amount');
        $remainingCash = $this->calculateRemainingCash($period, $totalBidAmount);
        
        return [
            'period_info' => [
                'id' => $period->id,
                'name' => $period->period_name,
                'status' => $period->status
            ],
            'cash_flow' => $cashFlow,
            'bids' => [
                'total_amount' => $totalBidAmount,
                'count' => $bids->count(),
                'average_bid' => $bids->count() > 0 ? $totalBidAmount / $bids->count() : 0,
                'details' => $bids
            ],
            'winners' => [
                'count' => $winners->count(),
                'total_prizes' => $winners->sum('final_prize'),
                'details' => $winners
            ],
            'remaining_cash' => $remainingCash,
            'next_month_projection' => $this->projectCashAccumulation($period, 1)[0] ?? null
        ];
    }
    
    /**
     * Validate bid amount meets minimum requirements
     */
    public function validateBidAmount(float $bidAmount): array
    {
        $errors = [];
        
        if ($bidAmount < self::MINIMUM_BID) {
            $errors[] = "Bid minimum adalah Rp " . number_format(self::MINIMUM_BID, 0, ',', '.');
        }
        
        if ($bidAmount > self::MAIN_PRIZE) {
            $errors[] = "Bid tidak boleh melebihi hadiah utama Rp " . number_format(self::MAIN_PRIZE, 0, ',', '.');
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Calculate monthly net cash accumulation (sisa kas bulanan)
     */
    public function calculateMonthlyNetCash(float $bidAmount): float
    {
        $totalIncoming = (self::PARTICIPANT_COUNT * self::MONTHLY_INSTALLMENT) - self::ADMIN_FEE_PER_WINNER + $bidAmount;
        return $totalIncoming - self::MAIN_PRIZE;
    }
    
    /**
     * Get cash accumulation history
     */
    public function getCashAccumulationHistory(): Collection
    {
        return CashFlow::where('group_id', $this->group->id)
            ->orderBy('month_key', 'asc')
            ->get()
            ->map(function ($cashFlow) {
                return [
                    'period_name' => $cashFlow->month_name,
                    'previous_balance' => $cashFlow->previous_balance,
                    'monthly_net' => $cashFlow->net_cash_flow,
                    'accumulated_balance' => $cashFlow->remaining_cash,
                    'winner_count' => $this->determineWinnerCountFromCash($cashFlow->remaining_cash)
                ];
            });
    }
    
    /**
     * Update cash flow when payment is made
     */
    public function updateCashFlowOnPayment(MonthlyPeriod $period, float $amount)
    {
        $monthKey = $period->period_start->format('Y-m');
        $cashFlow = CashFlow::where('group_id', $this->group->id)
            ->where('month_key', $monthKey)
            ->first();
            
        if ($cashFlow) {
            $cashFlow->monthly_installments += $amount;
            $cashFlow->updateRemainingCash();
        }
    }
    
    /**
     * Update cash flow when bid is placed
     */
    public function updateCashFlowOnBid(MonthlyPeriod $period, float $bidAmount)
    {
        $monthKey = $period->period_start->format('Y-m');
        $cashFlow = CashFlow::where('group_id', $this->group->id)
            ->where('month_key', $monthKey)
            ->first();
            
        if ($cashFlow) {
            $cashFlow->total_bids += $bidAmount;
            $cashFlow->updateRemainingCash();
        }
    }
    
    /**
     * Update cash flow when prize is given
     */
    public function updateCashFlowOnPrize(MonthlyPeriod $period, float $prizeAmount)
    {
        $monthKey = $period->period_start->format('Y-m');
        $cashFlow = CashFlow::where('group_id', $this->group->id)
            ->where('month_key', $monthKey)
            ->first();
            
        if ($cashFlow) {
            $cashFlow->prizes_given += $prizeAmount;
            $cashFlow->updateRemainingCash();
        }
    }
    
    /**
     * Determine winner count from cash amount
     */
    private function determineWinnerCountFromCash(float $cashAmount): int
    {
        return $cashAmount >= self::MAIN_PRIZE ? 2 : 1;
    }
    
    /**
     * Get current month cash flow
     */
    public function getCurrentMonthCashFlow(): ?CashFlow
    {
        $currentMonthKey = now()->format('Y-m');
        return CashFlow::where('group_id', $this->group->id)
            ->where('month_key', $currentMonthKey)
            ->first();
    }
    
    /**
     * Get cash flow by month key
     */
    public function getCashFlowByMonth(string $monthKey): ?CashFlow
    {
        return CashFlow::where('group_id', $this->group->id)
            ->where('month_key', $monthKey)
            ->first();
    }
}
