@extends('layouts.admin')

@section('title', 'Detail Periode - ' . $period->period_name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h3 class="mb-0"><i class="fas fa-calendar me-2"></i>Detail Periode</h3>
        <p class="text-muted mb-0">{{ $period->period_name }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informasi Periode
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Nama Periode:</strong></td>
                        <td>{{ $period->period_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Kelompok:</strong></td>
                        <td>{{ $period->group->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Mulai:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($period->period_start)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Selesai:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($period->period_end)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @switch($period->status)
                                @case('active')
                                    <span class="badge bg-primary">Aktif</span>
                                    @break
                                @case('bidding')
                                    <span class="badge bg-warning">Bidding</span>
                                    @break
                                @case('drawing')
                                    <span class="badge bg-info">Drawing</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Selesai</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $period->status }}</span>
                            @endswitch
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Informasi Keuangan (Informasi Kas yang Digunakan)
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary bg-opacity-10">
                                <h6 class="mb-0 text-primary"><i class="fas fa-arrow-down me-2"></i>(Inflow/Masuk)</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Realisasi Setoran (Sekarang):</small>
                                    <p class="mb-1 fw-bold text-primary">Rp {{ number_format($period->total_installments, 0, ',', '.') }}</p>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Nilai Lelang (Bid):</small>
                                    <p class="mb-1 fw-bold text-success">Rp {{ number_format($highestBid, 0, ',', '.') }}</p>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Akumulasi Kas (Bulan Lalu):</small>
                                    <p class="mb-1 fw-bold text-info">Rp {{ number_format($period->previous_cash_balance, 0, ',', '.') }}</p>
                                </div>
                                <div class="mb-0">
                                    <small class="text-muted">Dana Iuran Bersih (Realisasi):</small>
                                    <p class="mb-0 fw-bold text-primary">
                                        Rp {{ number_format($period->total_installments, 0, ',', '.') }} - Rp {{ number_format($period->shu_amount, 0, ',', '.') }} = Rp {{ number_format($period->total_installments - $period->shu_amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger bg-opacity-10">
                                <h6 class="mb-0 text-danger"><i class="fas fa-arrow-up me-2"></i>(Outflow/Keluar)</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Status Pemenang:</small>
                                    <p class="mb-1 fw-bold {{ $period->winners->count() > 0 ? 'text-success' : 'text-muted' }}">
                                        {{ $period->winners->count() > 0 ? $period->winners->count() . ' Pemenang' : 'Belum Ada' }}
                                    </p>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Harga Motor:</small>
                                    <p class="mb-1 fw-bold">Rp {{ number_format($period->winners->count() > 0 ? $period->group->main_prize : 0, 0, ',', '.') }}</p>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Biaya Admin (SHU):</small>
                                    <p class="mb-1 fw-bold">Rp {{ number_format($period->shu_amount, 0, ',', '.') }}</p>
                                </div>
                                <div class="mb-0">
                                    <small class="text-muted">Total Tagihan Hadiah:</small>
                                    @php
                                        $mainPrize = $period->group->main_prize;
                                        $winnerCount = $period->winners->count();
                                        $winnerReceives = ($winnerCount > 0) ? ($mainPrize - $highestBid) : 0;
                                    @endphp
                                    <p class="mb-0 fw-bold text-success">
                                        Rp {{ number_format($winnerCount > 0 ? $mainPrize : 0, 0, ',', '.') }} - Rp {{ number_format($highestBid, 0, ',', '.') }} = Rp {{ number_format($winnerReceives, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning bg-opacity-10">
                                <h6 class="mb-0 text-dark"><i class="fas fa-exchange-alt me-2"></i>Perhitungan Aliran Kas</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <small class="text-muted">Dana Saat Ini:</small>
                                            <p class="mb-1 fw-bold">Rp {{ number_format($period->available_funds, 0, ',', '.') }}</p>
                                            <small class="text-muted">(Dana Iuran Bersih + Nilai Lelang)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <small class="text-muted">Sisa Bersih Periode Ini:</small>
                                            <p class="mb-1 fw-bold text-primary">Rp {{ number_format($period->available_funds - ($period->winners->count() * $period->group->main_prize), 0, ',', '.') }}</p>
                                            <small class="text-muted">(Dana Saat Ini - Harga Motor)</small>
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
                            <div class="card-header bg-success bg-opacity-10">
                                <h6 class="mb-0 text-success"><i class="fas fa-chart-line me-2"></i>(Hasil Akhir & Akumulasi)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="p-2 bg-light rounded">
                                            <small class="text-muted">Sisa Kas:</small>
                                            <h5 class="mb-0 fw-bold text-success">Rp {{ number_format($period->remaining_cash, 0, ',', '.') }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2 bg-light rounded">
                                            <small class="text-muted">Total Kas Berjalan:</small>
                                            <h5 class="mb-0 fw-bold text-primary">Rp {{ number_format($period->remaining_cash, 0, ',', '.') }}</h5>
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

<!-- Pemenang Periode -->
@if($period->winners && $period->winners->count() > 0)
<div class="card shadow mb-3">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">
            <i class="fas fa-trophy me-2"></i>Daftar Pemenang
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No Undian</th>
                        <th>Nama Peserta</th>
                        <th>NIK</th>
                        <th>Bagian</th>
                        <th>Tanggal Menang</th>
                        <th>Hadiah</th>
                        <th>Lelang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($period->winners as $winner)
                    <tr>
                        <td>
                            <span class="badge bg-primary">{{ $winner->participant->lottery_number ?? '-' }}</span>
                        </td>
                        <td>{{ $winner->participant->name ?? 'N/A' }}</td>
                        <td>{{ $winner->participant->nik ?? '-' }}</td>
                        <td>{{ $winner->participant->shift ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($winner->draw_time)->format('d/m/Y H:i') }}</td>
                        <td class="text-end">Rp {{ number_format($winner->final_prize, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($winner->bid_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Tombol Aksi -->
<div class="card shadow">
    <div class="card-body text-end">
        <a href="{{ route('admin.periods.edit', $period->id) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit me-1"></i>Edit Periode
        </a>
        @if($period->status === 'completed')
        <button class="btn btn-success" disabled>
            <i class="fas fa-check me-1"></i>Periode Selesai
        </button>
        @else
        <a href="{{ route('admin.groups.auction.process', $period->group_id) }}?period_id={{ $period->id }}" class="btn btn-primary me-2">
            <i class="fas fa-gavel me-1"></i>Proses Undian
        </a>
        @endif
    </div>
</div>
@endsection
