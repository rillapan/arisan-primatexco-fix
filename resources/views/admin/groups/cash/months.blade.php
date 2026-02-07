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
            <i class="fas fa-money-bill-wave me-2 text-success"></i>Kelola Kas - {{ $group->name }}
        </h1>
        <p class="text-muted mb-0">Kelola kas arisan</p>
    </div>
</div>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                        </div>
                    </div>


                        <!-- Group Info Cards -->
                        @php
                            $latestAccumulation = count($monthlyData) > 0 ? (collect($monthlyData)->first()['accumulation'] ?? 0) : 0;
                        @endphp
                        <div class="row mb-4 g-3">
                            <div class="col-md">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}</h5>
                                        <p class="mb-0 small">Iuran Bulanan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="card bg-primary text-white h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <h5>{{ $group->participants->where('is_active', true)->count() }} / {{ $group->winners->count() }}</h5>
                                        <p class="mb-0 small">Peserta Aktif / Pemenang</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="card bg-warning text-white h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <i class="fas fa-calendar fa-2x mb-2"></i>
                                        <h5>{{ $group->monthlyPeriods->count() }} Bulan</h5>
                                        <p class="mb-0 small">Total Periode / Kas Bulanan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="card bg-info text-white h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <i class="fas fa-gift fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($group->main_prize, 0, ',', '.') }}</h5>
                                        <p class="mb-0 small">Hadiah Utama</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="card bg-danger text-white h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <i class="fas fa-wallet fa-2x mb-2 text-white"></i>
                                        <h5>Rp {{ number_format($latestAccumulation, 0, ',', '.') }}</h5>
                                        <p class="mb-0 small">Akumulasi Kas Lelang</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Month Selection Cards -->
                        <h5 class="mb-3">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Pilih Bulan untuk Melihat Detail Kas
                        </h5>

                        <!-- Create New Month Button Removed - cash should only be created when creating periods, not automatically for groups -->

                        @forelse($monthlyData as $cardKey => $monthData)
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card h-100 border-primary hover-card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <!-- clickable area -->
                                            <div class="col-md-8">
                                                <a href="{{ route('admin.groups.cash.month.detail', ['groupId' => $group->id, 'monthKey' => $monthData['month_key'], 'period_id' => $monthData['period_id'] ?? null]) }}" 
                                                   class="text-decoration-none text-dark">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <div class="mb-2">
                                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Bulan Kas</small>
                                                                <h5 class="card-title text-primary mb-1 fw-bold">
                                                                    <i class="fas fa-calendar-check me-2"></i>
                                                                    {{ $monthData['month_name'] }}
                                                                </h5>
                                                            </div>
                                                            
                                                            @if(isset($monthData['period_name']) && $monthData['period_name'])
                                                                <div class="mb-2">
                                                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nama Periode</small>
                                                                    <div>
                                                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">
                                                                            <i class="fas fa-bookmark me-1"></i>
                                                                            {{ $monthData['period_name'] }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <p class="text-muted mb-0">
                                                                <small>
                                                                    <i class="far fa-clock me-1"></i>
                                                                    {{ $monthData['first_date']->format('d/m/Y') }} - {{ $monthData['last_date']->format('d/m/Y') }}
                                                                </small>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6 border-start">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <small class="text-muted d-block">Angsuran:</small>
                                                                    <span class="fw-bold {{ $monthData['payment_count'] >= $monthData['participant_count'] ? 'text-success' : 'text-danger' }}">
                                                                        {{ $monthData['payment_count'] }}/{{ $monthData['participant_count'] }}
                                                                    </span>
                                                                </div>
                                                                <div class="col-6">
                                                                    <small class="text-muted d-block">Sisa:</small> 
                                                                    @if($monthData['net_surplus'] <= 0)
                                                                        <span class="fw-bold text-muted">-</span>
                                                                    @else
                                                                        <span class="fw-bold text-primary">Rp {{ number_format($monthData['net_surplus'], 0, ',', '.') }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-12 mt-2">
                                                                    <small class="text-muted">Akumulasi:</small> 
                                                                    @if($monthData['accumulation'] <= 0)
                                                                        <span class="fw-bold text-muted">-</span>
                                                                    @else
                                                                        <span class="fw-bold text-info">Rp {{ number_format($monthData['accumulation'], 0, ',', '.') }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <!-- Action area (NOT inside <a>) -->
                                            <div class="col-md-4">
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('admin.groups.cash.month.detail', ['groupId' => $group->id, 'monthKey' => $monthData['month_key'], 'period_id' => $monthData['period_id'] ?? null]) }}" 
                                                       class="btn btn-outline-primary btn-sm position-relative">
                                                        <i class="fas fa-eye me-1"></i>
                                                        Lihat Detail
                                                        @if(isset($unreadPaymentCount) && $unreadPaymentCount > 0)
                                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                                {{ $unreadPaymentCount }}
                                                                <span class="visually-hidden">unread notifications</span>
                                                            </span>
                                                        @endif
                                                    </a>
                                                    <button class="btn btn-outline-danger btn-sm" type="button"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteMonthModal-{{ $cardKey }}">
                                                        <i class="fas fa-trash me-1"></i>
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Data Kas</h5>
                            <p class="text-muted">
                                Belum ada transaksi kas untuk kelompok ini. 
                                Tambahkan angsuran massal untuk memulai pencatatan kas bulanan.
                            </p>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkInstallmentModal">
                                <i class="fas fa-plus me-2"></i>
                                Tambah Angsuran Pertama
                            </button>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Installment Confirmation Modal -->
    <div class="modal fade" id="bulkInstallmentModal" tabindex="-1" aria-labelledby="bulkInstallmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkInstallmentModalLabel">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Angsuran Bulanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.groups.cash.bulk.installment', $group->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Informasi Penting</h6>
                            <ul class="mb-0">
                                <li>Admin menambahkan angsuran ke angsuran berikutnya (bertambah satu).</li>
                                <li>Angsuran akan otomatis diberikan kepada seluruh peserta melalui potongan gaji.</li>
                                <li>Informasi angsuran akan otomatis dikirimkan ke akun masing-masing peserta.</li>
                                <li>Setiap peserta akan menerima bukti angsuran yang dapat diakses melalui akun mereka.</li>
                            </ul>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Jumlah Peserta Aktif:</label>
                                <input type="text" class="form-control" value="{{ $group->participants->where('is_active', true)->count() }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Besar Angsuran per Peserta:</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" value="{{ number_format($group->monthly_installment, 0, ',', '.') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Angsuran Berikutnya:</label>
                                <input type="text" class="form-control fw-bold text-warning" 
                                       value="Setiap peserta mendapat angsuran berikutnya (bertambah satu)" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Total Angsuran yang Akan Diproses:</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold text-success" 
                                           value="{{ number_format($group->participants->where('is_active', true)->count() * $group->monthly_installment, 0, ',', '.') }}" 
                                           readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Tanggal Pembayaran</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="Contoh: Angsuran bulanan {{ date('F Y') }}"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="bulk_create_next_months" class="form-label">Buat Otomatis Untuk Bulan Mendatang</label>
                            <select class="form-select" id="bulk_create_next_months" name="create_next_months">
                                <option value="0">Hanya bulan ini</option>
                                <option value="1">Dan bulan depan (2 bulan total)</option>
                                <option value="2">Dan 2 bulan ke depan (3 bulan total)</option>
                                <option value="3">Dan 3 bulan ke depan (4 bulan total)</option>
                                <option value="5">Dan 5 bulan ke depan (6 bulan total)</option>
                                <option value="11">Setahun penuh (12 bulan total)</option>
                            </select>
                            <div class="form-text">Pilih berapa banyak bulan ke depan yang ingin Anda buat secara otomatis</div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Sistem akan otomatis melewati peserta yang sudah melakukan pembayaran untuk periode ini.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-plus me-2"></i>
                            Proses Angsuran untuk Semua Peserta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('styles')
    <style>
        .hover-card {
            transition: all 0.3s ease;
        }
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
@endpush

    <!-- Create New Month Modal Removed - cash should only be created when creating periods, not automatically for groups -->



@endsection

@section('modals')
    @foreach($monthlyData as $cardKey => $monthData)
    <!-- Delete Month Confirmation Modal -->
    <div class="modal fade" id="deleteMonthModal-{{ $cardKey }}" tabindex="-1" aria-labelledby="deleteMonthModalLabel-{{ $cardKey }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteMonthModalLabel-{{ $cardKey }}">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        Konfirmasi Hapus Kas Bulan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Perhatian:</strong> Aksi ini akan menghapus <strong>semua transaksi kas</strong> pada bulan <strong>{{ $monthData['month_name'] }}</strong>.
                        Data yang sudah dihapus tidak dapat dikembalikan.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Jumlah Transaksi:</small>
                            <div class="fw-bold">{{ $monthData['payment_count'] }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Total Kas Bulan Ini:</small>
                            <div class="fw-bold text-danger">Rp {{ number_format($monthData['total_amount'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.groups.cash.month.delete', ['groupId' => $group->id, 'monthKey' => $monthData['month_key']]) }}?period_id={{ $monthData['period_id'] ?? '' }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endsection

