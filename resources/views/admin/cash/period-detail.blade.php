<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kas - {{ $period->period_name }}</title>
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
            <li class="breadcrumb-item"><a href="{{ route('admin.cash.dashboard', $group->id) }}">Kelola Kas</a></li>
            <li class="breadcrumb-item active">{{ $period->period_name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-chart-line me-2"></i>
            Detail Kas - {{ $period->period_name }}
        </h2>
        <div>
            <a href="{{ route('admin.cash.dashboard', $group->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('admin.cash.history', $group->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-history me-1"></i> Riwayat
            </a>
            <a href="{{ route('admin.groups.cash.month.print-all-receipts', [$group->id, $monthKey]) }}" class="btn btn-success">
                <i class="fas fa-print me-1"></i> Cetak Semua Bukti
            </a>
        </div>
    </div>

    <!-- Period Status -->
    <div class="alert alert-{{ $period->status == 'completed' ? 'success' : ($period->status == 'active' ? 'info' : 'warning') }} mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-2"></i>
                <strong>Status Periode:</strong> 
                <span class="badge bg-{{ $period->status == 'completed' ? 'success' : ($period->status == 'active' ? 'info' : 'warning') }}">
                    {{ ucfirst($period->status) }}
                </span>
            </div>
            <div>
                <small>
                    {{ $period->period_start->format('d M Y') }} - {{ $period->period_end->format('d M Y') }}
                </small>
            </div>
        </div>
    </div>

    <!-- Cash Flow Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Saldo Awal</h6>
                            <h4 class="mb-0">Rp {{ number_format($cashFlow['previous_cash_balance'], 0, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Iuran</h6>
                            <h4 class="mb-0">Rp {{ number_format($cashFlow['total_installments'], 0, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Dana Tersedia</h6>
                            <h4 class="mb-0">Rp {{ number_format($cashFlow['available_funds'], 0, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Sisa Kas</h6>
                            <h4 class="mb-0">Rp {{ number_format($analysis['remaining_cash'], 0, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-piggy-bank fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Cash Flow - New Card Format -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Input Detail Kas - Halaman Eksekusi
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
                                    <td class="text-end fw-bold">Rp {{ number_format($cashFlow['previous_cash_balance'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Total Iuran</td>
                                    <td>(90 × Rp175.000)</td>
                                    <td class="text-end text-success">+ Rp {{ number_format($cashFlow['total_installments'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Nilai Bid</td>
                                    <td>(Input Admin)</td>
                                    <td class="text-end text-success">+ Rp {{ number_format($analysis['bids']['total_amount'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Harga Motor</td>
                                    <td>(Statik)</td>
                                    <td class="text-end text-danger">- Rp {{ number_format($cashFlow['main_prize'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Potongan SHU</td>
                                    <td>(Statik)</td>
                                    <td class="text-end text-danger">- Rp {{ number_format($cashFlow['admin_fee'], 0, ',', '.') }}</td>
                                </tr>
                                <tr class="table-warning">
                                    <td><strong>SISA KAS BULAN INI</strong></td>
                                    <td><strong>(Iuran + Bid) - (Motor + SHU)</strong></td>
                                    <td class="text-end fw-bold">Rp {{ number_format($analysis['remaining_cash'], 0, ',', '.') }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>TOTAL AKUMULASI</strong></td>
                                    <td><strong>Saldo Awal + Sisa Bulan Ini</strong></td>
                                    <td class="text-end fw-bold">Rp {{ number_format($cashFlow['previous_cash_balance'] + $analysis['remaining_cash'], 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Live Calculation Warning -->
                    @if($cashFlow['total_installments'] < 15750000)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Angsuran belum lunas semua, saldo kas mungkin tidak akurat.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bids Details -->
    @if($analysis['bids']['details']->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Detail Bid Masuk
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No. Undi</th>
                            <th>Nama Peserta</th>
                            <th>Departemen</th>
                            <th>Shift</th>
                            <th>Nilai Bid</th>
                            <th>Status</th>
                            <th>Waktu Bid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analysis['bids']['details']->sortByDesc('bid_amount') as $bid)
                        <tr>
                            <td>{{ $bid->participant->lottery_number }}</td>
                            <td>{{ $bid->participant->name }}</td>
                            <td>{{ $bid->participant->department }}</td>
                            <td>{{ $bid->participant->shift }}</td>
                            <td class="fw-bold">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $bid->status == 'accepted' ? 'success' : ($bid->status == 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($bid->status) }}
                                </span>
                            </td>
                            <td>{{ $bid->bid_time->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Winners Details -->
    @if($analysis['winners']['details']->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-trophy me-2"></i>
                Detail Pemenang
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No. Undi</th>
                            <th>Nama Peserta</th>
                            <th>Departemen</th>
                            <th>Shift</th>
                            <th>Hadiah Utama</th>
                            <th>Nilai Bid</th>
                            <th>Hadiah Akhir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analysis['winners']['details'] as $winner)
                        <tr>
                            <td>{{ $winner->participant->lottery_number }}</td>
                            <td>{{ $winner->participant->name }}</td>
                            <td>{{ $winner->participant->department }}</td>
                            <td>{{ $winner->participant->shift }}</td>
                            <td>Rp {{ number_format($winner->main_prize, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($winner->bid_amount, 0, ',', '.') }}</td>
                            <td class="fw-bold text-success">Rp {{ number_format($winner->final_prize, 0, ',', '.') }}</td>
                            <td>
                                @if($winner->needs_draw)
                                    <span class="badge bg-info">Diundi</span>
                                @else
                                    <span class="badge bg-success">Langsung</span>
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

    <!-- Future Projections -->
    @if($projections)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-line me-2"></i>
                Proyeksi Kas Kedepan
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

    <!-- Calculation Explanation -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Penjelasan Perhitungan
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-calculator me-2"></i>Formula Perhitungan:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Total Iuran:</strong> 90 peserta × Rp175.000 = Rp15.750.000</li>
                        <li><strong>Biaya Admin:</strong> Jumlah pemenang × Rp500.000</li>
                        <li><strong>Dana Tersedia:</strong> Saldo awal + Total iuran - Biaya admin</li>
                        <li><strong>Sisa Kas:</strong> Dana tersedia - Total hadiah yang diberikan</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-chart-pie me-2"></i>Logika Pemenang:</h6>
                    <ul class="list-unstyled">
                        <li><strong>1 Pemenang:</strong> Jika saldo kas < Rp17.500.000</li>
                        <li><strong>2 Pemenang:</strong> Jika saldo kas ≥ Rp17.500.000</li>
                        <li><strong>Hadiah Akhir:</strong> Harga pokok motor - Nilai bid</li>
                        <li><strong>Akumulasi:</strong> Sisa kas bulanan ditambahkan ke saldo berikutnya</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>
