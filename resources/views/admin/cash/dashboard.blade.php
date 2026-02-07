<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kas - {{ $group->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-gift me-2"></i>
                Sistem Arisan Admin
            </a>
            <div class="navbar-nav ms-auto">
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link text-decoration-none">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.groups') }}">Kelompok</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.groups.manage', $group->id) }}">{{ $group->name }}</a></li>
            <li class="breadcrumb-item active">Kelola Kas</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-coins me-2"></i>
            Kelola Kas - {{ $group->name }}
        </h2>
        <div>
            <a href="{{ route('admin.cash.history', $group->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-history me-1"></i> Riwayat Kas
            </a>
            @if($currentPeriod)
                <a href="{{ route('admin.cash.period', [$group->id, $currentPeriod->id]) }}" class="btn btn-primary">
                    <i class="fas fa-chart-line me-1"></i> Detail Periode
                </a>
            @endif
        </div>
    </div>

    <!-- Current Period Overview -->
    @if($currentPeriod && $cashAnalysis)
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card" style="background: linear-gradient(135deg, #e8f4f8 0%, #d1e7dd 100%); border: 1px solid #b8daff;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title" style="color: #0c5460;">Saldo Kas Saat Ini</h6>
                                <h4 class="mb-0" style="color: #0c5460;">Rp {{ number_format($cashAnalysis['cash_flow']['previous_cash_balance'], 0, ',', '.') }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-wallet fa-2x" style="color: #0c5460;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" style="background: linear-gradient(135deg, #e7f5e7 0%, #d4edda 100%); border: 1px solid #c3e6cb;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title" style="color: #155724;">Dana Tersedia</h6>
                                <h4 class="mb-0" style="color: #155724;">Rp {{ number_format($cashAnalysis['cash_flow']['available_funds'], 0, ',', '.') }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-money-bill-wave fa-2x" style="color: #155724;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" style="background: linear-gradient(135deg, #e6f7ff 0%, #d1ecf1 100%); border: 1px solid #bee5eb;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title" style="color: #0c5460;">Jumlah Pemenang</h6>
                                <h4 class="mb-0" style="color: #0c5460;">{{ $cashAnalysis['cash_flow']['winner_count'] }} Orang</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-trophy fa-2x" style="color: #0c5460;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 1px solid #ffeaa7;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title" style="color: #856404;">Total Bid</h6>
                                <h4 class="mb-0" style="color: #856404;">Rp {{ number_format($cashAnalysis['bids']['total_amount'], 0, ',', '.') }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-gavel fa-2x" style="color: #856404;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Flow Breakdown - New Format -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Rincian Aliran Kas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Deskripsi</th>
                                        <th>Perhitungan</th>
                                        <th>Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Saldo Awal</td>
                                        <td>(Dari Periode Lalu)</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($cashAnalysis['cash_flow']['previous_cash_balance'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Iuran</td>
                                        <td>(90 × Rp175.000)</td>
                                        <td class="text-end text-success">+ Rp {{ number_format($cashAnalysis['cash_flow']['total_installments'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nilai Bid</td>
                                        <td>(Input Admin)</td>
                                        <td class="text-end text-success">+ Rp {{ number_format($cashAnalysis['bids']['total_amount'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Harga Motor</td>
                                        <td>(Statik)</td>
                                        <td class="text-end text-danger">- Rp {{ number_format($cashAnalysis['cash_flow']['main_prize'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Potongan SHU</td>
                                        <td>(Statik)</td>
                                        <td class="text-end text-danger">- Rp {{ number_format($cashAnalysis['cash_flow']['admin_fee'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><strong>SISA KAS BULAN INI</strong></td>
                                        <td><strong>(Iuran + Bid) - (Motor + SHU)</strong></td>
                                        <td class="text-end fw-bold">Rp {{ number_format($cashAnalysis['remaining_cash'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>TOTAL AKUMULASI</strong></td>
                                        <td><strong>Saldo Awal + Sisa Bulan Ini</strong></td>
                                        <td class="text-end fw-bold">Rp {{ number_format($cashAnalysis['cash_flow']['previous_cash_balance'] + $cashAnalysis['remaining_cash'], 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Projections -->
        @if($projections)
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Proyeksi Akumulasi Kas (6 Bulan Kedepan)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bulan ke-</th>
                                <th>Saldo Proyeksi</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projections as $projection)
                            <tr>
                                <td>{{ $projection['month'] }}</td>
                                <td>Rp {{ number_format($projection['projected_balance'], 0, ',', '.') }}</td>
                                <td>
                                    @if($projection['can_have_two_winners'])
                                        <span class="badge bg-success">2 Pemenang</span>
                                    @else
                                        <span class="badge bg-primary">1 Pemenang</span>
                                    @endif
                                </td>
                                <td>
                                    @if($projection['can_have_two_winners'])
                                        <small class="text-success">Cukup untuk 2 pemenang</small>
                                    @else
                                        <small class="text-muted">Masih 1 pemenang</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Belum ada periode aktif. Silakan buat periode baru untuk mulai mengelola kas.
        </div>
    @endif

    <!-- Update Bid Section with Live Calculation -->
    @if($currentPeriod)
    <div class="card mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">
                <i class="fas fa-gavel me-2"></i>
                Update Nilai Lelang - {{ $currentPeriod->period_name }}
            </h5>
        </div>
        <div class="card-body">
            <!-- Live Calculation Display -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-calculator me-2"></i>Perhitungan Real-Time</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <small>Saldo Awal:</small><br>
                                <strong>Rp {{ number_format($cashAnalysis['cash_flow']['previous_cash_balance'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small>Total Iuran:</small><br>
                                <strong class="text-success">Rp {{ number_format($cashAnalysis['cash_flow']['total_installments'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small>Nilai Bid Saat Ini:</small><br>
                                <strong class="text-success" id="currentBidDisplay">Rp {{ number_format($cashAnalysis['bids']['total_amount'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small>Total Akumulasi:</small><br>
                                <strong class="text-primary" id="totalAccumulationDisplay">Rp {{ number_format($cashAnalysis['cash_flow']['previous_cash_balance'] + $cashAnalysis['remaining_cash'], 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="updateBidForm" onsubmit="updateBid(event)">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <label for="participantSelect" class="form-label">Pilih Peserta</label>
                        <select class="form-select" id="participantSelect" name="participant_id" required>
                            <option value="">-- Pilih Peserta --</option>
                            @foreach($participants as $participant)
                                <option value="{{ $participant->id }}">{{ $participant->name }} ({{ $participant->lottery_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="bidAmount" class="form-label">Nilai Bid (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="bidAmount" name="bid_amount" 
                                   placeholder="2250000" min="2250000" max="17500000" step="10000" required oninput="calculateLive()">
                        </div>
                        <small class="text-muted">Minimal: Rp 2.250.000, Maksimal: Rp 17.500.000</small>
                    </div>
                    <div class="col-md-4">
                        <label for="bidTime" class="form-label">Waktu Bid</label>
                        <input type="datetime-local" class="form-control" id="bidTime" name="bid_time" required>
                    </div>
                </div>
                
                <!-- Live Calculation Results -->
                <div class="row mt-3" id="liveCalculationResults" style="display: none;">
                    <div class="col-md-12">
                        <div class="card" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 1px solid #dee2e6;">
                            <div class="card-body">
                                <h6 class="card-title" style="color: #495057;">Proyeksi Perhitungan:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Pemasukan:</td>
                                                <td class="text-end">Rp <span id="incomeProjection">{{ number_format($cashAnalysis['cash_flow']['total_installments'], 0, ',', '.') }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>Pengeluaran:</td>
                                                <td class="text-end">Rp <span id="expenseProjection">{{ number_format($cashAnalysis['cash_flow']['main_prize'] + $cashAnalysis['cash_flow']['admin_fee'], 0, ',', '.') }}</span></td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td>Sisa Bersih:</td>
                                                <td class="text-end">Rp <span id="netProjection">{{ number_format($cashAnalysis['remaining_cash'], 0, ',', '.') }}</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-{{ $cashAnalysis['cash_flow']['total_installments'] < 15750000 ? 'warning' : 'success' }}" id="paymentStatusAlert">
                                            <i class="fas fa-{{ $cashAnalysis['cash_flow']['total_installments'] < 15750000 ? 'exclamation-triangle' : 'check-circle' }} me-2"></i>
                                            <span id="paymentStatusText">
                                                @if($cashAnalysis['cash_flow']['total_installments'] < 15750000)
                                                    Perhatian: Angsuran belum lunas semua, saldo kas mungkin tidak akurat.
                                                @else
                                                    Semua peserta telah membayar iuran.
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-plus me-2"></i>Update Bid
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetBidForm()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </form>
            
            <div id="bidResult" class="mt-3"></div>
        </div>
    </div>
    @endif

    <!-- Daftar Peserta dan Status Pembayaran -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Daftar Peserta dan Status Pembayaran Bulan Ini
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card" style="background: linear-gradient(135deg, #e7f5e7 0%, #d4edda 100%); border: 1px solid #c3e6cb;">
                            <div class="card-body text-center">
                                <h6 style="color: #155724;">Sudah Bayar</h6>
                                <h4 style="color: #155724;">{{ $participants->where('payment_status', 'paid')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 1px solid #ffeaa7;">
                            <div class="card-body text-center">
                                <h6 style="color: #856404;">Belum Bayar</h6>
                                <h4 style="color: #856404;">{{ $participants->where('payment_status', 'unpaid')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card" style="background: linear-gradient(135deg, #e6f7ff 0%, #d1ecf1 100%); border: 1px solid #bee5eb;">
                            <div class="card-body text-center">
                                <h6 style="color: #0c5460;">Total Peserta</h6>
                                <h4 style="color: #0c5460;">{{ $participants->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card" style="background: linear-gradient(135deg, #e8f4f8 0%, #d1e7dd 100%); border: 1px solid #b8daff;">
                            <div class="card-body text-center">
                                <h6 style="color: #0c5460;">% Pembayaran</h6>
                                <h4 style="color: #0c5460;">{{ $participants->count() > 0 ? round(($participants->where('payment_status', 'paid')->count() / $participants->count()) * 100, 1) : 0 }}%</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Undi</th>
                                <th>Nama Peserta</th>
                                <th>Departemen</th>
                                <th>Shift</th>
                                <th>Status Pembayaran</th>
                                <th>Jumlah Angsuran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $participant)
                            <tr>
                                <td>{{ $participant->lottery_number }}</td>
                                <td>{{ $participant->name }}</td>
                                <td>{{ $participant->department }}</td>
                                <td>{{ $participant->shift }}</td>
                                <td>
                                    @if($participant->payment_status == 'paid')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Sudah Bayar
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($participant->payment_status == 'paid')
                                        <span class="fw-bold text-success">Rp {{ number_format($participant->monthly_installment, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">Rp {{ number_format($participant->monthly_installment, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($participant->payment_status == 'unpaid')
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="markAsPaid({{ $participant->id }})">
                                            <i class="fas fa-check me-1"></i>Tandai Bayar
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewPaymentDetails({{ $participant->id }})">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Cash Accumulation History -->
    @if($history->count() > 0)
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                Riwayat Akumulasi Kas
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($history as $index => $item)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm h-100" style="background: linear-gradient(135deg, {{ $index % 4 == 0 ? '#e8f4f8' : ($index % 4 == 1 ? '#e7f5e7' : ($index % 4 == 2 ? '#e6f7ff' : '#fff3cd')) }} 0%, {{ $index % 4 == 0 ? '#d1e7dd' : ($index % 4 == 1 ? '#d4edda' : ($index % 4 == 2 ? '#d1ecf1' : '#ffeaa7')) }} 100%); border: 1px solid {{ $index % 4 == 0 ? '#b8daff' : ($index % 4 == 1 ? '#c3e6cb' : ($index % 4 == 2 ? '#bee5eb' : '#ffeaa7') }};">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-calendar-alt fa-2x" style="color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }};"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1" style="color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }};">{{ $item['period_name'] }}</h6>
                                        <small class="text-muted">Periode ke-{{ $index + 1 }}</small>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small style="color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }};">Saldo Awal</small>
                                        <div class="fw-bold" style="color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }};">Rp {{ number_format($item['previous_balance'], 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small style="color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }};">Sisa Bulanan</small>
                                        <div class="fw-bold text-success">+Rp {{ number_format($item['monthly_net'], 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small style="color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }};">Saldo Akumulasi</small>
                                        <div class="fw-bold" style="color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }};">Rp {{ number_format($item['accumulated_balance'], 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small style="color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }};">Pemenang</small>
                                        <div>
                                            <span class="badge" style="background-color: {{ $item['winner_count'] == 2 ? '#28a745' : '#007bff' }}; color: white;">
                                                {{ $item['winner_count'] }} Pemenang
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-sm btn-outline-primary" style="border-color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }}; color: {{ $index % 4 == 0 ? '#0c5460' : ($index % 4 == 1 ? '#155724' : ($index % 4 == 2 ? '#0c5460' : '#856404') }};">
                                                <i class="fas fa-eye me-1"></i>Lihat Detail
                                            </button>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i>Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Cash Calculator Modal -->
<div class="modal fade" id="cashCalculatorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calculator me-2"></i>
                    Kalkulator Kas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="bidAmount" class="form-label">Jumlah Bid (Rp)</label>
                    <input type="number" class="form-control" id="bidAmount" 
                           placeholder="Contoh: 6000000" min="2250000" max="17500000" step="10000">
                    <small class="text-muted">Minimal: Rp 2.250.000, Maksimal: Rp 17.500.000</small>
                </div>
                <div id="calculationResult" class="alert alert-info" style="display: none;">
                    <h6>Hasil Perhitungan:</h6>
                    <div id="resultContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="calculateCash()">Hitung</button>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button for Calculator -->
<button class="btn btn-primary btn-lg rounded-circle position-fixed bottom-0 end-0 m-4" 
        style="z-index: 1000;" data-bs-toggle="modal" data-bs-target="#cashCalculatorModal"
        title="Kalkulator Kas">
    <i class="fas fa-calculator"></i>
</button>
@endsection

@push('scripts')
<script>
function calculateCash() {
    const bidAmount = parseFloat(document.getElementById('bidAmount').value);
    const resultDiv = document.getElementById('calculationResult');
    const resultContent = document.getElementById('resultContent');
    
    if (!bidAmount || bidAmount < 2250000) {
        resultDiv.className = 'alert alert-danger';
        resultContent.innerHTML = 'Bid minimal adalah Rp 2.250.000';
        resultDiv.style.display = 'block';
        return;
    }
    
    if (bidAmount > 17500000) {
        resultDiv.className = 'alert alert-danger';
        resultContent.innerHTML = 'Bid tidak boleh melebihi Rp 17.500.000';
        resultDiv.style.display = 'block';
        return;
    }
    
    // Calculate based on the system logic
    const monthlyInstallment = 90 * 175000; // Rp 15.750.000
    const adminFee = 500000; // Rp 500.000 per winner
    const mainPrize = 17500000; // Rp 17.500.000
    
    const totalIncoming = monthlyInstallment + bidAmount;
    const availableFunds = totalIncoming - adminFee;
    const monthlyNet = availableFunds - mainPrize;
    
    resultDiv.className = 'alert alert-success';
    resultContent.innerHTML = `
        <table class="table table-sm">
            <tr>
                <td>Total Iuran:</td>
                <td class="text-end">Rp ${monthlyInstallment.toLocaleString('id-ID')}</td>
            </tr>
            <tr>
                <td>Bid Amount:</td>
                <td class="text-end">Rp ${bidAmount.toLocaleString('id-ID')}</td>
            </tr>
            <tr>
                <td>Total Pemasukan:</td>
                <td class="text-end">Rp ${totalIncoming.toLocaleString('id-ID')}</td>
            </tr>
            <tr>
                <td>Biaya Admin:</td>
                <td class="text-end">Rp ${adminFee.toLocaleString('id-ID')}</td>
            </tr>
            <tr class="border-top">
                <td><strong>Sisa Kas Bulanan:</strong></td>
                <td class="text-end fw-bold">Rp ${monthlyNet.toLocaleString('id-ID')}</td>
            </tr>
        </table>
    `;
    resultDiv.style.display = 'block';
}

// Auto-calculate on input
document.getElementById('bidAmount')?.addEventListener('input', function() {
    if (this.value) {
        calculateCash();
    }
});

// Live calculation function
function calculateLive() {
    const bidAmount = parseFloat(document.getElementById('bidAmount').value) || 0;
    const liveResults = document.getElementById('liveCalculationResults');
    const currentBidDisplay = document.getElementById('currentBidDisplay');
    const totalAccumulationDisplay = document.getElementById('totalAccumulationDisplay');
    const incomeProjection = document.getElementById('incomeProjection');
    const expenseProjection = document.getElementById('expenseProjection');
    const netProjection = document.getElementById('netProjection');
    
    // Constants from system
    const previousBalance = parseFloat({{ $cashAnalysis['cash_flow']['previous_cash_balance'] }});
    const totalInstallments = parseFloat({{ $cashAnalysis['cash_flow']['total_installments'] }});
    const currentTotalBids = parseFloat({{ $cashAnalysis['bids']['total_amount'] }});
    const mainPrize = parseFloat({{ $cashAnalysis['cash_flow']['main_prize'] }});
    const adminFee = parseFloat({{ $cashAnalysis['cash_flow']['admin_fee'] }});
    
    if (bidAmount > 0) {
        // Show live results
        liveResults.style.display = 'block';
        
        // Calculate projections with new bid
        const newTotalBids = currentTotalBids + bidAmount;
        const totalIncome = totalInstallments + newTotalBids;
        const totalExpense = mainPrize + adminFee;
        const netCash = totalIncome - totalExpense;
        const totalAccumulation = previousBalance + netCash;
        
        // Update displays
        currentBidDisplay.textContent = 'Rp ' + newTotalBids.toLocaleString('id-ID');
        totalAccumulationDisplay.textContent = 'Rp ' + totalAccumulation.toLocaleString('id-ID');
        
        incomeProjection.textContent = totalIncome.toLocaleString('id-ID');
        expenseProjection.textContent = totalExpense.toLocaleString('id-ID');
        netProjection.textContent = netCash.toLocaleString('id-ID');
        
        // Update payment status alert
        const paymentStatusAlert = document.getElementById('paymentStatusAlert');
        const paymentStatusText = document.getElementById('paymentStatusText');
        
        if (totalInstallments < 15750000) {
            paymentStatusAlert.className = 'alert alert-warning';
            paymentStatusText.textContent = 'Perhatian: Angsuran belum lunas semua, saldo kas mungkin tidak akurat.';
        } else {
            paymentStatusAlert.className = 'alert alert-success';
            paymentStatusText.textContent = 'Semua peserta telah membayar iuran.';
        }
    } else {
        // Hide live results
        liveResults.style.display = 'none';
        
        // Reset displays to current values
        currentBidDisplay.textContent = 'Rp ' + currentTotalBids.toLocaleString('id-ID');
        totalAccumulationDisplay.textContent = 'Rp ' + (previousBalance + {{ $cashAnalysis['remaining_cash'] }}).toLocaleString('id-ID');
    }
}

// Update Bid functionality
function updateBid(event) {
    event.preventDefault();
    
    const form = document.getElementById('updateBidForm');
    const formData = new FormData(form);
    const resultDiv = document.getElementById('bidResult');
    
    fetch(`/admin/groups/{{ $group->id }}/cash/update-bid`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Berhasil!</strong> Bid sebesar Rp ${data.bid_amount.toLocaleString('id-ID')} untuk ${data.participant_name} telah ditambahkan.
                    <br><small>Total bid periode ini: Rp ${data.total_bids.toLocaleString('id-ID')}</small>
                </div>
            `;
            form.reset();
            
            // Optionally refresh the page after 2 seconds to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong> Terjadi kesalahan saat memproses bid.
            </div>
        `;
    });
}

function resetBidForm() {
    document.getElementById('updateBidForm').reset();
    document.getElementById('bidResult').innerHTML = '';
}

// Set default datetime to current time
document.addEventListener('DOMContentLoaded', function() {
    const bidTimeInput = document.getElementById('bidTime');
    if (bidTimeInput) {
        const now = new Date();
        const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 16);
        bidTimeInput.value = localDateTime;
    }
});

// Mark as paid functionality (placeholder)
function markAsPaid(participantId) {
    // Implementation for marking participant as paid
    console.log('Mark as paid:', participantId);
}

// View payment details functionality (placeholder)
function viewPaymentDetails(participantId) {
    // Implementation for viewing payment details
    console.log('View payment details:', participantId);
}
</script>
</body>
</html>
