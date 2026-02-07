@extends('layouts.admin')

@section('title', 'Input Bid Manual - ' . $group->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-hand-holding-usd me-2"></i>
                Input Bid Manual
            </h2>
            <p class="text-muted mb-0">{{ $group->name }} - {{ $period->period_name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.groups.auction.process', [$group->id, 'period_id' => $period->id]) }}" 
               class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Period Information Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Periode</h6>
                            <h4 class="mb-0">{{ $period->period_name }}</h4>
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
                            <h6 class="card-title">Lelang Minimal</h6>
                            <h4 class="mb-0">Rp {{ number_format($group->min_bid, 0, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-down fa-2x"></i>
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
                            <h6 class="card-title">Lelang Maksimal</h6>
                            <h4 class="mb-0">Rp {{ number_format($group->max_bid, 0, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-up fa-2x"></i>
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
                            <h6 class="card-title">Status</h6>
                            <h4 class="mb-0">{{ ucfirst($period->status) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
<div class="card mb-4">
    <div class="card-header bg-white text-dark">
        <h5 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>
            Informasi Tambahan
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <p class="mb-2"><strong>Kas Bulan yang Digunakan:</strong><br>
                <span class="text-primary">{{ $cashMonthUsedName ?? '-' }}</span></p>
            </div>
            <div class="col-md-3">
                <p class="mb-2"><strong>Sisa Kas Bulan Lalu:</strong><br>
                <span class="text-success">Rp {{ number_format($period->previous_cash_balance ?? 0, 0, ',', '.') }}</span></p>
            </div>
            <div class="col-md-3">
                <p class="mb-2"><strong>SHU:</strong><br>
                <span class="text-warning">Rp {{ number_format($period->shu_amount ?? 0, 0, ',', '.') }}</span></p>
            </div>
        </div>
    </div>
</div>

<!-- Instructions -->
<div class="card mb-4">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Petunjuk Input Bid Manual
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Hanya menampilkan peserta yang <strong>belum memasukkan lelang</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Masukkan nilai lelang untuk setiap peserta yang ingin ikut
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Kosongkan atau isi dengan 0 untuk peserta yang tidak ikut
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Bid harus antara <strong>Rp {{ number_format($group->min_bid, 0, ',', '.') }}</strong> dan <strong>Rp {{ number_format($group->max_bid, 0, ',', '.') }}</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-trophy text-warning me-2"></i>
                        Peserta dengan bid tertinggi akan menjadi pemenang
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-magic text-primary me-2"></i>
                        Gunakan fitur Quick Fill untuk mengisi bid cepat
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

    <!-- Bid Input Form -->
<form action="{{ route('admin.groups.auction.manual-bid.store', [$group->id, $period->id]) }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Daftar Peserta yang Belum Lelang
                </h5>
                <span class="badge bg-primary">
                    {{ $participantsWithBids->count() }} Peserta
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="8%">No Undian</th>
                            <th width="25%">Nama Peserta</th>
                            <th width="10%">Shift</th>
                            <th width="12%">NIK</th>
                            <th width="15%">Status</th>
                            <th width="20%">Nilai Lelang</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($participantsWithBids as $participantData)
                        <tr>
                            <td>
                                <span class="lottery-number">
                                    {{ $participantData['participant']->lottery_number }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <span class="d-block text-dark fw-bold">{{ $participantData['participant']->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $participantData['participant']->shift }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $participantData['participant']->nik }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Belum Lelang
                                </span>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           name="bids[{{ $participantData['participant']->id }}]" 
                                           class="form-control bid-input" 
                                           placeholder="0"
                                           min="0" 
                                           max="{{ $group->max_bid }}"
                                           step="1000"
                                           value="{{ $participantData['bid_amount'] ?? '' }}"
                                           data-participant-name="{{ $participantData['participant']->name }}"
                                           data-min-bid="{{ $group->min_bid }}"
                                           data-max-bid="{{ $group->max_bid }}">
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Belum ada bid
                                </small>
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary quick-fill-btn"
                                        data-min="{{ $group->min_bid }}"
                                        data-max="{{ $group->max_bid }}"
                                        data-participant-id="{{ $participantData['participant']->id }}"
                                        title="Quick Fill">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-success">Semua Peserta Sudah Lelang</h5>
                                <p class="text-muted">Tidak ada peserta yang perlu diinputkan lelang secara manual</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <!-- Quick Fill Actions -->
<div class="card mt-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-magic me-2"></i>
            Quick Fill (Isi Cepat)
        </h5>
    </div>
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label">Nilai Bid:</label>
                <input type="number" 
                       id="quickFillAmount" 
                       class="form-control" 
                       placeholder="Masukkan nilai"
                       min="{{ $group->min_bid }}"
                       max="{{ $group->max_bid }}"
                       step="1000"
                       value="{{ $group->min_bid }}">
            </div>
            <div class="col-md-9">
                <div class="btn-group">
                    <button type="button" 
                            class="btn btn-success me-2"
                            onclick="fillAllBids()">
                        <i class="fas fa-fill me-2"></i>
                        Isi Semua
                    </button>
                    <button type="button" 
                            class="btn btn-outline-danger me-2"
                            onclick="clearAllBids()">
                        <i class="fas fa-eraser me-2"></i>
                        Kosongkan Semua
                    </button>
                    <button type="button" 
                            class="btn btn-outline-info"
                            onclick="fillRandomBids()">
                        <i class="fas fa-random me-2"></i>
                        Random
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Actions -->
<div class="d-flex justify-content-end mt-4 mb-4">
    <div class="btn-group mb-5">
        <button type="submit" 
                class="btn btn-lg btn-success">
            <i class="fas fa-save me-2"></i>
            Simpan Semua Bid
        </button>
    </div>
</div>
</form>

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

@push('scripts')
<script>
    // Format number to Indonesian currency format
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID').format(amount);
    }

    // Validate bid input
    function validateBid(input) {
        const value = parseFloat(input.value);
        const minBid = parseFloat(input.dataset.minBid);
        const maxBid = parseFloat(input.dataset.maxBid);
        const participantName = input.dataset.participantName;

        if (value > 0) {
            if (value < minBid) {
                input.setCustomValidity(`Bid minimal untuk ${participantName} adalah Rp ${formatCurrency(minBid)}`);
                input.classList.add('is-invalid');
            } else if (value > maxBid) {
                input.setCustomValidity(`Bid maksimal untuk ${participantName} adalah Rp ${formatCurrency(maxBid)}`);
                input.classList.add('is-invalid');
            } else {
                input.setCustomValidity('');
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        } else {
            input.setCustomValidity('');
            input.classList.remove('is-invalid', 'is-valid');
        }
    }

    // Add event listeners to all bid inputs
    document.querySelectorAll('.bid-input').forEach(input => {
        input.addEventListener('input', function() {
            validateBid(this);
        });
    });

    // Quick fill functions
    function fillAllBids() {
        const amount = document.getElementById('quickFillAmount').value;
        if (!amount) {
            alert('Masukkan nilai bid terlebih dahulu');
            return;
        }
        
        document.querySelectorAll('.bid-input').forEach(input => {
            input.value = amount;
            validateBid(input);
        });
    }

    function fillRandomBids() {
        const minBid = parseFloat(document.querySelector('.bid-input').dataset.minBid);
        const maxBid = parseFloat(document.querySelector('.bid-input').dataset.maxBid);
        
        document.querySelectorAll('.bid-input').forEach(input => {
            const randomAmount = Math.floor(Math.random() * (maxBid - minBid + 1)) + minBid;
            input.value = randomAmount;
            validateBid(input);
        });
    }

    function clearAllBids() {
        document.querySelectorAll('.bid-input').forEach(input => {
            input.value = '';
            input.classList.remove('is-invalid', 'is-valid');
            input.setCustomValidity('');
        });
    }

    function validateAndSubmit() {
        let isValid = true;
        let invalidCount = 0;
        
        document.querySelectorAll('.bid-input').forEach(input => {
            validateBid(input);
            if (input.classList.contains('is-invalid')) {
                isValid = false;
                invalidCount++;
            }
        });
        
        if (!isValid) {
            alert(`Terdapat ${invalidCount} bid yang tidak valid. Silakan periksa kembali.`);
            return false;
        }
        
        // Check if at least one bid is entered
        const bidsEntered = Array.from(document.querySelectorAll('.bid-input')).filter(input => input.value && parseFloat(input.value) > 0);
        if (bidsEntered.length === 0) {
            alert('Masukkan setidaknya satu bid untuk disimpan.');
            return false;
        }
        
        // Submit the form properly using the submit button
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.click();
    }

    // Quick fill for individual participant
    document.querySelectorAll('.quick-fill-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const participantId = this.dataset.participantId;
            const minBid = parseFloat(this.dataset.min);
            const maxBid = parseFloat(this.dataset.max);
            const randomAmount = Math.floor(Math.random() * (maxBid - minBid + 1)) + minBid;
            
            const input = document.querySelector(`input[name="bids[${participantId}]"]`);
            input.value = randomAmount;
            validateBid(input);
        });
    });
</script>
@endpush
</div>
@endsection
