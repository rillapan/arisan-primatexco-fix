@extends('layouts.admin')

@section('title', 'Dashboard Admin - Sistem Arisan')

@section('content')
    <!-- Auction Results Table -->
    <!-- Auction Results Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">
                <i class="fas fa-gavel me-2"></i>
                Hasil Lelang Arisan
            </h5>
            <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                <select name="month" class="form-select form-select-sm" style="width: auto;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('id')->monthName }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="form-select form-select-sm" style="width: auto;">
                    @foreach($availablePeriods->unique('year') as $period)
                        <option value="{{ $period->year }}" {{ $selectedYear == $period->year ? 'selected' : '' }}>
                            {{ $period->year }}
                        </option>
                    @endforeach
                    @if($availablePeriods->isEmpty())
                        <option value="{{ now()->year }}">{{ now()->year }}</option>
                    @endif
                </select>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.dashboard.export-pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="btn btn-sm btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kelompok</th>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Bag/Shift</th>
                            <th>NIK</th>
                            <th>Nilai Lelang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auctionResults as $result)
                        <tr>
                            <td>
                                <span class="badge bg-primary">{{ $result['group_name'] }}</span>
                            </td>
                            <td>{{ $result['lottery_number'] }}</td>
                            <td>
                                <strong>{{ $result['participant_name'] }}</strong>
                            </td>
                            <td>
                                {{ $result['department'] ?? '-' }} / <span class="badge bg-info">{{ $result['shift'] }}</span>
                            </td>
                            <td>{{ $result['nik'] }}</td>
                            <td>
                                <span class="text-success fw-bold">
                                    Rp {{ number_format($result['bid_amount'], 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i><br>
                                    Belum ada hasil lelang untuk periode {{ \Carbon\Carbon::create()->month($selectedMonth)->locale('id')->monthName }} {{ $selectedYear }}
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Group Summary Cards -->
    <div class="row mb-4">
        @foreach($groupSummaries as $summary)
        <div class="col-md-6 col-lg-6 mb-3"> <!-- Changed to col-lg-6 since space is smaller (col-9) -->
            <div class="card h-100">
                <div class="card-header bg-info">
                    <h6 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        {{ $summary['group']->name }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="p-2">
                                <h4 class="text-primary mb-1">{{ $summary['period_count'] }}</h4>
                                <small class="text-muted">Jumlah Periode</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2">
                                <h4 class="text-success mb-1">{{ $summary['total_winners'] }}</h4>
                                <small class="text-muted">Jumlah Pemenang</small>
                            </div>
                        </div>
                    </div>
                    @if($summary['current_period'])
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">Periode Saat Ini:</small><br>
                        <strong class="text-primary">{{ $summary['current_period']->period_name }}</strong>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-top">
                    <a href="{{ route('admin.groups.manage', $summary['group']->id) }}" 
                       class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-right me-1"></i>
                        Kelola Kelompok
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

@endsection

@section('modals')
    <!-- Add Group Modal -->
    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGroupModalLabel">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Kelompok Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.groups.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Nama Kelompok <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_participants" class="form-label">
                                        <i class="fas fa-users me-1"></i>Maksimal Peserta <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                                           id="max_participants" name="max_participants" value="{{ old('max_participants', 90) }}" 
                                           min="2"  required>
                                    @error('max_participants')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimal 2 peserta</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="monthly_installment" class="form-label">
                                        <i class="fas fa-money-bill-wave me-1"></i>Angsuran Bulanan <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('monthly_installment') is-invalid @enderror" 
                                               id="monthly_installment" name="monthly_installment" 
                                               value="{{ old('monthly_installment', 175000) }}" min="10000" step="1000" required>
                                    </div>
                                    @error('monthly_installment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimal Rp 10.000</small>
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="main_prize" class="form-label">
                                        <i class="fas fa-gift me-1"></i>Hadiah Utama <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('main_prize') is-invalid @enderror" 
                                               id="main_prize" name="main_prize" 
                                               value="{{ old('main_prize', 17500000) }}" min="100000" step="10000" required>
                                    </div>
                                    @error('main_prize')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimal Rp 100.000</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="shu" class="form-label">
                                        <i class="fas fa-hand-holding-usd me-1"></i>SHU <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('shu') is-invalid @enderror" 
                                               id="shu" name="shu" 
                                               value="{{ old('shu', 500000) }}" min="0" step="10000" required>
                                    </div>
                                    @error('shu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sisa Hasil Usaha</small>
                                </div>
                            </div>
                           
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="min_bid" class="form-label">
                                        <i class="fas fa-arrow-down me-1"></i>Lelang Minimum <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('min_bid') is-invalid @enderror" 
                                               id="min_bid" name="min_bid" 
                                               value="{{ old('min_bid', 2250000) }}" min="0" step="10000" required>
                                    </div>
                                    @error('min_bid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Penawaran minimum</small>
                                </div>
                            </div>
                           
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_bid" class="form-label">
                                        <i class="fas fa-arrow-up me-1"></i>Lelang Maksimum <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('max_bid') is-invalid @enderror" 
                                               id="max_bid" name="max_bid" 
                                               value="{{ old('max_bid', 6000000) }}" min="0" step="10000" required>
                                    </div>
                                    @error('max_bid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Penawaran maksimum</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Deskripsi
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Opsional: Deskripsi singkat tentang kelompok</small>
                        </div>

                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Kelompok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto refresh every 30 seconds
    setInterval(function() {
        if (!document.querySelector('.modal.show')) { // Only refresh if no modal is open
            window.location.reload();
        }
    }, 30000);
</script>
@endpush