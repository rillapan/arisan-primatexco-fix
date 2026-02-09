@extends('layouts.admin')

@section('content')


    <div class="container-fluid mt-4">
         <!-- Back Button -->
                        <div class="mb-3">
                            <a href="{{ route('admin.groups.cash.manage', $group->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    Detail Kas {{ $cashFlowName }} - {{ $group->name }}
                                </h4>
                                @if($generatingPeriod)
                                    <small class="mb-0 mt-1 d-block">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Dihasilkan oleh periode: <strong>{{ $generatingPeriod->period_name }}</strong>
                                        ({{ $generatingPeriod->period_start->format('d/m/Y') }} - {{ $generatingPeriod->period_end->format('d/m/Y') }})
                                    </small>
                                @else
                                    <small class="mb-0 mt-1 d-block text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Belum ada periode yang terkait dengan kas bulanan ini
                                    </small>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('admin.groups.cash.month.export-pdf', ['groupId' => $group->id, 'monthKey' => $monthKey, 'period_id' => request('period_id')]) }}" 
                                   class="btn btn-danger btn-sm me-2" 
                                   target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i>
                                    Export PDF
                                </a>
                                <a href="{{ route('admin.groups.cash.month.print-all-receipts', [$group->id, $monthKey]) }}" 
                                   class="btn btn-warning btn-sm me-2 position-relative">
                                    <i class="fas fa-print me-1"></i>
                                    Cetak Semua Bukti
                                    @php
                                        $unreadCount = \App\Models\Payment::whereHas('participant', function($query) use ($group) {
                                                        $query->where('group_id', $group->id);
                                                    })
                                                    ->where('is_notification_read', false)
                                                    ->where('is_confirmed', true)
                                                    ->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $unreadCount }}
                                            <span class="visually-hidden">unread notifications</span>
                                        </span>
                                    @endif
                                </a>
                                <button type="button" class="btn btn-info btn-sm me-2 text-white" data-bs-toggle="modal" data-bs-target="#bulkInstallmentModal">
                                    <i class="fas fa-users me-1"></i>
                                    Tambah Angsuran Semua Peserta
                                </button>
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                       

                        <!-- Calculation Logic (Moved Up) -->
                        @php
                            $calTotalInstallments = $totalInstallments;
                            $calWinnerReceives = $totalPrizesGiven; 
                            $winnerReceives = $calWinnerReceives;
                            $calNetSurplusThisMonth = $remainingCash; 
                            $calAccumulation = $remainingCash;
                            $hasCalculation = false;

                            if($generatingPeriod) {
                                $hasCalculation = true;
                                $period = $generatingPeriod;
                                
                                // Get group info
                                $participantCount = $period->group->participants()->where('is_active', true)->count();
                                $monthlyInstallment = $period->group->monthly_installment;
                                $mainPrize = $period->group->main_prize;
                                $shuAmount = $period->group->shu ?? 500000;
                                 // Calculate previous cash balance (from previous periods)
                                 $calPreviousCashBalance = isset($calPreviousCashBalanceFromController) ? $calPreviousCashBalanceFromController : ($period->previous_cash_balance ?? 0);
                                 
                                 // Fallback: If 0, try to calculate accumulation from all previous periods
                                 if ($calPreviousCashBalance == 0 && !isset($calPreviousCashBalanceFromController)) {
                                     $allPrevPeriods = \App\Models\MonthlyPeriod::where('group_id', $group->id)
                                         ->where('period_start', '<', $period->period_start)
                                         ->orderBy('period_start', 'asc')
                                         ->get();
                                     
                                     $runningAccumulation = 0;
                                     foreach($allPrevPeriods as $p) {
                                         $pCount = $group->participants->where('is_active', true)->count();
                                         $pInstallment = $pCount * $group->monthly_installment;
                                         $pHighestBid = $p->bids->max('bid_amount') ?? 0;
                                         $pSurplus = ($pInstallment + $pHighestBid) - ($group->main_prize + ($group->shu ?? 500000));
                                         
                                         // If a period has a stored balance, trust it as a starting point, otherwise use running total
                                         $startBal = ($p->previous_cash_balance > 0) ? $p->previous_cash_balance : $runningAccumulation;
                                         $runningAccumulation = $startBal + $pSurplus;
                                     }
                                     $calPreviousCashBalance = $runningAccumulation;
                                 }
                                
                                // Calculate current period status
                                $calTotalInstallments = $group->payments->sum('amount');
                                
                                // Request: Default to Realized (Actual) Calculation to avoid negative confusion
                                $calProjectedInstallment = $participantCount * $monthlyInstallment;
                                
                                // Get bids only from winners for this period
                                $winnerBids = [];
                                if ($period->winners->count() > 0) {
                                    foreach ($period->winners as $winner) {
                                        if ($winner->bid_amount > 0) {
                                            $winnerBids[] = $winner->bid_amount;
                                        }
                                    }
                                }
                                $totalBids = array_sum($winnerBids);
                                $highestBid = !empty($winnerBids) ? max($winnerBids) : 0;
                                
                                // Calculate for 2 winners case
                                $winnerCount = $period->winners->count();
                                
                                // 1. Rumus Inflow (Pemasukan Bersih Bulan Ini)
                                // Dana Iuran Bersih = (Total Angsuran Masuk) - (jumlah pemenang x SHU)
                                // UPDATED: Gunakan realisasi ($calTotalInstallments) agar sesuai kondisi sebenarnya (misal 0/90)
                                $calNetFunds = $calTotalInstallments - ($winnerCount * $shuAmount);
                                
                                // Total Bid = bid1 + bid2 (sum of all bids)
                                $calTotalBidAmount = $totalBids;
                                
                                // 2. Rumus Outflow (Pengeluaran Hadiah)
                                // Total Harga Motor = Jumlah Pemenang x Harga Satuan Motor
                                $calTotalMotorPrice = $winnerCount * $mainPrize;
                                
                                // 3. Rumus Logika Aliran Kas (Dana Saat Ini)
                                // Dana Saat Ini = Dana Iuran Bersih + Total Bid
                                $calCurrentFund = $calNetFunds + $calTotalBidAmount;
                                
                                // Sisa Bersih Periode Ini = Dana Saat Ini - Total Harga Motor
                                $calFinalRemainingCash = $calCurrentFund - $calTotalMotorPrice;
                                
                                // 4. Rumus Akumulasi Akhir (Saldo Dompet)
                                // Total Kas Berjalan = Saldo Akumulasi Lalu + Sisa Bersih Periode Ini
                                $calTotalRunningCash = $calPreviousCashBalance + $calFinalRemainingCash;
                                
                                // For display purposes
                                $calNetSurplusThisMonth = $calFinalRemainingCash;
                                $calAccumulation = $calTotalRunningCash;
                                
                                // Calculate winner receives (per person) - using individual bid amounts
                                $calWinnerReceives = [];
                                if ($winnerCount > 0) {
                                    foreach ($period->winners as $winner) {
                                        $calWinnerReceives[$winner->id] = $mainPrize - ($winner->bid_amount ?? 0);
                                    }
                                }
                                $firstWinner = $period->winners->first();
                                $winnerReceives = $firstWinner ? ($calWinnerReceives[$firstWinner->id] ?? 0) : 0;
                                
                                // Total prizes given to all winners
                                $totalPrizesGiven = array_sum($calWinnerReceives);
                                
                                // For Detailed Card Display (Prepare variables)
                                $periodStart = $period->period_start;
                                $previousPeriodMonth = $periodStart->copy()->subMonth();
                                $previousPeriodMonthName = $previousPeriodMonth->locale('id')->monthName . ' ' . $previousPeriodMonth->year;
                            }
                        @endphp

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                        <!-- Keep Actual Collected Amount for the Summary Card to be accurate -->
                                        <h5 class="mb-0">Rp {{ number_format($calTotalInstallments, 0, ',', '.') }}</h5>
                                        <small>Total Angsuran Masuk (Aktual)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                        <h5 class="mb-0">Rp {{ number_format($totalPrizesGiven ?? 0, 0, ',', '.') }}</h5>
                                        <small>Total Hadiah Diberikan ({{ $winnerCount ?? 0 }} Pemenang)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-dark h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <i class="fas fa-wallet fa-2x mb-2"></i>
                                        <h5 class="mb-0">Rp {{ number_format($calNetSurplusThisMonth, 0, ',', '.') }}</h5>
                                        <small>Sisa Kas Tersedia (bulan ini)</small>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="card bg-primary text-white h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <i class="fas fa-coins fa-2x mb-2"></i>
                                            <h5 class="mb-0">Rp {{ number_format($calAccumulation, 0, ',', '.') }}</h5>
                                        <small>Akumulasi Kas</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Period Calculation Card (Same format as Draw page) -->
                        @if($generatingPeriod)
                        <div class="card mb-4 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>
                                    Perhitungan Kas - {{ $generatingPeriod->period_name }}
                                </h5>
                            </div>
                            <div class="card-body">

                                
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-calculator me-2"></i>Format Perhitungan Periode</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-0"><strong>Periode:</strong> {{ $period->period_name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-0"><strong>Kas Acuan:</strong> Diambil dari kas bulan {{ $previousPeriodMonthName }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-primary h-100">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="fas fa-arrow-down me-2"></i>(Inflow/Masuk)</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-2">
                                                    <small class="text-muted">Target Setoran (Potensi):</small>
                                                    @if($calTotalInstallments > 0)
                                                        <p class="mb-1 fw-bold">{{ $participantCount }} × Rp {{ number_format($monthlyInstallment, 0, ',', '.') }} = Rp {{ number_format($calProjectedInstallment, 0, ',', '.') }}</p>
                                                    @else
                                                        <p class="mb-1 fw-bold">0 × Rp {{ number_format($monthlyInstallment, 0, ',', '.') }} = Rp 0</p>
                                                    @endif
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">Realisasi Setoran (Sekarang):</small>
                                                    <p class="mb-1 fw-bold text-primary">{{ $paidParticipantCount }} peserta × Rp {{ number_format($monthlyInstallment, 0, ',', '.') }} = Rp {{ number_format($calTotalInstallments, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">Total Bid:</small>
                                                    <p class="mb-1 fw-bold text-success">Rp {{ number_format($calTotalBidAmount, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">Akumulasi Kas (Bulan Lalu):</small>
                                                    <p class="mb-1 fw-bold text-info">Rp {{ number_format($calPreviousCashBalance, 0, ',', '.') }}</p>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <small class="text-muted">Dana Iuran Bersih:</small>
                                                     @if(($calNetFunds ?? 0) <= 0)
                                                        <p class="mb-0 fw-bold text-primary">0 peserta × Rp {{ number_format($monthlyInstallment, 0, ',', '.') }} - ({{ $winnerCount }} × Rp {{ number_format($shuAmount, 0, ',', '.') }}) = Rp 0</p>
                                                    @else
                                                        <p class="mb-0 fw-bold text-primary">({{ $paidParticipantCount }} × Rp {{ number_format($monthlyInstallment, 0, ',', '.') }}) - ({{ $winnerCount }} × Rp {{ number_format($shuAmount, 0, ',', '.') }}) = Rp {{ number_format($calNetFunds, 0, ',', '.') }}</p>
                                                    @endif
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">Total Bid:</small>
                                                    <p class="mb-1 fw-bold text-success">
                                                        @foreach($period->winners as $winner)
                                                            @if($winner->bid_amount > 0)
                                                                Rp {{ number_format($winner->bid_amount, 0, ',', '.') }}{{ !$loop->last ? ' + ' : '' }}
                                                            @endif
                                                        @endforeach
                                                        = Rp {{ number_format($calTotalBidAmount, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-danger h-100">
                                            <div class="card-header bg-danger text-white">
                                                <h6 class="mb-0"><i class="fas fa-arrow-up me-2"></i>  (Outflow/Keluar)</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-2">
                                                    <small class="text-muted">Status Pemenang:</small>
                                                    <p class="mb-1 fw-bold {{ $winnerCount > 0 ? 'text-success' : 'text-muted' }}">
                                                        {{ $winnerCount > 0 ? $winnerCount . ' Pemenang' : 'Belum Ada' }}
                                                    </p>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">Total Harga Motor:</small>
                                                    <p class="mb-1 fw-bold">{{ $winnerCount }} × Rp {{ number_format($mainPrize, 0, ',', '.') }} = Rp {{ number_format($calTotalMotorPrice, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">Total SHU:</small>
                                                    <p class="mb-1 fw-bold">{{ $winnerCount }} × Rp {{ number_format($shuAmount, 0, ',', '.') }} = Rp {{ number_format($winnerCount * $shuAmount, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="mb-0">
                                                    <small class="text-muted">Total Hadiah (Semua Pemenang):</small>
                                                    <p class="mb-0 fw-bold text-success">
                                                        Rp {{ number_format($totalPrizesGiven, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Perhitungan Aliran Kas (Logika Sistem)</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <small class="text-muted">Rumus untuk menentukan ketersediaan dana saat transaksi berlangsung:</small>
                                                            <p class="mb-1 fw-bold">Dana Saat Ini: [Dana Iuran Bersih] + [Total Bid]</p>
                                                            <p class="mb-1">Rp {{ number_format($calNetFunds, 0, ',', '.') }} + Rp {{ number_format($calTotalBidAmount, 0, ',', '.') }} = <span class="text-success">Rp {{ number_format($calCurrentFund, 0, ',', '.') }}</span></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <small class="text-muted">Sisa Bersih Periode Ini (Status Sekarang):</small>
                                                            <p class="mb-1 fw-bold">[Dana Saat Ini] - [Total Harga Motor]</p>
                                                            <p class="mb-1">Rp {{ number_format($calCurrentFund, 0, ',', '.') }} - Rp {{ number_format($calTotalMotorPrice, 0, ',', '.') }} = <span class="text-primary fw-bold">Rp {{ number_format($calFinalRemainingCash, 0, ',', '.') }}</span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="card border-success">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>(Hasil Akhir & Akumulasi)</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="mb-2"><small class="text-muted">Informasi ini yang akan tampil pada tabel Ringkasan Kas:</small></p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="p-2 bg-light rounded mb-2">
                                                            <small class="text-muted">Sisa Bersih (Kas Lelang):</small>
                                                            <p class="mb-0 fw-bold text-success">[Sisa Dana Setelah Hadiah Diberikan] = Rp {{ number_format($calFinalRemainingCash, 0, ',', '.') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="p-2 bg-light rounded mb-2">
                                                            <small class="text-muted">Total Kas Berjalan:</small>
                                                            <p class="mb-0 fw-bold text-primary">[Saldo Akumulasi Bulan Lalu] + [Sisa Bersih Periode Ini] = Rp {{ number_format($calTotalRunningCash, 0, ',', '.') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Ringkasan Informasi untuk Peserta (Hadiah Neto) -->
                                                @if($winnerCount > 0)
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <div class="p-3 bg-info bg-opacity-10 rounded border border-info">
                                                            <h6 class="text-info mb-3"><i class="fas fa-info-circle me-2"></i>Ringkasan Informasi untuk Peserta (Hadiah Neto)</h6>
                                                            <div class="row">
                                                                @foreach($period->winners as $index => $winner)
                                                                <div class="col-md-6 mb-3">
                                                                    <div class="card border-info">
                                                                        <div class="card-body p-3">
                                                                            <h6 class="text-info mb-2">Pemenang {{ $index + 1 }}</h6>
                                                                            <div class="mb-2">
                                                                                <small class="text-muted">Nama:</small>
                                                                                <p class="mb-1 fw-bold">{{ $winner->participant->name }}</p>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <small class="text-muted">No. Undian:</small>
                                                                                <p class="mb-1 fw-bold">{{ $winner->participant->lottery_number }}</p>
                                                                            </div>
                                                                            <div class="mb-0">
                                                                                <small class="text-muted">Diterima (Harga Motor - Bid):</small>
                                                                                <p class="mb-0 fw-bold text-success">
                                                                                    Rp {{ number_format($mainPrize, 0, ',', '.') }} - Rp {{ number_format($winner->bid_amount ?? 0, 0, ',', '.') }} = Rp {{ number_format($mainPrize - ($winner->bid_amount ?? 0), 0, ',', '.') }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Perhitungan kas belum tersedia karena belum ada periode aktif yang ditetapkan untuk bulan ini.
                        </div>
                        @endif

                       
                        <!-- Main Table with the requested format -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Peserta</th>
                                        <th>Bag/Shift</th>
                                        <th>NIK</th>
                                        <th>Tanggal</th>
                                        <th>Angsuran Ke</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cashData as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $data['participant']->name }}</strong>
                                            <br><small class="text-muted">No: {{ $data['participant']->lottery_number }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $data['participant']->shift }}</span>
                                        </td>
                                        <td>{{ $data['participant']->nik }}</td>
                                        <td>{{ $data['date'] }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $data['installment_count'] }}</span>
                                        </td>
                                        <td>
                                            @if($data['keterangan'] === '-')
                                                <span class="text-muted">-</span>
                                            @else
                                                <span class="badge bg-success">{{ $data['keterangan'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($data['payment']))
                                                <span class="text-success fw-bold">
                                                    Rp {{ number_format($data['amount'], 0, ',', '.') }}
                                                </span>
                                                <br>
                                                <a href="{{ route('admin.payments.receipt', $data['payment']->id) }}" 
                                                   target="_blank" class="btn btn-xs btn-outline-primary mt-1">
                                                    <i class="fas fa-file-invoice me-1"></i>
                                                    {{ $data['payment']->receipt_number }}
                                                </a>
                                            @else
                                                <span class="text-success fw-bold">
                                                    Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i><br>
                                            Belum ada data kas untuk periode {{ $monthName }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-chart-line me-2"></i>
                                            Ringkasan Kas {{ $monthName }}
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted">Potongan/setoran tanggal:</small>
                                                <h5 class="text-primary">{{ $groupCreationDate }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Administrasi (SHU):</small>
                                                <h5 class="text-info">Rp {{ number_format($group->shu ?? 0, 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Telah lelang:</small>
                                                <h5 class="text-success">{{ $totalPreviousWinners }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Sisa Kas Bulan Ini:</small>
                                                    <h5 class="text-primary">Rp {{ number_format($calTotalRunningCash, 0, ',', '.') }}</h5>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <small class="text-muted">Harga pokok SPM:</small>
                                                <h5 class="text-danger">Rp {{ number_format($group->main_prize, 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Lelang Minimal:</small>
                                                <h5 class="text-secondary">Rp {{ number_format($group->min_bid, 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Akumulasi Kas:</small>
                                                    <h5 class="text-primary">Rp {{ number_format($calTotalRunningCash, 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Jumlah Setoran:</small>
                                                <h5 class="text-dark">Rp {{ number_format($totalInstallments, 0, ',', '.') }}</h5>
                                                <small class="text-muted">{{ $paidParticipantCount }} peserta × Rp {{ number_format($monthlyInstallment, 0, ',', '.') }}</small>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <small class="text-muted">Total Pemenang:</small>
                                                <h5 class="text-success">{{ collect($cashData)->where('keterangan', '!=', '-')->count() }}</h5>
                                                @if($winnersInMonth->count() > 0)
                                                    <small class="text-info">
                                                        @foreach($winnersInMonth as $winner)
                                                            {{ $winner['participant_name'] }} ({{ $winner['lottery_number'] }}){{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Total Angsuran:</small>
                                                <h5 class="text-info">{{ count($cashData) }}</h5>
                                                <small class="text-muted">Total: Rp {{ number_format($totalInstallments, 0, ',', '.') }}</small>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Angsuran Setiap Peserta:</small>
                                                <h5 class="text-warning">Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Installment Modal -->
    <div class="modal fade" id="addInstallmentModal" tabindex="-1" aria-labelledby="addInstallmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInstallmentModalLabel">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Angsuran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.groups.cash.add.installment', ['groupId' => $group->id, 'monthKey' => $monthKey]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                            <p class="mb-0">Tambahkan angsuran untuk peserta tertentu pada bulan {{ $monthName }}. Nomor angsuran akan otomatis bertambah dari angsuran terakhir peserta.</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="monthly_period_id" class="form-label">Digunakan untuk Periode <span class="text-danger">*</span></label>
                                <select class="form-select" id="monthly_period_id" name="monthly_period_id" required>
                                    <option value="">Pilih periode terlebih dahulu...</option>
                                    @if($generatingPeriod)
                                    <option value="{{ $generatingPeriod->id }}" selected data-period-name="{{ $generatingPeriod->period_name }}">
                                        {{ $generatingPeriod->period_name }} ({{ $generatingPeriod->period_start->format('d/m/Y') . ' - ' . $generatingPeriod->period_end->format('d/m/Y') }}) 
                                        <span class="text-success">[Periode yang menghasilkan kas {{ $monthName }}]</span>
                                    </option>
                                    @endif
                                    @foreach($group->monthlyPeriods as $period)
                                        @if(!$generatingPeriod || $period->id != $generatingPeriod->id)
                                        <option value="{{ $period->id }}" data-period-name="{{ $period->period_name }}">
                                            {{ $period->period_name }} ({{ $period->period_start->format('d/m/Y') . ' - ' . $period->period_end->format('d/m/Y') }})
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="form-text">Harus memilih periode sebelum menambah angsuran</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="participant_id" class="form-label">Pilih Peserta</label>
                                <select class="form-select" id="participant_id" name="participant_id" required>
                                    <option value="">Pilih peserta...</option>
                                    @foreach($group->participants as $participant)
                                    <option value="{{ $participant->id }}" data-last-installment="{{ $nextInstallments[$participant->id] - 1 ?? 0 }}">
                                        {{ $participant->lottery_number }} - {{ $participant->name }} ({{ $participant->shift }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="installment_number" class="form-label">Angsuran Ke</label>
                                <input type="number" class="form-control" id="installment_number" name="installment_number"
                                       placeholder="Default: tambah satu dari terakhir" min="1" readonly>
                                <div class="form-text">Otomatis: angsuran terakhir + 1 (sesuai peserta)</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Jumlah</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="amount" name="amount"
                                           step="0.01" min="0" value="{{ $group->monthly_installment }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_date" class="form-label">Tanggal Pembayaran</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date"
                                       value="{{ \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Angsuran ke [nomor] untuk bulan {{ $monthName }}" readonly></textarea>
                                <div class="form-text">Akan otomatis terisi berdasarkan pilihan peserta dan periode</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>
                            Simpan Angsuran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Installment Confirmation Modal -->
    <div class="modal fade" id="bulkInstallmentModal" tabindex="-1" aria-labelledby="bulkInstallmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="bulkInstallmentModalLabel">
                        <i class="fas fa-users me-2"></i>
                        Tambah Angsuran Bulanan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.groups.cash.bulk.installment', $group->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="monthKey" value="{{ $monthKey }}">
                    <div class="modal-body p-4">
                        <!-- Summary Card -->
                        <div class="card bg-gradient-primary text-black mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-2">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            Periode: {{ $monthName }}
                                        </h6>
                                        <p class="mb-0 small opacity-90">
                                            Sistem akan menambahkan angsuran untuk semua peserta aktif yang belum membayar pada periode ini
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="bg-white bg-opacity-25 rounded p-3">
                                            <div class="small opacity-90">Total Peserta Aktif</div>
                                            <div class="h3 mb-0">{{ $group->participants->where('is_active', true)->count() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Period Selection -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-cog me-2 text-primary"></i>
                                    Konfigurasi Periode
                                </h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="bulk_monthly_period_id" class="form-label fw-bold">
                                            Periode Angsuran <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-lg" id="bulk_monthly_period_id" name="monthly_period_id" required>
                                            <option value="">Pilih periode terlebih dahulu...</option>
                                            @if($generatingPeriod)
                                            <option value="{{ $generatingPeriod->id }}" selected data-period-name="{{ $generatingPeriod->period_name }}">
                                                {{ $generatingPeriod->period_name }} ({{ $generatingPeriod->period_start->format('d/m/Y') . ' - ' . $generatingPeriod->period_end->format('d/m/Y') }}) 
                                                <span class="text-success">[Periode yang menghasilkan kas {{ $monthName }}]</span>
                                            </option>
                                            @endif
                                            @foreach($group->monthlyPeriods as $period)
                                                @if(!$generatingPeriod || $period->id != $generatingPeriod->id)
                                                <option value="{{ $period->id }}" data-period-name="{{ $period->period_name }}">
                                                    {{ $period->period_name }} ({{ $period->period_start->format('d/m/Y') . ' - ' . $period->period_end->format('d/m/Y') }})
                                                </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Pilih periode yang akan digunakan untuk pencatatan angsuran
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                    Detail Pembayaran
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Jumlah Peserta Aktif</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-users"></i>
                                            </span>
                                            <input type="text" class="form-control" value="{{ $group->participants->where('is_active', true)->count() }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Angsuran per Peserta</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-tag"></i>
                                            </span>
                                            <input type="text" class="form-control" value="Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Angsuran Berikutnya</label>
                                        @php
                                            $paidParticipantIds = collect($cashData)->pluck('participant.id')->unique();
                                            $totalActiveCount = $group->participants->where('is_active', true)->count();
                                            $remainingCount = max(0, $totalActiveCount - $paidParticipantIds->count());
                                            
                                            $firstParticipant = $group->participants->where('is_active', true)->first();
                                            $nextInstallmentNumber = $firstParticipant ? ($nextInstallments[$firstParticipant->id] ?? 1) : 1;
                                        @endphp
                                        <div class="input-group">
                                            <span class="input-group-text bg-warning text-dark">
                                                <i class="fas fa-hashtag"></i>
                                            </span>
                                            <input type="text" class="form-control fw-bold text-warning" 
                                                   value="Ke-{{ $nextInstallmentNumber }}" readonly>
                                        </div>
                                        <div class="form-text">
                                            <small class="text-muted">{{ $remainingCount }} peserta belum bayar</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Summary -->
                        <div class="card bg-success bg-gradient text-white mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-2">
                                            <i class="fas fa-calculator me-2"></i>
                                            Total Angsuran yang Akan Diproses
                                        </h6>
                                        <p class="mb-0 small opacity-90">
                                            {{ $remainingCount }} peserta × Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="bg-white bg-opacity-25 rounded p-3">
                                            <div class="small opacity-90">Total Pembayaran</div>
                                            <div class="h3 mb-0">Rp {{ number_format($remainingCount * $group->monthly_installment, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Settings -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-sliders-h me-2 text-info"></i>
                                    Pengaturan Tambahan
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="bulk_payment_date" class="form-label fw-bold">
                                            <i class="fas fa-calendar me-1"></i>
                                            Tanggal Pembayaran
                                        </label>
                                        <input type="date" class="form-control" id="bulk_payment_date" name="payment_date" 
                                               value="{{ \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bulk_notes" class="form-label fw-bold">
                                            <i class="fas fa-comment me-1"></i>
                                            Keterangan
                                        </label>
                                        <textarea class="form-control" id="bulk_notes" name="notes" rows="2" 
                                                  placeholder="Akan otomatis terisi berdasarkan periode" readonly></textarea>
                                        <div class="form-text">
                                            <small class="text-muted">Akan otomatis terisi berdasarkan periode yang dipilih</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Alert -->
                        <div class="alert alert-warning border-warning bg-warning bg-opacity-10">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="alert-heading mb-1">Perhatian Penting</h6>
                                    <p class="mb-0">
                                        Sistem akan otomatis melewati <strong>{{ $paidParticipantIds->count() }}</strong> peserta yang sudah melakukan pembayaran untuk periode ini.
                                        Hanya <strong>{{ $remainingCount }}</strong> peserta yang akan diproses angsurannya.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            Batal
                        </button>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle me-2"></i>
                            Proses Angsuran untuk {{ $remainingCount }} Peserta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Individual installment form
            const individualInstallmentForm = document.querySelector('form[action*="add.installment"]');
            if (individualInstallmentForm) {
                individualInstallmentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
                            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                            alertDiv.innerHTML = `
                                <button type="button" class="btn-close" data-bs-dismiss="alert">×</button>
                                <strong>Sukses!</strong> ${data.message}
                            `;
                            document.body.appendChild(alertDiv);
                            
                            // Auto-remove after 3 seconds
                            setTimeout(() => {
                                if (alertDiv.parentNode) {
                                    alertDiv.parentNode.removeChild(alertDiv);
                                }
                            }, 3000);
                            
                            // Refresh the payment data without full page reload
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menambah angsuran');
                    });
                });
            }
            const monthlyPeriodSelect = document.getElementById('monthly_period_id');
            const participantSelect = document.getElementById('participant_id');
            const installmentNumberInput = document.getElementById('installment_number');
            const notesTextarea = document.getElementById('notes');
            
            function updateInstallmentAndNotes() {
                const selectedPeriod = monthlyPeriodSelect.options[monthlyPeriodSelect.selectedIndex];
                const selectedParticipant = participantSelect.options[participantSelect.selectedIndex];
                
                if (selectedPeriod.value && selectedParticipant.value) {
                    // Get last installment count
                    const lastInstallment = parseInt(selectedParticipant.dataset.lastInstallment) || 0;
                    const nextInstallment = lastInstallment + 1;
                    
                    // Update installment number
                    installmentNumberInput.value = nextInstallment;
                    
                    // Update notes
                    const periodName = selectedPeriod.dataset.periodName;
                    notesTextarea.value = `Angsuran ke ${nextInstallment} untuk periode ${periodName}`;
                } else {
                    // Reset fields if selections are incomplete
                    installmentNumberInput.value = '';
                    notesTextarea.value = '';
                }
            }
            
            // Bulk installment modal
            const bulkInstallmentForm = document.querySelector('form[action*="bulk.installment"]');
            if (bulkInstallmentForm) {
                bulkInstallmentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
                            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                            alertDiv.innerHTML = `
                                <button type="button" class="btn-close" data-bs-dismiss="alert">×</button>
                                <strong>Sukses!</strong> ${data.message}
                            `;
                            document.body.appendChild(alertDiv);
                            
                            // Auto-remove after 3 seconds
                            setTimeout(() => {
                                if (alertDiv.parentNode) {
                                    alertDiv.parentNode.removeChild(alertDiv);
                                }
                            }, 3000);
                            
                            // Close the modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkInstallmentModal'));
                            if (modal) {
                                modal.hide();
                            }
                            
                            // Reload the page immediately to show updated data
                            window.location.reload();
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memproses angsuran bulanan');
                    });
                });
            }
            const bulkMonthlyPeriodSelect = document.getElementById('bulk_monthly_period_id');
            const bulkNotesTextarea = document.getElementById('bulk_notes');
            
            function updateBulkNotes() {
                const selectedPeriod = bulkMonthlyPeriodSelect.options[bulkMonthlyPeriodSelect.selectedIndex];
                
                if (selectedPeriod.value) {
                    const periodName = selectedPeriod.dataset.periodName;
                    bulkNotesTextarea.value = `Angsuran bulanan untuk periode ${periodName}`;
                } else {
                    bulkNotesTextarea.value = '';
                }
            }
            
            // Add event listeners
            monthlyPeriodSelect.addEventListener('change', updateInstallmentAndNotes);
            participantSelect.addEventListener('change', updateInstallmentAndNotes);
            bulkMonthlyPeriodSelect.addEventListener('change', updateBulkNotes);
        });
    </script>

@endsection
@section('scripts')
    <script src="{{ asset('js/admin/groups/cash/month-detail.js') }}"></script>
@endsection
