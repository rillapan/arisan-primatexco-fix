@extends('layouts.participant')

@section('title', 'Daftar Lelang - ' . $period->period_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-gavel me-2"></i>
                Daftar Lelang
            </h2>
            <p class="text-muted mb-0">{{ $period->period_name }} - {{ $period->group->name }}</p>
        </div>
        <div>
            <a href="{{ route('participant.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Period Information Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Informasi Periode
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <small class="text-muted">Periode</small><br>
                    <strong>{{ $period->period_start->format('d M Y') }} - {{ $period->period_end->format('d M Y') }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Status</small><br>
                    <span class="badge bg-{{ $period->status === 'bidding' ? 'success' : ($period->status === 'completed' ? 'primary' : 'secondary') }}">
                        {{ ucfirst($period->status) }}
                    </span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Hadiah Utama</small><br>
                    <strong class="text-primary">Rp {{ number_format($period->group->main_prize, 0, ',', '.') }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Total Peserta</small><br>
                    <strong>{{ $period->group->participants->where('is_active', true)->count() }} Orang</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Bids Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Daftar Lelang
            </h5>
        </div>
        <div class="card-body">
            @if($bids->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>No. Undi</th>
                                <th>Nama Peserta</th>
                                <th>Shift</th>
                                <th>Nilai Lelang</th>
                                <th>Waktu</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bids as $index => $bid)
                                @php
                                    $currentParticipant = Auth::guard('participant')->user();
                                    $isCurrentUser = $bid->participant_id === $currentParticipant->id;
                                    $isRelatedAccount = $bid->participant->nik === $currentParticipant->nik;
                                @endphp
                                <tr class="{{ ($isCurrentUser || $isRelatedAccount) ? 'table-primary' : '' }}">
                                    <td class="fw-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="lottery-number">
                                            {{ $bid->participant->lottery_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="d-block text-dark fw-bold">{{ $bid->participant->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $bid->participant->shift }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $bid->created_at->format('d M H:i') }}</small>
                                    </td>
                                    <td>
                                        @php
                                            // Check if this bid belongs to current user or any user with same NIK (related accounts)
                                            $currentParticipant = Auth::guard('participant')->user();
                                            $isCurrentUser = $bid->participant_id === $currentParticipant->id;
                                            $isRelatedAccount = $bid->participant->nik === $currentParticipant->nik;
                                        @endphp
                                        @if($isCurrentUser || $isRelatedAccount)
                                            <span class="badge bg-warning">Anda</span>
                                        @else
                                            <span class="badge bg-secondary">Peserta Lain</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-gavel fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Lelang</h5>
                    <p class="text-muted">Belum ada peserta yang memasukkan lelang untuk periode ini.</p>
                    @if($period->status === 'bidding')
                        <a href="{{ route('participant.bid.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Masukkan Lelang Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Your Bid Status -->
    @if($participantBid)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Status Lelang Anda
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted">Nilai Lelang Anda</small><br>
                        <strong class="text-success">Rp {{ number_format($participantBid->bid_amount, 0, ',', '.') }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Peringkat Anda</small><br>
                        <strong class="text-primary">
                            {{ $bids->search(function($bid) use ($participantBid) {
                                return $bid->id === $participantBid->id;
                            }) + 1 }} dari {{ $bids->count() }}
                        </strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Waktu Memasukkan</small><br>
                        <strong>{{ $participantBid->created_at->format('d M Y H:i') }}</strong>
                    </div>
                </div>
                @if($period->status === 'bidding')
                    <div class="mt-3">
                        <a href="{{ route('participant.bid.create') }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Ubah Lelang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
.lottery-number {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 0.35rem 0.65rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-block;
    min-width: 50px;
    text-align: center;
}
</style>
@endpush
@endsection
