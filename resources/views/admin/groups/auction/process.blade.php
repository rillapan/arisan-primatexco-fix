@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.groups.manage', $group->id) }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-gavel me-2 text-primary"></i>Proses Undian - {{ $group->name }}
        </h1>
        <p class="text-muted mb-0">Proses kocok arisan</p>
    </div>
</div>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-gavel me-2"></i>
                            Proses Undian - {{ $group->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Period Selection -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            Pilih Periode untuk Proses Undian
                                        </h6>
                                        <form action="{{ route('admin.groups.auction.process', $group->id) }}" method="GET" class="row g-3">
                                            <div class="col-md-8">
                                                <label for="period_id" class="form-label">Periode:</label>
                                                <select class="form-select" id="period_id" name="period_id" onchange="this.form.submit()">
                                                    <option value="">-- Pilih Periode --</option>
                                                    @foreach($group->monthlyPeriods as $period)
                                                        <option value="{{ $period->id }}" 
                                                                {{ ($periodWithBids && $periodWithBids->id == $period->id) ? 'selected' : '' }}>
                                                            {{ $period->period_name }} 
                                                            ({{ $period->period_start->format('d M Y') }} - {{ $period->period_end->format('d M Y') }})
                                                            [{{ ucfirst($period->status) }}]
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label d-block">&nbsp;</label>
                                                <a href="{{ route('admin.groups.periods.create', $group->id) }}" class="btn btn-success w-100">
                                                    <i class="fas fa-plus me-2"></i>
                                                    Buat Periode Baru
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($periodWithBids)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6>
                                        <i class="fas fa-info-circle me-2"></i>
                                        Informasi Periode Saat Ini
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Periode:</strong> {{ $periodWithBids->period_name }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Status:</strong> 
                                            <span class="badge bg-{{ $periodWithBids->status === 'active' ? 'success' : 'warning' }}">
                                                {{ ucfirst($periodWithBids->status) }}
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total Lelang:</strong> {{ $periodWithBids->bids->count() }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Lelang Minimal:</strong> Rp {{ number_format($group->min_bid, 0, ',', '.') }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Lelang Maksimal:</strong> Rp {{ number_format($group->max_bid, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <hr class="my-2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Kas bulan yang digunakan:</strong>
                                            {{ $cashMonthUsedName ?? '-' }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Total Angsuran:</strong>
                                            Rp {{ number_format($monthInstallments ?? 0, 0, ',', '.') }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Sisa kas bulan lalu:</strong>
                                            Rp {{ number_format($periodWithBids->previous_cash_balance ?? 0, 0, ',', '.') }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>SHU:</strong>
                                            Rp {{ number_format($periodWithBids->shu_amount ?? 0, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <h6>
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Belum Ada Periode Dipilih
                                    </h6>
                                    <p>Silakan pilih periode dari dropdown di atas atau buat periode baru untuk memulai proses undian.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($periodWithBids)
                        
                        @if($periodWithBids->status === 'completed' && $periodWithBids->winners->count() > 0)
                        <div class="card mb-4 border-success shadow-sm">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-trophy me-2"></i>
                                    Daftar Pemenang Periode Ini
                                </h5>
                                <span class="badge bg-white text-success">
                                    {{ $periodWithBids->winners->count() }} Pemenang
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No Undian</th>
                                                <th>Nama Pemenang</th>
                                                <th class="text-end">Nilai Bid</th>
                                                <th class="text-end">Hadiah Akhir (Diterima)</th>
                                                <th class="text-center">Waktu Menang</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($periodWithBids->winners as $winner)
                                            <tr>
                                                <td><span class="badge bg-primary">{{ $winner->participant->lottery_number }}</span></td>
                                                <td>
                                                    <strong>{{ $winner->participant->name }}</strong>
                                                    <div class="small text-muted">{{ $winner->participant->nik }} | {{ $winner->participant->shift }}</div>
                                                </td>
                                                <td class="text-end fw-bold text-primary">Rp {{ number_format($winner->bid_amount, 0, ',', '.') }}</td>
                                                <td class="text-end fw-bold text-success">Rp {{ number_format($winner->final_prize, 0, ',', '.') }}</td>
                                                <td class="text-center"><small class="text-muted">{{ $winner->draw_time ? $winner->draw_time->format('d/m/Y H:i') : '-' }}</small></td>
                                                <td class="text-center">
                                                    @if($winner->bid_id)
                                                        <a href="{{ route('admin.bids.show', $winner->bid_id) }}" class="btn btn-sm btn-info shadow-sm">
                                                            <i class="fas fa-eye me-1"></i>Lihat Detail
                                                        </a>
                                                    @else
                                                        <span class="text-muted small">Tanpa Bid (Undian)</span>
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

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2 text-primary"></i>
                                {{ $periodWithBids->status === 'completed' ? 'Riwayat Lelang' : 'Daftar Lelang Berjalan' }}
                            </h5>
                            @if($periodWithBids->status !== 'completed')
                                <span class="badge bg-primary">{{ $periodWithBids->bids->count() }} Peserta Bid</span>
                            @endif
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No Undian</th>
                                        <th>Nama Peserta</th>
                                        <th>Bag/Shift</th>
                                        <th>NIK</th>
                                        <th>Nilai Lelang</th>
                                        <th>Waktu Bid</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($periodWithBids->bids as $bid)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $bid->participant->lottery_number }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $bid->participant->name }}</strong>
                                            @if($periodWithBids->status === 'completed' && $periodWithBids->winners->contains('participant_id', $bid->participant_id))
                                                <span class="badge bg-warning ms-2">
                                                    <i class="fas fa-trophy me-1"></i>Pemenang
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $bid->participant->shift }}</span>
                                        </td>
                                        <td>{{ $bid->participant->nik }}</td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $bid->bid_time->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($bid->status === 'submitted')
                                                <span class="badge bg-success">Disubmit</span>
                                            @elseif($bid->status === 'accepted')
                                                <span class="badge bg-primary">Diterima</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $bid->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.bids.show', $bid->id) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($periodWithBids->status !== 'completed')
                                                    <a href="{{ route('admin.bids.edit', $bid->id) }}" 
                                                       class="btn btn-outline-warning" 
                                                       title="Edit Bid">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-secondary" 
                                                            title="Edit Bid (Tidak dapat mengedit periode yang sudah selesai)" 
                                                            disabled>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-gavel fa-2x mb-2"></i><br>
                                            Belum ada bid untuk periode ini<br>
                                            <a href="{{ route('admin.groups.auction.manual-bid', [$group->id, $periodWithBids->id]) }}" 
                                               class="btn btn-warning mt-2">
                                                <i class="fas fa-hand-holding-usd me-2"></i>
                                                Input Bid Manual
                                            </a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($periodWithBids->bids->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-trophy me-2"></i>
                                                Proses Pengundian
                                            </h6>
                                            <div>
                                                <a href="{{ route('admin.groups.auction.manual-bid', [$group->id, $periodWithBids->id]) }}" 
                                                   class="btn btn-warning">
                                                    <i class="fas fa-hand-holding-usd me-2"></i>
                                                    Input Bid Manual
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <?php
                                        $highestBid = $periodWithBids->bids->max('bid_amount');
                                        $highestBidders = $periodWithBids->bids->where('bid_amount', $highestBid);
                                        $winnerCount = $periodWithBids->calculateWinnerCount();
                                        ?>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted">Bid Tertinggi:</small>
                                                <h5 class="text-success">Rp {{ number_format($highestBid, 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Jumlah Pemenang:</small>
                                                <h5 class="text-primary">{{ $winnerCount }}</h5>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Peserta dengan Bid Tertinggi:</small>
                                                <h5 class="text-warning">{{ $highestBidders->count() }}</h5>
                                            </div>
                                        </div>

                                        @if($highestBidders->count() <= $winnerCount)
                                            <div class="alert alert-success mt-3">
                                                <i class="fas fa-check-circle me-2"></i>
                                                <strong>Tidak Perlu Undian:</strong> Jumlah peserta dengan bid tertinggi tidak melebihi jumlah pemenang.
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Perlu Undian:</strong> Jumlah peserta dengan bid tertinggi melebihi jumlah pemenang.
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            @if($highestBidders->count() > $winnerCount && ($periodWithBids->status === 'bidding' || $periodWithBids->status === 'drawing'))
                                                <a href="{{ route('admin.draw.start', $periodWithBids->id) }}" class="btn btn-success btn-lg">
                                                    <i class="fas fa-play me-2"></i>
                                                    Mulai Proses Undian
                                                </a>
                                            @elseif($highestBidders->count() > $winnerCount && $periodWithBids->status !== 'completed')
                                                <a href="{{ route('admin.draw.start', $periodWithBids->id) }}" class="btn btn-warning btn-lg">
                                                    <i class="fas fa-sync me-2"></i>
                                                    Mulai Undian ({{ $highestBidders->count() }} Peserta)
                                                </a>
                                            @elseif($periodWithBids->status === 'bidding')
                                                <a href="{{ route('admin.draw.start', $periodWithBids->id) }}" class="btn btn-success btn-lg">
                                                    <i class="fas fa-play me-2"></i>
                                                    Mulai Proses Undian
                                                </a>
                                            @elseif($periodWithBids->status === 'drawing')
                                                <a href="{{ route('admin.draw.start', $periodWithBids->id) }}" class="btn btn-warning btn-lg">
                                                    <i class="fas fa-sync me-2"></i>
                                                    Lanjutkan Proses Undian
                                                </a>
                                            @else
                                                <button class="btn btn-secondary btn-lg" disabled>
                                                    <i class="fas fa-check me-2"></i>
                                                    Periode Sudah Selesai
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif

                        @if($periodWithBids)
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <h5>{{ $group->participants->count() }}</h5>
                                        <p class="mb-0">Total Peserta</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-gavel fa-2x mb-2"></i>
                                        <h5>{{ $periodWithBids->bids->count() }}</h5>
                                        <p class="mb-0">Total Bid</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-trophy fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($highestBid ?? 0, 0, ',', '.') }}</h5>
                                        <p class="mb-0">Bid Tertinggi</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-gift fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($group->main_prize, 0, ',', '.') }}</h5>
                                        <p class="mb-0">Hadiah Utama</p>
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



@endsection

