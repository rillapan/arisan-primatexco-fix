@extends('layouts.admin')

@section('title', 'Detail Bid - ' . $bid->participant->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-eye me-2"></i>
            Detail Bid
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.groups.auction.process', [$bid->monthlyPeriod->group_id, 'period_id' => $bid->monthly_period_id]) }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>
                    Proses Undian
                </a>
                <a href="{{ route('admin.periods.edit', $bid->monthly_period_id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-calendar-edit me-1"></i>
                    Edit Periode
                </a>
                @if($bid->monthlyPeriod->status !== 'completed')
                    <a href="{{ route('admin.bids.edit', $bid->id) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>
                        Edit Bid
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Participant Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white text-dark py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user me-2 text-success"></i>
                        Informasi Peserta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Nama Peserta</small>
                            <span class="fw-bold">{{ $bid->participant->name }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">No Undian</small>
                            <span class="badge bg-primary px-3 rounded-pill">{{ $bid->participant->lottery_number }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Bag/Shift</small>
                            <span class="badge bg-info">{{ $bid->participant->shift }}</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">NIK</small>
                            <span class="fw-bold">{{ $bid->participant->nik }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white text-dark py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-alt me-2 text-success"></i>
                        Informasi Periode
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Nama Periode</small>
                            <span class="fw-bold">{{ $bid->monthlyPeriod->period_name }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Grup</small>
                            <span class="fw-bold">{{ $bid->monthlyPeriod->group->name }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Status Periode</small>
                            <span class="badge bg-{{ $bid->monthlyPeriod->status === 'active' ? 'success' : ($bid->monthlyPeriod->status === 'completed' ? 'warning' : 'info') }}">
                                {{ ucfirst($bid->monthlyPeriod->status) }}
                            </span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Rentang Periode</small>
                            <span class="fw-bold">{{ $bid->monthlyPeriod->period_start->format('d M Y') }} - {{ $bid->monthlyPeriod->period_end->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bid Information -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-info py-3">
            <h5 class="mb-0 text-white fw-bold">
                <i class="fas fa-gavel me-2"></i>
                Informasi Bid
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <small class="text-muted d-block mb-2">Nilai Bid</small>
                        <h4 class="text-primary mb-0">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <small class="text-muted d-block mb-2">Status Bid</small>
                        <h4 class="mb-0">
                            @if($bid->status === 'submitted')
                                <span class="badge bg-success fs-6 px-3 py-2">Disubmit</span>
                            @elseif($bid->status === 'accepted')
                                <span class="badge bg-warning fs-6 px-3 py-2">Diterima</span>
                            @else
                                <span class="badge bg-secondary fs-6 px-3 py-2">{{ $bid->status }}</span>
                            @endif
                        </h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <small class="text-muted d-block mb-2">Waktu Bid</small>
                        <h4 class="text-dark mb-0">{{ $bid->bid_time->format('d/m/Y H:i') }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <small class="text-muted d-block mb-2">Hadiah Utama</small>
                        <h4 class="text-success mb-0">Rp {{ number_format($bid->monthlyPeriod->group->main_prize, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bid Proof Display -->
     <!-- untuk menampilkan bukti lelang permanen -->
    @if($bid->is_permanent)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 text-dark" >
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-file-contract me-2 text-primary"></i>
                Bukti Lelang Permanen
            </h5>
        </div>
        <div class="card-body text-center bg-light">
            <div class="mb-3">
                <h6 class="text-muted mb-3">Peserta telah menyimpan lelang ini secara permanen.</h6>
                <div class="d-inline-block p-2 bg-white border rounded shadow-sm">
                    <img src="{{ route('admin.bids.download-proof', $bid->id) }}" alt="Bukti Lelang" class="img-fluid" style="max-height: 500px; width: auto;">
                </div>
            </div>
            <div>
                <a href="{{ route('admin.bids.download-proof', $bid->id) }}" download="bukti-lelang-{{ $bid->id }}.png" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> Download Bukti
                </a>
            </div>
        </div>
    </div>
    @endif


    <!-- Additional Information -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 text-dark">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                Informasi Tambahan
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <small class="text-muted d-block mb-1">Estimasi Hadiah Bersih</small>
                        <div class="fw-bold text-success fs-5">Rp {{ number_format($bid->monthlyPeriod->group->main_prize - $bid->bid_amount, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <small class="text-muted d-block mb-1">ID Bid</small>
                        <div class="fw-bold text-dark">#{{ $bid->id }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4">
        <a href="{{ route('admin.groups.auction.process', [$bid->monthlyPeriod->group_id, 'period_id' => $bid->monthly_period_id]) }}" 
           class="btn btn-outline-secondary py-2 px-4 order-2 order-md-1">
            <i class="fas fa-arrow-left me-1"></i>
            Kembali ke Proses Undian
        </a>
        @if($bid->monthlyPeriod->status !== 'completed')
            <a href="{{ route('admin.bids.edit', $bid->id) }}" 
               class="btn btn-warning py-2 px-4 order-1 order-md-2">
                <i class="fas fa-edit me-1"></i>
                Edit Bid
            </a>
        @endif
    </div>
@endsection
