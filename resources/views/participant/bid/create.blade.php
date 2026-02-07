@extends('layouts.participant')

@section('title', 'Masukkan Nilai Lelang - Sistem Arisan')

@section('content')
    <h2>
        <i class="fas fa-hand-holding-usd me-2"></i>
        Masukkan Nilai Lelang
    </h2>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Period Information -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white text-dark py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                Informasi Periode
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Nama Periode</small>
                            <span class="fw-bold">{{ $currentPeriod->period_name }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-{{ $currentPeriod->status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($currentPeriod->status) }}
                            </span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Tanggal Periode</small>
                            <span class="fw-bold">{{ $currentPeriod->period_start->format('d/m/Y') }} - {{ $currentPeriod->period_end->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Lelang Minimal</small>
                            <span class="fw-bold text-success">Rp {{ number_format($currentPeriod->group->min_bid, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Lelang Maksimal</small>
                            <span class="fw-bold text-danger">Rp {{ number_format($currentPeriod->group->max_bid, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Status Lelang Anda</small>
                            @if($existingBid)
                                @if($existingBid->is_permanent)
                                    <span class="badge bg-danger"><i class="fas fa-lock me-1"></i>Lelang Permanen (Rp {{ number_format($existingBid->bid_amount, 0, ',', '.') }})</span>
                                @else
                                    <span class="badge bg-info">Sudah Lelang (Rp {{ number_format($existingBid->bid_amount, 0, ',', '.') }}) - <small>Ubah nilai di bawah</small></span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Belum Lelang</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-2 bg-light rounded shadow-sm h-100">
                        <small class="text-muted d-block mb-1 px-2">Kas bulan yang digunakan:</small>
                        <div class="fw-bold px-2">{{ $cashMonthUsedName ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 bg-light rounded shadow-sm h-100">
                        <small class="text-muted d-block mb-1 px-2">Sisa kas bulan lalu:</small>
                        <div class="fw-bold px-2 text-success">Rp {{ number_format($currentPeriod->previous_cash_balance ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 bg-light rounded shadow-sm h-100">
                        <small class="text-muted d-block mb-1 px-2">SHU:</small>
                        <div class="fw-bold px-2 text-primary">Rp {{ number_format($currentPeriod->shu_amount ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bid Form -->
    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
        <div class="card-header bg-success py-3">
            <h5 class="mb-0 text-white fw-bold">
                @if($existingBid)
                    <i class="fas fa-edit me-2"></i>Ubah Nilai Lelang
                @else
                    <i class="fas fa-plus me-2"></i>Masukkan Nilai Lelang
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if(($isPeriodEnded ?? false))
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <div class="d-flex">
                        <i class="fas fa-exclamation-triangle mt-1 me-3 fs-4"></i>
                        <div>
                            <strong class="d-block mb-1">Periode lelang sudah berakhir.</strong>
                            <p class="mb-0 small">Anda tidak dapat mengubah nilai lelang lagi. Jika Anda membutuhkan bantuan, silakan hubungi admin.</p>
                        </div>
                    </div>
                </div>
            @endif
            <form method="POST" action="{{ route('participant.bid.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="bid_amount" class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave me-1 text-success"></i>
                                Nilai Lelang (Rp)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('bid_amount') is-invalid @enderror py-2 fw-bold" 
                                       id="bid_amount" name="bid_amount" 
                                       min="{{ $currentPeriod->group->min_bid }}" 
                                       max="{{ $currentPeriod->group->max_bid }}"
                                       value="{{ old('bid_amount', $existingBid->bid_amount ?? '') }}"
                                       placeholder="{{ number_format($currentPeriod->group->min_bid, 0, ',', '.') }}"
                                       required
                                       {{ ($isPeriodEnded ?? false) || ($existingBid && $existingBid->is_permanent) ? 'disabled' : '' }}>
                            </div>
                            <div class="form-text small text-muted">
                                Rentang: Rp {{ number_format($currentPeriod->group->min_bid, 0, ',', '.') }} - 
                                Rp {{ number_format($currentPeriod->group->max_bid, 0, ',', '.') }}
                            </div>
                            @error('bid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm my-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-calculator me-2"></i>
                        <strong class="mb-0">Estimasi Hadiah Diterima:</strong>
                    </div>
                    <div class="bg-white rounded p-3 text-dark mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Hadiah Utama:</span>
                            <span class="fw-bold">Rp {{ number_format($currentPeriod->group->main_prize, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Nilai Lelang Anda:</span>
                            <span class="text-danger fw-bold">- <span id="bidExample">Rp 0</span></span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total Hadiah Bersih:</span>
                            <span class="text-success fw-bold fs-5" id="prizeExample">Rp {{ number_format($currentPeriod->group->main_prize, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <small class="text-muted fst-italic">*) Hasil akhir mungkin berbeda tergantung dari hasil lelang peserta lainnya.</small>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-end gap-2">
                    <a href="{{ route('participant.dashboard') }}" class="btn btn-outline-secondary py-2 px-4 order-2 order-md-1">
                        <i class="fas fa-times me-1"></i>Batal
                    </a>
                    
                    @if($existingBid && $existingBid->is_permanent)
                        <a href="{{ route('participant.bid.download-proof', $existingBid->id) }}" class="btn btn-primary py-2 px-4 order-1 order-md-2">
                            <i class="fas fa-download me-1"></i>Unduh Bukti PNG
                        </a>
                    @elseif($existingBid && !$existingBid->is_permanent)
                        <button type="submit" class="btn btn-success py-2 px-4 order-1 order-md-2" {{ ($isPeriodEnded ?? false) ? 'disabled' : '' }}>
                            <i class="fas fa-save me-1"></i>Perbarui Nilai Lelang
                        </button>
                        <button type="button" class="btn btn-danger py-2 px-4 order-1 order-md-2" 
                                onclick="if(confirm('Simpan lelang secara permanen? Setelah disimpan permanen, nilai lelang TIDAK DAPAT diubah lagi oleh siapapun.')) { document.getElementById('permanentForm').submit(); }">
                            <i class="fas fa-check-double me-1"></i>Simpan Lelang Permanen
                        </button>
                    @else
                        <button type="submit" class="btn btn-success py-2 px-4 order-1 order-md-2" {{ ($isPeriodEnded ?? false) ? 'disabled' : '' }}>
                            <i class="fas fa-plus me-1"></i>Masukkan Lelang
                        </button>
                    @endif
                </div>
            </form>

            @if($existingBid && !$existingBid->is_permanent)
                <form id="permanentForm" action="{{ route('participant.bid.permanent', $existingBid->id) }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endif
        </div>
    </div>

    <!-- All Bids Table -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-info py-3">
            <h5 class="mb-0 text-white fw-bold">
                <i class="fas fa-list me-2"></i>
                Hasil Lelang Semua Peserta
            </h5>
        </div>
        <div class="card-body p-0">
            @if(count($allBids ?? []) > 0)
                <!-- Desktop Table -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 120px;">No Undian</th>
                                <th>Nama Peserta</th>
                                <th>Bag/Shift</th>
                                <th class="text-end pe-4">Nilai Lelang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allBids as $bid)
                                <tr class="{{ $bid->participant_id == $participant->id ? 'table-warning border-start border-4 border-warning' : '' }}">
                                    <td class="ps-4">
                                        <span class="badge bg-primary px-3 rounded-pill">{{ $bid->participant->lottery_number }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $bid->participant->name }}</span>
                                        @if($bid->participant_id == $participant->id)
                                            <span class="badge bg-warning text-dark ms-2 small">Anda</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $bid->participant->shift }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <span class="text-success fw-bold">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View -->
                <div class="d-md-none">
                    @foreach($allBids as $bid)
                        <div class="p-3 border-bottom {{ $bid->participant_id == $participant->id ? 'bg-warning-subtle' : '' }}" 
                             style="{{ $bid->participant_id == $participant->id ? 'background-color: #fff9e6;' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary rounded-pill">{{ $bid->participant->lottery_number }}</span>
                                <span class="text-success fw-bold">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-dark">{{ $bid->participant->name }}</span>
                                    @if($bid->participant_id == $participant->id)
                                        <span class="badge bg-warning text-dark ms-1 small">Anda</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $bid->participant->shift }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-gavel fa-3x text-light mb-3"></i><br>
                    <p class="text-muted">Belum ada data lelang pada periode ini.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function updateEstimate(bidAmount) {
        const amount = parseFloat(bidAmount) || 0;
        const mainPrize = {{ $currentPeriod->group->main_prize }};
        const finalPrize = mainPrize - amount;
        
        document.getElementById('bidExample').textContent = 'Rp ' + amount.toLocaleString('id-ID');
        document.getElementById('prizeExample').textContent = 'Rp ' + (finalPrize > 0 ? finalPrize : 0).toLocaleString('id-ID');
    }

    document.getElementById('bid_amount').addEventListener('input', function(e) {
        updateEstimate(e.target.value);
    });

    // Initialize on page load
    window.addEventListener('load', function() {
        const initialValue = document.getElementById('bid_amount').value;
        if (initialValue) {
            updateEstimate(initialValue);
        }
    });
</script>
@endpush
