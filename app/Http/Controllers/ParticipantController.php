<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\MonthlyPeriod;
use App\Models\Bid;
use App\Models\Payment;
use App\Models\Winner;
use App\Models\KtaSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\CashManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ParticipantController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Get the effective deadline for bidding
     * Uses bid_deadline if set, otherwise falls back to period_end
     */
    private function getBidDeadline($period)
    {
        return $period->bid_deadline ? $period->bid_deadline : $period->period_end->copy()->endOfDay();
    }

    /**
     * Get unread bukti angsuran count for a participant
     * Counts unread notifications for all participant entries with the same NIK
     */
    private function getUnreadBuktiAngsuranCount($participant)
    {
        $sameNikParticipantIds = Participant::where('nik', $participant->nik)->pluck('id')->toArray();
        return Payment::whereIn('participant_id', $sameNikParticipantIds)
                      ->where('is_notification_read', false)
                      ->where('is_confirmed', true)
                      ->count();
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'lottery_number' => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::guard('participant')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('participant.dashboard'));
        }

        return back()->withErrors([
            'lottery_number' => 'The provided credentials do not match our records.',
        ])->onlyInput('lottery_number');
    }

    public function logout(Request $request)
    {
        Auth::guard('participant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function dashboard()
    {
        // Always fetch fresh data from database (in case admin updated participant info)
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Fetch related accounts (same NIK)
        $relatedParticipants = Participant::where('nik', $participant->nik)
                                ->where('id', '!=', $participant->id)
                                ->with('group')
                                ->get();

        $currentPeriod = $participant->group->currentPeriod();
        $currentBid = null;
        $bidHistory = [];
        
        if ($currentPeriod) {
            $currentBid = $participant->getCurrentMonthBid();
            $bidHistory = $participant->bids()->with('monthlyPeriod')->orderBy('created_at', 'desc')->get();
        }
        
        // Initialize CashManagementService with group
        $cashService = new CashManagementService($participant->group);
        
        // Get all periods for participant's group (asc order for accumulation calculation)
        $periodsForCalc = $participant->group->monthlyPeriods()
            ->with(['winners.participant', 'bids', 'group'])
            ->orderBy('period_start', 'asc')
            ->get();
        
        $runningAccumulation = 0;
        foreach ($periodsForCalc as $period) {
            // Use CashManagementService for calculations
            $cashFlow = $cashService->calculateMonthlyCashFlow($period);
            $analysis = $cashService->getCashFlowAnalysis($period);
            
            // Get actual data for this period
            $participantCount = $period->group->participants()->where('is_active', true)->count();
            
            // Calculate actual installments with fallback to date-matching for robustness
            $monthKey = $period->period_start->format('Y-m');
            $actualInstallments = Payment::where('group_id', $period->group_id)
                ->where(function($query) use ($period, $monthKey) {
                    $query->where('monthly_period_id', $period->id)
                          ->orWhereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$monthKey]);
                })
                ->sum('amount');
                
            $highestBid = (float)($period->bids->max('bid_amount') ?? 0);
            $winnerCount = $period->winners->count(); // Use actual winner count from database
            
            // Get bids only from winners for this period (matching cashMonthDetail logic)
            $winnerBids = [];
            if ($winnerCount > 0) {
                foreach ($period->winners as $winner) {
                    if ($winner->bid_amount > 0) {
                        $winnerBids[] = $winner->bid_amount;
                    }
                }
            }
            $totalBids = array_sum($winnerBids);
            $totalHighestBid = $totalBids; // Use total bids from winners
            
            // Financial Breakdown using new calculation logic (Synced with AdminController)
            $calPreviousCashBalance = ($period->previous_cash_balance > 0) ? (float)$period->previous_cash_balance : (float)$runningAccumulation;
            
            // 1. Rumus Inflow (Pemasukan Bersih Bulan Ini)
            // Dana Iuran Bersih = Actual Installments - (jumlah pemenang x SHU)
            $shuAmount = (float)($period->group->shu ?? 500000);
            $totalShuAmount = $shuAmount * $winnerCount;
            $calNetFunds = $actualInstallments - $totalShuAmount;
            
            // 2. Total Bid = bid1 + bid2 (sum of all winner bids)
            $calTotalBidAmount = $totalBids;
            
            // 3. Rumus Outflow (Pengeluaran Hadiah)
            // Total Harga Motor = Jumlah Pemenang x Harga Satuan Motor
            $mainPrize = (float)$period->group->main_prize;
            $totalMainPrize = $mainPrize * $winnerCount;
            $calTotalMotorPrice = $totalMainPrize;
            
            // 4. Rumus Logika Aliran Kas (Dana Saat Ini)
            // Dana Saat Ini = Dana Iuran Bersih + Total Bid
            $calCurrentFund = $calNetFunds + $calTotalBidAmount;
            
            // Sisa Bersih Periode Ini = Dana Saat Ini - Total Harga Motor
            $calFinalRemainingCash = $calCurrentFund - $calTotalMotorPrice;
            
            // 5. Rumus Akumulasi Akhir (Saldo Dompet)
            // Total Kas Berjalan = Saldo Akumulasi Lalu + Sisa Bersih Periode Ini
            $calTotalRunningCash = $calPreviousCashBalance + $calFinalRemainingCash;

            // Update Running Accumulation for next iteration
            $runningAccumulation = $calTotalRunningCash;

            // Attach to period object
            $period->calc_participant_count = $participantCount;
            $period->calc_monthly_installment = (float)$period->group->monthly_installment;
            
            // Calculate actual number of participants who have paid
            $paidParticipantCount = Payment::where('monthly_period_id', $period->id)
                                        ->where('is_confirmed', true)
                                        ->distinct('participant_id')
                                        ->count('participant_id');
            $period->calc_paid_participant_count = $paidParticipantCount;
            
            $period->calc_main_prize = $totalMainPrize;
            $period->calc_shu_amount = $totalShuAmount;
            $period->calc_actual_installments = $actualInstallments;
            $period->calc_projected_installment = $participantCount * (float)$period->group->monthly_installment;
            $period->calc_highest_bid = $totalHighestBid;
            $period->calc_winner_count = $winnerCount;
            $period->calc_previous_cash_balance = $calPreviousCashBalance;
            $period->calc_net_funds = $calNetFunds;
            $period->calc_current_fund = $calCurrentFund;
            $period->calc_final_remaining_cash = $calFinalRemainingCash;
            $period->calc_total_running_cash = $calTotalRunningCash;
            $period->calc_winner_receives = ($winnerCount > 0) ? ($mainPrize - $highestBid) : 0; 
            
            // Prep for previous month name display
            $periodStart = $period->period_start;
            $prevMonth = $periodStart->copy()->subMonthNoOverflow();
            $period->calc_prev_month_name = $prevMonth->locale('id')->monthName . ' ' . $prevMonth->year;
            
            // Standard values for compatibility with views
            $period->calculated_surplus = $calFinalRemainingCash;
            $period->calculated_accumulation = $calTotalRunningCash;
        }

        // Final list for display (desc order) - WITH FILTER for Prima 22 Sept 2022
        $allPeriods = $periodsForCalc->filter(function($p) use ($participant) {
            // Check if group is Prima 22 (ID 2) and period is Sept 2022
            if ($participant->group_id == 2 && $p->period_name == 'September 2022' && $p->winners->isEmpty()) {
                return false;
            }
            return true;
        })->sortByDesc('period_start');
        
        // Get unread notification count for bukti angsuran (using NIK-based count)
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        
        return view('participant.dashboard', compact('participant', 'currentPeriod', 'currentBid', 'bidHistory', 'allPeriods', 'unreadBuktiAngsuranCount', 'relatedParticipants'));
    }

    public function showBidForm()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Get unread notification count for bukti angsuran (using NIK-based count)
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        
        $currentPeriod = $participant->group->currentPeriod();
        
        if (!$currentPeriod) {
            return redirect()->route('participant.dashboard')->with('error', 'No active period found');
        }
        
        if ($currentPeriod->status === 'completed') {
            return redirect()->route('participant.dashboard')->with('error', 'Bidding period has ended');
        }
        
        if ($participant->has_won) {
            return redirect()->route('participant.dashboard')->with('error', 'You have already won and cannot participate further');
        }

        // IMPORTANT: scope existing bid strictly to the current active period
        // so the status is real-time and also reflects admin-input bids.
        $existingBid = Bid::where('monthly_period_id', $currentPeriod->id)
            ->where('participant_id', $participant->id)
            ->first();

        $cashMonthUsed = null;
        $cashMonthUsedName = null;
        $previousMonthRemainingCash = null;

        if ($currentPeriod && !empty($currentPeriod->period_start)) {
            $monthStart = $currentPeriod->period_start->copy()->startOfMonth();
            $cashMonth = $monthStart->copy()->subMonthNoOverflow();
            $cashMonthUsed = $cashMonth->format('Y-m');
            $cashMonthUsedName = $cashMonth->locale('id')->monthName . ' ' . $cashMonth->year;

            $cashMonthEnd = $cashMonth->copy()->endOfMonth();

            $monthInstallments = Payment::where('group_id', $participant->group_id)
                ->whereDate('payment_date', '<=', $cashMonthEnd)
                ->sum('amount');

            $monthPrizes = Winner::whereHas('monthlyPeriod', function ($query) use ($participant) {
                    $query->where('group_id', $participant->group_id);
                })
                ->whereNotNull('draw_time')
                ->whereDate('draw_time', '<=', $cashMonthEnd)
                ->sum('final_prize');

            $previousMonthRemainingCash = $monthInstallments - $monthPrizes;
        }

        $allBids = Bid::with(['participant'])
            ->where('monthly_period_id', $currentPeriod->id)
            ->orderBy('bid_amount', 'desc')
            ->get();
        
        $deadline = $this->getBidDeadline($currentPeriod);
        $isPeriodEnded = now()->greaterThan($deadline);

        return view('participant.bid.create', compact('participant', 'currentPeriod', 'existingBid', 'cashMonthUsed', 'cashMonthUsedName', 'previousMonthRemainingCash', 'allBids', 'isPeriodEnded', 'unreadBuktiAngsuranCount'));
    }

    public function submitBid(Request $request)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        $currentPeriod = $participant->group->currentPeriod();
        
        if (!$currentPeriod) {
            return back()->with('error', 'No active period found');
        }

        if ($participant->has_won) {
            return back()->with('error', 'Anda sudah menang dan tidak dapat berpartisipasi dalam lelang lagi.');
        }

        $deadline = $this->getBidDeadline($currentPeriod);
        if (now()->greaterThan($deadline)) {
            return back()->with('error', 'Waktu input lelang Anda telah habis. Batas waktu penawaran telah berakhir.');
        }
        
        $validated = $request->validate([
            'bid_amount' => 'required|numeric|min:' . $currentPeriod->group->min_bid . '|max:' . $currentPeriod->group->max_bid
        ]);

        $existingBid = Bid::where('monthly_period_id', $currentPeriod->id)
            ->where('participant_id', $participant->id)
            ->first();
        if ($existingBid) {
            if ($existingBid->is_permanent) {
                return back()->with('error', 'Lelang ini sudah bersifat permanen dan tidak dapat diubah.');
            }
            $existingBid->update([
                'bid_amount' => $validated['bid_amount'],
                'bid_time' => now()
            ]);
        } else {
            Bid::create([
                'monthly_period_id' => $currentPeriod->id,
                'participant_id' => $participant->id,
                'bid_amount' => $validated['bid_amount'],
                'bid_time' => now(),
                'is_permanent' => false
            ]);
        }

        $formattedAmount = 'Rp ' . number_format($validated['bid_amount'], 0, ',', '.');
        return redirect()->route('participant.bid.create')
            ->with('success', 'Lelang berhasil disimpan. Nilai lelang Anda saat ini: ' . $formattedAmount);
    }

    public function makePermanent(Request $request, $bidId)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        $bid = Bid::findOrFail($bidId);

        // Check if bid belongs to current participant or any of their linked accounts (same NIK)
        $allowedParticipantIds = Participant::where('nik', $participant->nik)->pluck('id')->toArray();
        if (!in_array($bid->participant_id, $allowedParticipantIds)) {
            abort(403, 'Unauthorized access: Bid does not belong to your account(s).');
        }

        $bid->update(['is_permanent' => true]);

        return redirect()->route('participant.bid.create')
            ->with('success', 'Lelang Anda telah disimpan secara permanen. Anda sekarang dapat mengunduh bukti lelang.');
    }

    public function downloadBidProof($bidId)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        $bid = Bid::with(['monthlyPeriod.group'])->findOrFail($bidId);

        // Check if bid belongs to current participant or any of their linked accounts (same NIK)
        $allowedParticipantIds = Participant::where('nik', $participant->nik)->pluck('id')->toArray();
        if (!in_array($bid->participant_id, $allowedParticipantIds)) {
            abort(403, 'Unauthorized access: Bid does not belong to your account(s).');
        }

        // Generate PNG using GD
        $width = 800;
        $height = 600;
        $image = imagecreatetruecolor($width, $height);

        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $blue = imagecolorallocate($image, 0, 102, 204);
        $dark = imagecolorallocate($image, 33, 37, 41);
        $green = imagecolorallocate($image, 40, 167, 69);
        $gray = imagecolorallocate($image, 108, 117, 125);

        imagefill($image, 0, 0, $white);

        // Border
        imagerectangle($image, 10, 10, $width - 11, $height - 11, $blue);
        imagerectangle($image, 15, 15, $width - 16, $height - 16, $blue);

        // Header
        $title = "BUKTI PENAWARAN LELANG ARISAN";
        imagestring($image, 5, ($width - (strlen($title) * 9)) / 2, 50, $title, $blue);

        $groupName = strtoupper($bid->monthlyPeriod->group->name);
        imagestring($image, 4, ($width - (strlen($groupName) * 7)) / 2, 80, $groupName, $dark);

        // Info
        $y = 150;
        $lineHeight = 40;
        
        $info = [
            "Nama Peserta   : " . $participant->name,
            "NIK            : " . ($participant->nik ?? '-'),
            "No. Undian     : " . $participant->lottery_number,
            "Periode        : " . $bid->monthlyPeriod->period_name,
            "Nilai Lelang   : Rp " . number_format($bid->bid_amount, 0, ',', '.'),
            "Waktu Input    : " . $bid->bid_time->format('d-m-Y H:i:s'),
            "Status         : PERMANEN (TIDAK DAPAT DIUBAH)"
        ];

        foreach ($info as $text) {
            imagestring($image, 4, 100, $y, $text, $dark);
            $y += $lineHeight;
        }

        // Footnote
        $footer = "Dicetak pada: " . now()->format('d-m-Y H:i:s');
        imagestring($image, 3, 100, $height - 60, $footer, $gray);
        
        $watermark = "SISTEM ARISAN PRIMATEXCO";
        imagestring($image, 3, $width - 250, $height - 60, $watermark, $blue);

        // Output
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="bukti_lelang_' . $participant->lottery_number . '_' . now()->format('YmdHis') . '.png"');
        imagepng($image);
        imagedestroy($image);
        exit;
    }

    public function storeBid(Request $request)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        $validated = $request->validate([
            'period_id' => 'required|exists:monthly_periods,id',
            'bid_amount' => 'required|numeric|min:0'
        ]);
        
        $period = MonthlyPeriod::with('group')->findOrFail($validated['period_id']);
        
        // Verify participant belongs to this period's group
        if ($period->group_id !== $participant->group_id) {
            return back()->with('error', 'Unauthorized access');
        }
        
        if ($participant->has_won) {
            return back()->with('error', 'Anda sudah menang dan tidak dapat berpartisipasi dalam lelang lagi.');
        }

        if ($period->status !== 'bidding') {
            return back()->with('error', 'Periode lelang sudah ditutup.');
        }
        
        $deadline = $this->getBidDeadline($period);
        if (now()->greaterThan($deadline)) {
            return back()->with('error', 'Waktu input lelang Anda telah habis. Batas waktu penawaran telah berakhir.');
        }
        
        // Validate bid amount against group limits
        $request->validate([
            'bid_amount' => 'required|numeric|min:' . $period->group->min_bid . '|max:' . $period->group->max_bid
        ]);
        
        // Check if bid already exists
        $existingBid = Bid::where('monthly_period_id', $period->id)
            ->where('participant_id', $participant->id)
            ->first();
            
        if ($existingBid) {
            if ($existingBid->is_permanent) {
                return back()->with('error', 'Lelang ini sudah bersifat permanen dan tidak dapat diubah.');
            }
            $existingBid->update([
                'bid_amount' => $validated['bid_amount'],
                'bid_time' => now()
            ]);
        } else {
            Bid::create([
                'monthly_period_id' => $period->id,
                'participant_id' => $participant->id,
                'bid_amount' => $validated['bid_amount'],
                'bid_time' => now(),
                'is_permanent' => false
            ]);
        }

        $formattedAmount = 'Rp ' . number_format($validated['bid_amount'], 0, ',', '.');
        return redirect()->route('participant.dashboard')
            ->with('success', 'Lelang berhasil disimpan. Nilai lelang Anda saat ini: ' . $formattedAmount);
    }

    public function results(Request $request)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Get unread notification count for bukti angsuran (using NIK-based count)
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        
        $selectedPeriodId = $request->query('period_id');
        $selectedPeriod = null;
        
        if ($selectedPeriodId) {
            $selectedPeriod = $participant->group->monthlyPeriods()
                ->with(['bids.participant', 'winners.participant', 'saksis.participant.group'])
                ->find($selectedPeriodId);
        }
        
        // Get all participants for the group to list everyone status
        $allGroupParticipants = $participant->group->participants()
            ->with(['bids' => function($q) use ($selectedPeriod) {
                if ($selectedPeriod) {
                    $q->where('monthly_period_id', $selectedPeriod->id);
                } else {
                    $q->whereNull('id'); // Empty result if no period selected
                }
            }])
            ->where('is_active', true)
            ->orderBy('lottery_number', 'asc')
            ->get();
            
        $maxBidAmount = 0;
        if ($selectedPeriod) {
            $maxBidAmount = $selectedPeriod->bids->max('bid_amount') ?? 0;
        }
        
        // Get all periods for the selection list
        $allPeriods = $participant->group->monthlyPeriods()
            ->with('winners')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Filter out Prima 22 Sept 2022 (sync period)
        if ($participant->group_id == 2) {
            $allPeriods = $allPeriods->filter(function($p) {
                return !($p->period_name == 'September 2022' && $p->winners->isEmpty());
            });
        }
        
        return view('participant.results', compact('participant', 'selectedPeriod', 'allPeriods', 'unreadBuktiAngsuranCount', 'allGroupParticipants', 'maxBidAmount'));
    }

    public function winners()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Get unread notification count for bukti angsuran (using NIK-based count)
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        
        $winners = $participant->group->winners()
            ->with(['participant', 'monthlyPeriod'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('participant.winners', compact('participant', 'winners', 'unreadBuktiAngsuranCount'));
    }

    public function profile()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Get unread notification count for bukti angsuran (using NIK-based count)
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        
        $participant->load(['group', 'winner.monthlyPeriod']);
        return view('participant.profile', compact('participant', 'unreadBuktiAngsuranCount'));
    }

    public function updateProfile(Request $request)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'shift' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Increased to 5MB
            'captured_photo' => 'nullable|string',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed'
        ]);
        
        // Handle photo upload
        // Find all participants with the same NIK to keep profiles in sync
        $linkedParticipants = Participant::where('nik', $participant->nik)->get();
        
        // Handle photo upload with Intervention Image
        $photoPath = $participant->photo;
        
        // Initialize Intervention Image Manager
        $imageManager = new \Intervention\Image\ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver()
        );
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($participant->photo) {
                Storage::disk('public')->delete($participant->photo);
            }
            
            $file = $request->file('photo');
            $filename = time() . '_participant_' . $participant->nik . '.webp';
            
            // Process image: resize to 300x300 and convert to WebP
            $image = $imageManager->read($file->getPathname())
                ->cover(300, 300) // Crop otomatis ke tengah (1:1)
                ->toWebp(80); // Konversi ke WebP dengan kualitas 80%
            
            // Ensure directory exists
            if (!Storage::disk('public')->exists('uploads/participants')) {
                Storage::disk('public')->makeDirectory('uploads/participants');
            }
            
            Storage::disk('public')->put('uploads/participants/' . $filename, (string) $image);
            $photoPath = 'uploads/participants/' . $filename;
            
        } elseif ($request->filled('captured_photo')) {
            // Delete old photo if exists
            if ($participant->photo) {
                Storage::disk('public')->delete($participant->photo);
            }

            try {
                $imageData = $request->input('captured_photo');
                
                // Simple header removal
                if (strpos($imageData, 'base64,') !== false) {
                    $imageData = explode('base64,', $imageData)[1];
                }
                
                // Decode
                $decodedImage = base64_decode($imageData);
                
                if ($decodedImage === false) {
                    throw new \Exception('Gagal mendecode gambar base64');
                }
                
                $filename = time() . '_participant_' . $participant->nik . '.webp';
                
                // Process image: resize to 300x300 and convert to WebP
                $image = $imageManager->read($decodedImage)
                    ->cover(300, 300) // Crop otomatis ke tengah (1:1)
                    ->toWebp(80); // Konversi ke WebP dengan kualitas 80%
                
                // Ensure directory exists
                if (!Storage::disk('public')->exists('uploads/participants')) {
                    Storage::disk('public')->makeDirectory('uploads/participants');
                }
                
                Storage::disk('public')->put('uploads/participants/' . $filename, (string) $image);
                $photoPath = 'uploads/participants/' . $filename;
                
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal memproses foto dari kamera: ' . $e->getMessage());
            }
        }

        // Prepare common updates
        $commonUpdates = [
            'name' => $validated['name'],
            'photo' => $photoPath,
            'department' => $validated['department'],
            'shift' => $validated['shift'],
        ];

        // Only update password if new password is provided
        if ($request->filled('new_password')) {
            $inputCurrentPassword = trim($request->current_password); // Trim input like AuthController
            
            // Fetch raw data
            $userAuthData = \Illuminate\Support\Facades\DB::table('participants')
                ->where('id', $participant->id)
                ->select('password', 'lottery_number', 'nik', 'is_password_changed')
                ->first();

            $currentPasswordValid = false;
            
            // 1. Hash Check (Primary)
            if ($userAuthData && \Illuminate\Support\Facades\Hash::check($inputCurrentPassword, $userAuthData->password)) {
                $currentPasswordValid = true;
            }
            
            // 2. Fallback checks (Mirroring AuthController logic)
            if (!$currentPasswordValid) {
                // If password hasn't been changed yet (or even if it has, if the hash matches, we caught it above. 
                // If hash failed, check plain text fallbacks IF strictly necessary, 
                // but AuthController only allows these if !is_password_changed.
                // However, since the user IS logged in, we should trust these credentials if they match the initial setup.)
                
                $isDefaultState = !$userAuthData->is_password_changed;
                
                if ($isDefaultState) {
                    if (strtolower($inputCurrentPassword) === strtolower($userAuthData->lottery_number) || 
                        strtolower($inputCurrentPassword) === strtolower($userAuthData->nik)) {
                        $currentPasswordValid = true;
                    }
                }
            }

            if (!$currentPasswordValid) {
                \Illuminate\Support\Facades\Log::warning('Password Update Failed', [
                    'id' => $participant->id,
                    'input' => $inputCurrentPassword,
                    'nik' => $userAuthData->nik,
                    'is_changed' => $userAuthData->is_password_changed
                ]);
                return back()->withErrors(['current_password' => 'Password saat ini tidak cocok']);
            }
            
            if ($inputCurrentPassword === $request->new_password) {
                 return back()->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password saat ini']);
            }
            
            $commonUpdates['password'] = $request->new_password; 
            $commonUpdates['is_password_changed'] = true;
        }

        // Apply updates to all linked accounts
        foreach ($linkedParticipants as $p) {
            $p->update($commonUpdates);
        }
        
        return redirect()->route('participant.profile')->with('success', 'Profil dan semua akun tertaut berhasil diperbarui');
    }

    public function deletePhoto()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Find all participants with the same NIK to keep profiles in sync
        $linkedParticipants = Participant::where('nik', $participant->nik)->get();

        if ($participant->photo) {
            Storage::disk('public')->delete($participant->photo);
            
            foreach ($linkedParticipants as $p) {
                $p->update(['photo' => null]);
            }
            
            return redirect()->route('participant.profile')->with('success', 'Foto profil berhasil dihapus untuk semua akun tertaut.');
        }

        return redirect()->route('participant.profile')->with('error', 'Tidak ada foto profil untuk dihapus.');
    }

    public function updateBid(Request $request, $bidId)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Find the bid and verify it belongs to the participant
        $bid = Bid::with('monthlyPeriod')->findOrFail($bidId);
        
        if ($bid->participant_id !== $participant->id) {
            abort(403, 'Unauthorized access');
        }
        
        $period = $bid->monthlyPeriod;
        
        if ($participant->has_won) {
            return back()->with('error', 'Anda sudah menang dan tidak dapat mengubah lelang lagi.');
        }

        if ($period->status !== 'bidding') {
            return back()->with('error', 'Periode lelang sudah ditutup.');
        }
        
        $deadline = $this->getBidDeadline($period);
        if (now()->greaterThan($deadline)) {
            return back()->with('error', 'Waktu input lelang Anda telah habis. Batas waktu penawaran telah berakhir.');
        }
        
        $validated = $request->validate([
            'bid_amount' => 'required|numeric|min:' . $period->group->min_bid . '|max:' . $period->group->max_bid
        ]);
        
        if ($bid->is_permanent) {
            return back()->with('error', 'Lelang ini sudah bersifat permanen dan tidak dapat diubah.');
        }

        $bid->update([
            'bid_amount' => $validated['bid_amount'],
            'bid_time' => now()
        ]);

        $formattedAmount = 'Rp ' . number_format($validated['bid_amount'], 0, ',', '.');
        return redirect()->route('participant.dashboard')
            ->with('success', 'Lelang berhasil diperbarui. Nilai lelang Anda saat ini: ' . $formattedAmount);
    }

    public function viewAuction($periodId)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Get unread notification count for bukti angsuran (using NIK-based count)
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        
        $period = MonthlyPeriod::with(['group', 'bids.participant', 'winners.participant'])
            ->findOrFail($periodId);
        
        // Verify participant belongs to this period's group
        if ($period->group_id !== $participant->group_id) {
            abort(403, 'Unauthorized access');
        }
        
        // Get all bids for this period with participant info
        $bids = $period->bids()
            ->with('participant')
            ->orderBy('bid_amount', 'desc')
            ->get();
        
        // Get participant's current bid
        $participantBid = $bids->where('participant_id', $participant->id)->first();
        
        return view('participant.auction.view', compact('period', 'bids', 'participantBid', 'unreadBuktiAngsuranCount'));
    }

    public function buktiAngsuran()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Mark unread notifications as read ONLY for the current participant
        Payment::where('participant_id', $participant->id)
                ->where('is_notification_read', false)
                ->where('is_confirmed', true)
                ->update([
                    'is_notification_read' => true,
                    'notification_read_at' => now()
                ]);
        
        // Get unread notification count (will be 0 after marking as read)
        $unreadBuktiAngsuranCount = 0;
        
        // Get payments ONLY for the current participant account
        // Order by monthly period start date desc, then payment date desc to match admin cash manage
        $payments = Payment::with(['monthlyPeriod' => function($query) {
                $query->orderBy('period_start', 'desc');
            }, 'confirmedBy', 'participant.group'])
            ->where('participant_id', $participant->id)
            ->where('is_confirmed', true)
            ->orderByRaw('
                CASE 
                    WHEN monthly_period_id IS NOT NULL THEN (
                        SELECT period_start FROM monthly_periods WHERE id = monthly_period_id
                    )
                    ELSE payment_date
                END DESC
            ')
            ->orderBy('payment_date', 'desc')
            ->get();
        
        // Load CashFlow data for each payment's period
        foreach ($payments as $payment) {
            if ($payment->monthlyPeriod) {
                $cashFlow = \App\Models\CashFlow::where('monthly_period_id', $payment->monthlyPeriod->id)->first();
                $payment->cash_flow_name = $cashFlow ? $cashFlow->month_name : null;
            } else {
                $payment->cash_flow_name = null;
            }
        }
        
        return view('participant.bukti-angsuran', compact('participant', 'payments', 'unreadBuktiAngsuranCount'));
    }

    public function viewReceipt($paymentId)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        $payment = Payment::with([
            'participant',
            'monthlyPeriod.winners.participant',
            'monthlyPeriod.bids', // Needed for calculation
            'group.participants',
            'confirmedBy'
        ])->findOrFail($paymentId);

        // Security check: ensure the payment belongs to a participant with the same NIK (same person)
        // This allows participants registered in multiple groups to view all their receipts
        $sameNikParticipantIds = Participant::where('nik', $participant->nik)->pluck('id')->toArray();
        if (!in_array($payment->participant_id, $sameNikParticipantIds)) {
            abort(403, 'Unauthorized access');
        }

        $group = $payment->group;
        $contextPeriod = $payment->monthlyPeriod;

        // -------------------------------------------------------------
        // CALCULATIONS (Synchronized with Admin View - POTENTIAL BASIS)
        // -------------------------------------------------------------
        
        // 1. Context Values
        $participantCount = $group->participants->where('is_active', true)->count();
        $monthlyInstallment = $group->monthly_installment;
        $potentialInstallment = $participantCount * $monthlyInstallment;
        $mainPrize = $group->main_prize;
        $shuAmount = $group->shu ?? 500000;
        
        // 2. Calculation logic
        $previousCashBalance = 0;
        $winnerCount = 0;
        $highestBid = 0;
        $totalBids = 0;
        
        if ($contextPeriod) {
            $winnerCount = $contextPeriod->winners->count();
            
            // Get bids
            $winnerBids = [];
            foreach ($contextPeriod->winners as $winner) {
                if ($winner->bid_amount > 0) {
                    $winnerBids[] = $winner->bid_amount;
                }
            }
            $totalBids = array_sum($winnerBids);
            
            // Previous Cash Balance calculation
            $previousCashBalance = $contextPeriod->previous_cash_balance ?? 0;
            
            if ($previousCashBalance == 0) {
                 // Fallback calculation logic
                 $allPrevPeriods = \App\Models\MonthlyPeriod::where('group_id', $group->id)
                    ->where('period_start', '<', $contextPeriod->period_start)
                    ->orderBy('period_start', 'asc')
                    ->get();
                
                $running = 0;
                foreach ($allPrevPeriods as $p) {
                    $pCount = $group->participants->where('is_active', true)->count(); 
                    $pInstallment = $pCount * $monthlyInstallment;
                    $pHighestBid = $p->bids->max('bid_amount') ?? 0;
                    $pWinnerCount = $p->winners->count();
                    $pTotalPrize = $pWinnerCount * $mainPrize;
                    $pShuDed = ($pWinnerCount > 0) ? ($pWinnerCount * $shuAmount) : 0;
                    
                    $pSurplus = ($pInstallment - $pShuDed) + $pHighestBid - $pTotalPrize;
                    
                    $startBal = ($p->previous_cash_balance > 0) ? $p->previous_cash_balance : $running;
                    $running = $startBal + $pSurplus;
                }
                $previousCashBalance = $running;
            }
        }
        
        // 3. Current Month Logic
        // Inflow
        $totalShuDeduction = ($winnerCount > 0) ? ($winnerCount * $shuAmount) : 0;
        $netFunds = $potentialInstallment - $totalShuDeduction;
        $currentFund = $netFunds + $totalBids;
        
        // Outflow
        $totalMotorPrice = $winnerCount * $mainPrize; 
        
        // Result
        $surplus = $currentFund - $totalMotorPrice;
        $remainingCash = $previousCashBalance + $surplus; // Sisa Kas Lelang Saat Ini

        // -------------------------------------------------------------
        // Additional Info
        // -------------------------------------------------------------
        // Total winners BEFORE this period
        $previousWinnersCount = \App\Models\Winner::whereHas('monthlyPeriod', function($query) use ($group, $contextPeriod) {
            $query->where('group_id', $group->id)
                  ->where('period_start', '<', $contextPeriod->period_start);
        })->count();

        // Winners IN this period
        $currentPeriodWinnersCount = $winnerCount;

        // Formatted vars
        $lastAuctionDate = $contextPeriod->period_end;

        // Management signatures
        $admin_signature = \App\Models\Management::where('nama_lengkap', 'Arbi Muhtarom')
                                               ->orWhere('jabatan', 'Admin')
                                               ->first() ?? \App\Models\Management::find(3);

        return view('participant.receipt', compact(
            'payment', 
            'previousWinnersCount', 
            'currentPeriodWinnersCount', 
            'lastAuctionDate', 
            'remainingCash',
            'group',
            'admin_signature'
        ));
    }

    public function switchAccount($targetId)
    {
        $currentParticipant = Auth::guard('participant')->user()->fresh();
        
        // Find the target participant
        $targetParticipant = Participant::findOrFail($targetId);
        
        // Validation: Verify that the target has the same NIK as the current user
        if ($targetParticipant->nik !== $currentParticipant->nik) {
            abort(403, 'Unauthorized switch.');
        }
        
        // Perform the login switch
        Auth::guard('participant')->login($targetParticipant);
        
        return redirect()->route('participant.dashboard')->with('success', 'Berhasil beralih ke akun ' . $targetParticipant->lottery_number);
    }
    public function hubungiKami()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        $customerServices = \App\Models\CustomerService::where('is_active', true)->get();

        return view('participant.hubungi-kami', compact('participant', 'customerServices', 'unreadBuktiAngsuranCount'));
    }

    public function kta()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        $setting = KtaSetting::first() ?? new KtaSetting();
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        
        return view('participant.kta', compact('participant', 'setting', 'unreadBuktiAngsuranCount'));
    }

    public function terms()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        
        return view('participant.terms', compact('participant', 'unreadBuktiAngsuranCount'));
    }

    public function downloadKta()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        $settings = KtaSetting::first();
        if (!$settings) {
            $settings = new KtaSetting(); // Defaults
        }
        
        // Load relationships
        $participant->load(['group', 'winner.monthlyPeriod']);
        
        $pdf = Pdf::loadView('participant.kta-pdf', compact('participant', 'settings'));
        
        $filename = 'KTA-' . $participant->nik . '.pdf';
        
        return $pdf->download($filename);
    }

    public function documentations()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        
        // Get unread notification count for bukti angsuran (using NIK-based count)
        $unreadBuktiAngsuranCount = $this->getUnreadBuktiAngsuranCount($participant);
        
        // Get periods with documentation
        $periodsWithDocs = $participant->group->monthlyPeriods()
            ->whereHas('documentations')
            ->with(['documentations', 'winners'])
            ->orderBy('period_start', 'desc')
            ->get();
            
        return view('participant.documentations.list', compact('participant', 'periodsWithDocs', 'unreadBuktiAngsuranCount'));
    }

    public function debugPasswordForm()
    {
        $participant = Auth::guard('participant')->user()->fresh();
        $dbData = \Illuminate\Support\Facades\DB::table('participants')
            ->where('id', $participant->id)
            ->select('password', 'lottery_number')
            ->first();

        return response()->make('
            <html>
            <head>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            </head>
            <body class="p-5">
                <h1>Debug Password for: ' . $participant->name . '</h1>
                <p><strong>ID:</strong> ' . $participant->id . '</p>
                 <p><strong>Lottery Number (as fallback):</strong> ' . $participant->lottery_number . '</p>
                 <p><strong>Is Password in DB?</strong> ' . (empty($dbData->password) ? 'NO' : 'YES') . '</p>
                <div class="alert alert-info">
                    <strong>Hash Preview:</strong> ' . substr($dbData->password, 0, 15) . '...
                </div>
                
                <hr>
                
                <form method="POST" action="' . route('participant.debug.password.check') . '">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <div class="mb-3">
                        <label>Test Password Input:</label>
                        <input type="text" name="test_password" class="form-control" placeholder="Enter password to check">
                    </div>
                    <button type="submit" class="btn btn-primary">Check Password</button>
                    <a href="' . route('participant.profile') . '" class="btn btn-secondary">Back to Profile</a>
                </form>
            </body>
            </html>
        ');
    }

    public function debugPasswordCheck(Request $request)
    {
        $participant = Auth::guard('participant')->user()->fresh();
        $input = $request->test_password;
        
        $fresh = \Illuminate\Support\Facades\DB::table('participants')->where('id', $participant->id)->first();
        
        $checkHash = \Illuminate\Support\Facades\Hash::check($input, $fresh->password);
        $checkLottery = ($input === $participant->lottery_number);
        
        $msg = "<html><body style='padding:50px; font-family:sans-serif;'>";
        $msg .= "<h1>Diagnostic Result</h1>";
        $msg .= "<p>Input was: <strong>" . htmlspecialchars($input) . "</strong></p>";
        
        $msg .= "<h3>Check Results:</h3>";
        $msg .= "<ul>";
        $msg .= "<li><strong>Matches Database Hash?</strong> " . ($checkHash ? '<span style="color:green; font-weight:bold">YES (MATCH)</span>' : '<span style="color:red; font-weight:bold">NO</span>') . "</li>";
        $msg .= "<li><strong>Matches Lottery Number (Fallback)?</strong> " . ($checkLottery ? '<span style="color:green; font-weight:bold">YES (MATCH)</span>' : '<span style="color:red; font-weight:bold">NO</span>') . "</li>";
        $msg .= "</ul>";
        
        $msg .= "<hr><h3>What this means:</h3>";
        if ($checkHash) {
             $msg .= "<p style='color:green'>The password you entered IS CORRECT according to the database hash.</p>";
        } elseif ($checkLottery) {
             $msg .= "<p style='color:orange'>The password matches your Lottery Number. This works as a fallback.</p>";
        } else {
             $msg .= "<p style='color:red'>The password is INCORRECT. It matches neither the current database hash nor your lottery number.</p>";
        }
        
        $msg .= "<br><a href='" . route('participant.debug.password') . "'>Try Another Password</a>";
        $msg .= "</body></html>";
        
        return $msg;
    }
}
