<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undian Pemenang - Sistem Arisan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0.8rem 0;
        }
        
        .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.5px;
            color: white !important;
        }

        .spinner-container {
            position: relative;
            width: 750px;
            height: 750px;
            margin: 0 auto 100px auto;
        }
        
        .spinner-wheel {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            position: relative;
            overflow: hidden;
            border: 4px solid #fff; /* White rim like the image */
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            transition: transform 15s cubic-bezier(0.2, 0.5, 0.1, 1);
        }
        
        /* Pointer styling - Larger and on the Right */
        .spinner-pointer {
            position: absolute;
            top: 50%;
            right: -10px; 
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 25px solid transparent;
            border-bottom: 25px solid transparent;
            border-right: 50px solid #FFD700; /* Gold color, pointing inwards */
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
            z-index: 10;
        }
        
        .spin-button {
            position: absolute;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 20;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .spinning {
            animation: spin 0.5s linear infinite;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg text-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-gift me-2"></i>
                SISTEM ARISAN PRIMKOPKAR PRIMA
            </a>
            <div class="navbar-nav ms-auto">
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link text-decoration-none text-white" style="opacity: 0.9; font-weight: 500;">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="fas fa-dice me-2"></i>Undian Pemenang</h3>
                    <a href="{{ route('admin.groups.manage', $period->group_id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Cara Menentukan Siapa Yang Masuk Undian</h5>
                    </div>
                    <div class="card-body">
                        <!-- Kepengurusan Information -->
                        @if($period->saksis && $period->saksis->count() > 0)
                            <div class="alert alert-info border-info bg-info bg-opacity-10 mb-4">
                                <h6 class="alert-heading mb-3"><i class="fas fa-users me-2"></i>Saksi</h6>
                                <div class="row">
                                    @foreach($period->saksis as $saksi)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            @if($saksi->foto)
                                                                <img src="{{ asset('uploads/saksi/' . $saksi->foto) }}" 
                                                                     alt="{{ $saksi->nama_pengurus }}" 
                                                                     class="rounded-circle" 
                                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                                     style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-user text-white"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1">{{ $saksi->nama_pengurus }}</h6>
                                                            <small class="text-muted">{{ $saksi->jabatan }}</small>
                                                            @if($saksi->ttd)
                                                                <div class="mt-2">
                                                                    <small class="text-muted">Tanda Tangan:</small><br>
                                                                    <img src="{{ asset('uploads/saksi/' . $saksi->ttd) }}" 
                                                                         alt="Tanda Tangan {{ $saksi->nama_pengurus }}" 
                                                                         class="img-fluid border rounded" 
                                                                         style="max-height: 30px;">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Prosedur Penentuan Pemenang</h6>
                                <p>Pemenang ditentukan berdasarkan pemberian angka lelang tertinggi.</p>
                                <ul>
                                    <li><strong>Apabila hanya terdapat satu peserta dengan angka tertinggi</strong>, maka peserta tersebut dinyatakan sebagai pemenang untuk bulan tersebut.</li>
                                    <li><strong>Jika terdapat lebih dari satu peserta dengan angka tertinggi yang sama</strong>, maka pemenang akan ditentukan melalui proses undian.</li>
                                </ul>
                            </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Periode:</strong> {{ $period->period_name }}</p>
                                <p><strong>Kelompok:</strong> {{ $period->group->name }}</p>
                                <p><strong>Bid Tertinggi:</strong> Rp {{ number_format($highestBid, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Peserta dengan Bid Sama:</strong> {{ $highestBidders->count() }} orang</p>
                                <p><strong>Jumlah Pemenang Dibutuhkan:</strong> {{ $winnerCount }} orang</p>
                                <p><strong>Status:</strong> <span class="badge bg-danger">Perlu Undian</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Witness/Saksi Selection Section -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Pemilihan Saksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label fw-bold">Filter Kelompok</label>
                                <select class="form-select" id="groupFilter">
                                    <option value="">Semua Kelompok</option>
                                    @php
                                        $groups = $eligibleWitnesses->pluck('group')->unique('id');
                                    @endphp
                                    @foreach($groups as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold">Cari Nama atau NIK</label>
                                <input type="text" class="form-control" id="nameSearch" placeholder="Ketik nama atau NIK...">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-10">
                                <label for="saksi_selector" class="form-label fw-bold">Pilih Peserta Saksi</label>
                                <select class="form-select" id="saksi_selector">
                                    <option value="">-- Pilih Peserta --</option>
                                    @foreach($eligibleWitnesses as $participant)
                                        <option value="{{ $participant->id }}" 
                                                data-group-id="{{ $participant->group->id }}"
                                                data-nama="{{ $participant->name }}"
                                                data-nik="{{ $participant->nik ?? '-' }}"
                                                data-group-name="{{ $participant->group->name }}">
                                            {{ $participant->name }} - {{ $participant->group->name }} ({{ $participant->nik ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-success w-100" id="addSaksiBtn">
                                    <i class="fas fa-plus me-1"></i> Tambah
                                </button>
                            </div>
                        </div>

                        <div id="saksiPreview" class="mt-3" style="display: none;">
                            <div class="card border-info bg-info bg-opacity-10">
                                <div class="card-body p-3">
                                    <h6 class="mb-2 fw-bold text-info"><i class="fas fa-users me-2"></i>Daftar Saksi Terpilih:</h6>
                                    <div id="selectedSaksiList">
                                        <!-- Selected witnesses will be displayed here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Perhitungan Kas - {{ $period->period_name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            // Get period info
                            $periodStart = $period->period_start;
                            $previousMonth = $periodStart->copy()->subMonth();
                            $previousMonthName = $previousMonth->locale('id')->monthName . ' ' . $previousMonth->year;
                            
                            // Get group info
                            $participantCount = $period->group->participants()->where('is_active', true)->count();
                            $monthlyInstallment = $period->group->monthly_installment;
                            $mainPrize = $period->group->main_prize;
                            $shuAmount = $period->group->shu ?? 200000;
                            $minBid = $period->group->min_bid;
                            $winnerCount = $winnerCount ?? 2; // Default to 2 winners
                            
                            // Calculate previous cash balance (from previous periods)
                            $previousCashBalance = $period->previous_cash_balance ?? 0;
                            
                            // Fallback: If 0, try to find the actual previous period's projected balance
                            if ($previousCashBalance == 0) {
                                $prevP = \App\Models\MonthlyPeriod::where('group_id', $period->group_id)
                                    ->where('period_start', '<', $period->period_start)
                                    ->orderBy('period_start', 'desc')
                                    ->first();
                                if ($prevP) {
                                    $pCount = $period->group->participants->where('is_active', true)->count();
                                    $pInstallment = $pCount * $period->group->monthly_installment;
                                    $pHighestBid = $prevP->bids->max('bid_amount') ?? 0;
                                    // Adjust for multiple winners in previous period too
                                    $prevWinnerCount = $prevP->winners->count();
                                    $pSurplus = ($pInstallment + $pHighestBid) - (($period->group->main_prize * $prevWinnerCount) + (($period->group->shu ?? 200000) * $prevWinnerCount));
                                    $previousCashBalance = $prevP->previous_cash_balance + $pSurplus;
                                }
                            }
                            
                            // Calculate current period
                            $totalInstallments = $participantCount * $monthlyInstallment;
                            
                            // Adjust calculations for multiple winners
                            $totalMainPrize = $mainPrize * $winnerCount;
                            $totalShuAmount = $shuAmount * $winnerCount;
                            $totalHighestBid = $highestBid * $winnerCount; // Total bid from 2 winners
                            
                            // Dana Iuran Bersih = Setoran - Total SHU
                            $netFunds = $totalInstallments - $totalShuAmount;
                            
                            // Dana Saat Ini = Dana Iuran Bersih + Total Bid
                            $currentFund = $netFunds + $totalHighestBid;
                            
                            // Sisa Bersih = Dana Saat Ini - Total Harga Motor
                            $finalRemainingCash = $currentFund - $totalMainPrize;
                            
                            // Total Kas Berjalan
                            $totalRunningCash = $previousCashBalance + $finalRemainingCash;
                            
                            // Calculate what winner receives (per winner)
                            $winnerReceives = $mainPrize - $highestBid;
                        @endphp
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-calculator me-2"></i>Format Perhitungan Periode</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-0"><strong>Periode:</strong> {{ $period->period_name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0"><strong>Kas Acuan:</strong> Diambil dari kas bulan {{ $previousMonthName }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-arrow-down me-2"></i>(Inflow/Masuk)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <small class="text-muted">Setoran Kotor:</small>
                                            <p class="mb-1 fw-bold">{{ $participantCount }} × Rp {{ number_format($monthlyInstallment, 0, ',', '.') }} = Rp {{ number_format($totalInstallments, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Nilai Lelang (Bid):</small>
                                            <p class="mb-1 fw-bold text-success">{{ $winnerCount }} × Rp {{ number_format($highestBid, 0, ',', '.') }} = Rp {{ number_format($totalHighestBid, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Akumulasi Kas:</small>
                                            <p class="mb-1 fw-bold text-info">Rp {{ number_format($previousCashBalance, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="mb-0">
                                            <small class="text-muted">Dana Iuran Bersih:</small>
                                            <p class="mb-0 fw-bold text-primary">Rp {{ number_format($totalInstallments, 0, ',', '.') }} - Rp {{ number_format($totalShuAmount, 0, ',', '.') }} = Rp {{ number_format($netFunds, 0, ',', '.') }}</p>
                                            <small class="text-info">(SHU untuk {{ $winnerCount }} pemenang: {{ $winnerCount }} × Rp {{ number_format($shuAmount, 0, ',', '.') }})</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0"><i class="fas fa-arrow-up me-2"></i> (Outflow/Keluar)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <small class="text-muted">Harga Motor:</small>
                                            <p class="mb-1 fw-bold">Rp {{ number_format($mainPrize, 0, ',', '.') }} × {{ $winnerCount }} = Rp {{ number_format($totalMainPrize, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Biaya Admin (SHU):</small>
                                            <p class="mb-1 fw-bold">Rp {{ number_format($shuAmount, 0, ',', '.') }} × {{ $winnerCount }} = Rp {{ number_format($totalShuAmount, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="mb-0">
                                            <small class="text-muted">Total yang Diterima Pemenang (per pemenang):</small>
                                            <p class="mb-0 fw-bold text-success">Rp {{ number_format($mainPrize, 0, ',', '.') }} - Rp {{ number_format($highestBid, 0, ',', '.') }} = Rp {{ number_format($winnerReceives, 0, ',', '.') }}</p>
                                            <small class="text-info">Total untuk {{ $winnerCount }} pemenang: {{ $winnerCount }} × Rp {{ number_format($winnerReceives, 0, ',', '.') }} = Rp {{ number_format($winnerReceives * $winnerCount, 0, ',', '.') }}</small>
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
                                                    <p class="mb-1 fw-bold">Dana Saat Ini: [Dana Iuran Bersih] + [Total Nilai Lelang (Bid)]</p>
                                                    <p class="mb-1">Rp {{ number_format($netFunds, 0, ',', '.') }} + Rp {{ number_format($totalHighestBid, 0, ',', '.') }} = <span class="text-success">Rp {{ number_format($currentFund, 0, ',', '.') }}</span></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <small class="text-muted">Sisa Bersih Periode Ini:</small>
                                                    <p class="mb-1 fw-bold">[Dana Saat Ini] - [Total Harga Motor]</p>
                                                    <p class="mb-1">Rp {{ number_format($currentFund, 0, ',', '.') }} - Rp {{ number_format($totalMainPrize, 0, ',', '.') }} = <span class="text-primary fw-bold">Rp {{ number_format($finalRemainingCash, 0, ',', '.') }}</span></p>
                                                    <small class="text-info">(Total harga motor untuk {{ $winnerCount }} pemenang)</small>
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
                                                    <p class="mb-0 fw-bold text-success">[Hasil Pengurangan Dana Saat Ini dengan Harga Motor] = Rp {{ number_format($finalRemainingCash, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-2 bg-light rounded mb-2">
                                                    <small class="text-muted">Total Kas Berjalan:</small>
                                                    <p class="mb-0 fw-bold text-primary">[Saldo Akumulasi Bulan Lalu] + [Sisa Bersih Periode Ini] = Rp {{ number_format($totalRunningCash, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 p-3 bg-white border rounded">
                                            <div class="text-center">
                                                <small class="text-muted">Format Tabel Ringkasan:</small>
                                                <p class="mb-0 fw-bold">Sisa Kas Bulan {{ $periodStart->locale('id')->monthName }}: <span class="text-success">Rp {{ number_format($finalRemainingCash, 0, ',', '.') }}</span> | Akumulasi: <span class="text-primary">Rp {{ number_format($totalRunningCash, 0, ',', '.') }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Spinner Undian</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="spinner-container">
                            <div class="spinner-pointer"></div>
                            @php
                                $biddersForWheel = $highestBidders->values()->map(function($b, $index) {
                                    return [
                                        'index' => $index + 1,
                                        'bid_id' => $b->id,
                                        'lottery_number' => $b->participant->lottery_number,
                                        'name' => $b->participant->name,
                                        'shift' => $b->participant->shift,
                                        'nik' => $b->participant->nik,
                                        'bid_amount' => $b->bid_amount,
                                    ];
                                });
                            @endphp
                            <div class="spinner-wheel" id="spinnerWheel">
                                <canvas id="wheelCanvas" width="750" height="750" style="display:block;"></canvas>
                            </div>
                            <button type="button" class="btn btn-danger btn-lg spin-button" id="spinButton" onclick="startSpin()">
                                <i class="fas fa-play me-2"></i>Mulai Undian
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Daftar Peserta yang Diundi</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.draw.perform', $period->id) }}" id="winnerForm">
                            @csrf
                            <input type="hidden" id="final_saksi_ids" name="saksi_ids" value="">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No. Undian</th>
                                            <th>Nama</th>
                                            <th>Shift</th>
                                            <th>NIK</th>
                                            <th>Jumlah Bid</th>
                                            <th>Hadiah yang Diterima</th>
                                            <th>Pilih Pemenang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($highestBidders as $bidder)
                                        <tr>
                                            <td>{{ $bidder->participant->lottery_number }}</td>
                                            <td>
                                                <strong>{{ $bidder->participant->name }}</strong>
                                                @if($bidder->participant->has_won)
                                                    <span class="badge bg-warning">Sudah Menang</span>
                                                @endif
                                            </td>
                                            <td>{{ $bidder->participant->shift }}</td>
                                            <td>{{ $bidder->participant->nik }}</td>
                                            <td>Rp {{ number_format($bidder->bid_amount, 0, ',', '.') }}</td>
                                            <td class="text-success">
                                                <strong>Rp {{ number_format($period->group->main_prize - $bidder->bid_amount, 0, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="winners[]" 
                                                           value="{{ $bidder->id }}" id="winner_{{ $bidder->id }}"
                                                           @if($bidder->participant->has_won) disabled @endif>
                                                    <label class="form-check-label" for="winner_{{ $bidder->id }}">
                                                        Pilih
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Penting:</strong> Pilih exactly {{ $winnerCount }} pemenang dari daftar di atas.
                                Peserta yang sudah menang tidak dapat dipilih lagi.
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('admin.groups.manage', $period->group_id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-success" id="submitWinners" disabled>
                                    <i class="fas fa-trophy me-1"></i>Tentukan Pemenang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
    </div>

    <!-- Winner Modal -->
    <div class="modal fade" id="winnerModal" tabindex="-1" aria-labelledby="winnerModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-warning border-4">
                <div class="modal-header bg-warning text-dark justify-content-center">
                    <h5 class="modal-title font-weight-bold" id="winnerModalLabel">
                        <i class="fas fa-crown me-2"></i>SELAMAT! PEMENANG UNDIAN
                    </h5>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-4">
                        <i class="fas fa-trophy fa-4x text-warning"></i>
                    </div>
                    <h2 class="fw-bold mb-3" id="modalWinnerName">Nama Peserta</h2>
                    <div class="row text-start px-4">
                        <div class="col-6 mb-2 text-muted">NIK:</div>
                        <div class="col-6 mb-2 fw-bold" id="modalWinnerNik">-</div>
                        
                        <div class="col-6 mb-2 text-muted">Nomor Undian:</div>
                        <div class="col-6 mb-2 fw-bold" id="modalWinnerLottery">-</div>

                        <div class="col-6 mb-2 text-muted">Shift:</div>
                        <div class="col-6 mb-2 fw-bold" id="modalWinnerShift">-</div>

                        <div class="col-6 mb-2 text-muted">Jumlah Lelang:</div>
                        <div class="col-6 mb-2 fw-bold" id="modalWinnerBidAmount">-</div>
                        
                        <div class="col-6 mb-2 text-muted">Total Didapat:</div>
                        <div class="col-6 mb-2 fw-bold text-success fs-5" id="modalWinnerTotal">-</div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary btn-lg px-5" data-bs-dismiss="modal">TUTUP</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        let isSpinning = false;
        let selectedWinner = null;
        const winnerCount = {{ $winnerCount }};
        const totalBidders = {{ $highestBidders->count() }};
        const winnerReceives = {{ $winnerReceives }};
        const biddersForWheel = @json($biddersForWheel);
        let lastWinnerIndex = null;

        document.addEventListener('DOMContentLoaded', function() {
            fitCanvasToWheel();
            drawWheel();
        });

        window.addEventListener('resize', function() {
            fitCanvasToWheel();
            drawWheel();
        });

        function fitCanvasToWheel() {
            const wheel = document.getElementById('spinnerWheel');
            const canvas = document.getElementById('wheelCanvas');
            if (!wheel || !canvas) return;

            const size = Math.floor(Math.min(wheel.clientWidth, wheel.clientHeight));
            if (size <= 0) return;

            canvas.width = size;
            canvas.height = size;
        }

        const spinAudio = new Audio('{{ asset('storage/spinning-wheel.mp3') }}');

        function drawWheel() {
            const canvas = document.getElementById('wheelCanvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const size = canvas.width;
            const center = size / 2;
            const radius = center - 5; // Slight padding

            ctx.clearRect(0, 0, size, size);

            if (!biddersForWheel || biddersForWheel.length === 0) return;

            const segmentAngle = (Math.PI * 2) / biddersForWheel.length;
            
            // Palette: Red, Blue, Green, Yellow (Alternating)
            // Using slightly adjusted colors for better aesthetic but solid look
            const palette = ['#d32f2f', '#1976d2', '#66bb6a', '#fbc02d'];
            // Text color: White for Red/Blue, Black for Green/Yellow
            const textColors = ['#ffffff', '#ffffff', '#000000', '#000000'];

            biddersForWheel.forEach((bidder, i) => {
                const color = palette[i % 4];
                const textColor = textColors[i % 4];
                
                const startAngle = i * segmentAngle;
                const endAngle = startAngle + segmentAngle;

                // Draw Segment
                ctx.beginPath();
                ctx.moveTo(center, center);
                ctx.arc(center, center, radius, startAngle, endAngle);
                ctx.closePath();
                ctx.fillStyle = color;
                ctx.fill();
                
                // Segment Border (White divider)
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 2;
                ctx.stroke();

                // Draw Text
                ctx.save();
                ctx.translate(center, center);
                // Rotate to the center of the segment
                ctx.rotate(startAngle + segmentAngle / 2);
                ctx.textAlign = "right";
                ctx.textBaseline = "middle";
                ctx.fillStyle = textColor;
                
                // Calculate font size based on segment count (Dynamic based on size)
                // Adjusted to ensure text fits comfortably within segments
                let fontSize = Math.floor(size / 20);
                if (biddersForWheel.length > 20) fontSize = Math.floor(size / 35);
                else if (biddersForWheel.length > 10) fontSize = Math.floor(size / 25);

                ctx.font = `bold ${fontSize}px Arial, sans-serif`;

                let text =  bidder.lottery_number;
                // Simple truncation
                if (text.length > 15) text = text.substring(0, 15) + '...';
                
                // Text Outline Logic for Readability
                ctx.lineWidth = 3;
                if (textColor === '#ffffff') {
                    ctx.strokeStyle = 'rgba(0, 0, 0, 0.8)'; // Dark outline for white text
                } else {
                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.9)'; // White outline for black text
                }
                ctx.lineJoin = 'round';
                ctx.strokeText(text, radius - 20, 0);

                // Position text at the edge, reading inwards
                ctx.fillText(text, radius - 20, 0);
                
                ctx.restore();
            });

            // Draw Center Circle (White Hole)
            ctx.beginPath();
            ctx.arc(center, center, radius * 0.2, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();
            ctx.strokeStyle = '#e0e0e0';
            ctx.lineWidth = 2;
            ctx.stroke();
        }

        function startSpin() {
            if (isSpinning) return;
            
            isSpinning = true;
            const spinButton = document.getElementById('spinButton');
            const spinnerWheel = document.getElementById('spinnerWheel');
            
            spinButton.disabled = true;
            spinButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengundi...';
            
            // Reset transform before spinning to ensure smooth animation
            spinnerWheel.style.transition = 'none';
            spinnerWheel.style.transform = 'rotate(0deg)';
            
            // Force reflow to apply the reset
            spinnerWheel.offsetHeight;
            
            // Re-enable transition for the spin animation
            spinnerWheel.style.transition = 'transform 15s cubic-bezier(0.2, 0.5, 0.1, 1)';
            
            // Play Audio
            spinAudio.currentTime = 0;
            spinAudio.play().catch(e => console.log('Audio play failed:', e));
            
            // Determine random rotation
            // We want it to spin for 15 seconds.
            // 15 seconds spin with many rotations to keep it fast initially.
            const minSpins = 40; // More spins for 15 seconds to keep it looking fast
            const randomDegree = Math.floor(Math.random() * 360);
            const totalDegrees = (minSpins * 360) + randomDegree;
            
            // Apply CSS transform
            spinnerWheel.style.transform = `rotate(${totalDegrees}deg)`;
            
            setTimeout(() => {
                // Calculate Winner
                // The pointer is at 0 degrees (Right).
                // Rotation is Clockwise.
                const finalAngle = totalDegrees % 360;
                const normalizedAngle = (360 - finalAngle) % 360;
                
                const segmentAngle = 360 / totalBidders;
                const winnerIndex = Math.floor(normalizedAngle / segmentAngle);
                
                // Update UI state
                lastWinnerIndex = winnerIndex;
                const winnerData = biddersForWheel[winnerIndex];
                
                // Show Winner Modal
                document.getElementById('modalWinnerName').innerText = winnerData.name;
                document.getElementById('modalWinnerNik').innerText = winnerData.nik;
                document.getElementById('modalWinnerLottery').innerText = winnerData.lottery_number;
                document.getElementById('modalWinnerShift').innerText = winnerData.shift;
                document.getElementById('modalWinnerBidAmount').innerText = 'Rp ' + winnerData.bid_amount.toLocaleString('id-ID');
                document.getElementById('modalWinnerTotal').innerText = 'Rp ' + winnerReceives.toLocaleString('id-ID');
                
                const winnerModal = new bootstrap.Modal(document.getElementById('winnerModal'));
                winnerModal.show();

                // Festive Confetti Effect
                triggerFestiveConfetti();

                // Select in form
                const checkboxes = document.querySelectorAll('input[name="winners[]"]');
                if (checkboxes[winnerIndex] && !checkboxes[winnerIndex].disabled) {
                    checkboxes[winnerIndex].checked = true;
                }
                
                // Stop Audio
                spinAudio.pause();
                spinAudio.currentTime = 0;
                
                // Store the final rotation for next spin
                spinnerWheel.style.transition = 'none';
                spinnerWheel.style.transform = `rotate(${finalAngle}deg)`;
                
                spinButton.disabled = false;
                spinButton.innerHTML = '<i class="fas fa-redo me-2"></i>Undi Lagi';
                isSpinning = false;
                
                checkSelection();
            }, 15000); // 15s matches transition
        }

        function triggerFestiveConfetti() {
            var duration = 5 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 2000 };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                // since particles fall down, start a bit higher than random
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }

        function checkSelection() {
            const checkboxes = document.querySelectorAll('input[name="winners[]"]:checked');
            const submitButton = document.getElementById('submitWinners');
            
            if (checkboxes.length === winnerCount) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }

        // Add event listeners to checkboxes
        document.querySelectorAll('input[name="winners[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', checkSelection);
        });

        // Form validation
        document.getElementById('winnerForm').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="winners[]"]:checked');
            if (checkboxes.length !== winnerCount) {
                e.preventDefault();
                alert(`Anda harus memilih exactly ${winnerCount} pemenang!`);
                return;
            }

            // Also validate that witnesses are selected if you want
            const saksiIds = document.getElementById('final_saksi_ids').value;
            if (!saksiIds) {
                if(!confirm('Anda belum memilih saksi. Lanjutkan tanpa saksi?')) {
                    e.preventDefault();
                }
            }
        });

        // --- Saksi Selection Logic ---
        const groupFilter = document.getElementById('groupFilter');
        const nameSearch = document.getElementById('nameSearch');
        const saksiSelector = document.getElementById('saksi_selector');
        const resetFilters = document.getElementById('resetFilters');
        const addSaksiBtn = document.getElementById('addSaksiBtn');
        const saksiPreview = document.getElementById('saksiPreview');
        const selectedSaksiList = document.getElementById('selectedSaksiList');
        const finalSaksiIdsInput = document.getElementById('final_saksi_ids');

        let selectedSaksiIds = new Set();
        const allSaksiOptions = Array.from(saksiSelector.options).slice(1); // skip first placeholder

        function filterSaksi() {
            const groupVal = groupFilter.value;
            const searchVal = nameSearch.value.toLowerCase();

            // Clear selector
            saksiSelector.innerHTML = '<option value="">-- Pilih Peserta --</option>';

            allSaksiOptions.forEach(opt => {
                const optGroup = opt.getAttribute('data-group-id');
                const optName = opt.getAttribute('data-nama').toLowerCase();
                const optNik = (opt.getAttribute('data-nik') || '').toLowerCase();
                const optId = opt.value;

                // Don't show if already selected
                if (selectedSaksiIds.has(optId)) return;

                const matchGroup = !groupVal || optGroup === groupVal;
                const matchSearch = !searchVal || optName.includes(searchVal) || optNik.includes(searchVal);

                if (matchGroup && matchSearch) {
                    saksiSelector.appendChild(opt.cloneNode(true));
                }
            });
        }

        groupFilter.addEventListener('change', filterSaksi);
        nameSearch.addEventListener('input', filterSaksi);
        resetFilters.addEventListener('click', () => {
            groupFilter.value = '';
            nameSearch.value = '';
            filterSaksi();
        });

        function updateSaksiUI() {
            if (selectedSaksiIds.size === 0) {
                saksiPreview.style.display = 'none';
                finalSaksiIdsInput.value = '';
            } else {
                saksiPreview.style.display = 'block';
                selectedSaksiList.innerHTML = '';
                
                const idsArray = Array.from(selectedSaksiIds);
                finalSaksiIdsInput.value = idsArray.join(',');

                idsArray.forEach(id => {
                    const opt = allSaksiOptions.find(o => o.value === id);
                    if (opt) {
                        const nama = opt.getAttribute('data-nama');
                        const groupName = opt.getAttribute('data-group-name');
                        const nik = opt.getAttribute('data-nik');

                        const div = document.createElement('div');
                        div.className = 'd-flex justify-content-between align-items-center bg-white p-2 rounded border mb-2 shadow-sm';
                        div.innerHTML = `
                            <div>
                                <span class="fw-bold">${nama}</span> 
                                <span class="badge bg-secondary ms-2">${groupName}</span>
                                <small class="text-muted ms-2">NIK: ${nik}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-saksi" data-id="${id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                        selectedSaksiList.appendChild(div);
                    }
                });

                // Re-bind remove buttons
                document.querySelectorAll('.remove-saksi').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        selectedSaksiIds.delete(id);
                        updateSaksiUI();
                        filterSaksi();
                    });
                });
            }
        }

        addSaksiBtn.addEventListener('click', () => {
            const val = saksiSelector.value;
            if (val) {
                selectedSaksiIds.add(val);
                updateSaksiUI();
                saksiSelector.value = '';
                filterSaksi();
            } else {
                alert('Pilih saksi terlebih dahulu dari daftar.');
            }
        });

        // Initialize filter
        filterSaksi();
    </script>
</body>
</html>