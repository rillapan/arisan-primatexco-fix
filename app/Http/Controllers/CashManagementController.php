<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\MonthlyPeriod;
use App\Services\CashManagementService;
use Illuminate\Http\Request;

class CashManagementController extends Controller
{
    private CashManagementService $cashService;
    
    public function __construct(CashManagementService $cashService)
    {
        $this->cashService = $cashService;
    }
    
    /**
     * Display cash management dashboard for a group
     */
    public function dashboard($groupId)
    {
        $group = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true);
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'desc');
            }
        ])->findOrFail($groupId);
        
        // Initialize service with group
        $this->cashService = new CashManagementService($group);
        
        // Get current period or latest period
        $currentPeriod = $group->monthlyPeriods->where('status', '!=', 'completed')->first();
        if (!$currentPeriod && $group->monthlyPeriods->count() > 0) {
            $currentPeriod = $group->monthlyPeriods->first();
        }
        
        $cashAnalysis = null;
        $projections = [];
        $history = [];
        
        if ($currentPeriod) {
            $cashAnalysis = $this->cashService->getCashFlowAnalysis($currentPeriod);
            $projections = $this->cashService->projectCashAccumulation($currentPeriod, 6);
        }
        
        $history = $this->cashService->getCashAccumulationHistory();
        
        // Get participants with payment status for current month
        $participants = $this->getParticipantsWithPaymentStatus($group, $currentPeriod);
        
        return view('admin.cash.dashboard', compact(
            'group', 
            'currentPeriod', 
            'cashAnalysis', 
            'projections', 
            'history',
            'participants'
        ));
    }
    
    /**
     * Get participants with their payment status for current period
     */
    private function getParticipantsWithPaymentStatus($group, $currentPeriod)
    {
        $participants = $group->participants->where('is_active', true);
        
        if ($currentPeriod) {
            // Get payments for current period
            $currentMonthKey = $currentPeriod->period_start->format('Y-m');
            $payments = \App\Models\Payment::where('group_id', $group->id)
                ->whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$currentMonthKey])
                ->get()
                ->keyBy('participant_id');
            
            // Add payment status to each participant
            $participants = $participants->map(function($participant) use ($payments) {
                $payment = $payments->get($participant->id);
                $participant->payment_status = $payment ? 'paid' : 'unpaid';
                $participant->payment_amount = $payment ? $payment->amount : 0;
                $participant->monthly_installment = $group->monthly_installment;
                return $participant;
            });
        } else {
            // No current period, all participants are unpaid
            $participants = $participants->map(function($participant) use ($group) {
                $participant->payment_status = 'unpaid';
                $participant->payment_amount = 0;
                $participant->monthly_installment = $group->monthly_installment;
                return $participant;
            });
        }
        
        return $participants;
    }
    
    /**
     * Show detailed cash flow for a specific period
     */
    public function periodCashFlow($groupId, $periodId)
    {
        $group = Group::findOrFail($groupId);
        $period = MonthlyPeriod::with(['group', 'bids.participant', 'winners.participant'])
            ->where('group_id', $groupId)
            ->findOrFail($periodId);
        
        // Initialize service with group
        $this->cashService = new CashManagementService($group);
        
        $cashFlow = $this->cashService->calculateMonthlyCashFlow($period);
        $analysis = $this->cashService->getCashFlowAnalysis($period);
        $projections = $this->cashService->projectCashAccumulation($period, 3);
        
        return view('admin.cash.period-detail', compact(
            'group', 
            'period', 
            'cashFlow', 
            'analysis', 
            'projections'
        ));
    }
    
    /**
     * Show cash accumulation history and projections
     */
    public function cashHistory($groupId)
    {
        $group = Group::with([
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'asc');
            }
        ])->findOrFail($groupId);
        
        // Initialize service with group
        $this->cashService = new CashManagementService($group);
        
        $history = $this->cashService->getCashAccumulationHistory();
        
        // Get projections from latest period
        $latestPeriod = $group->monthlyPeriods->last();
        $projections = [];
        if ($latestPeriod) {
            $projections = $this->cashService->projectCashAccumulation($latestPeriod, 12);
        }
        
        return view('admin.cash.history', compact(
            'group', 
            'history', 
            'projections',
            'latestPeriod'
        ));
    }
    
    /**
     * Calculate bid validation and cash impact
     */
    public function validateBid(Request $request, $groupId)
    {
        $request->validate([
            'bid_amount' => 'required|numeric|min:0'
        ]);
        
        $group = Group::findOrFail($groupId);
        
        // Initialize service with group
        $this->cashService = new CashManagementService($group);
        
        $bidAmount = (float) $request->bid_amount;
        $validation = $this->cashService->validateBidAmount($bidAmount);
        
        if (!$validation['valid']) {
            return response()->json([
                'valid' => false,
                'errors' => $validation['errors']
            ], 422);
        }
        
        // Calculate cash impact
        $monthlyNet = $this->cashService->calculateMonthlyNetCash($bidAmount);
        
        return response()->json([
            'valid' => true,
            'bid_amount' => $bidAmount,
            'monthly_net_cash' => $monthlyNet,
            'formatted_bid' => 'Rp ' . number_format($bidAmount, 0, ',', '.'),
            'formatted_monthly_net' => 'Rp ' . number_format($monthlyNet, 0, ',', '.')
        ]);
    }
    
    /**
     * API endpoint to get current cash status
     */
    public function cashStatus($groupId)
    {
        $group = Group::with([
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'desc');
            }
        ])->findOrFail($groupId);
        
        // Initialize service with group
        $this->cashService = new CashManagementService($group);
        
        $currentPeriod = $group->monthlyPeriods->where('status', '!=', 'completed')->first();
        
        if (!$currentPeriod) {
            return response()->json([
                'status' => 'no_active_period',
                'message' => 'Tidak ada periode aktif'
            ]);
        }
        
        $cashFlow = $this->cashService->calculateMonthlyCashFlow($currentPeriod);
        $analysis = $this->cashService->getCashFlowAnalysis($currentPeriod);
        
        return response()->json([
            'status' => 'active',
            'period' => [
                'id' => $currentPeriod->id,
                'name' => $currentPeriod->period_name,
                'status' => $currentPeriod->status
            ],
            'cash_flow' => $cashFlow,
            'analysis' => [
                'total_bids' => $analysis['bids']['total_amount'],
                'bid_count' => $analysis['bids']['count'],
                'winner_count' => $analysis['winners']['count'],
                'remaining_cash' => $analysis['remaining_cash']
            ]
        ]);
    }
    
    /**
     * Simulate cash flow scenarios
     */
    public function simulateCashFlow(Request $request, $groupId)
    {
        $request->validate([
            'bid_amount' => 'required|numeric|min:0',
            'months_ahead' => 'required|integer|min:1|max:12'
        ]);
        
        $group = Group::findOrFail($groupId);
        
        // Initialize service with group
        $this->cashService = new CashManagementService($group);
        
        $bidAmount = (float) $request->bid_amount;
        $monthsAhead = (int) $request->months_ahead;
        
        // Get current period
        $currentPeriod = $group->monthlyPeriods->where('status', '!=', 'completed')->first();
        if (!$currentPeriod) {
            return response()->json([
                'error' => 'Tidak ada periode aktif untuk simulasi'
            ], 422);
        }
        
        // Calculate projections with different bid scenarios
        $projections = $this->cashService->projectCashAccumulation($currentPeriod, $monthsAhead);
        $monthlyNet = $this->cashService->calculateMonthlyNetCash($bidAmount);
        
        // Simulate with the given bid amount
        $simulatedProjections = [];
        $currentBalance = $currentPeriod->previous_cash_balance;
        
        for ($i = 1; $i <= $monthsAhead; $i++) {
            $simulatedBalance = $currentBalance + $monthlyNet;
            
            $simulatedProjections[] = [
                'month' => $i,
                'current_balance' => $currentBalance,
                'simulated_balance' => $simulatedBalance,
                'monthly_net' => $monthlyNet,
                'can_have_two_winners' => $simulatedBalance >= CashManagementService::MAIN_PRIZE,
                'months_to_two_winners' => $this->calculateMonthsToTwoWinners($simulatedBalance, $monthlyNet)
            ];
            
            $currentBalance = $simulatedBalance;
        }
        
        return response()->json([
            'bid_amount' => $bidAmount,
            'monthly_net' => $monthlyNet,
            'current_period' => [
                'name' => $currentPeriod->period_name,
                'current_balance' => $currentPeriod->previous_cash_balance
            ],
            'projections' => $simulatedProjections
        ]);
    }
    
    /**
     * Update bid for current period
     */
    public function updateBid(Request $request, $groupId)
    {
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'bid_amount' => 'required|numeric|min:2250000|max:17500000',
            'bid_time' => 'required|date'
        ]);
        
        $group = Group::findOrFail($groupId);
        
        // Get current period
        $currentPeriod = $group->monthlyPeriods->where('status', '!=', 'completed')->first();
        if (!$currentPeriod) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada periode aktif'
            ], 422);
        }
        
        // Check if participant already has a bid for this period
        $existingBid = \App\Models\Bid::where('monthly_period_id', $currentPeriod->id)
            ->where('participant_id', $request->participant_id)
            ->first();
            
        if ($existingBid) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta sudah memiliki bid untuk periode ini'
            ], 422);
        }
        
        // Create new bid
        $bid = \App\Models\Bid::create([
            'monthly_period_id' => $currentPeriod->id,
            'participant_id' => $request->participant_id,
            'bid_amount' => $request->bid_amount,
            'bid_time' => $request->bid_time
        ]);
        
        // Update cash flow
        $this->cashService = new CashManagementService($group);
        $this->cashService->updateCashFlowOnBid($currentPeriod, $request->bid_amount);
        
        // Get participant name
        $participant = \App\Models\Participant::find($request->participant_id);
        
        // Calculate total bids for this period
        $totalBids = \App\Models\Bid::where('monthly_period_id', $currentPeriod->id)
            ->sum('bid_amount');
        
        return response()->json([
            'success' => true,
            'bid_amount' => $request->bid_amount,
            'participant_name' => $participant->name,
            'total_bids' => $totalBids
        ]);
    }
    
    /**
     * Helper method to calculate months needed to reach two winners
     */
    private function calculateMonthsToTwoWinners(float $currentBalance, float $monthlyNet): int
    {
        if ($currentBalance >= CashManagementService::MAIN_PRIZE) {
            return 0;
        }
        
        $monthsNeeded = 0;
        $balance = $currentBalance;
        
        while ($balance < CashManagementService::MAIN_PRIZE && $monthsNeeded < 24) { // Max 24 months
            $balance += $monthlyNet;
            $monthsNeeded++;
        }
        
        return $monthsNeeded;
    }
}
