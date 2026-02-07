<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Group;
use App\Models\Participant;
use App\Models\MonthlyPeriod;
use App\Models\Payment;
use App\Models\Winner;
use App\Models\Bid;
use App\Models\Auction;
use App\Models\User;
use App\Models\Saksi;
use App\Models\Position;
use App\Models\Management;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ParticipantsExport;
use App\Exports\ParticipantsTemplateExport;
use App\Imports\ParticipantsImport;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get filter inputs or default to current date
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedYear = (int) $request->input('year', now()->year);

        // Get distinct months/years available in MonthlyPeriod for the filter dropdown
        $availablePeriods = MonthlyPeriod::selectRaw('YEAR(period_start) as year, MONTH(period_start) as month')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Get auction results filtered by selected month/year
        $auctionResults = Winner::with([
            'participant', 
            'monthlyPeriod.group',
            'bid'
        ])
        ->whereHas('monthlyPeriod', function($query) use ($selectedMonth, $selectedYear) {
            $query->whereYear('period_start', $selectedYear)
                  ->whereMonth('period_start', $selectedMonth);
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($winner) {
            return [
                'group_name' => $winner->monthlyPeriod->group->name,
                'lottery_number' => $winner->participant->lottery_number,
                'participant_name' => $winner->participant->name,
                'shift' => $winner->participant->shift,
                'department' => $winner->participant->department, // Assuming 'Bag' refers to department
                'nik' => $winner->participant->nik,
                'bid_amount' => $winner->bid_amount,
                // period_info is no longer strictly needed for the table columns requested, but good to have in data just in case
                'period_info' => $winner->monthlyPeriod->period_name
            ];
        });

        // Get groups with shift summaries (existing logic)
        $groups = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true);
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'desc');
            },
            'monthlyPeriods.winners.participant'
        ])->get();

        // Calculate shift summaries for each group (existing logic)
        $groupSummaries = $groups->map(function ($group) {
            $currentPeriod = $group->monthlyPeriods->firstWhere('status', '!=', 'completed');
            $totalWinners = $group->monthlyPeriods->sum(function($period) {
                return $period->winners->count();
            });
            
            // Get current period number (total periods created)
            $periodCount = $group->monthlyPeriods->count();
            
            return [
                'group' => $group,
                'current_period' => $currentPeriod,
                'period_count' => $periodCount,
                'total_winners' => $totalWinners,
                'active_participants' => $group->participants->count()
            ];
        });

        return view('admin.dashboard', compact('auctionResults', 'groupSummaries', 'availablePeriods', 'selectedMonth', 'selectedYear'));
    }

    public function exportAuctionResultsPdf(Request $request)
    {
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedYear = (int) $request->input('year', now()->year);

        // Get auction results filtered by selected month/year
        $auctionResults = Winner::with([
            'participant', 
            'monthlyPeriod.group',
            'bid'
        ])
        ->whereHas('monthlyPeriod', function($query) use ($selectedMonth, $selectedYear) {
            $query->whereYear('period_start', $selectedYear)
                  ->whereMonth('period_start', $selectedMonth);
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($winner) {
            return [
                'group_name' => $winner->monthlyPeriod->group->name,
                'lottery_number' => $winner->participant->lottery_number,
                'participant_name' => $winner->participant->name,
                'shift' => $winner->participant->shift,
                'department' => $winner->participant->department,
                'nik' => $winner->participant->nik,
                'bid_amount' => $winner->bid_amount,
                'period_name' => $winner->monthlyPeriod->period_name
            ];
        });

        $monthName = \Carbon\Carbon::create()->month($selectedMonth)->locale('id')->monthName;
        $title = "Laporan Pemenang Arisan - $monthName $selectedYear";

        $pdf = Pdf::loadView('admin.exports.auction-results-pdf', compact('auctionResults', 'title', 'selectedMonth', 'selectedYear', 'monthName'));
        
        return $pdf->download('laporan-pemenang-arisan-'.$selectedMonth.'-'.$selectedYear.'.pdf');
    }

    public function groups()
    {
        $groups = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true);
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'asc');
            },
            'monthlyPeriods.bids',
            'monthlyPeriods.winners'
        ])->get();

        // Calculate accumulation for each group
        foreach ($groups as $group) {
            $runningAccumulation = 0;
            
            foreach ($group->monthlyPeriods as $period) {
                $shuAmount = (float)($group->shu ?? 500000);
                $mainPrize = (float)$group->main_prize;
                
                // Calculate actual installments for this period
                $actualInstallments = Payment::where('monthly_period_id', $period->id)->sum('amount');
                $winnerCount = $period->winners->count();
                
                // Get bids only from winners for this period (matching manageGroup logic)
                $winnerBids = [];
                if ($winnerCount > 0) {
                    foreach ($period->winners as $winner) {
                        if ($winner->bid_amount > 0) {
                            $winnerBids[] = $winner->bid_amount;
                        }
                    }
                }
                $totalBids = array_sum($winnerBids);
                
                // Financial Breakdown matching logic (synced with periods method)
                $calPreviousCashBalance = ($period->previous_cash_balance > 0) ? (float)$period->previous_cash_balance : (float)$runningAccumulation;
                
                // 1. Dana Iuran Bersih = (Total Angsuran Masuk) - (jumlah pemenang x SHU)
                $calNetFunds = $actualInstallments - ($winnerCount * $shuAmount);
                
                // 2. Total Bid = sum of all winner bids
                $calTotalBidAmount = $totalBids;
                
                // 3. Total Harga Motor = Jumlah Pemenang x Harga Satuan Motor
                $calTotalMotorPrice = $winnerCount * $mainPrize;
                
                // 4. Dana Saat Ini = Dana Iuran Bersih + Total Bid
                $calCurrentFund = $calNetFunds + $calTotalBidAmount;
                
                // 5. Sisa Bersih Periode Ini = Dana Saat Ini - Total Harga Motor
                $calFinalRemainingCash = $calCurrentFund - $calTotalMotorPrice;
                
                // 6. Total Kas Berjalan = Saldo Akumulasi Lalu + Sisa Bersih Periode Ini
                $calTotalRunningCash = $calPreviousCashBalance + $calFinalRemainingCash;
                
                $runningAccumulation = $calTotalRunningCash;
            }
            
            $group->total_accumulation = $runningAccumulation;
        }

        return view('admin.groups.index', compact('groups'));
    }

    public function createGroup()
    {
        return view('admin.groups.create');
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|min:1|unique:groups,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_participants' => 'required|integer',
            'monthly_installment' => 'required|numeric|min:0',
            'main_prize' => 'required|numeric|min:0',
            'shu' => 'required|numeric|min:0',
            'min_bid' => 'required|numeric|min:0',
            'max_bid' => 'required|numeric|min:0'
        ], [
            'id.required' => 'ID Kelompok wajib diisi.',
            'id.integer' => 'ID Kelompok harus berupa angka.',
            'id.min' => 'ID Kelompok minimal 1.',
            'id.unique' => 'ID Kelompok sudah digunakan. Silakan pilih ID lain.',
        ]);

        Group::create($validated);
        return redirect()->route('admin.groups')->with('success', 'Kelompok berhasil dibuat dengan ID: ' . $validated['id']);
    }

    public function participants($groupId)
    {
        $group = Group::findOrFail($groupId);
        $participants = $group->participants()->orderBy('lottery_number')->get();
        return view('admin.participants.index', compact('group', 'participants'));
    }

    public function importParticipants(Request $request, $groupId)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240' // Max 10MB
        ]);

        // Increase execution time for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        try {
            $startTime = microtime(true);
            
            // Check if file was uploaded
            if (!$request->hasFile('excel_file')) {
                return redirect()->back()
                    ->with('error', 'No file uploaded');
            }
            
            $file = $request->file('excel_file');
            
            // Check if file is valid
            if (!$file->isValid()) {
                return redirect()->back()
                    ->with('error', 'File upload error: ' . $file->getErrorMessage());
            }
            
            Log::info('Starting import for group ' . $groupId . ' with file: ' . $file->getClientOriginalName());
            
            $importer = new ParticipantsImport($groupId);
            \Maatwebsite\Excel\Facades\Excel::import($importer, $file);
            
            $importErrors = $importer->getErrors();
            $executionTime = round(microtime(true) - $startTime, 2);
            
            Log::info('Import completed in ' . $executionTime . ' seconds');
            
            if (!empty($importErrors)) {
                $errorMsg = "Import selesai, namun ada beberapa baris yang bermasalah:<br><ul>";
                foreach($importErrors as $err) {
                    $errorMsg .= "<li>" . $err . "</li>";
                }
                $errorMsg .= "</ul>Silakan periksa kembali file Anda.";
                return redirect()->back()->with('warning', $errorMsg);
            }
            
            return redirect()->back()
                ->with('success', "Data peserta berhasil diimpor dalam {$executionTime} detik.");
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            Log::error('Excel validation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Excel format error: ' . $e->getMessage());
                
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during import: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Database error: ' . $e->getMessage());
                
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Error importing participants: ' . $e->getMessage());
        }
    }

    public function downloadParticipantTemplate($groupId)
    {
        return Excel::download(new ParticipantsTemplateExport, 'template_import_peserta.xlsx');
    }

    public function deleteAllParticipants($groupId)
    {
        $group = Group::findOrFail($groupId);
        
        // Get all participant IDs for this group
        $participantIds = $group->participants()->pluck('id');
        
        if ($participantIds->isEmpty()) {
            return redirect()->back()
                ->with('info', 'Tidak ada peserta untuk dihapus dalam grup ini.');
        }
        
        // Check if any participants have related data using direct queries
        $hasPayments = Payment::whereIn('participant_id', $participantIds)->exists();
        $hasBids = Bid::whereIn('participant_id', $participantIds)->exists();
        $hasWinners = Winner::whereIn('participant_id', $participantIds)->exists();
        
        if ($hasPayments || $hasBids || $hasWinners) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus semua peserta karena ada data pembayaran, lelang, atau pemenang yang terkait dengan peserta dalam grup ini.');
        }
        
        $participantCount = $participantIds->count();
        
        // Delete all participants for this group
        $group->participants()->delete();
        
        return redirect()->back()
            ->with('success', "Berhasil menghapus {$participantCount} peserta dari grup {$group->name}.");
    }

    public function groupSettings($groupId)
    {
        $group = Group::with(['participants', 'monthlyPeriods', 'payments', 'bids', 'winners'])->findOrFail($groupId);
        return view('admin.groups.settings', compact('group'));
    }

    public function updateGroupSettings(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_participants' => 'required|integer|min:1|max:200',
            'monthly_installment' => 'required|numeric|min:0',
            'main_prize' => 'required|numeric|min:0',
            'shu' => 'required|numeric|min:0',
            'min_bid' => 'required|numeric|min:0',
            'max_bid' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $group->update($validated);
        
        return redirect()->route('admin.groups.settings', $groupId)
            ->with('success', 'Pengaturan grup berhasil diperbarui.');
    }

    public function deleteGroup($groupId)
    {
        $group = Group::findOrFail($groupId);
        
        // Delete all related data in proper order to avoid foreign key constraints
        $group->winners()->delete();
        $group->bids()->delete();
        $group->payments()->delete();
        $group->monthlyPeriods()->delete();
        $group->participants()->delete();
        
        // Finally delete the group
        $group->delete();
        
        return redirect()->route('admin.groups')
            ->with('success', "Grup '{$group->name}' dan semua data terkait berhasil dihapus.");
    }

    public function monthlyPeriods()
    {
        $periods = MonthlyPeriod::with('group')->orderBy('period_start', 'desc')->get();
        $groups = Group::where('is_active', true)->get();
        return view('admin.periods.index', compact('periods', 'groups'));
    }

    public function createMonthlyPeriod()
    {
        $groups = Group::where('is_active', true)->get();
        return view('admin.periods.create', compact('groups'));
    }

    public function storeMonthlyPeriod(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'period_name' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'previous_cash_balance' => 'required|numeric|min:0'
        ]);

        $group = Group::findOrFail($validated['group_id']);
        $participantCount = $group->participants()->where('is_active', true)->count();
        $totalInstallments = $participantCount * $group->monthly_installment;
        
        $validated['total_installments'] = $totalInstallments;
        $validated['total_amount'] = $validated['previous_cash_balance'] + $totalInstallments;
        $validated['access_code'] = strtoupper(Str::random(8));
        
        $winnerCount = $validated['previous_cash_balance'] < $group->main_prize ? 1 : 2;
        $shuPerWinner = $group->shu ?? 0;
        $validated['shu_amount'] = $shuPerWinner * $winnerCount;
        $validated['available_funds'] = $validated['total_amount'] - $validated['shu_amount'];

        MonthlyPeriod::create($validated);
        return redirect()->route('admin.periods')->with('success', 'Monthly period created successfully');
    }

    public function bidding($periodId)
    {
        $period = MonthlyPeriod::with(['group', 'bids.participant'])->findOrFail($periodId);
        $bids = $period->bids()->with('participant')->orderBy('bid_amount', 'desc')->get();
        
        return view('admin.bidding.index', compact('period', 'bids'));
    }

    public function startDrawing($periodId)
    {
        $period = MonthlyPeriod::with(['group', 'bids.participant', 'saksis'])->findOrFail($periodId);
 
        // Hanya bid dari peserta AKTIF yang BELUM MENANG
        // Bid valid (> 0) dianggap ikut lelang
        $highestBid = $period->bids()
            ->whereHas('participant', function($q) {
                $q->where('is_active', true)->where('has_won', false);
            })
            ->where('bid_amount', '>', 0)
            ->max('bid_amount');
 
        if ($highestBid === null) {
            return redirect()->back()->with('error', 'Belum ada bid/lelang yang masuk dari peserta aktif periode ini.');
        }
 
        $highestBidders = $period->bids()
            ->where('bid_amount', $highestBid)
            ->whereHas('participant', function($q) {
                $q->where('is_active', true)->where('has_won', false);
            })
            ->with('participant')
            ->get();
 
        // Determine winner count based on cash balance (1 or 2 winners)
        $winnerCount = $period->calculateWinnerCount();
 
        if ($highestBidders->count() < $winnerCount) {
            return redirect()->back()->with(
                'error',
                "Bid tertinggi hanya {$highestBidders->count()} peserta, tetapi periode ini butuh {$winnerCount} pemenang. Pastikan minimal {$winnerCount} peserta memasukkan bid tertinggi yang sama."
            );
        }

        // 1) Jika hanya 1 orang bid tertinggi dan periode butuh 1 pemenang -> langsung menang
        if ($highestBidders->count() == 1 && $winnerCount == 1) {
            $bid = $highestBidders->first();
            $finalPrize = $period->group->main_prize - $bid->bid_amount;

            Winner::create([
                'monthly_period_id' => $period->id,
                'participant_id' => $bid->participant_id,
                'bid_id' => $bid->id,
                'main_prize' => $period->group->main_prize,
                'bid_amount' => $bid->bid_amount,
                'final_prize' => $finalPrize,
                'needs_draw' => false,
                'draw_time' => now()
            ]);

            $bid->participant->update(['has_won' => true, 'won_at' => now()]);

            $remainingCash = $period->calculateRemainingCash($highestBid);
            $period->update([
                'remaining_cash' => $remainingCash,
                'status' => 'completed'
            ]);

            return redirect()->route('admin.bidding', $periodId)
                ->with('success', 'Pemenang ditentukan: ' . $bid->participant->name . ' dengan bid tertinggi Rp ' . number_format($bid->bid_amount, 0, ',', '.'));
        }

        // 2) Jika jumlah peserta bid tertinggi sama dengan jumlah pemenang yang dibutuhkan DAN jumlah pemenang > 1 -> langsung menang semua
        if ($highestBidders->count() == $winnerCount && $winnerCount > 1) {
            foreach ($highestBidders as $bid) {
                $finalPrize = $period->group->main_prize - $bid->bid_amount;

                Winner::create([
                    'monthly_period_id' => $period->id,
                    'participant_id' => $bid->participant_id,
                    'bid_id' => $bid->id,
                    'main_prize' => $period->group->main_prize,
                    'bid_amount' => $bid->bid_amount,
                    'final_prize' => $finalPrize,
                    'needs_draw' => false,
                    'draw_time' => now()
                ]);

                $bid->participant->update(['has_won' => true, 'won_at' => now()]);
            }

            $remainingCash = $period->calculateRemainingCash($highestBid);
            $period->update([
                'remaining_cash' => $remainingCash,
                'status' => 'completed'
            ]);

            return redirect()->route('admin.bidding', $periodId)
                ->with('success', "Pemenang ditentukan langsung dari bid tertinggi Rp " . number_format($highestBid, 0, ',', '.') . " (" . $winnerCount . " pemenang)");
        }

        // 3) Jika lebih dari 1 orang bid tertinggi sama -> mereka akan diundi

        // Calculate values for the view
        $participantCount = $period->group->participants()->where('is_active', true)->count();
        $installmentAmount = $period->group->monthly_installment;
        $grossDeposit = $participantCount * $installmentAmount;
        $accumulatedCash = $period->previous_cash_balance;
        $adminFee = $period->group->shu; // Assuming this is the fixed admin fee
        $netContribution = ($grossDeposit + $accumulatedCash) - $adminFee;
        
        $bidValue = $highestBid;
        $currentFund = $netContribution + $bidValue; // Dana Saat Ini
        $mainPrize = $period->group->main_prize;
        $netRemainingPeriod = $currentFund - $mainPrize; // Sisa Bersih Periode Ini
        
        // Determine the reference month (previous month of period start)
        $periodStart = \Carbon\Carbon::parse($period->period_start);
        $referenceMonthDate = $periodStart->copy()->subMonth();
        $referenceMonthName = $referenceMonthDate->locale('id')->monthName . ' ' . $referenceMonthDate->year;
        
        $calculation = [
            'participantCount' => $participantCount,
            'installmentAmount' => $installmentAmount,
            'grossDeposit' => $grossDeposit,
            'accumulatedCash' => $accumulatedCash,
            'adminFee' => $adminFee,
            'netContribution' => $netContribution,
            'bidValue' => $bidValue,
            'currentFund' => $currentFund,
            'mainPrize' => $mainPrize,
            'netRemainingPeriod' => $netRemainingPeriod,
            'referenceMonthName' => $referenceMonthName,
            'winnerTotalReceived' => $mainPrize - $bidValue,
            'totalCashRunning' => $accumulatedCash + $netRemainingPeriod // Lit. Formula
        ];

        // Fetch participants who are already in the Saksi registry to exclude them
        $alreadyWitnessParticipantIds = \App\Models\Saksi::whereNotNull('participant_id')->pluck('participant_id')->toArray();

        // Fetch all active participants as eligible witnesses, excluding those who are already witnesses
        $eligibleWitnesses = \App\Models\Participant::with('group')
            ->where('is_active', true)
            ->whereNotIn('id', $alreadyWitnessParticipantIds)
            ->orderBy('name')
            ->get();

        return view('admin.bidding.draw', compact('period', 'highestBidders', 'winnerCount', 'highestBid', 'calculation', 'eligibleWitnesses'));
    }

    public function performDraw(Request $request, $periodId)
    {
        $period = MonthlyPeriod::findOrFail($periodId);
        $selectedWinners = $request->input('winners', []);
        
        // Get highest valid bid (>0) from active non-winners
        $highestBid = $period->bids()
            ->whereHas('participant', function($q) {
                $q->where('is_active', true)->where('has_won', false);
            })
            ->where('bid_amount', '>', 0)
            ->max('bid_amount');

        if ($highestBid === null) {
            return redirect()->back()->with('error', 'Belum ada bid/lelang yang masuk dari peserta aktif periode ini.');
        }

        $highestBidders = $period->bids()
            ->where('bid_amount', $highestBid)
            ->whereHas('participant', function($q) {
                $q->where('is_active', true)->where('has_won', false);
            })
            ->get();
        
        // Validate that we're selecting from highest bidders only
        $winnerCount = $period->calculateWinnerCount();
        $selectedWinnerIds = array_intersect($selectedWinners, $highestBidders->pluck('id')->toArray());
        
        if (count($selectedWinnerIds) != $winnerCount) {
            return redirect()->back()->with('error', "Harap pilih exactly {$winnerCount} pemenang dari bid tertinggi.");
        }
        
        $totalPrizes = 0;
        foreach ($highestBidders as $bid) {
            $isWinner = in_array($bid->id, $selectedWinnerIds);
            
            if ($isWinner) {
                $finalPrize = $period->group->main_prize - $bid->bid_amount;
                $totalPrizes += $finalPrize;
                
                Winner::create([
                    'monthly_period_id' => $period->id,
                    'participant_id' => $bid->participant_id,
                    'bid_id' => $bid->id,
                    'main_prize' => $period->group->main_prize,
                    'bid_amount' => $bid->bid_amount,
                    'final_prize' => $finalPrize,
                    'needs_draw' => true,
                    'draw_time' => now()
                ]);
                
                $bid->participant->update(['has_won' => true, 'won_at' => now()]);
            }
        }
        
        // Calculate remaining cash based on winner count and bid amount
        $remainingCash = $period->calculateRemainingCash($highestBid);
        $period->update([
            'remaining_cash' => $remainingCash,
            'status' => 'completed'
        ]);

        // Handle Saksi Saving
        $saksiIdsInput = $request->input('saksi_ids');
        if (!empty($saksiIdsInput)) {
            $participantIds = explode(',', $saksiIdsInput);
            $saksiIdsToSync = [];
            
            // Get or Create Position "Saksi"
            $position = Position::firstOrCreate(['name' => 'Saksi'], ['description' => 'Saksi dari Peserta']);

            foreach ($participantIds as $pId) {
                // Check if this participant already has a Saksi record
                $saksi = Saksi::where('participant_id', $pId)->first();
                
                if (!$saksi) {
                    $participant = \App\Models\Participant::with('group')->find($pId);
                    if ($participant) {
                        $saksi = Saksi::create([
                            'participant_id' => $pId,
                            'position_id' => $position->id,
                            'nama_pengurus' => $participant->name,
                            'jabatan' => 'Saksi (Kelompok ' . ($participant->group->name ?? '-') . ')',
                            'is_active' => true,
                        ]);
                    }
                }
                if ($saksi) {
                    $saksiIdsToSync[] = $saksi->id;
                }
            }
            
            // Sync with Monthly Period
            $period->saksis()->sync($saksiIdsToSync);
        }
        
        return redirect()->route('admin.groups.manage', $period->group_id)
            ->with('success', "Undian selesai. {$winnerCount} pemenang telah ditentukan dari bid tertinggi Rp " . number_format($highestBid, 0, ',', '.'));
    }

    public function showPeriod($id)
    {
        $period = MonthlyPeriod::with(['group', 'bids.participant', 'winners.participant'])->findOrFail($id);
        $group = $period->group;

        // Load participants for the group to get active count
        $activeParticipants = $group->participants()->where('is_active', true)->get();
        $participantCount = $activeParticipants->count();
        $monthlyInstallment = $group->monthly_installment;
        $mainPrize = $group->main_prize;
        $shuAmount = $group->shu ?? 500000;

        // 1. Calculate total bids from winners (matching cashMonthDetail logic)
        $totalBids = $period->winners->sum('bid_amount') ?? 0;
        $highestBid = $period->bids->max('bid_amount') ?? 0;

        // 2. Calculate actual installments for THIS period
        $actualInstallments = Payment::where('monthly_period_id', $period->id)->sum('amount');

        // 3. Calculate previous cash balance using same logic as cashMonthDetail
        $previousCashBalance = $period->previous_cash_balance ?? 0;
        
        // Fallback: If 0, calculate accumulation from all previous periods (matching cashMonthDetail)
        if ($previousCashBalance == 0) {
            $runningAccumulation = 0;
            $allPreviousPeriods = MonthlyPeriod::where('group_id', $group->id)
                ->where('period_start', '<', $period->period_start)
                ->orderBy('period_start', 'asc')
                ->get();
                
            foreach ($allPreviousPeriods as $prevP) {
                $prevPayments = Payment::where('monthly_period_id', $prevP->id)->sum('amount');
                $prevWinnerCount = $prevP->winners()->count();
                $prevTotalBids = $prevP->winners()->sum('bid_amount');
                $prevShu = $group->shu ?? 500000;
                $prevPrize = $group->main_prize;
                
                $prevNetFunds = $prevPayments - ($prevWinnerCount * $prevShu);
                $prevTotalMotorPrice = $prevWinnerCount * $prevPrize;
                $prevSurplus = ($prevNetFunds + $prevTotalBids) - $prevTotalMotorPrice;
                
                // Chaining logic as used in cashMonthDetail
                $startBal = ($prevP->previous_cash_balance > 0) ? $prevP->previous_cash_balance : $runningAccumulation;
                $runningAccumulation = $startBal + $prevSurplus;
            }
            $previousCashBalance = $runningAccumulation;
        }

        // 4. Calculate current status
        $winnerCount = $period->winners->count();
        
        // Financial calculation matching cashMonthDetail logic
        $shuAmount = $group->shu ?? 500000;
        $mainPrize = $group->main_prize;
        
        // Net Funds = actual payments - (admin fee if winner exists)
        $netFunds = $actualInstallments - ($winnerCount > 0 ? ($winnerCount * $shuAmount) : 0);
        
        // Total Motor Price = Jumlah Pemenang x Harga Satuan Motor
        $totalMotorPrice = $winnerCount * $mainPrize;
        
        // Sisa Bersih Periode Ini = (Net Funds + Total Bids) - Total Motor Price
        $finalRemainingCash = ($netFunds + $totalBids) - $totalMotorPrice;
        
        // Akumulasi Kas = previous balance + sisa bersih
        $totalRunningCash = $previousCashBalance + $finalRemainingCash;

        // Override period attributes for the view to ensure consistency
        $period->previous_cash_balance = $previousCashBalance;
        $period->total_installments = $actualInstallments;
        $period->total_amount = $actualInstallments + $previousCashBalance;
        $period->shu_amount = $winnerCount > 0 ? ($winnerCount * $shuAmount) : 0;
        $period->available_funds = $netFunds + $totalBids;
        $period->remaining_cash = $totalRunningCash;

        return view('admin.periods.show', compact('period', 'highestBid', 'totalBids'));
    }

    public function editPeriod($id)
    {
        $period = MonthlyPeriod::with(['group', 'saksis.participant.group', 'bids.participant', 'winners.participant'])->findOrFail($id);
        $groups = Group::where('is_active', true)->get();
        
        // Get IDs of participants who are already witnesses to exclude them
        $alreadyWitnessParticipantIds = \App\Models\Saksi::whereNotNull('participant_id')->pluck('participant_id')->toArray();

        // All active participants can be witnesses if they haven't been one before
        $eligibleWitnesses = \App\Models\Participant::with('group')
            ->where('is_active', true)
            ->whereNotIn('id', $alreadyWitnessParticipantIds)
            ->orderBy('name')
            ->get();
            
        return view('admin.periods.edit', compact('period', 'groups', 'eligibleWitnesses'));
    }

    public function updatePeriod(Request $request, $id)
    {
        $period = MonthlyPeriod::findOrFail($id);
        
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'period_name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('monthly_periods')->ignore($id)->where('group_id', $request->group_id)
            ],
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'bid_deadline' => 'nullable|date|after_or_equal:period_start',
            'previous_cash_balance' => 'required|numeric|min:0',
            'status' => 'required|in:active,bidding,drawing,completed',
            'motor_slots' => 'required|integer|min:1|max:2',
            'notes' => 'nullable|string'
        ]);

        $group = Group::findOrFail($validated['group_id']);
        $participantCount = $group->participants()->where('is_active', true)->count();
        $totalInstallments = $participantCount * $group->monthly_installment;
        
        $validated['total_installments'] = $totalInstallments;
        $validated['total_amount'] = $validated['previous_cash_balance'] + $totalInstallments;
        
        // Use consistent calculation for 1 or 2 winners based on previous_cash_balance
        $winnerCount = $validated['previous_cash_balance'] < $group->main_prize ? 1 : 2;
        
        // Match SHU calculation with storeGroupPeriod (fixed 500k per period or per winner?)
        // In storeGroupPeriod it was forced to 500000.
        // In MonthlyPeriod mode calculateAvailableFunds it is 500000 * winnerCount.
        // I will follow the model's logic if possible or keep it consistent with what was there.
        $shuPerWinner = $group->shu ?? 500000;
        $validated['shu_amount'] = 500000; // Consistent with storeGroupPeriod force 500k
        
        $validated['available_funds'] = $validated['total_amount'] - $validated['shu_amount'];

        $period->update($validated);

        // Handle Saksi Saving
        $saksiIdsInput = $request->input('saksi_ids');
        if (isset($saksiIdsInput)) {
            $participantIds = array_filter(explode(',', $saksiIdsInput));
            $saksiIdsToSync = [];
            
            // Get or Create Position "Saksi"
            $position = \App\Models\Position::firstOrCreate(['name' => 'Saksi'], ['description' => 'Saksi dari Peserta']);

            foreach ($participantIds as $pId) {
                // Check if this participant already has a Saksi record
                $saksi = Saksi::where('participant_id', $pId)->first();
                
                if (!$saksi) {
                    $participant = \App\Models\Participant::with('group')->find($pId);
                    if ($participant) {
                        $saksi = Saksi::create([
                            'participant_id' => $pId,
                            'position_id' => $position->id,
                            'nama_pengurus' => $participant->name,
                            'jabatan' => 'Saksi (Kelompok ' . ($participant->group->name ?? '-') . ')',
                            'is_active' => true,
                        ]);
                    }
                }
                if ($saksi) {
                    $saksiIdsToSync[] = $saksi->id;
                }
            }
            
            // Sync with Monthly Period
            $period->saksis()->sync($saksiIdsToSync);
        }

        return redirect()->route('admin.periods')->with('success', 'Periode ' . $period->period_name . ' berhasil diperbarui');
    }

    public function deletePeriod($id)
    {
        $period = MonthlyPeriod::findOrFail($id);
        
        // Delete related data first (Cascading Delete)
        
        // 1. Delete Winners
        $period->winners()->delete();
        
        // 2. Delete Bids
        $period->bids()->delete();
        
        // 3. Delete CashFlow entry if exists
        \App\Models\CashFlow::where('monthly_period_id', $period->id)->delete();

        // 4. Detach Saksis
        $period->saksis()->detach();

        // 5. Unlink or Delete Payments? 
        // Usually better to unlink so payment record remains but is no longer attached to this specific period logic
        // However, if the period is deleted, the installment count logic might break if we just unlink.
        // For now, let's Set Null for safety, or Delete if strict. 
        // Given 'deleteGroup' deletes payments, let's DELETE payments associated with this specific period 
        // to cleanly rollback the state as if the period never happened.
        Payment::where('monthly_period_id', $period->id)->delete();
        
        // Delete the period
        $period->delete();
        
        return redirect()->back()->with('success', "Periode '{$period->period_name}' berhasil dihapus beserta data terkait.");
    }

    public function periods()
    {
        $groups = Group::where('is_active', true)->orderBy('name')->get();
        $groupId = request('group_id');
        $selectedGroup = $groupId ? Group::find($groupId) : null;

        if (!$groupId) {
            $periods = collect();
            return view('admin.periods.index', compact('periods', 'groups', 'selectedGroup'));
        }

        $query = MonthlyPeriod::with(['group', 'winners.participant', 'bids', 'group.participants'])
            ->where('group_id', $groupId);
        
        $periods = $query->get();
        
        // Calculate accumulation for each period (always calculate ASC for consistency)
        $periods = $periods->sortBy('period_start');
        $runningAccumulation = 0;
        foreach ($periods as $period) {
            $group = $period->group;
            $shuAmount = (float)($group->shu ?? 500000);
            $mainPrize = (float)$group->main_prize;
            
            // Calculate actual installments for this period
            $actualInstallments = Payment::where('monthly_period_id', $period->id)->sum('amount');
            $winnerCount = $period->winners->count();
            
            // Get bids only from winners for this period (matching manageGroup logic)
            $winnerBids = [];
            if ($winnerCount > 0) {
                foreach ($period->winners as $winner) {
                    if ($winner->bid_amount > 0) {
                        $winnerBids[] = $winner->bid_amount;
                    }
                }
            }
            $totalBids = array_sum($winnerBids);
            
            // Financial Breakdown using new calculation logic (matching manageGroup exactly)
            $calPreviousCashBalance = ($period->previous_cash_balance > 0) ? (float)$period->previous_cash_balance : (float)$runningAccumulation;
            
            // 1. Rumus Inflow (Pemasukan Bersih Bulan Ini)
            $calNetFunds = ($actualInstallments) - ($winnerCount * $shuAmount);
            
            // 2. Total Bid = bid1 + bid2 (sum of all winner bids)
            $calTotalBidAmount = $totalBids;
            
            // 3. Rumus Outflow (Pengeluaran Hadiah)
            $calTotalMotorPrice = $winnerCount * $mainPrize;
            
            // 4. Rumus Logika Aliran Kas (Dana Saat Ini)
            $calCurrentFund = $calNetFunds + $calTotalBidAmount;
            
            // Sisa Bersih Periode Ini = Dana Saat Ini - Total Harga Motor
            $calFinalRemainingCash = $calCurrentFund - $calTotalMotorPrice;
            
            // 5. Rumus Akumulasi Akhir (Saldo Dompet)
            $calTotalRunningCash = $calPreviousCashBalance + $calFinalRemainingCash;

            // Attach to period object
            $period->calculated_surplus = $calFinalRemainingCash;
            $period->calculated_accumulation = $calTotalRunningCash;
            
            $runningAccumulation = $calTotalRunningCash;
        }
        
        // Return to DESC for view
        $periods = $periods->sortByDesc('period_start');
        
        return view('admin.periods.index', compact('periods', 'groups', 'selectedGroup'));
    }

    public function groupPeriods($groupId)
    {
        $group = Group::findOrFail($groupId);
        $periods = MonthlyPeriod::where('group_id', $groupId)->with(['group', 'winners.participant', 'bids', 'group.participants'])->orderBy('period_start', 'desc')->get();
        
        // Calculate accumulation for each period (always calculate ASC for consistency)
        $periods = $periods->sortBy('period_start');
        $runningAccumulation = 0;
        foreach ($periods as $period) {
            $group = $period->group;
            $shuAmount = (float)($group->shu ?? 500000);
            $mainPrize = (float)$group->main_prize;
            
            // Calculate actual installments for this period
            $actualInstallments = Payment::where('monthly_period_id', $period->id)->sum('amount');
            $highestBid = (float)($period->bids->max('bid_amount') ?? 0);
            $winnerCount = $period->winners->count();
            
            // Financial Breakdown matching Participant logic
            $calPreviousCashBalance = ($period->previous_cash_balance > 0) ? (float)$period->previous_cash_balance : (float)$runningAccumulation;
            
            $calNetFunds = $actualInstallments - ($winnerCount > 0 ? $shuAmount : 0);
            $calCurrentFund = $calNetFunds + $highestBid;
            $calFinalRemainingCash = $calCurrentFund - ($winnerCount > 0 ? $mainPrize : 0);
            $calTotalRunningCash = $calPreviousCashBalance + $calFinalRemainingCash;

            // Attach to period object
            $period->calculated_surplus = $calFinalRemainingCash;
            $period->calculated_accumulation = $calTotalRunningCash;
            
            $runningAccumulation = $calTotalRunningCash;
        }
        
        // Return to DESC for view
        $periods = $periods->sortByDesc('period_start');
        
        $groups = Group::where('is_active', true)->get();
        $selectedGroup = $group;
        return view('admin.periods.index', compact('periods', 'groups', 'selectedGroup'));
    }

        public function createGroupPeriod($groupId)
    {
        $group = Group::with(['participants' => function ($query) {
            $query->where('is_active', true);
        }])->findOrFail($groupId);

        // Fetch Historical Cash Flows for Dropdown
        $cashFlows = \App\Models\CashFlow::where('group_id', $groupId)
            ->orderBy('month_key', 'desc')
            ->get();

        $referenceOptions = [];
        foreach ($cashFlows as $cf) {
            $balance = $cf->remaining_cash;

            // Match logic with Month Detail Page using new calculation for 2 winners
            if ($cf->monthlyPeriod) {
                $p = $cf->monthlyPeriod;
                $participantCount = $group->participants->where('is_active', true)->count();
                $monthlyInstallment = $group->monthly_installment;
                $mainPrize = $group->main_prize;
                $shuAmount = $group->shu ?? 500000;
                
                // Get winner count and bids
                $winnerCount = $p->winners->count();
                
                // Get bids only from winners for this period
                $winnerBids = [];
                if ($winnerCount > 0) {
                    foreach ($p->winners as $winner) {
                        if ($winner->bid_amount > 0) {
                            $winnerBids[] = $winner->bid_amount;
                        }
                    }
                }
                $totalBids = array_sum($winnerBids);
                
                // 1. Rumus Inflow (Pemasukan Bersih Bulan Ini)
                // Dana Iuran Bersih = (jumlah peserta x nominal iuran) - (jumlah pemenang x SHU)
                $netFunds = ($participantCount * $monthlyInstallment) - ($winnerCount * $shuAmount);
                
                // 2. Total Bid = bid1 + bid2 (sum of all winner bids)
                $calTotalBidAmount = $totalBids;
                
                // 3. Rumus Outflow (Pengeluaran Hadiah)
                // Total Harga Motor = Jumlah Pemenang x Harga Satuan Motor
                $totalMotorPrice = $winnerCount * $mainPrize;
                
                // 4. Rumus Logika Aliran Kas (Dana Saat Ini)
                // Dana Saat Ini = Dana Iuran Bersih + Total Bid
                $currentFund = $netFunds + $calTotalBidAmount;
                
                // Sisa Bersih Periode Ini = Dana Saat Ini - Total Harga Motor
                $surplus = $currentFund - $totalMotorPrice;
                
                // Total Kas Berjalan = Saldo Akumulasi Lalu + Sisa Bersih Periode Ini
                $balance = $p->previous_cash_balance + $surplus;
            }

            // Calculate next month name
            $currentMonth = \Carbon\Carbon::createFromFormat('Y-m', $cf->month_key);
            $nextMonth = $currentMonth->copy()->addMonth();
            $nextMonthName = $nextMonth->locale('id')->monthName . ' ' . $nextMonth->year;

            $referenceOptions[] = [
                'id' => $cf->id,
                'source_type' => 'cash_flow',
                'label' => "{$cf->month_name} (Saldo Akhir: Rp " . number_format($balance, 0, ',', '.') . ")",
                'balance' => $balance,
                'month_key' => $cf->month_key,
                'next_month_name' => $nextMonthName
            ];
        }

        return view('admin.periods.create', compact('group', 'referenceOptions'));
    }

    public function storeGroupPeriod(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        
        $validated = $request->validate([
            'period_name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('monthly_periods')->where('group_id', $groupId)
            ],
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'bid_deadline' => 'nullable|date',
            'reference_source' => 'required|string', // 'manual' or period_id
            'manual_balance' => 'nullable|numeric|min:0',
            'custom_cash_name' => 'nullable|string|max:255', // Added custom name validation
            'motor_slots' => 'required|integer|min:1|max:2',
            'notes' => 'nullable|string'
        ]);

        // Determine Opening Balance
        $previousBalance = 0;
        
        if ($validated['reference_source'] === 'manual') {
            $previousBalance = $validated['manual_balance'] ?? 0;
        } else {
            // Trust the manual_balance field which was populated by JS with the projected balance
            // This ensures consistency between what the user saw in the dropdown and what is saved.
            $previousBalance = $validated['manual_balance'] ?? 0;
        }

        // Prepare Data
        $participantCount = $group->participants()->where('is_active', true)->count();
        $monthlyInstallment = $group->monthly_installment;
        $totalInstallments = $participantCount * $monthlyInstallment;
        
        $winnerCount = $validated['motor_slots'];
        $shuPerWinner = $group->shu ?? 500000;
        $totalShu = $shuPerWinner * $winnerCount; // Usually 1 SHU per period logic, but if 2 winners maybe 2 SHU? 
        // Note: Usually SHU is fixed per period expense? Assuming fixed per period or per winner?
        // Prompt implies "Potongan SHU... - Rp500.000" (singular).
        // If 2 slots, maybe check business logic? For now assume standard 500k.
        $totalShu = 500000; // Force constant for now unless specified otherwise

        $periodData = [
            'group_id' => $groupId,
            'period_name' => $validated['period_name'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'bid_deadline' => $validated['bid_deadline'] ?? null,
            'previous_cash_balance' => $previousBalance,
            'motor_slots' => $winnerCount,
            'status' => 'bidding', // Default to bidding so participants can immediately place bids
            'notes' => $validated['notes'],
            'total_installments' => $totalInstallments, // Set expected
            'total_amount' => $previousBalance + $totalInstallments,
            'shu_amount' => $totalShu,
            'available_funds' => ($previousBalance + $totalInstallments) - $totalShu,
            'remaining_cash' => $previousBalance // Will accumulate
        ];

        $period = \App\Models\MonthlyPeriod::create($periodData);
        
        // Trigger Automatic Cash Table Entry
        $this->createCashEntryForPeriod($period, $group, $validated['custom_cash_name'] ?? null);
        
        return redirect()->route('admin.groups.manage', $groupId)
            ->with('success', 'Periode baru berhasil dibuat dan Kas Bulanan telah diaktifkan!');
    }

    /**
     * Update existing period status to bidding
     */
    public function updatePeriodStatusToBidding($groupId, $periodId)
    {
        $period = MonthlyPeriod::findOrFail($periodId);
        
        // Only update if status is 'active' and not already 'bidding'
        if ($period->status === 'active') {
            $period->update(['status' => 'bidding']);
            return redirect()->back()->with('success', 'Status periode berhasil diubah menjadi bidding');
        }
        
        return redirect()->back()->with('info', 'Status periode sudah bidding atau completed');
    }

    /**
     * Membuat entri kas otomatis untuk periode baru (Current Period)
     */
    private function createCashEntryForPeriod($period, $group, $customName = null)
    {
        $currentMonthKey = $period->period_start->format('Y-m');
        // Use custom name if provided, otherwise default format
        $currentMonthName = $customName ?: ($period->period_start->locale('id')->monthName . ' ' . $period->period_start->year);
        
        // Find existing cash flow linked to THIS SPECIFIC PERIOD 
        // We no longer reuse by month_key to prevent "mixing" (kecampur)
        $existingCashEntry = \App\Models\CashFlow::where('monthly_period_id', $period->id)->first();
            
        if (!$existingCashEntry) {
            \App\Models\CashFlow::create([
                'group_id' => $group->id,
                'monthly_period_id' => $period->id, // Link fixed to this period
                'month_key' => $currentMonthKey,
                'month_name' => $currentMonthName,
                'previous_balance' => $period->previous_cash_balance,
                'monthly_installments' => 0, // Will update via Payments
                'total_bids' => 0,
                'admin_fees' => $period->shu_amount,
                'prizes_given' => 0,
                'remaining_cash' => $period->previous_cash_balance, // Start with previous
                'status' => 'draft'
            ]);
        } else {
            // Update existing linked to this period
            $updateData = [
                 'month_key' => $currentMonthKey,
                 'previous_balance' => $period->previous_cash_balance
            ];
            
            if ($customName) {
                $updateData['month_name'] = $customName;
            }
            
            $existingCashEntry->update($updateData);
        }
    }

    public function groupWinners($groupId)
    {
        $group = Group::findOrFail($groupId);
        $winners = Winner::with(['participant', 'monthlyPeriod'])
            ->whereHas('monthlyPeriod', function($query) use ($groupId) {
                $query->where('group_id', $groupId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.winners.index', compact('winners'))->with('selectedGroup', $group);
    }

    public function winners()
    {
        $winners = Winner::with(['participant', 'monthlyPeriod.group'])->orderBy('created_at', 'desc')->get();
        return view('admin.winners.index', compact('winners'));
    }

    // Group Management Methods
    public function manageGroup($groupId)
    {
        $group = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true);
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'asc');
            },
            'monthlyPeriods.winners.participant',
            'monthlyPeriods.bids'
        ])->findOrFail($groupId);

        // Pre-calculate accumulation for the table (always calculate ASC for consistency, then we can sort for view)
        $runningAccumulation = 0;
        foreach ($group->monthlyPeriods as $period) {
            $shuAmount = (float)($group->shu ?? 500000);
            $mainPrize = (float)$group->main_prize;
            $monthlyInstallment = (float)$group->monthly_installment;
            $participantCount = $group->participants->where('is_active', true)->count();
            
            // Calculate actual installments for this period
            $actualInstallments = Payment::where('monthly_period_id', $period->id)->sum('amount');
            $winnerCount = $period->winners->count();
            
            // Get bids only from winners for this period
            $winnerBids = [];
            if ($winnerCount > 0) {
                foreach ($period->winners as $winner) {
                    if ($winner->bid_amount > 0) {
                        $winnerBids[] = $winner->bid_amount;
                    }
                }
            }
            $totalBids = array_sum($winnerBids);
            $highestBid = !empty($winnerBids) ? max($winnerBids) : 0;
            
            // Financial Breakdown using new calculation logic
            $calPreviousCashBalance = ($period->previous_cash_balance > 0) ? (float)$period->previous_cash_balance : (float)$runningAccumulation;
            
            // 1. Rumus Inflow (Pemasukan Bersih Bulan Ini)
            // Dana Iuran Bersih = (Total Angsuran Masuk) - (jumlah pemenang x SHU)
            // UPDATED: Gunakan realisasi pembayaran ($actualInstallments) agar sesuai kondisi sebenarnya/realita
            $calNetFunds = ($actualInstallments) - ($winnerCount * $shuAmount);
            
            // 2. Total Bid = bid1 + bid2 (sum of all winner bids)
            $calTotalBidAmount = $totalBids;
            
            // 3. Rumus Outflow (Pengeluaran Hadiah)
            // Total Harga Motor = Jumlah Pemenang x Harga Satuan Motor
            $calTotalMotorPrice = $winnerCount * $mainPrize;
            
            // 4. Rumus Logika Aliran Kas (Dana Saat Ini)
            // Dana Saat Ini = Dana Iuran Bersih + Total Bid
            $calCurrentFund = $calNetFunds + $calTotalBidAmount;
            
            // Sisa Bersih Periode Ini = Dana Saat Ini - Total Harga Motor
            $calFinalRemainingCash = $calCurrentFund - $calTotalMotorPrice;
            
            // 5. Rumus Akumulasi Akhir (Saldo Dompet)
            // Total Kas Berjalan = Saldo Akumulasi Lalu + Sisa Bersih Periode Ini
            $calTotalRunningCash = $calPreviousCashBalance + $calFinalRemainingCash;

            // Attach to period object
            $period->calculated_surplus = $calFinalRemainingCash;
            $period->calculated_accumulation = $calTotalRunningCash;
            
            $runningAccumulation = $calTotalRunningCash;
        }
        
        // Sort DESC for view as requested ("buat agar sama")
        $group->setRelation('monthlyPeriods', $group->monthlyPeriods->sortByDesc('period_start'));
        
        // Get unread payment notification count for this group
        $unreadPaymentCount = Payment::whereHas('participant', function($query) use ($group) {
                    $query->where('group_id', $group->id);
                })
                ->where('is_notification_read', false)
                ->where('is_confirmed', true)
                ->count();
        
        return view('admin.groups.manage', compact('group', 'unreadPaymentCount'));
    }

    public function manageParticipants($groupId)
    {
        // Auto-sync status: Reset any participant marked as won but has no winner record (orphans)
        \App\Models\Participant::where('group_id', $groupId)
            ->where('has_won', true)
            ->whereDoesntHave('winner')
            ->update(['has_won' => false, 'won_at' => null]);
            
        // Also ensure anyone with a winner record IS marked as won
        \App\Models\Participant::where('group_id', $groupId)
            ->where('has_won', false)
            ->whereHas('winner')
            ->update(['has_won' => true]);

        $group = Group::with(['participants' => function($query) {
        $query->orderBy('registration_status', 'desc') // pending first usually or approved? Let's do pending first to highlight them
             ->orderBy('lottery_number');
        }, 'participants.winner', 'participants.bids.monthlyPeriod', 'monthlyPeriods' => function($query) {
            $query->orderBy('period_start', 'desc');
        }])->findOrFail($groupId);

        // Get the same period logic as auction process page
        // Default to current period (first non-completed period)
        $currentPeriod = $group->monthlyPeriods->where('status', '!=', 'completed')->first();
        
        // If there's a specific period being used in auction process, we should match that
        // For now, use the same logic as auction process page
        if (!$currentPeriod && $group->monthlyPeriods->count() > 0) {
            $currentPeriod = $group->monthlyPeriods->first();
        }

        // Alternative approach: load bids directly for each participant
        $participantsData = $group->participants->map(function($participant) use ($currentPeriod) {
            $currentBid = null;

            // According to requirements: only show bid amount for winners
            if ($participant->has_won && $participant->winner) {
                // Load the winning bid from the winner record
                $currentBid = (object) [
                    'bid_amount' => $participant->winner->bid_amount,
                    'monthly_period_id' => $participant->winner->monthly_period_id
                ];
            }
            // For non-winners, $currentBid remains null, which will display "-" in the view

            $winner = $participant->winner;

            return [
                'participant' => $participant,
                'current_bid' => $currentBid,
                'winner_info' => $winner,
                'has_won' => $participant->has_won,
                'won_date' => $winner ? $winner->created_at->format('d/m/Y') : '-'
            ];
        });

        return view('admin.groups.participants.manage', compact('group', 'participantsData', 'currentPeriod'));
    }

    public function toggleRegistration(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        $group->is_registration_active = $request->has('is_registration_active');
        $group->save();

        $status = $group->is_registration_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', 'Pendaftaran untuk kelompok ' . $group->name . ' berhasil ' . $status . '.');
    }

    public function approveParticipant(Request $request, $participantId)
    {
        $participant = Participant::findOrFail($participantId);

        $participant->registration_status = 'approved';
        $participant->is_active = true;
        
        // Ensure password is consistent with lottery_number (which was generated at registration)
        if ($participant->lottery_number) {
            $participant->password = Hash::make($participant->lottery_number);
        }
        
        $participant->save();

        return redirect()->back()->with('success', 'Peserta ' . $participant->name . ' berhasil disetujui.');
    }

    public function approveAllParticipants(Request $request, $groupId)
    {
        $pendingParticipants = Participant::where('group_id', $groupId)
            ->where('registration_status', 'pending')
            ->get();

        if ($pendingParticipants->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada pendaftaran pending untuk kelompok ini.');
        }

        $count = 0;
        foreach ($pendingParticipants as $participant) {
            $participant->registration_status = 'approved';
            $participant->is_active = true;
            
            if ($participant->lottery_number) {
                $participant->password = Hash::make($participant->lottery_number);
            }
            
            $participant->save();
            $count++;
        }

        return redirect()->back()->with('success', "Berhasil menyetujui {$count} pendaftaran peserta.");
    }

    public function exportGroupWinnersPdf($groupId)
    {
        $group = Group::with([
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'asc');
            },
            'monthlyPeriods.winners.participant',
            'participants' => function($query) {
                $query->where('is_active', true);
            },
            'monthlyPeriods.bids'
        ])->findOrFail($groupId);

        // Pre-calculate accumulation for the table
        $runningAccumulation = 0;
        $totalRows = 0; // Initialize row counter

        foreach ($group->monthlyPeriods as $period) {
            $pCount = $group->participants->where('is_active', true)->count();
            $pInstallment = $pCount * $group->monthly_installment;
            $pHighestBid = $period->bids->max('bid_amount') ?? 0;
            $pSurplus = ($pInstallment - ($group->shu ?? 500000)) + $pHighestBid - $group->main_prize;
            
            // Build accumulation
            $startBal = ($period->previous_cash_balance > 0) ? $period->previous_cash_balance : $runningAccumulation;
            $period->calculated_surplus = $pSurplus;
            $period->calculated_accumulation = $startBal + $pSurplus;
            
            $runningAccumulation = $period->calculated_accumulation;

            // Count rows for PDF height
            $winnerCount = $period->winners->count();
            $totalRows += ($winnerCount > 0) ? $winnerCount : 1;
        }

        // Calculate dynamic height
        // Base height (Header + Footer + Margins) approx 150pt
        // Row height approx 30pt
        $baseHeight = 180; 
        $rowHeight = 35;
        $calculatedHeight = $baseHeight + ($totalRows * $rowHeight);
        
        // Minimum height should be relevant (e.g. A4 Landscape height is ~595pt)
        // If content is small, we adjust to content. If content is large, we expand.
        // But to ensure it looks good, let's just set the height to the calculated one.
        // Width of A4 Landscape is 841.89pt
        
        $customPaper = [0, 0, 841.89, max($calculatedHeight, 400)];

        $pdf = Pdf::loadView('admin.groups.pdf.winners', compact('group'));
        $pdf->setPaper($customPaper);
        
        return $pdf->download('daftar-pemenang-' . Str::slug($group->name) . '.pdf');
    }

    public function manageCash($groupId)
    {
        $group = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true)->orderBy('lottery_number');
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'asc');
            },
            'monthlyPeriods.winners.participant',
            'monthlyPeriods.bids',
            'payments'
        ])->findOrFail($groupId);

        $monthlyData = [];

        // 1. Driving Force: Monthly Periods
        // Each period gets its own card (Unmixed)
        foreach ($group->monthlyPeriods as $period) {
            $monthKey = $period->period_start->format('Y-m');
            $customCash = \App\Models\CashFlow::where('monthly_period_id', $period->id)->first();
            
            // Unique key to separate periods even in the same month
            $cardKey = $monthKey . '_p' . $period->id;
            
            $monthlyData[$cardKey] = [
                'month_name' => $customCash ? $customCash->month_name : ($period->period_start->locale('id')->monthName . ' ' . $period->period_start->year),
                'month_key' => $monthKey,
                'period_id' => $period->id,
                'payment_count' => 0,
                'total_amount' => 0,
                'first_date' => $period->period_start->copy()->startOfMonth(),
                'last_date' => $period->period_start->copy()->endOfMonth(),
                'participant_count' => $group->participants->where('is_active', true)->count(),
                'winning_bid' => $period->bids->max('bid_amount') ?? 0,
                'net_surplus' => 0,
                'accumulation' => 0,
                'period_name' => $period->period_name,
                'winners' => $period->winners->map(fn($w) => $w->participant ? $w->participant->name : 'Unknown')->toArray(),
            ];
            
            // Associate payments linked specifically to this period
            $periodPayments = $group->payments->where('monthly_period_id', $period->id);
            $monthlyData[$cardKey]['payment_count'] = $periodPayments->count();
            $monthlyData[$cardKey]['total_amount'] = $periodPayments->sum('amount');
        }

        // 2. Fallback: Payments with NO period_id (Historical/Orphaned)
        foreach ($group->payments->where('monthly_period_id', null) as $payment) {
            $monthKey = $payment->payment_date->format('Y-m');
            if (!isset($monthlyData[$monthKey])) {
                $monthlyData[$monthKey] = [
                    'month_name' => $payment->payment_date->locale('id')->monthName . ' ' . $payment->payment_date->year,
                    'month_key' => $monthKey,
                    'period_id' => null,
                    'payment_count' => 0,
                    'total_amount' => 0,
                    'first_date' => $payment->payment_date->copy()->startOfMonth(),
                    'last_date' => $payment->payment_date->copy()->endOfMonth(),
                    'participant_count' => $group->participants->where('is_active', true)->count(),
                    'winning_bid' => 0,
                    'net_surplus' => 0,
                    'accumulation' => 0,
                    'period_name' => null,
                    'winners' => [],
                ];
            }
            $monthlyData[$monthKey]['payment_count']++;
            $monthlyData[$monthKey]['total_amount'] += $payment->amount;
        }

        // 3. Calculation Logic
        ksort($monthlyData); // Sort by key in ascending order (oldest first) for correct accumulation
        
        $runningAccumulation = 0;
        foreach ($monthlyData as $key => &$data) {
            $monthlyInstallment = $group->monthly_installment;
            $pCount = $data['participant_count'];
            $expectedTotalInstallment = $pCount * $monthlyInstallment;
            $prize = $group->main_prize;
            $shu = $group->shu ?? 500000;
            
            if ($data['period_id']) {
                $actualInstallment = $data['total_amount'] ?? 0;
                $period = $group->monthlyPeriods->find($data['period_id']);
                $winnerCount = count($data['winners']);
                
                // Get bids only from winners for this period
                $winnerBids = [];
                if ($winnerCount > 0 && $period) {
                    foreach ($period->winners as $winner) {
                        if ($winner->bid_amount > 0) {
                            $winnerBids[] = $winner->bid_amount;
                        }
                    }
                }
                $totalBids = array_sum($winnerBids);
                
                // 1. Rumus Inflow (Pemasukan Bersih Bulan Ini)
                // Dana Iuran Bersih = (Total Angsuran Masuk) - (jumlah pemenang x SHU)
                // UPDATED: Gunakan actual payment ($data['total_amount']) agar sesuai realita (0 jika belum ada yang bayar)
                $netFunds = ($data['total_amount']) - ($winnerCount * $shu);
                
                // 2. Total Bid = bid1 + bid2 (sum of all winner bids)
                $calTotalBidAmount = $totalBids;
                
                // 3. Rumus Outflow (Pengeluaran Hadiah)
                // Total Harga Motor = Jumlah Pemenang x Harga Satuan Motor
                $totalMotorPrice = $winnerCount * $prize;
                
                // 4. Rumus Logika Aliran Kas (Dana Saat Ini)
                // Dana Saat Ini = Dana Iuran Bersih + Total Bid
                $currentFund = $netFunds + $calTotalBidAmount;
                
                // Sisa Bersih Periode Ini = Dana Saat Ini - Total Harga Motor
                $surplus = $currentFund - $totalMotorPrice;
                $data['net_surplus'] = $surplus;
                
                // If the period has a stored previous balance (and it's not the first one or we want to trust it), 
                // we can use it. But for total consistency across cards, chaining is safer.
                // Trust stored balance if it's non-zero, otherwise use running
                $currentBase = ($period && $period->previous_cash_balance > 0) ? $period->previous_cash_balance : $runningAccumulation;
                
                $data['accumulation'] = $currentBase + $surplus;
                $runningAccumulation = $data['accumulation'];
            } else {
                // For orphan payments, just carry over the accumulation
                $data['accumulation'] = $runningAccumulation;
            }
        }
        unset($data); // Break reference

        krsort($monthlyData); // Sort by key in descending order (newest first) for display

        // Get unread payment notification count for this group
        $unreadPaymentCount = Payment::whereHas('participant', function($query) use ($group) {
                    $query->where('group_id', $group->id);
                })
                ->where('is_notification_read', false)
                ->where('is_confirmed', true)
                ->count();

        return view('admin.groups.cash.months', compact('group', 'monthlyData', 'unreadPaymentCount'));
    }

    public function cashMonthDetail(Request $request, $groupId, $monthKey)
    {
        $periodId = $request->query('period_id');

        $group = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true)->orderBy('lottery_number');
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'desc');
            },
            'monthlyPeriods.winners.participant',
            'payments' => function($query) use ($monthKey, $periodId) {
                if ($periodId) {
                    $query->where('monthly_period_id', $periodId);
                } else {
                    $query->whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$monthKey]);
                }
                $query->orderBy('payment_date', 'asc')
                      ->orderBy('installment_number', 'asc');
            },
            'payments.participant'
        ])->findOrFail($groupId);

        // Get month name for display
        $dateParts = explode('-', $monthKey);
        $monthDate = \Carbon\Carbon::createFromDate($dateParts[0], $dateParts[1], 1);
        $monthName = $monthDate->locale('id')->monthName . ' ' . $dateParts[0];

        // Find generating period
        $generatingPeriod = null;
        if ($periodId) {
            $generatingPeriod = $group->monthlyPeriods->firstWhere('id', $periodId);
        }
        
        // Mark all unread notifications for this group's payments as read when admin views cash detail
        Payment::whereHas('participant', function($query) use ($group) {
                    $query->where('group_id', $group->id);
                })
                ->where('is_notification_read', false)
                ->where('is_confirmed', true)
                ->update([
                    'is_notification_read' => true,
                    'notification_read_at' => now()
                ]);
        
        // Fallback: search by monthKey if not found or no periodId
        if (!$generatingPeriod) {
            $generatingPeriod = $group->monthlyPeriods->filter(function($p) use ($monthKey) {
                return $p->period_start->format('Y-m') === $monthKey;
            })->first();
        }

        $cashData = [];
        
        // Get all winners for this group to check participant win status
        $winners = Winner::with(['participant', 'monthlyPeriod'])
            ->whereHas('monthlyPeriod', function($query) use ($groupId) {
                $query->where('group_id', $groupId);
            })
            ->get()
            ->keyBy('participant_id');
        
        // Add payment records with correct installment numbers and winner info
        foreach ($group->payments as $payment) {
            $winnerInfo = $winners->get($payment->participant_id);
            $keterangan = $winnerInfo && $winnerInfo->draw_time
                ? 'Sudah menang di ' . $winnerInfo->monthlyPeriod->period_end->format('d/m/Y')
                : ($winnerInfo ? 'Sudah menang di ' . $winnerInfo->monthlyPeriod->period_end->format('d/m/Y') : '-');
                
            $cashData[] = [
                'participant' => $payment->participant,
                'period' => $payment->monthlyPeriod,
                'installment_count' => $payment->installment_number,
                'date' => $payment->payment_date->format('d/m/Y'),
                'notes' => 'Angsuran ke-' . $payment->installment_number . ' - ' . $payment->monthlyPeriod->period_name,
                'keterangan' => $keterangan,
                'amount' => $payment->amount,
                'payment' => $payment
            ];
        }

        // Sort by date and lottery number
        usort($cashData, function($a, $b) {
            if ($a['date'] === $b['date']) {
                return (int)$a['participant']->lottery_number - (int)$b['participant']->lottery_number;
            }
            return strtotime($a['date']) - strtotime($b['date']);
        });

        // Calculate actual values for all years (removed current year zero-out logic)
        $totalInstallments = collect($cashData)->sum('amount');
        $totalPrizesGiven = collect($cashData)->where('keterangan', '!=', '-')->count() > 0 
            ? collect($cashData)->where('keterangan', '!=', '-')->sum(function($item) use ($winners) {
                $winner = $winners->get($item['participant']->id);
                return $winner ? $winner->final_prize : 0;
            })
            : 0;
        $remainingCash = $totalInstallments - $totalPrizesGiven;
        
        // Calculate previous month remaining cash
        $previousMonthDate = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->subMonth();
        $previousMonthKey = $previousMonthDate->format('Y-m');
        
        $previousMonthInstallments = Payment::where('group_id', $groupId)
            ->whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$previousMonthKey])
            ->sum('amount');
            
        $previousMonthPrizes = Winner::whereHas('monthlyPeriod', function($query) use ($groupId) {
                $query->where('group_id', $groupId);
            })
            ->whereRaw("DATE_FORMAT(draw_time, '%Y-%m') = ?", [$previousMonthKey])
            ->sum('final_prize');
            
        $previousMonthRemainingCash = $previousMonthInstallments - $previousMonthPrizes;

        // Calculate accurate accumulation from all previous months (consistent with manageCash)
        $runningAccumulation = 0;
        $allPreviousPeriods = MonthlyPeriod::where('group_id', $groupId)
            ->where('period_start', '<', $monthDate->copy()->startOfMonth())
            ->orderBy('period_start', 'asc')
            ->get();
            
        foreach ($allPreviousPeriods as $prevP) {
            $prevPayments = Payment::where('monthly_period_id', $prevP->id)->sum('amount');
            $prevWinnerCount = $prevP->winners()->count();
            $prevTotalBids = $prevP->winners()->sum('bid_amount');
            $prevShu = $group->shu ?? 500000;
            $prevPrize = $group->main_prize;
            
            $prevNetFunds = $prevPayments - ($prevWinnerCount * $prevShu);
            $prevTotalMotorPrice = $prevWinnerCount * $prevPrize;
            $prevSurplus = ($prevNetFunds + $prevTotalBids) - $prevTotalMotorPrice;
            
            // Chaining logic as used in manageCash
            $startBal = ($prevP->previous_cash_balance > 0) ? $prevP->previous_cash_balance : $runningAccumulation;
            $runningAccumulation = $startBal + $prevSurplus;
        }
        $calPreviousCashBalanceFromController = $runningAccumulation;

        // Get winner details for summary
        $winnersInMonth = collect($cashData)
            ->where('keterangan', '!=', '-')
            ->map(function($item) use ($winners) {
                $winner = $winners->get($item['participant']->id);
                return [
                    'participant_name' => $item['participant']->name,
                    'lottery_number' => $item['participant']->lottery_number,
                    'keterangan' => $item['keterangan']
                ];
            });

        // Calculate total installment if all participants paid
        $totalPotentialInstallment = $group->participants->where('is_active', true)->count() * $group->monthly_installment;

        // Calculate additional data for summary statistics
        $groupCreationDate = $group->created_at->format('d/m/Y');
        
        // Count total winners before current month
        $totalPreviousWinners = Winner::whereHas('monthlyPeriod', function($query) use ($groupId) {
                $query->where('group_id', $groupId);
            })
            ->whereRaw("DATE_FORMAT(draw_time, '%Y-%m') < ?", [$monthKey])
            ->count();

        // Calculate next installment numbers for bulk modal
        $nextInstallments = [];
        foreach ($group->participants as $participant) {
            $lastInstallment = Payment::where('participant_id', $participant->id)
                ->max('installment_number') ?? 0;
            $nextInstallments[$participant->id] = $lastInstallment + 1;
        }

        // Calculate actual number of participants who have paid for this month
        $paidParticipantCount = 0;
        if ($periodId) {
            $paidParticipantCount = Payment::where('monthly_period_id', $periodId)
                                        ->where('is_confirmed', true)
                                        ->distinct('participant_id')
                                        ->count('participant_id');
        } else {
            // For historical payments without period_id
            $paidParticipantCount = Payment::whereHas('participant', function($query) use ($group) {
                        $query->where('group_id', $group->id);
                    })
                    ->whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$monthKey])
                    ->where('is_confirmed', true)
                    ->distinct('participant_id')
                    ->count('participant_id');
        }

        // Calculate highest bid for the generating period if it exists
        $highestBid = 0;
        $cashFlowName = null;
        if ($generatingPeriod) {
            $highestBid = $generatingPeriod->bids()->max('bid_amount') ?? 0;
            
            // Get cash flow name for this period
            $cashFlow = \App\Models\CashFlow::where('monthly_period_id', $generatingPeriod->id)->first();
            $cashFlowName = $cashFlow ? $cashFlow->month_name : $monthName;
        } else {
            $cashFlowName = $monthName;
        }

        return view('admin.groups.cash.month-detail', compact('group', 'cashData', 'monthName', 'monthKey', 'totalInstallments', 'totalPrizesGiven', 'remainingCash', 'nextInstallments', 'winnersInMonth', 'totalPotentialInstallment', 'groupCreationDate', 'previousMonthRemainingCash', 'totalPreviousWinners', 'generatingPeriod', 'highestBid', 'cashFlowName', 'paidParticipantCount', 'calPreviousCashBalanceFromController'));
    }

    public function exportCashMonthPdf(Request $request, $groupId, $monthKey)
    {
        $periodId = $request->query('period_id');

        // Reuse the same data as cashMonthDetail
        $group = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true)->orderBy('lottery_number');
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'desc');
            },
            'monthlyPeriods.winners.participant',
            'payments' => function($query) use ($monthKey, $periodId) {
                if ($periodId) {
                    $query->where('monthly_period_id', $periodId);
                } else {
                    $query->whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$monthKey]);
                }
                $query->orderBy('payment_date', 'desc')
                      ->orderBy('installment_number', 'desc');
            },
            'payments.participant'
        ])->findOrFail($groupId);

        // Get month name for display
        $dateParts = explode('-', $monthKey);
        $monthDate = \Carbon\Carbon::createFromDate($dateParts[0], $dateParts[1], 1);
        $monthName = $monthDate->locale('id')->monthName . ' ' . $dateParts[0];

        // Find generating period (copy logic from cashMonthDetail)
        $generatingPeriod = null;
        if ($periodId) {
            $generatingPeriod = $group->monthlyPeriods->firstWhere('id', $periodId);
        }
        
        // Fallback: search by monthKey if not found or no periodId
        if (!$generatingPeriod) {
            $generatingPeriod = $group->monthlyPeriods->filter(function($p) use ($monthKey) {
                return $p->period_start->format('Y-m') === $monthKey;
            })->first();
        }

        $cashData = [];

        // Get all winners for this group to check participant win status
        $winners = Winner::with(['participant', 'monthlyPeriod'])
            ->whereHas('monthlyPeriod', function($query) use ($groupId) {
                $query->where('group_id', $groupId);
            })
            ->get()
            ->keyBy('participant_id');

        // Add payment records with correct installment numbers and winner info
        foreach ($group->payments as $payment) {
            $winnerInfo = $winners->get($payment->participant_id);
            $keterangan = $winnerInfo && $winnerInfo->draw_time
                ? 'Sudah menang di ' . $winnerInfo->monthlyPeriod->period_end->format('d/m/Y')
                : ($winnerInfo ? 'Sudah menang di ' . $winnerInfo->monthlyPeriod->period_end->format('d/m/Y') : '-');

            $cashData[] = [
                'participant' => $payment->participant,
                'period' => $payment->monthlyPeriod,
                'installment_count' => $payment->installment_number,
                'date' => $payment->payment_date->format('d/m/Y'),
                'notes' => 'Angsuran ke-' . $payment->installment_number . ' - ' . $payment->monthlyPeriod->period_name,
                'keterangan' => $keterangan,
                'amount' => $payment->amount,
                'payment' => $payment
            ];
        }

        // Sort by date and lottery number
        usort($cashData, function($a, $b) {
            if ($a['date'] === $b['date']) {
                // Handle null participants gracefully if needed, though they should exist
                $lotteryA = $a['participant'] ? (int)$a['participant']->lottery_number : 0;
                $lotteryB = $b['participant'] ? (int)$b['participant']->lottery_number : 0;
                return $lotteryA - $lotteryB;
            }
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // -------------------------------------------------------------
        // CALCULATIONS (Synchronized with Web View Logic - POTENTIAL BASIS)
        // -------------------------------------------------------------
        
        // 0. Calculate realized installments (needed for PDF view footer)
        $totalInstallments = collect($cashData)->sum('amount');

        // 1. Context Values
        $participantCount = $group->participants->where('is_active', true)->count();
        $monthlyInstallment = $group->monthly_installment;
        $potentialInstallment = $participantCount * $monthlyInstallment;
        $mainPrize = $group->main_prize;
        $shuAmount = $group->shu ?? 500000;
        
        // 2. Determine base values from generating period or fallback
        $contextPeriod = $generatingPeriod; 
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
            $highestBid = !empty($winnerBids) ? max($winnerBids) : 0;
            
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
                    // Use actual payments for historical periods
                    $pActualInstallments = Payment::where('monthly_period_id', $p->id)->sum('amount');
                    $pHighestBid = $p->bids->max('bid_amount') ?? 0;
                    $pWinnerCount = $p->winners->count();
                    $pTotalShu = $pWinnerCount * $shuAmount;
                    $pTotalPrize = $pWinnerCount * $mainPrize;
                    
                    // Calculate surplus based on actual payments
                    $pSurplus = ($pActualInstallments + $pHighestBid) - ($pTotalPrize + $pTotalShu);
                    
                    $startBal = ($p->previous_cash_balance > 0) ? $p->previous_cash_balance : $running;
                    $running = $startBal + $pSurplus;
                }
                $previousCashBalance = $running;
            }
        }
        
        // 3. Logic: Dana Iuran Bersih (Net Funds)
        // Use ACTUAL installments, not potential
        $totalShuDeduction = ($winnerCount > 0) ? ($winnerCount * $shuAmount) : 0;
        $netFunds = ($totalInstallments > 0) ? ($totalInstallments - $totalShuDeduction) : 0;
        
        // 4. Logic: Dana Saat Ini (Current Available Funds)
        // Net Funds + Total Bids
        $currentFund = ($totalInstallments > 0) ? ($netFunds + $totalBids) : 0;
        
        // 5. Logic: Outflow (Total Harga Motor)
        // Total winners * Main Prize
        $totalMotorPrice = $winnerCount * $mainPrize; 
        
        // 6. Logic: Sisa Bersih Periode Ini (Surplus)
        // Only calculate if there are actual installments
        $surplus = ($totalInstallments > 0) ? ($currentFund - $totalMotorPrice) : 0;
        
        // 7. Logic: Total Kas Berjalan (Accumulation)
        // Only add surplus if there are actual installments
        $remainingCash = ($totalInstallments > 0) ? ($previousCashBalance + $surplus) : $previousCashBalance;
        
        // Vars for view compatibility
        $totalPrizesGiven = $totalMotorPrice; // In web view context "Total Harga Motor"
        
        // Calculation variables for view
        $calNetFunds = $netFunds;
        $calCurrentFund = $currentFund;
        $calTotalMotorPrice = $totalMotorPrice;
        $calTotalRunningCash = $remainingCash;
        $calAccumulation = $remainingCash;
        $calTotalBidAmount = $totalBids;
        
        // Get winners dict - ONLY for this period context
        $winnersInMonth = collect([]);
        
        if ($contextPeriod && $contextPeriod->winners->count() > 0) {
            $winnersInMonth = $contextPeriod->winners->map(function($winner) {
                return [
                    'participant_name' => $winner->participant->name,
                    'lottery_number' => $winner->participant->lottery_number,
                    'keterangan' => 'Menang Periode Ini'
                ];
            });
        } else {
            // Fallback (only if no context period defined, specific to date view)
            $winnersInMonth = collect($cashData)
                ->where('keterangan', '!=', '-')
                ->map(function($item) {
                    return [
                        'participant_name' => $item['participant']->name,
                        'lottery_number' => $item['participant']->lottery_number,
                        'keterangan' => $item['keterangan']
                    ];
                });
        }

        $groupCreationDate = $group->created_at->format('d/m/Y');

        $pdf = Pdf::loadView('admin.groups.cash.export-pdf', compact(
            'group', 'cashData', 'monthName', 'monthKey', 
            'totalInstallments', 
            'potentialInstallment', // Added this
            'totalPrizesGiven', 
            'remainingCash', 
            'winnersInMonth', 
            'groupCreationDate',
            'previousCashBalance',
            'netFunds',
            'highestBid',
            'totalBids', // Added this
            'currentFund',
            'surplus',
            'winnerCount',
            'generatingPeriod',
            'participantCount',
            'monthlyInstallment',
            'shuAmount',
            'totalShuDeduction'
        ));
        
        return $pdf->download('laporan-kas-' . $group->name . '-' . $monthKey . '.pdf');
    }

    public function deleteCashMonth(Request $request, $groupId, $monthKey)
    {
        try {
            $requestMonthKey = (string) $monthKey;
            $periodId = $request->query('period_id');
            
            if (!preg_match('/^\d{4}-\d{2}$/', $requestMonthKey)) {
                return redirect()->back()->with('error', 'Format bulan tidak valid. Gunakan format YYYY-MM.');
            }

            $group = Group::findOrFail($groupId);
            $deletedMessages = [];

            // Wrap in transaction for safety
            \DB::transaction(function () use ($groupId, $requestMonthKey, $periodId, &$deletedMessages) {
                // 1. Identify and Reset Winner Status for Participants
                $periodsToReset = $periodId 
                    ? \App\Models\MonthlyPeriod::where('group_id', $groupId)->where('id', $periodId)->get()
                    : \App\Models\MonthlyPeriod::where('group_id', $groupId)
                        ->whereRaw("DATE_FORMAT(period_start, '%Y-%m') = ?", [$requestMonthKey])
                        ->get();

                foreach ($periodsToReset as $period) {
                    $winnerParticipantIds = $period->winners()->pluck('participant_id')->toArray();
                    if (!empty($winnerParticipantIds)) {
                        \App\Models\Participant::whereIn('id', $winnerParticipantIds)->update([
                            'has_won' => false,
                            'won_at' => null
                        ]);
                    }
                }

                // 2. Delete by Period ID if provided (PRECISE)
                if ($periodId) {
                    $period = \App\Models\MonthlyPeriod::where('group_id', $groupId)->find($periodId);
                    if ($period) {
                        // Cascade will handle Winners and Payments linked to this ID
                        $period->delete();
                        $deletedMessages[] = "periode dan datanya";
                    }
                } else {
                    // 3. Fallback: Search by Month Key (for blocks without period_id)
                    // Delete Monthly Periods starting in this month
                    $deletedPeriodsCount = \App\Models\MonthlyPeriod::where('group_id', $groupId)
                        ->whereRaw("DATE_FORMAT(period_start, '%Y-%m') = ?", [$requestMonthKey])
                        ->delete();
                    
                    if ($deletedPeriodsCount > 0) {
                        $deletedMessages[] = "{$deletedPeriodsCount} periode";
                    }
                }

                // 3. Delete leftover Payments in this month (not linked to a period, or if matching month key)
                $deletedPayments = Payment::where('group_id', $groupId)
                    ->whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$requestMonthKey])
                    ->delete();
                
                if ($deletedPayments > 0) {
                    $deletedMessages[] = "{$deletedPayments} transaksi";
                }

                // 4. Delete CashFlow Records
                $deletedCashFlows = \App\Models\CashFlow::where('group_id', $groupId)
                    ->where('month_key', $requestMonthKey)
                    ->delete();
                    
                if ($deletedCashFlows > 0) {
                    $deletedMessages[] = "data cash flow";
                }
            });

            if (empty($deletedMessages)) {
                return redirect()->route('admin.groups.cash.manage', $groupId)
                    ->with('warning', 'Tidak ada data pada bulan tersebut untuk dihapus.');
            }

            $message = "Berhasil menghapus: " . implode(', ', $deletedMessages) . " untuk bulan {$requestMonthKey}.";

            return redirect()->route('admin.groups.cash.manage', $groupId)
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // createCashMonth method removed - cash should only be created when creating periods, not automatically for groups

    public function addInstallment(Request $request, $groupId, $monthKey)
    {
        try {
            $validated = $request->validate([
                'participant_id' => 'required|exists:participants,id',
                'monthly_period_id' => 'required|exists:monthly_periods,id',
                'installment_number' => 'nullable|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'notes' => 'nullable|string'
            ]);

            $group = Group::with([
                'participants' => function($query) {
                    $query->where('is_active', true);
                }
            ])->findOrFail($groupId);

            $participant = $group->participants->where('id', $validated['participant_id'])->first();
            
            if (!$participant) {
                return redirect()->back()->with('error', 'Peserta tidak ditemukan dalam kelompok ini.');
            }

            // Always calculate installment number on server (authoritative)
            $lastInstallmentNumber = Payment::where('participant_id', $participant->id)
                ->max('installment_number');
            $validated['installment_number'] = $lastInstallmentNumber ? $lastInstallmentNumber + 1 : 1;

            // Create payment record
            $payment = Payment::create([
                'participant_id' => $participant->id,
                'monthly_period_id' => $validated['monthly_period_id'],
                'group_id' => $groupId,
                'amount' => $validated['amount'],
                'installment_number' => $validated['installment_number'],
                'payment_date' => \Carbon\Carbon::createFromFormat('Y-m-d', $validated['payment_date']),
                'payment_method' => 'potongan_gaji',
                'notes' => $validated['notes'] ?? 'Angsuran ke-' . $validated['installment_number'] . ' untuk bulan ' . \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->locale('id')->monthName,
                'is_confirmed' => true
            ]);

            // Check if request is AJAX or wants JSON
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil menambahkan angsuran untuk ' . $participant->name . ' (Angsuran ke-' . $validated['installment_number'] . ')'
                ]);
            }

            return redirect()->route('admin.groups.cash.month.detail', ['groupId' => $groupId, 'monthKey' => $monthKey])
                ->with('success', 'Berhasil menambahkan angsuran untuk ' . $participant->name . ' (Angsuran ke-' . $validated['installment_number'] . ')');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function processAuction($groupId)
    {
        $group = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true);
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'desc');
            }
        ])->findOrFail($groupId);

        $periodWithBids = null;
        $cashMonthUsed = null;
        $cashMonthUsedName = null;
        $previousMonthRemainingCash = null;
        $monthInstallments = 0;
        
        // Check if a specific period is selected
        if (request()->has('period_id') && request('period_id')) {
            $periodWithBids = MonthlyPeriod::with([
                'group',
                'bids' => function($query) {
                    $query->whereHas('participant', function($q) {
                        $q->where('is_active', true)->where('has_won', false);
                    })->where('bid_amount', '>', 0)->with('participant')->orderBy('bid_amount', 'desc');
                },
                'winners.participant'
            ])->findOrFail(request('period_id'));
        } else {
            // Default to current period (first non-completed period)
            $currentPeriod = $group->monthlyPeriods->where('status', '!=', 'completed')->first();
            if ($currentPeriod) {
                $periodWithBids = MonthlyPeriod::with([
                    'group',
                    'bids' => function($query) {
                        $query->whereHas('participant', function($q) {
                            $q->where('is_active', true)->where('has_won', false);
                        })->where('bid_amount', '>', 0)->with('participant')->orderBy('bid_amount', 'desc');
                    },
                    'winners.participant'
                ])->findOrFail($currentPeriod->id);
            }
        }

        if ($periodWithBids && !empty($periodWithBids->period_start)) {
            $monthStart = $periodWithBids->period_start->copy()->startOfMonth();
            $cashMonth = $monthStart->copy()->subMonthNoOverflow();
            $cashMonthUsed = $cashMonth->format('Y-m');
            $cashMonthUsedName = $cashMonth->locale('id')->monthName . ' ' . $cashMonth->year;

            $cashMonthEnd = $cashMonth->copy()->endOfMonth();

            // Calculate projected installment (same as Target Setoran in cash month-detail)
            $participantCount = $group->participants->where('is_active', true)->count();
            $monthlyInstallment = $group->monthly_installment;
            $monthInstallments = $participantCount * $monthlyInstallment;

            $monthPrizes = Winner::whereHas('monthlyPeriod', function ($query) use ($groupId) {
                    $query->where('group_id', $groupId);
                })
                ->whereNotNull('draw_time')
                ->whereDate('draw_time', '<=', $cashMonthEnd)
                ->sum('final_prize');

            $previousMonthRemainingCash = $monthInstallments - $monthPrizes;
        }

        return view('admin.groups.auction.process', compact('group', 'periodWithBids', 'cashMonthUsed', 'cashMonthUsedName', 'previousMonthRemainingCash', 'monthInstallments'));
    }

    public function exportParticipants($groupId)
    {
        $group = Group::findOrFail($groupId);
        $fileName = 'peserta_' . $group->name . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new ParticipantsExport($groupId), $fileName);
    }

    public function generateReceipt($paymentId)
    {
        $payment = Payment::with([
            'participant',
            'monthlyPeriod.winners.participant', // Eager load winners
            'monthlyPeriod.bids', // Eager load bids for calculation
            'group.participants',
            'confirmedBy'
        ])->findOrFail($paymentId);

        $group = $payment->group;
        $contextPeriod = $payment->monthlyPeriod;

        // -------------------------------------------------------------
        // CALCULATIONS (Synchronized with Export Logic - POTENTIAL BASIS)
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
                 // Fallback calculation logic (Re-calculate history if needed)
                 // NOTE: Using a simplified reliable query for previous balance
                 $allPrevPeriods = \App\Models\MonthlyPeriod::where('group_id', $group->id)
                    ->where('period_start', '<', $contextPeriod->period_start)
                    ->orderBy('period_start', 'asc')
                    ->get();
                
                $running = 0;
                foreach ($allPrevPeriods as $p) {
                    $pCount = $group->participants->where('is_active', true)->count(); 
                    $pInstallment = $pCount * $monthlyInstallment;
                    $pHighestBid = $p->bids->max('bid_amount') ?? 0;
                    $pWinnerCount = $p->winners->count(); // Use actual winner count from db
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
        $remainingCash = $previousCashBalance + $surplus;

        // -------------------------------------------------------------
        // Additional Info for Receipt View
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

        return view('admin.groups.cash.receipt', compact(
            'payment', 
            'previousWinnersCount', 
            'currentPeriodWinnersCount',
            'lastAuctionDate', 
            'remainingCash',
            'group',
            'admin_signature'
        ));
    }

    public function printAllReceipts($groupId, $monthKey)
    {
        $group = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true);
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'desc');
            }
        ])->findOrFail($groupId);

        // Parse the monthKey to get year and month
        if (preg_match('/^\d{4}-\d{2}$/', $monthKey)) {
            $dateParts = explode('-', $monthKey);
            $year = $dateParts[0];
            $month = $dateParts[1];
        } elseif (preg_match('/^\d{6}$/', $monthKey)) {
            $year = substr($monthKey, 0, 4);
            $month = substr($monthKey, 4, 2);
        } else {
            return redirect()->back()->with('error', 'Format month key tidak valid.');
        }

        $period = MonthlyPeriod::where('group_id', $groupId)
            ->whereYear('period_start', (int)$year)
            ->whereMonth('period_start', (int)$month)
            ->first();

        if (!$period) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan.');
        }

        $payments = Payment::with(['participant', 'monthlyPeriod'])
            ->where('monthly_period_id', $period->id)
            ->orderBy('receipt_number')
            ->get();

        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pembayaran untuk periode ini.');
        }

        // Logic from generateReceipt
        $contextPeriod = $period;
        $participantCount = $group->participants->where('is_active', true)->count();
        $monthlyInstallment = $group->monthly_installment;
        $potentialInstallment = $participantCount * $monthlyInstallment;
        $mainPrize = $group->main_prize;
        $shuAmount = $group->shu ?? 500000;
        
        $winnerCount = $contextPeriod->winners->count();
        $winnerBids = [];
        foreach ($contextPeriod->winners as $winner) {
            if ($winner->bid_amount > 0) {
                $winnerBids[] = $winner->bid_amount;
            }
        }
        $totalBids = array_sum($winnerBids);
        
        $previousCashBalance = $contextPeriod->previous_cash_balance ?? 0;
        if ($previousCashBalance == 0) {
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
        
        $totalShuDeduction = ($winnerCount > 0) ? ($winnerCount * $shuAmount) : 0;
        $netFunds = $potentialInstallment - $totalShuDeduction;
        $currentFund = $netFunds + $totalBids;
        $totalMotorPrice = $winnerCount * $mainPrize; 
        $surplus = $currentFund - $totalMotorPrice;
        $remainingCash = $previousCashBalance + $surplus;

        $previousWinnersCount = \App\Models\Winner::whereHas('monthlyPeriod', function($query) use ($group, $contextPeriod) {
            $query->where('group_id', $group->id)
                  ->where('period_start', '<', $contextPeriod->period_start);
        })->count();

        $currentPeriodWinnersCount = $winnerCount;
        $lastAuctionDate = $contextPeriod->period_end; 

        $admin_signature = \App\Models\Management::where('nama_lengkap', 'Arbi Muhtarom')
                                               ->orWhere('jabatan', 'Admin')
                                               ->first() ?? \App\Models\Management::find(3);

        try {
            $pdf = Pdf::loadView('admin.groups.cash.all-receipts', compact(
                'group', 'period', 'payments', 
                'previousWinnersCount', 'currentPeriodWinnersCount', 
                'lastAuctionDate', 'remainingCash', 'admin_signature'
            ));

            $pdf->setPaper('a4', 'portrait');
            $fileName = 'semua_bukti_' . str_replace(' ', '_', $group->name) . '_' . $monthKey . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    public function bulkInstallmentProcess(Request $request, $groupId)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
            'monthKey' => 'required|string',
            'monthly_period_id' => 'required|exists:monthly_periods,id',
            'create_next_months' => 'nullable|integer|min:0|max:12'
        ]);

        $monthKey = $validated['monthKey'];
        $periodId = $validated['monthly_period_id'];

        $group = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true);
            },
            'monthlyPeriods' => function($query) {
                $query->orderBy('period_start', 'asc');
            }
        ])->findOrFail($groupId);

        $currentPeriod = $group->monthlyPeriods->find($periodId);

        if (!$currentPeriod) {
            return redirect()->back()->with('error', 'Periode yang dipilih tidak ditemukan');
        }

        $createNextMonths = $validated['create_next_months'] ?? 0;

        $createdMonths = [];

        // Process each month to create
        for ($i = 0; $i <= $createNextMonths; $i++) {
            // Parse the initial month and year from the provided monthKey
            $dateParts = explode('-', $monthKey);
            $targetMonth = (int)$dateParts[1] + $i;
            $targetYear = (int)$dateParts[0];

            // Handle year overflow
            if ($targetMonth > 12) {
                $targetYear += floor(($targetMonth - 1) / 12);
                $targetMonth = (($targetMonth - 1) % 12) + 1;
            }

            $currentMonthKey = $targetYear . '-' . str_pad($targetMonth, 2, '0', STR_PAD_LEFT);
            
            // For future months, we need to find the appropriate period
            $activePeriod = $currentPeriod;
            if ($i > 0) {
                $activePeriod = $group->monthlyPeriods->filter(function($p) use ($currentMonthKey) {
                    return $p->period_start->format('Y-m') === $currentMonthKey;
                })->first();
                
                // If no period exists for a future month in the bulk request, skip it
                if (!$activePeriod) continue;
            }

            // Calculate payment date for each month
            $paymentDate = \Carbon\Carbon::createFromDate($targetYear, $targetMonth, 1);
            if ($i > 0) {
                $paymentDate->startOfMonth();
            } else {
                $paymentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['payment_date']);
            }

            $eligibleParticipants = [];
            $paymentsSkippedCount = 0;
            
            foreach ($group->participants as $participant) {
                // Check if payment already exists for this month AND period
                $existingPayment = Payment::where('participant_id', $participant->id)
                    ->where('monthly_period_id', $activePeriod->id)
                    ->first();
                
                if ($existingPayment) {
                    $paymentsSkippedCount++;
                } else {
                    $eligibleParticipants[] = $participant;
                }
            }
            
            if (empty($eligibleParticipants)) {
                return redirect()->back()->with('error', 'Tidak ada peserta yang bisa diproses untuk pembayaran bulanan. Semua peserta aktif sudah memiliki pembayaran untuk periode ini.');
            }

            $paymentsCreated = [];
            $paymentsSkippedCount = 0;

            // Process each participant for this month
            foreach ($eligibleParticipants as $participant) {
                // Calculate individual participant's next installment number
                $participantInstallmentNumber = Payment::where('participant_id', $participant->id)
                    ->max('installment_number') + 1;

                // Create payment record
                $payment = Payment::create([
                    'participant_id' => $participant->id,
                    'monthly_period_id' => $activePeriod->id,
                    'group_id' => $groupId,
                    'amount' => $group->monthly_installment,
                    'installment_number' => $participantInstallmentNumber,
                    'payment_date' => $paymentDate,
                    'payment_method' => 'potongan_gaji',
                    'notes' => $validated['notes'] ?? 'Pembayaran angsuran bulanan melalui potongan gaji',
                    'is_confirmed' => true
                ]);

                $paymentsCreated[] = [
                    'participant' => $participant->name,
                    'receipt_number' => $payment->receipt_number,
                    'amount' => $payment->amount
                ];
            }

            $monthName = \Carbon\Carbon::createFromFormat('Y-m', $currentMonthKey)->locale('id')->monthName . ' ' . $targetYear;

            if (count($paymentsCreated) > 0) {
                $createdMonths[] = [
                    'month_name' => $monthName,
                    'created_count' => count($paymentsCreated),
                    'skipped_count' => $paymentsSkippedCount
                ];
            }
        }

        if (empty($createdMonths)) {
            $message = 'Tidak ada angsuran yang dibuat. Semua bulan yang dipilih sudah memiliki pembayaran.';
        } else {
            $monthDetails = '';
            foreach ($createdMonths as $month) {
                $monthDetails .= $month['month_name'] . ' (' . $month['created_count'] . ' peserta), ';
            }
            $monthDetails = rtrim($monthDetails, ', ');

            if ($createNextMonths > 0) {
                $message = "Berhasil membuat angsuran untuk " . ($createNextMonths + 1) . " bulan: {$monthDetails}.";
            } else {
                $message = "Berhasil membuat angsuran {$createdMonths[0]['month_name']} untuk {$createdMonths[0]['created_count']} peserta.";
            }
        }

        // Check if request is AJAX or wants JSON
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        // Redirect back to the cash month detail page with success message
        return redirect()->route('admin.groups.cash.month.detail', ['groupId' => $groupId, 'monthKey' => $monthKey])
            ->with('success', $message);
    }

    public function manualBidInput($groupId, $periodId)
    {
        $group = Group::with([
            'participants' => function($query) {
                $query->where('is_active', true)->orderBy('lottery_number');
            }
        ])->findOrFail($groupId);

        $period = MonthlyPeriod::with([
            'bids.participant',
            'group'
        ])->findOrFail($periodId);

        $cashMonthUsed = null;
        $cashMonthUsedName = null;
        $previousMonthRemainingCash = null;

        if (!empty($period->period_start)) {
            $monthStart = $period->period_start->copy()->startOfMonth();
            $cashMonth = $monthStart->copy()->subMonthNoOverflow();
            $cashMonthUsed = $cashMonth->format('Y-m');
            $cashMonthUsedName = $cashMonth->locale('id')->monthName . ' ' . $cashMonth->year;

            $cashMonthEnd = $cashMonth->copy()->endOfMonth();

            $monthInstallments = Payment::where('group_id', $groupId)
                ->whereDate('payment_date', '<=', $cashMonthEnd)
                ->sum('amount');

            $monthPrizes = Winner::whereHas('monthlyPeriod', function ($query) use ($groupId) {
                    $query->where('group_id', $groupId);
                })
                ->whereNotNull('draw_time')
                ->whereDate('draw_time', '<=', $cashMonthEnd)
                ->sum('final_prize');

            $previousMonthRemainingCash = $monthInstallments - $monthPrizes;
        }

        // Get existing bids for this period
        $existingBids = $period->bids->keyBy('participant_id');

        // Filter participants to show only those who haven't placed bids yet AND haven't won yet
        $participantsWithoutBids = $group->participants->filter(function($participant) use ($existingBids) {
            return !$existingBids->has($participant->id) && !$participant->has_won;
        });

        // Prepare participants data for manual bid input (only those without bids)
        $participantsWithBids = $participantsWithoutBids->map(function($participant) use ($existingBids) {
            return [
                'participant' => $participant,
                'existing_bid' => null,
                'has_bid' => false,
                'bid_amount' => null
            ];
        });

        return view('admin.groups.auction.manual-bid', compact('group', 'period', 'participantsWithBids', 'cashMonthUsed', 'cashMonthUsedName', 'previousMonthRemainingCash'));
    }

    public function storeManualBid(Request $request, $groupId, $periodId)
    {
        $validated = $request->validate([
            'bids' => 'required|array',
            'bids.*' => 'nullable|numeric|min:0|max:10000000'
        ]);

        $group = Group::findOrFail($groupId);
        $period = MonthlyPeriod::findOrFail($periodId);

        $bidsCreated = 0;
        $bidsUpdated = 0;

        foreach ($validated['bids'] as $participantId => $bidAmount) {
                if ($bidAmount > 0) {
                    $participant = Participant::find($participantId);
                    if ($participant && $participant->has_won) {
                        continue; // Skip winners
                    }
                    
                    // Check if bid already exists
                    $existingBid = Bid::where('monthly_period_id', $periodId)
                        ->where('participant_id', $participantId)
                        ->first();

                    if ($existingBid) {
                        // Update existing bid
                        $existingBid->update([
                            'bid_amount' => $bidAmount,
                            'bid_time' => now(),
                            'status' => 'submitted'
                        ]);
                        $bidsUpdated++;
                    } else {
                        // Create new bid
                        Bid::create([
                            'monthly_period_id' => $periodId,
                            'participant_id' => $participantId,
                            'bid_amount' => $bidAmount,
                            'bid_time' => now(),
                            'status' => 'submitted'
                        ]);
                        $bidsCreated++;
                    }
                }
        }

        // Update period status to bidding if there are bids
        if ($period->bids()->count() > 0 && $period->status === 'active') {
            $period->update(['status' => 'bidding']);
        }

        return redirect()->route('admin.groups.auction.process', [$groupId, 'period_id' => $periodId])
            ->with('success', "Berhasil memproses bid manual: {$bidsCreated} bid baru, {$bidsUpdated} bid diperbarui.");
    }

    public function showBidDetail($bidId)
    {
        $bid = Bid::with(['participant', 'monthlyPeriod.group'])->findOrFail($bidId);
        
        return view('admin.bids.show', compact('bid'));
    }
    // untuk download bukti lelang permanen

    public function downloadBidProof($bidId)
    {
        $bid = Bid::with(['monthlyPeriod.group', 'participant'])->findOrFail($bidId);

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
            "Nama Peserta   : " . $bid->participant->name,
            "NIK            : " . ($bid->participant->nik ?? '-'),
            "No. Undian     : " . $bid->participant->lottery_number,
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
        $footer = "Dicetak pada: " . now()->format('d-m-Y H:i:s') . " (Admin View)";
        imagestring($image, 3, 100, $height - 60, $footer, $gray);
        
        $watermark = "SISTEM ARISAN PRIMATEXCO";
        imagestring($image, 3, $width - 250, $height - 60, $watermark, $blue);

        // Output
        header('Content-Type: image/png');
        // header('Content-Disposition: attachment; filename="bukti_lelang_' . $bid->participant->lottery_number . '_' . now()->format('YmdHis') . '.png"'); 
        // We want to display it, not force download necessarily, but usually usually acts as file response.
        // If used in <img> src, it will display.
        imagepng($image);
        imagedestroy($image);
        exit;
    }

    public function editBid($bidId)
    {
        $bid = Bid::with(['participant', 'monthlyPeriod.group'])->findOrFail($bidId);
        
        // Only allow editing if period is not completed
        if ($bid->monthlyPeriod->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Tidak dapat mengedit bid pada periode yang sudah selesai.');
        }
        
        return view('admin.bids.edit', compact('bid'));
    }

    public function updateBid(Request $request, $bidId)
    {
        $bid = Bid::with('monthlyPeriod')->findOrFail($bidId);
        
        // Only allow updating if period is not completed
        if ($bid->monthlyPeriod->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Tidak dapat mengubah bid pada periode yang sudah selesai.');
        }

        if ($bid->is_permanent) {
            return redirect()->back()
                ->with('error', 'Lelang ini sudah bersifat permanen dan tidak dapat diubah oleh admin sekalipun.');
        }

        $request->validate([
            'bid_amount' => 'required|numeric|min:0',
            'status' => 'required|in:submitted,accepted'
        ]);

        $bid->update([
            'bid_amount' => $request->bid_amount,
            'status' => $request->status
        ]);

        return redirect()->back()
            ->with('success', 'Bid berhasil diperbarui.');
    }

    public function deleteBid($bidId)
    {
        $bid = Bid::with('monthlyPeriod')->findOrFail($bidId);
        
        // Only allow deleting if period is not completed
        if ($bid->monthlyPeriod->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus bid pada periode yang sudah selesai.');
        }

        if ($bid->is_permanent) {
            return redirect()->back()
                ->with('error', 'Lelang ini sudah bersifat permanen dan tidak dapat dihapus.');
        }

        $bid->delete();

        return redirect()->back()
            ->with('success', 'Bid berhasil dihapus.');
    }

    public function showParticipantDetail($participantId)
    {
        $participant = Participant::with([
            'group',
            'bids.monthlyPeriod',
            'winner.monthlyPeriod',
            'bids' => function($query) {
                $query->orderBy('bid_time', 'desc');
            }
        ])->findOrFail($participantId);

        return view('admin.participants.show', compact('participant'));
    }

    public function resetParticipantPassword($participantId)
    {
        $participant = Participant::findOrFail($participantId);
        
        // Find all linked accounts with same NIK
        $linkedAccounts = Participant::where('nik', $participant->nik)
            ->where('id', '!=', $participant->id)
            ->get();
        
        $linkedCount = $linkedAccounts->count();
        
        // Reset current participant password
        $participant->update([
            'password' => $participant->lottery_number,
            'is_password_changed' => false
        ]);
        
        // Reset all linked accounts passwords too
        foreach ($linkedAccounts as $linked) {
            $linked->update([
                'password' => $linked->lottery_number,
                'is_password_changed' => false
            ]);
        }

        $message = 'Password peserta ' . $participant->name . ' berhasil direset ke default.';
        if ($linkedCount > 0) {
            $message .= ' ' . $linkedCount . ' akun terhubung lainnya juga telah direset.';
        }
        
        return redirect()->back()->with('success', $message);
    }

    // Kelola Jabatan Methods
    public function positions()
    {
        $positions = Position::with('saksis')->withCount('saksis')->get();
        return view('admin.positions.index', compact('positions'));
    }

    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        Position::create($validated);

        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function updatePosition(Request $request, $id)
    {
        $position = Position::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $position->update($validated);

        return redirect()->back()->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function deletePosition($id)
    {
        $position = Position::findOrFail($id);
        
        if ($position->saksis()->count() > 0) {
            return redirect()->back()->with('error', 'Jabatan tidak bisa dihapus karena masih digunakan oleh pengurus.');
        }

        $position->delete();

        return redirect()->back()->with('success', 'Jabatan berhasil dihapus.');
    }

    // Kepengurusan Management Methods
    public function saksi(\Illuminate\Http\Request $request)
    {
        $groups = Group::where('is_active', true)->orderBy('name')->get();
        $selectedGroupId = $request->query('group_id');
        $search = $request->query('search');
        $selectedGroup = null;

        $query = Saksi::with(['participant.group', 'monthlyPeriods.group'])
            ->whereHas('monthlyPeriods', function($q) {
                $q->has('winners');
            });

        if ($selectedGroupId) {
            $selectedGroup = Group::find($selectedGroupId);
            $query->whereHas('participant', function($q) use ($selectedGroupId) {
                $q->where('group_id', $selectedGroupId);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_pengurus', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('participant', function($qp) use ($search) {
                      $qp->where('nik', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        $saksis = $query->get();
            
        return view('admin.saksi.index', compact('saksis', 'groups', 'selectedGroupId', 'selectedGroup'));
    }

    public function createKepengurusan()
    {
        $positions = Position::all();
        return view('admin.saksi.create', compact('positions'));
    }

    public function storeKepengurusan(Request $request)
    {
        $request->validate([
            'jabatan' => 'required|string|max:255',
            'nama_pengurus' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd_drawing' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $data = $request->except(['foto', 'ttd', 'ttd_drawing']);

        // Handle photo upload with Intervention Image
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoName = time() . '_foto_' . uniqid() . '.webp';
            if (!file_exists(public_path('uploads/saksi'))) {
                mkdir(public_path('uploads/saksi'), 0777, true);
            }
            
            // Process image: resize to 300x300 and convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($foto->getPathname())
                ->cover(300, 300)
                ->toWebp(80);
            file_put_contents(public_path('uploads/saksi/' . $fotoName), (string) $image);
            $data['foto'] = $fotoName;
        }

        // Handle signature (Drawing or Upload)
        if ($request->filled('ttd_drawing')) {
            $imageData = $request->input('ttd_drawing');
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $ttdName = time() . '_ttd_draw_' . uniqid() . '.png';
            
            if (!file_exists(public_path('uploads/saksi'))) {
                mkdir(public_path('uploads/saksi'), 0777, true);
            }
            
            file_put_contents(public_path('uploads/saksi/' . $ttdName), base64_decode($imageData));
            $data['ttd'] = $ttdName;
        } elseif ($request->hasFile('ttd')) {
            $ttd = $request->file('ttd');
            $ttdName = time() . '_ttd_' . $ttd->getClientOriginalName();
            if (!file_exists(public_path('uploads/saksi'))) {
                mkdir(public_path('uploads/saksi'), 0777, true);
            }
            $ttd->move(public_path('uploads/saksi'), $ttdName);
            $data['ttd'] = $ttdName;
        }

        $data['is_active'] = $request->has('is_active');

        Saksi::create($data);

        return redirect()->route('admin.saksi')
            ->with('success', 'Data saksi berhasil ditambahkan.');
    }

    public function editKepengurusan($id)
    {
        $saksi = Saksi::findOrFail($id);
        return view('admin.saksi.edit', compact('saksi'));
    }

    public function updateKepengurusan(Request $request, $id)
    {
        $saksi = Saksi::findOrFail($id);

        $request->validate([
            'jabatan' => 'required|string|max:255',
            'nama_pengurus' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd_drawing' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $data = $request->except(['foto', 'ttd', 'ttd_drawing']);

        // Handle photo upload with Intervention Image
        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($saksi->foto) {
                $oldFotoPath = public_path('uploads/saksi/' . $saksi->foto);
                if (file_exists($oldFotoPath)) {
                    unlink($oldFotoPath);
                }
            }

            $foto = $request->file('foto');
            $fotoName = time() . '_foto_' . uniqid() . '.webp';
            
            // Process image: resize to 300x300 and convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($foto->getPathname())
                ->cover(300, 300)
                ->toWebp(80);
            file_put_contents(public_path('uploads/saksi/' . $fotoName), (string) $image);
            $data['foto'] = $fotoName;
        }

        // Handle signature (Drawing or Upload)
        if ($request->filled('ttd_drawing')) {
            // Delete old signature
            if ($saksi->ttd) {
                $oldTtdPath = public_path('uploads/saksi/' . $saksi->ttd);
                if (file_exists($oldTtdPath)) {
                    unlink($oldTtdPath);
                }
            }

            $imageData = $request->input('ttd_drawing');
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $ttdName = time() . '_ttd_draw_' . uniqid() . '.png';
            
            if (!file_exists(public_path('uploads/saksi'))) {
                mkdir(public_path('uploads/saksi'), 0777, true);
            }
            
            file_put_contents(public_path('uploads/saksi/' . $ttdName), base64_decode($imageData));
            $data['ttd'] = $ttdName;
        } elseif ($request->hasFile('ttd')) {
            // Delete old signature
            if ($saksi->ttd) {
                $oldTtdPath = public_path('uploads/saksi/' . $saksi->ttd);
                if (file_exists($oldTtdPath)) {
                    unlink($oldTtdPath);
                }
            }

            $ttd = $request->file('ttd');
            $ttdName = time() . '_ttd_' . $ttd->getClientOriginalName();
            $ttd->move(public_path('uploads/saksi'), $ttdName);
            $data['ttd'] = $ttdName;
        }

        $data['is_active'] = $request->has('is_active');

        $saksi->update($data);

        return redirect()->route('admin.saksi')
            ->with('success', 'Data saksi berhasil diperbarui.');
    }

    public function deleteKepengurusan($id)
    {
        $saksi = Saksi::findOrFail($id);

        // Delete photo and signature files
        if ($saksi->foto) {
            $fotoPath = public_path('uploads/saksi/' . $saksi->foto);
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        if ($saksi->ttd) {
            $ttdPath = public_path('uploads/saksi/' . $saksi->ttd);
            if (file_exists($ttdPath)) {
                unlink($ttdPath);
            }
        }

        $saksi->delete();

        return redirect()->route('admin.saksi')
            ->with('success', 'Data saksi berhasil dihapus.');
    }

    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        $managements = \App\Models\Management::with('position')->get();
        $positions = \App\Models\Position::with('managements')->withCount('managements')->get();
        return view('admin.profile', compact('admin', 'managements', 'positions'));
    }

    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'new_password' => 'nullable|min:6|confirmed'
        ]);
        
        // Update basic information
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        
        // Update password if new password is provided
        if ($request->filled('new_password')) {
            // Hash new password manually to ensure it's properly hashed
            $admin->password = Hash::make($request->new_password);
        }
        
        $admin->save();
        
        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui');
    }

    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('photo')) {
            $admin = Auth::guard('admin')->user();
            $file = $request->file('photo');
            $filename = time() . '_' . $admin->id . '.webp';

            // Hapus foto lama jika ada
            if ($admin->profile_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($admin->profile_photo);
            }

            // Memproses gambar menggunakan Intervention Image v3
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            
            $image = $manager->read($file->getPathname())
                ->cover(300, 300) // Crop otomatis ke tengah (1:1)
                ->toWebp(80); // Konversi ke WebP dengan kualitas 80%

            // Simpan ke storage (public disk)
            \Illuminate\Support\Facades\Storage::disk('public')->put('profiles/' . $filename, (string) $image);

            // Update path di database
            $admin->update([
                'profile_photo' => 'profiles/' . $filename
            ]);

            return back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal mengupload foto. Silakan coba lagi.');
    }

    public function updateParticipant(Request $request, $id)
    {
        $participant = \App\Models\Participant::findOrFail($id);
        $oldNik = $participant->nik;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lottery_number' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255',
            'shift' => 'nullable|string|max:255', // Bagian/Shift
        ]);
        
        // Find all linked accounts (same NIK) to update together
        $linkedAccounts = \App\Models\Participant::where('nik', $oldNik)
            ->where('id', '!=', $id)
            ->get();
        
        $linkedCount = $linkedAccounts->count();
        
        // Check if NIK is being changed
        $nikChanged = $oldNik !== $validated['nik'] && $validated['nik'];
        
        // Prepare update data for current participant
        $updateData = $validated;
        
        // If NIK changed, reset password to new lottery_number
        if ($nikChanged) {
            $updateData['password'] = $validated['lottery_number'];
            $updateData['is_password_changed'] = false;
        }
        
        // Update current participant
        $participant->update($updateData);
        
        // Update all linked accounts with same name, NIK, and shift
        if ($linkedCount > 0 && $oldNik) {
            foreach ($linkedAccounts as $linked) {
                $linkedUpdate = [
                    'name' => $validated['name'],
                    'nik' => $validated['nik'],
                    'shift' => $validated['shift'],
                ];
                
                // Also update lottery_number and reset password if NIK changed
                if ($nikChanged) {
                    $oldLottery = $linked->lottery_number;
                    // Replace old NIK prefix with new NIK in lottery_number
                    if (str_starts_with($oldLottery, $oldNik . '-')) {
                        $newLottery = str_replace($oldNik . '-', $validated['nik'] . '-', $oldLottery);
                        $linkedUpdate['lottery_number'] = $newLottery;
                        $linkedUpdate['password'] = $newLottery;
                        $linkedUpdate['is_password_changed'] = false;
                    }
                }
                
                $linked->update($linkedUpdate);
            }
        }
        
        $message = 'Data peserta berhasil diperbarui.';
        if ($linkedCount > 0) {
            $message .= ' ' . ($linkedCount) . ' akun terhubung lainnya juga telah diperbarui.';
        }
        if ($nikChanged) {
            $message .= ' Password telah direset ke No. Undian karena NIK diubah.';
        }
        
        return redirect()->back()->with('success', $message);
    }

    public function deleteParticipant($id)
    {
        $participant = \App\Models\Participant::with('winner')->findOrFail($id);
        
        // Check dependencies
        $hasBids = $participant->bids()->exists();
        $hasPayments = \App\Models\Payment::where('participant_id', $id)->exists();
        $isWinner = $participant->has_won || $participant->winner;

        if ($hasBids || $hasPayments || $isWinner) {
            return redirect()->back()->with('error', 'Peserta tidak dapat dihapus karena memiliki riwayat lelang, pembayaran, atau status pemenang.');
        }
        
        $participant->delete();
        
        return redirect()->back()->with('success', 'Peserta berhasil dihapus.');
    }
}