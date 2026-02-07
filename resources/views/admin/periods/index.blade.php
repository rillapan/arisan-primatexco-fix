@extends('layouts.admin')

@section('title', 'Periode Arisan - Sistem Arisan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-calendar-alt me-2"></i>
                Periode Arisan
            </h2>
            @if(isset($selectedGroup))
                <p class="text-muted mb-0">Kelompok: {{ $selectedGroup->name }}</p>
            @else
                <p class="text-muted mb-0">Kelola semua periode arisan dari setiap kelompok</p>
            @endif
        </div>
    </div>

    @if(request('group_id'))
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $periods->count() }}</h4>
                    <small class="text-muted">Total Periode</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-gavel fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $periods->where('status', 'bidding')->count() }}</h4>
                    <small class="text-muted">Sedang Lelang</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $periods->where('status', 'completed')->count() }}</h4>
                    <small class="text-muted">Selesai</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.periods') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="group_filter" class="form-label fw-bold">
                            <i class="fas fa-users me-1"></i>Filter Kelompok
                        </label>
                        <select class="form-select" id="group_filter" name="group_id">
                            <option value="">-- Pilih Kelompok --</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.periods') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header bg-white text-dark">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>
            Daftar Periode
        </h5>
    </div>
    <div class="card-body p-0">
        @if(request('group_id'))
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 100px;">No Undian</th>
                            <th>Bulan</th>
                            <th>Pemenang</th>
                            <th>Tanggal</th>
                            <th>NIK</th>
                            <th>Bagian</th>
                            <th class="text-end pe-4" width="150">Sisa Kas</th>
                            <th class="text-end pe-4" width="150">Akumulasi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                <tbody>
                    @forelse($periods as $index => $period)
                        @php
                            $winnersCount = $period->winners->count();
                            $hasWinner = $winnersCount > 0;
                        @endphp
                        
                        @if($hasWinner)
                            <tr>
                                <td class="ps-4 fw-bold text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    @foreach($period->winners as $wIndex => $winner)
                                        <div class="winner-item">
                                            <span class="lottery-number">
                                                {{ $winner->participant->lottery_number ?? '-' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="fw-bold text-dark">
                                    {{ $period->period_start->locale('id')->monthName }}
                                    <span class="text-muted fw-normal ms-1">{{ $period->period_start->year }}</span>
                                </td>
                                <td>
                                    @foreach($period->winners as $wIndex => $winner)
                                        <div class="winner-item">
                                            <span class="d-block text-dark fw-bold">{{ $winner->participant->name ?? 'N/A' }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($period->winners as $wIndex => $winner)
                                        <div class="winner-item">
                                            @if($winner->draw_time)
                                                <div>
                                                    <span class="text-dark">{{ \Carbon\Carbon::parse($winner->draw_time)->format('d/m/Y') }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($winner->draw_time)->format('H:i') }} WIB</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($period->winners as $wIndex => $winner)
                                        <div class="winner-item">
                                            <div><i class="fas fa-id-card text-muted me-1"></i> {{ $winner->participant->nik ?? '-' }}</div>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($period->winners as $wIndex => $winner)
                                        <div class="winner-item">
                                            <div><i class="fas fa-briefcase text-muted me-1"></i> {{ $winner->participant->shift ?? '-' }}</div>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold {{ ($period->calculated_surplus ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format($period->calculated_surplus, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold {{ ($period->calculated_accumulation ?? 0) < 0 ? 'text-danger' : 'text-primary' }}">
                                        Rp {{ number_format($period->calculated_accumulation, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
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
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.periods.show', $period->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.periods.edit', $period->id) }}" class="btn btn-sm btn-outline-info" title="Edit Periode">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($period->status === 'active')
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning position-relative" 
                                                    onclick="updatePeriodStatus({{ $period->id }}, {{ $period->group_id }})"
                                                    title="Ubah status menjadi bidding">
                                                <i class="fas fa-gavel"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-danger" onclick="deletePeriod({{ $period->id }}, '{{ $period->period_name }}', {{ $period->bids->count() }}, {{ $period->winners->count() }})" title="Hapus Periode">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <!-- Empty Period Row -->
                            <tr>
                                <td class="ps-4 fw-bold text-center">{{ $index + 1 }}</td>
                                <td class="text-center text-muted">-</td>
                                <td class="fw-bold text-dark">
                                    {{ $period->period_start->locale('id')->monthName }}
                                    <span class="text-muted fw-normal ms-1">{{ $period->period_start->year }}</span>
                                </td>
                                <td class="text-muted fst-italic">
                                    @if($period->status == 'bidding')
                                        <span class="text-warning fw-bold"><i class="fas fa-gavel me-1"></i>Sedang Lelang</span>
                                    @else
                                        Belum ada pemenang
                                    @endif
                                </td>
                                <td class="text-muted text-center">-</td>
                                <td class="text-muted text-center">-</td>
                                <td class="text-muted text-center">-</td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold {{ ($period->calculated_surplus ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format($period->calculated_surplus, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold {{ ($period->calculated_accumulation ?? 0) < 0 ? 'text-danger' : 'text-primary' }}">
                                        Rp {{ number_format($period->calculated_accumulation, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
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
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.periods.show', $period->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.periods.edit', $period->id) }}" class="btn btn-sm btn-outline-info" title="Edit Periode">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($period->status === 'active')
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning position-relative" 
                                                    onclick="updatePeriodStatus({{ $period->id }}, {{ $period->group_id }})"
                                                    title="Ubah status menjadi bidding">
                                                <i class="fas fa-gavel"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-danger" onclick="deletePeriod({{ $period->id }}, '{{ $period->period_name }}', {{ $period->bids->count() }}, {{ $period->winners->count() }})" title="Hapus Periode">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada periode bulanan</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPeriodModal">
                                <i class="fas fa-plus me-1"></i>Tambah Periode Baru
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-hand-point-up fa-3x text-primary mb-3"></i>
            <h5 class="text-muted">Pilih kelompok yang akan ditampilkan periodenya</h5>
        </div>
    @endif
    </div>
</div>
@endsection

@section('modals')
    <!-- Create Period Modal -->
    <div class="modal fade" id="createPeriodModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Periode Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ isset($selectedGroup) ? route('admin.groups.periods.store', $selectedGroup->id) : route('admin.periods.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="group_id" class="form-label">
                                        <i class="fas fa-users me-1"></i>Kelompok <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('group_id') is-invalid @enderror" 
                                            id="group_id" name="group_id" required {{ isset($selectedGroup) && $selectedGroup ? 'disabled' : '' }}>
                                        @if(isset($selectedGroup) && $selectedGroup)
                                            <option value="{{ $selectedGroup->id }}" selected>{{ $selectedGroup->name }}</option>
                                        @else
                                            <option value="">Pilih Kelompok</option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                                    {{ $group->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if(isset($selectedGroup) && $selectedGroup)
                                        <input type="hidden" name="group_id" value="{{ $selectedGroup->id }}">
                                    @endif
                                    @error('group_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_name" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Nama Periode <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('period_name') is-invalid @enderror" 
                                           id="period_name" name="period_name" value="{{ old('period_name') }}" required>
                                    @error('period_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_start" class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i>Tanggal Mulai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('period_start') is-invalid @enderror" 
                                           id="period_start" name="period_start" value="{{ old('period_start') }}" required>
                                    @error('period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_end" class="form-label">
                                        <i class="fas fa-calendar-check me-1"></i>Tanggal Selesai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('period_end') is-invalid @enderror" 
                                           id="period_end" name="period_end" value="{{ old('period_end') }}" required>
                                    @error('period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="previous_cash_balance" class="form-label">
                                        <i class="fas fa-money-bill-wave me-1"></i>Saldo Kas Sebelumnya
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('previous_cash_balance') is-invalid @enderror" 
                                               id="previous_cash_balance" name="previous_cash_balance" 
                                               value="{{ old('previous_cash_balance', 0) }}" min="0" step="1000">
                                    </div>
                                    @error('previous_cash_balance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>Status
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="bidding" {{ old('status') == 'bidding' ? 'selected' : '' }}>Bidding</option>
                                        <option value="drawing" {{ old('status') == 'drawing' ? 'selected' : '' }}>Drawing</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Periode
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<style>
.badge-lottery {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-block;
}

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
<script>
    function deletePeriod(id, periodName, bidsCount, winnersCount) {
        let confirmMessage = `Apakah Anda yakin ingin menghapus periode "${periodName}"?`;
        
        if (bidsCount > 0 || winnersCount > 0) {
            confirmMessage += '\n\n⚠️ PERINGATAN: Periode ini memiliki:';
            if (bidsCount > 0) confirmMessage += `\n• ${bidsCount} bid/lelang`;
            if (winnersCount > 0) confirmMessage += `\n• ${winnersCount} pemenang`;
            confirmMessage += '\n\nMenghapus periode ini akan menghapus semua data terkait!';
        }
        
        if (confirm(confirmMessage)) {
            // Check for CSRF token
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfMeta) {
                alert('Error: CSRF token meta tag not found. Please refresh the page.');
                return;
            }
            
            const csrfToken = csrfMeta.getAttribute('content');
            
            // Create form and submit with DELETE method
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.periods.delete", ":id") }}'.replace(':id', id);
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add DELETE method override
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    }

    function updatePeriodStatus(periodId, groupId) {
        if (confirm('Ubah status periode ini menjadi bidding? Peserta akan dapat memasukkan bid.')) {
            // Make AJAX call to update status
            fetch(`/admin/groups/${groupId}/periods/${periodId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show updated status
                    window.location.reload();
                } else {
                    alert('Gagal mengubah status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengubah status');
            });
        }
    }
</script>
@endpush
