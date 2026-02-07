@extends('layouts.admin')

@section('title', 'Penawaran Periode - Sistem Arisan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.periods') }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-gavel me-2 text-primary"></i>Penawaran Periode
        </h1>
        <p class="text-muted mb-0">{{ $period->period_name }} - {{ $period->group->name }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

                <div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informasi Periode
                </h5>
            </div>
                            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Status</h6>
                                            @switch($period->status)
    @case('active')
        <span class="badge bg-primary fs-6">Aktif</span>
        @break
    @case('bidding')
        <span class="badge bg-warning fs-6">Bidding</span>
        @break
    @case('drawing')
        <span class="badge bg-info fs-6">Drawing</span>
        @break
    @case('completed')
        <span class="badge bg-success fs-6">Selesai</span>
        @break
    @default
        <span class="badge bg-secondary fs-6">{{ $period->status }}</span>
@endswitch
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Total Penawaran</h6>
                            <h4 class="text-primary">{{ $bids->count() }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Penawaran Tertinggi</h6>
                            <h4 class="text-success">Rp {{ number_format($bids->first()?->bid_amount ?? 0, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Dana Tersedia</h6>
                            <h4 class="text-info">Rp {{ number_format($period->available_funds, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-gavel me-2"></i>Daftar Penawaran
        </h5>
        @if($period->status === 'bidding' && $bids->count() > 0)
            <a href="{{ route('admin.draw.start', $period->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-dice me-1"></i>Mulai Pengundian
            </a>
        @endif
    </div>
                    <div class="card-body">
    @if($bids->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Peserta</th>
                        <th>Nomor Undian</th>
                        <th>Jumlah Penawaran</th>
                        <th>Hadiah Akhir</th>
                        <th>Waktu Penawaran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bids as $index => $bid)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $bid->participant->name ?? '-' }}</strong>
                            @if($bid->participant->has_won)
                                <br><small class="text-success"><i class="fas fa-trophy"></i> Pernah Menang</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $bid->participant->lottery_number ?? '-' }}</span>
                        </td>
                        <td class="fw-bold text-primary">
                            Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                        </td>
                        <td class="fw-bold text-success">
                            Rp {{ number_format($period->group->main_prize - $bid->bid_amount, 0, ',', '.') }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($bid->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if($bid->participant->has_won)
                                <span class="badge bg-success">Menang</span>
                            @else
                                <span class="badge bg-info">Aktif</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h6><i class="fas fa-chart-bar me-2"></i>Statistik Penawaran</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Rata-rata Penawaran</h6>
                            <h4 class="text-primary">Rp {{ number_format($bids->avg('bid_amount'), 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Penawaran Terendah</h6>
                            <h4 class="text-warning">Rp {{ number_format($bids->min('bid_amount'), 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-gavel fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Belum Ada Penawaran</h4>
            <p class="text-muted">Peserta belum melakukan penawaran untuk periode ini</p>
            
            @if($period->status === 'active')
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Catatan:</strong> Periode masih aktif. Peserta dapat melakukan penawaran melalui portal peserta.
                </div>
            @endif
        </div>
    @endif
</div>
</div>

@if($period->winners->count() > 0)
<div class="card mt-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-trophy me-2"></i>Daftar Pemenang Periode Ini
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Pemenang</th>
                        <th>Nomor Undian</th>
                        <th>Hadiah Utama</th>
                        <th>Jumlah Penawaran</th>
                        <th>Hadiah Akhir</th>
                        <th>Waktu Menang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($period->winners as $index => $winner)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $winner->participant->name ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $winner->participant->lottery_number ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $winner->participant->lottery_number ?? '-' }}</span>
                        </td>
                        <td class="fw-bold">Rp {{ number_format($winner->main_prize, 0, ',', '.') }}</td>
                        <td class="fw-bold text-primary">Rp {{ number_format($winner->bid_amount, 0, ',', '.') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($winner->final_prize, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($winner->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
