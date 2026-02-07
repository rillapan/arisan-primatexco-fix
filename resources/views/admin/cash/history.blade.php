<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Kas - {{ $group->name }}</title>
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
            <li class="breadcrumb-item active">Riwayat Kas</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-history me-2"></i>
            Riwayat Kas - {{ $group->name }}
        </h2>
        <div>
            <a href="{{ route('admin.cash.dashboard', $group->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Periode</h6>
                            <h4 class="mb-0">{{ $history->count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
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
                            <h6 class="card-title">Saldo Akhir</h6>
                            <h4 class="mb-0">
                                @if($history->count() > 0)
                                    Rp {{ number_format($history->last()['accumulated_balance'], 0, ',', '.') }}
                                @else
                                    Rp 0
                                @endif
                            </h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-wallet fa-2x"></i>
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
                            <h6 class="card-title">Total Sisa Bulanan</h6>
                            <h4 class="mb-0">
                                Rp {{ number_format($history->sum('monthly_net'), 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h6 class="card-title">Periode 2 Pemenang</h6>
                            <h4 class="mb-0">{{ $history->where('winner_count', 2)->count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-trophy fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Accumulation History Table -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Tabel Riwayat Akumulasi Kas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Periode</th>
                            <th>Saldo Awal</th>
                            <th>Sisa Bulanan</th>
                            <th>Saldo Akumulasi</th>
                            <th>Status</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $index => $item)
                        <tr>
                            <td><strong>{{ $item['period_name'] }}</strong></td>
                            <td>Rp {{ number_format($item['previous_balance'], 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">+Rp {{ number_format($item['monthly_net'], 0, ',', '.') }}</td>
                            <td class="fw-bold">
                                Rp {{ number_format($item['accumulated_balance'], 0, ',', '.') }}
                                @if($item['accumulated_balance'] >= 17500000)
                                    <i class="fas fa-star text-warning ms-1" title="Cukup untuk 2 pemenang"></i>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $item['winner_count'] == 2 ? 'success' : 'primary' }}">
                                    {{ $item['winner_count'] }} Pemenang
                                </span>
                            </td>
                            <td>
                                @if($index == 0)
                                    <span class="badge bg-secondary">Awal</span>
                                @elseif($item['accumulated_balance'] > $history[$index-1]['accumulated_balance'])
                                    <span class="badge bg-success">
                                        <i class="fas fa-arrow-up me-1"></i>
                                        +Rp {{ number_format($item['accumulated_balance'] - $history[$index-1]['accumulated_balance'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Stabil</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cash Accumulation Chart -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-area me-2"></i>
                Grafik Akumulasi Kas
            </h5>
        </div>
        <div class="card-body">
            <canvas id="cashAccumulationChart" height="100"></canvas>
        </div>
    </div>

    <!-- Future Projections -->
    @if($projections)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-crystal-ball me-2"></i>
                Proyeksi 12 Bulan Kedepan
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
                            <td class="fw-bold">
                                Rp {{ number_format($projection['projected_balance'], 0, ',', '.') }}
                                @if($projection['can_have_two_winners'])
                                    <i class="fas fa-star text-warning ms-1" title="Cukup untuk 2 pemenang"></i>
                                @endif
                            </td>
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

    <!-- Analysis Summary -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-pie me-2"></i>
                Analisis Ringkasan
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-info-circle me-2"></i>Statistik Kas:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Rata-rata Sisa Bulanan:</strong> 
                            Rp {{ number_format($history->avg('monthly_net'), 0, ',', '.') }}</li>
                        <li><strong>Total Akumulasi:</strong> 
                            Rp {{ number_format($history->last()['accumulated_balance'] ?? 0, 0, ',', '.') }}</li>
                        <li><strong>Bulan ke 2 Pemenang Pertama:</strong> 
                            {{ $history->where('winner_count', 2)->first()['period_name'] ?? 'Belum' }}</li>
                        <li><strong>Efficiency Rate:</strong> 
                            {{ number_format(($history->sum('monthly_net') / ($history->sum('previous_balance') + $history->sum('monthly_net'))) * 100, 1) }}%</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-lightbulb me-2"></i>Insights:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Pattern:</strong> 
                            @if($history->where('winner_count', 2)->count() > 0)
                                Sistem sudah mencapai fase 2 pemenang
                            @else
                                Masih dalam fase akumulasi 1 pemenang
                            @endif
                        </li>
                        <li><strong>Growth:</strong> 
                            @if($history->count() > 1)
                                @php
                                    $growth = $history->last()['accumulated_balance'] - $history->first()['accumulated_balance'];
                                @endphp
                                +Rp {{ number_format($growth, 0, ',', '.') }} dari awal
                            @else
                                Data terbatas untuk analisis
                            @endif
                        </li>
                        <li><strong>Projection:</strong> 
                            @if($projections->first()['can_have_two_winners'])
                                Siap untuk 2 pemenang bulan depan
                            @else
                                Butuh {{ $projections->where('can_have_two_winners', true)->first()['month'] ?? 'banyak' }} bulan lagi
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('cashAccumulationChart');
    if (ctx) {
        const labels = @json($history->pluck('period_name'));
        const data = @json($history->pluck('accumulated_balance'));
        const previousBalances = @json($history->pluck('previous_balance'));
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Saldo Akumulasi',
                        data: data,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'Saldo Awal',
                        data: previousBalances,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderDash: [5, 5],
                        tension: 0.1,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Grafik Akumulasi Kas Per Periode'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
</body>
</html>
