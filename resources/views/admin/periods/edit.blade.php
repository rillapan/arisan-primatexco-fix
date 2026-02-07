@extends('layouts.admin')

@section('title', 'Edit Periode - Sistem Arisan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-edit me-2"></i>
                Edit Periode
            </h2>
            <p class="text-muted mb-0">Perbarui informasi periode arisan</p>
        </div>
        <a href="{{ route('admin.periods') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.periods.update', $period->id) }}" method="POST" id="editPeriodForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="final_saksi_ids" name="saksi_ids" value="{{ $period->saksis->pluck('participant_id')->implode(',') }}">

                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white text-dark">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Dasar</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="group_id" class="form-label fw-bold">Kelompok <span class="text-danger">*</span></label>
                                <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" disabled>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ old('group_id', $period->group_id) == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="group_id" value="{{ $period->group_id }}">
                            </div>
                            <div class="col-md-6">
                                <label for="period_name" class="form-label fw-bold">Nama Periode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('period_name') is-invalid @enderror" id="period_name" name="period_name" value="{{ old('period_name', $period->period_name) }}" required>
                                <small class="text-muted">Wajib unik dalam sistem.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="motor_slots" class="form-label fw-bold">Jumlah Pemenang (Slot) <span class="text-danger">*</span></label>
                                <select class="form-select @error('motor_slots') is-invalid @enderror" id="motor_slots" disabled>
                                    <option value="1" {{ old('motor_slots', $period->motor_slots) == 1 ? 'selected' : '' }}>1 Pemenang</option>
                                    <option value="2" {{ old('motor_slots', $period->motor_slots) == 2 ? 'selected' : '' }}>2 Pemenang</option>
                                </select>
                                <input type="hidden" name="motor_slots" value="{{ $period->motor_slots }}">
                                <small class="text-muted">Tentukan berapa banyak pemenang untuk periode ini.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" disabled>
                                    <option value="active" {{ old('status', $period->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="bidding" {{ old('status', $period->status) == 'bidding' ? 'selected' : '' }}>Bidding</option>
                                    <option value="drawing" {{ old('status', $period->status) == 'drawing' ? 'selected' : '' }}>Drawing</option>
                                    <option value="completed" {{ old('status', $period->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                <input type="hidden" name="status" value="{{ $period->status }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="period_start" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('period_start') is-invalid @enderror" id="period_start" name="period_start" value="{{ old('period_start', $period->period_start->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="period_end" class="form-label fw-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('period_end') is-invalid @enderror" id="period_end" name="period_end" value="{{ old('period_end', $period->period_end->format('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bid_deadline" class="form-label fw-bold">Batas Waktu Lelang</label>
                                <input type="datetime-local" class="form-control @error('bid_deadline') is-invalid @enderror" id="bid_deadline" name="bid_deadline" value="{{ old('bid_deadline', $period->bid_deadline ? $period->bid_deadline->format('Y-m-d\TH:i') : '') }}">
                                <small class="text-muted">Tentukan batas akhir peserta memasukkan bid.</small>
                            </div>
                        </div>
                    </div>
                </div>

                 Source & Cash Information
                <div class="card mb-4">
                    <div class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>Sumber Saldo & Kas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="previous_cash_balance" class="form-label fw-bold">Saldo Kas Sebelumnya <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('previous_cash_balance') is-invalid @enderror" 
                                           id="previous_cash_balance" name="previous_cash_balance" 
                                           value="{{ old('previous_cash_balance', (int)$period->previous_cash_balance) }}" 
                                           min="0" step="1000" required>
                                </div>
                                <small class="text-muted">Saldo yang dibawa dari periode/bulan sebelumnya.</small>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border">
                                    <small class="text-muted d-block mb-1">Total Dana Perkiraan:</small>
                                    <h4 class="mb-0 text-primary">Rp {{ number_format($period->total_amount, 0, ',', '.') }}</h4>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <small>Estimasi Angsuran:</small>
                                        <small class="fw-bold">Rp {{ number_format($period->total_installments, 0, ',', '.') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info bg-info bg-opacity-10 border-info mb-0">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-1"></i>Catatan Bisnis</h6>
                            <p class="small mb-0">Perubahan pada saldo kas akan secara otomatis memperbarui <strong>Data Tersedia</strong> dan <strong>Estimasi SHU</strong>. Jika periode sudah memiliki bid atau pemenang, pastikan perubahan tidak mengacaukan perhitungan yang sudah ada.</p>
                        </div>
                    </div>
                </div> 

                <div class="card mb-4">
                    <div class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-gavel me-2"></i>Daftar Peserta Lelang</h5>
                        <a href="{{ route('admin.groups.auction.manual-bid', [$period->group_id, $period->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>Input Bid Manual
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3" width="10%">No Undian</th>
                                        <th width="30%">Nama Peserta</th>
                                        <th width="25%">Nilai Lelang</th>
                                        <th width="15%">Status</th>
                                        <th class="text-end pe-3" width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($period->bids as $bid)
                                        <tr>
                                            <td class="ps-3 fw-bold">{{ $bid->participant->lottery_number }}</td>
                                            <td>{{ $bid->participant->name }}</td>
                                            <td class="fw-bold text-success">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</td>
                                            <td>
                                                @if($bid->status === 'submitted')
                                                    <span class="badge bg-success">Disubmit</span>
                                                @elseif($bid->status === 'accepted')
                                                    <span class="badge bg-primary">Diterima</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $bid->status }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.bids.edit', $bid->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Bid">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($period->status !== 'completed')
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-bid-btn" 
                                                                data-bid-id="{{ $bid->id }}" 
                                                                title="Hapus Bid">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted fst-italic">
                                                Belum ada peserta lelang untuk periode ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Witness Selection -->
                <div class="card mb-4">
                    <div class="card-header bg-white text-dark">
                        <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Data Saksi</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Pilih saksi untuk periode ini. Perubahan saksi tidak akan mempengaruhi hasil undian yang sudah dilakukan.</p>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-md-5">
                                <select class="form-select" id="groupFilter">
                                    <option value="">Semua Kelompok</option>
                                    @php
                                        $witnessGroups = $eligibleWitnesses->pluck('group')->unique('id');
                                    @endphp
                                    @foreach($witnessGroups as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" id="nameSearch" placeholder="Cari nama atau NIK peserta saksi...">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-10">
                                <select class="form-select" id="saksi_selector">
                                    <option value="">-- Pilih Peserta --</option>
                                    @foreach($eligibleWitnesses as $participant)
                                        <option value="{{ $participant->id }}" 
                                                data-group-id="{{ $participant->group->id }}"
                                                data-nama="{{ $participant->name }}"
                                                data-nik="{{ $participant->nik ?? '-' }}"
                                                data-group-name="{{ $participant->group->name }}">
                                            {{ $participant->name }} - {{ $participant->group->name }} ({{ $participant->nik ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success w-100" id="addSaksiBtn">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div id="saksiPreview" class="mt-3">
                            <div class="card border-info bg-info bg-opacity-10">
                                <div class="card-body p-3">
                                    <h6 class="mb-2 fw-bold text-info"><i class="fas fa-users me-2"></i>Saksi Terpilih:</h6>
                                    <div id="selectedSaksiList">
                                        <!-- Selected witnesses -->
                                        @foreach($period->saksis as $saksi)
                                            <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border mb-2 shadow-sm">
                                                <div>
                                                    <span class="fw-bold">{{ $saksi->participant->name ?? $saksi->nama_pengurus }}</span> 
                                                    <span class="badge bg-secondary ms-2">{{ $saksi->participant->group->name ?? '-' }}</span>
                                                    <small class="text-muted ms-2">NIK: {{ $saksi->participant->nik ?? '-' }}</small>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-saksi" data-id="{{ $saksi->participant_id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Summary Info -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Ringkasan Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Jumlah Lelang Saat Ini:</small>
                        <h4 class="mb-0 text-dark">{{ $period->bids->count() }} Penawaran</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Pemenang Ditetapkan:</small>
                        <h4 class="mb-0 text-success">{{ $period->winners->count() }} Pemenang</h4>
                    </div>
                    <hr>
                    @if($period->winners->isNotEmpty())
                        <div class="alert alert-warning py-2 mb-0">
                            <small><i class="fas fa-exclamation-triangle me-1"></i> Periode sudah selesai/memiliki pemenang. Berhati-hatilah saat mengubah data keuangan.</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Winners List if any -->
            @if($period->winners->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-white text-primary">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Pemenang</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($period->winners as $winner)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $winner->participant->name ?? 'N/A' }}</h6>
                                    <small class="text-muted">Bid: Rp {{ number_format($winner->bid_amount, 0, ',', '.') }}</small>
                                </div>
                                <span class="badge bg-success">Pemenang</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden Delete Bid Forms (outside main form to avoid nesting) -->
@foreach($period->bids as $bid)
    <form id="delete-bid-form-{{ $bid->id }}" action="{{ route('admin.bids.delete', $bid->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const groupFilter = document.getElementById('groupFilter');
        const nameSearch = document.getElementById('nameSearch');
        const saksiSelector = document.getElementById('saksi_selector');
        const addSaksiBtn = document.getElementById('addSaksiBtn');
        const saksiPreview = document.getElementById('saksiPreview');
        const selectedSaksiList = document.getElementById('selectedSaksiList');
        const finalSaksiIdsInput = document.getElementById('final_saksi_ids');

        // Initialize set from input
        let selectedSaksiIds = new Set(finalSaksiIdsInput.value ? finalSaksiIdsInput.value.split(',').filter(id => id !== '') : []);
        const allSaksiOptions = Array.from(saksiSelector.options).slice(1);

        function filterSaksi() {
            const groupVal = groupFilter.value;
            const searchVal = nameSearch.value.toLowerCase();

            saksiSelector.innerHTML = '<option value="">-- Pilih Peserta --</option>';

            allSaksiOptions.forEach(opt => {
                const optGroup = opt.getAttribute('data-group-id');
                const optName = opt.getAttribute('data-nama').toLowerCase();
                const optNik = opt.getAttribute('data-nik').toLowerCase();
                const optId = opt.value;

                if (selectedSaksiIds.has(optId)) return;

                const matchGroup = !groupVal || optGroup === groupVal;
                const matchSearch = !searchVal || optName.includes(searchVal) || optNik.includes(searchVal);

                if (matchGroup && matchSearch) {
                    saksiSelector.appendChild(opt.cloneNode(true));
                }
            });
        }

        function updateSaksiUI() {
            if (selectedSaksiIds.size === 0) {
                saksiPreview.style.display = 'none';
                finalSaksiIdsInput.value = '';
            } else {
                saksiPreview.style.display = 'block';
                selectedSaksiList.innerHTML = '';
                
                const idsArray = Array.from(selectedSaksiIds);
                finalSaksiIdsInput.value = idsArray.join(',');

                idsArray.forEach(id => {
                    const opt = allSaksiOptions.find(o => o.value === id);
                    if (opt) {
                        const nama = opt.getAttribute('data-nama');
                        const groupName = opt.getAttribute('data-group-name');
                        const nik = opt.getAttribute('data-nik');

                        const div = document.createElement('div');
                        div.className = 'd-flex justify-content-between align-items-center bg-white p-2 rounded border mb-2 shadow-sm';
                        div.innerHTML = `
                            <div>
                                <span class="fw-bold">${nama}</span> 
                                <span class="badge bg-secondary ms-2">${groupName}</span>
                                <small class="text-muted ms-2">NIK: ${nik}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-saksi" data-id="${id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                        selectedSaksiList.appendChild(div);
                    }
                });

                document.querySelectorAll('.remove-saksi').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        selectedSaksiIds.delete(id);
                        updateSaksiUI();
                        filterSaksi();
                    });
                });
            }
        }

        groupFilter.addEventListener('change', filterSaksi);
        nameSearch.addEventListener('input', filterSaksi);

        addSaksiBtn.addEventListener('click', () => {
            const val = saksiSelector.value;
            if (val) {
                selectedSaksiIds.add(val);
                updateSaksiUI();
                saksiSelector.value = '';
                filterSaksi();
            }
        });

        // Initialize UI
        updateSaksiUI();
        filterSaksi();

        // Date Validation
        const startDateInput = document.getElementById('period_start');
        const endDateInput = document.getElementById('period_end');

        function validateDates() {
            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);
            if (end < start) {
                endDateInput.setCustomValidity('Tanggal selesai tidak boleh sebelum tanggal mulai');
            } else {
                endDateInput.setCustomValidity('');
            }
        }

        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);

        // Handle Delete Bid Buttons
        document.querySelectorAll('.delete-bid-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const bidId = this.getAttribute('data-bid-id');
                if (confirm('Apakah Anda yakin ingin menghapus lelang dari peserta ini?')) {
                    const form = document.getElementById('delete-bid-form-' + bidId);
                    if (form) {
                        form.submit();
                    }
                }
            });
        });
    });
</script>
@endpush
