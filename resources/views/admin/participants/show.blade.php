@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-user-circle me-2 text-primary"></i>Detail Peserta
        </h1>
        <p class="text-muted mb-0">Informasi lengkap peserta: <strong>{{ $participant->name }}</strong></p>
    </div>
    <div>
        <a href="{{ route('admin.groups.participants.manage', $participant->group_id) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Left Column: Personal & Group Info -->
    <div class="col-lg-4 mb-4">
        <!-- Personal Info Card -->
        <div class="card shadow-sm mb-4 border-start-primary h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-id-card me-2"></i>Informasi Pribadi
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($participant->photo)
                        <img src="{{ asset('storage/' . $participant->photo) }}" alt="{{ $participant->name }}" class="mx-auto mb-3 shadow-sm border" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div class="avatar-circle mx-auto mb-3 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 120px; height: 120px; border-radius: 50%; font-size: 3rem;">
                            {{ strtoupper(substr($participant->name, 0, 1)) }}
                        </div>
                    @endif
                    <h5 class="fw-bold mb-0 text-dark">{{ $participant->name }}</h5>
                    <span class="text-muted fw-medium">{{ $participant->nik ?? 'NIK Belum diisi' }}</span>
                </div>
                
                <hr class="sidebar-divider">
                
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Nomor Undian</small>
                    <div class="d-flex align-items-center mt-1">
                        <span class="badge bg-primary fs-5 px-3 py-2 rounded-pill shadow-sm">
                            {{ $participant->lottery_number }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Bagian / Shift</small>
                    <div class="fs-6 text-dark mt-1 fw-bold">
                        {{ $participant->shift ?? '-' }}
                    </div>
                </div>

                <div class="mb-0">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Status Akun</small>
                    <div class="mt-1">
                        @if($participant->is_active)
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Aktif</span>
                        @else
                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Non-Aktif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Status & Statistics -->
    <div class="col-lg-8 mb-4">
        <!-- Group & Status Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users me-2"></i>Status Keanggotaan
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <small class="text-muted text-uppercase fw-bold">Asal Kelompok</small>
                            <div class="h4 mb-0 fw-bold text-gray-800 mt-2">
                                {{ $participant->group->name }}
                            </div>
                            <small class="text-primary"><i class="fas fa-building me-1"></i>ID Kelompok: {{ $participant->group_id }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <small class="text-muted text-uppercase fw-bold">Status Pemenang</small>
                            <div class="d-flex align-items-center mt-2">
                                @if($participant->has_won)
                                    <div class="h4 mb-0 fw-bold text-success">
                                        <i class="fas fa-trophy me-2"></i>Sudah Menang
                                    </div>
                                @else
                                    <div class="h4 mb-0 fw-bold text-secondary">
                                        <i class="fas fa-clock me-2"></i>Belum Menang
                                    </div>
                                @endif
                            </div>
                            @if($participant->winner)
                                <small class="text-success">Pada {{ $participant->winner->created_at->format('d M Y') }}</small>
                            @else
                                <small class="text-muted">Masih berhak mengikuti lelang</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Winner Details (Conditional) -->
        @if($participant->winner)
        <div class="card shadow-sm mb-4 border-start-success">
            <div class="card-header bg-success text-white py-3">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-crown me-2"></i>Detail Kemenangan
                </h6>
            </div>
            <div class="card-body bg-light-success">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-award fa-4x text-success opacity-50"></i>
                    </div>
                    <div class="col">
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-3">
                                <small class="text-muted fw-bold d-block">Periode</small>
                                <span class="h6 text-dark fw-bold">{{ $participant->winner->monthlyPeriod->period_name }}</span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <small class="text-muted fw-bold d-block">Harga Lelang</small>
                                <span class="h6 text-danger fw-bold">Rp {{ number_format($participant->winner->bid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <small class="text-muted fw-bold d-block">Uang Diterima</small>
                                <span class="h6 text-success fw-bold">Rp {{ number_format($participant->winner->final_prize, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <small class="text-muted fw-bold d-block">Waktu</small>
                                <span class="text-dark">{{ $participant->winner->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($participant->winner->notes)
                <div class="mt-3 p-2 bg-white rounded border border-success-subtle">
                    <small class="text-muted fw-bold"><i class="fas fa-sticky-note me-1"></i>Catatan:</small>
                    <p class="mb-0 text-dark small">{{ $participant->winner->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Bid History List -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history me-2"></i>Riwayat Harga Lelang
                </h6>
                <span class="badge bg-secondary rounded-pill">{{ $participant->bids->count() }} Percobaan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Periode</th>
                                <th>Nilai Tawaran</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Status Periode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($participant->bids as $bid)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $bid->monthlyPeriod->period_name }}</td>
                                <td>
                                    <span class="text-primary fw-bold">
                                        Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <i class="far fa-clock me-1"></i>{{ $bid->bid_time->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    @if($bid->status === 'submitted')
                                        <span class="badge bg-info">Diajukan</span>
                                    @elseif($bid->status === 'accepted')
                                        <span class="badge bg-success">Pemenang</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $bid->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @php
                                        $pStatus = $bid->monthlyPeriod->status;
                                        $badgeClass = $pStatus === 'active' ? 'bg-success' : ($pStatus === 'bidding' ? 'bg-warning text-dark' : 'bg-secondary');
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill font-monospace" style="font-size: 0.75rem;">
                                        {{ strtoupper($pStatus) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <div class="my-3">
                                        <i class="fas fa-gavel fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0">Belum ada riwayat tawaran lelang.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
