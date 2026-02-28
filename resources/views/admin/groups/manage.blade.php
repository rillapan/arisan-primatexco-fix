@extends('layouts.admin')

@section('title', 'Kelola Kelompok - ' . $group->name)

@push('styles')
<style>
    /* Custom Card Styles for this page */
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    
    .text-xs { font-size: .7rem; }
    .text-gray-300 { color: #dddfeb!important; }
    .text-gray-800 { color: #5a5c69!important; }
    
    /* Action Cards */
    .action-card {
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        background: white;
        height: 100%;
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        border-color: #4e73df;
        text-decoration: none; /* Prevent underline on hover */
    }
    
    .icon-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        transition: transform 0.3s ease;
    }

    .action-card:hover .icon-wrapper {
        transform: scale(1.1);
    }
    
    /* Color variants for icon wrappers */
    .icon-wrapper.bg-primary-soft { background-color: rgba(78, 115, 223, 0.1); color: #4e73df; }
    .icon-wrapper.bg-success-soft { background-color: rgba(28, 200, 138, 0.1); color: #1cc88a; }
    .icon-wrapper.bg-warning-soft { background-color: rgba(246, 194, 62, 0.1); color: #f6c23e; }
    .icon-wrapper.bg-info-soft    { background-color: rgba(54, 185, 204, 0.1); color: #36b9cc; }

    /* Table Styles */
    .avatar-circle-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f8f9fc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: #6e707e;
    }
    
    .table thead th {
        background-color: #e3f2fd;
        color: #262628;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e3e6f0;
        border-top: none;
    }
    
    .table tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 0.75rem;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .badge-lottery {
        background-color: #eaecf4;
        color: #5a5c69;
        font-weight: 600;
        padding: 0.35em 0.65em;
        border-radius: 0.35rem;
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
    
    .winner-item {
        padding: 0.25rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .winner-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .winner-item:first-child {
        padding-top: 0;
    }
    
    .font-weight-medium { font-weight: 500; }
    .font-weight-bold { font-weight: 700; }
    
    .empty-state-icon {
        background-color: #f8f9fc;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .bg-soft-professional {
        background: linear-gradient(180deg, #ffffff 0%, #f1f4f9 100%);
        border: none;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #printable-area, #printable-area * {
            visibility: visible;
        }
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }
        /* Hide the print button and other non-essential elements inside the card when printing */
        .d-print-none {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-users-cog me-2 text-primary"></i>{{ $group->name }}
        </h1>
        <p class="text-muted mb-0">Dashboard Kelola Kelompok Arisan</p>
    </div>
</div>

@if(!request()->has('from_menu'))
<!-- Stats Cards Row -->
<div class="row mb-4">
    <!-- Peserta Aktif Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Peserta Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $group->participants->count() }}</div>
                        <div class="text-xs text-muted mt-1">
                            <span class="text-nowrap">Maks: {{ $group->max_participants }} peserta</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Iuran Bulanan Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Iuran Bulanan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}</div>
                         <div class="text-xs text-muted mt-1">
                            <span class="text-nowrap">setiap peserta dalam satu bulan</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hadiah Utama Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Hadiah Utama</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($group->main_prize, 0, ',', '.') }}</div>
                         <div class="text-xs text-muted mt-1">
                            <span class="text-nowrap">Total hadiah menang</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-gift fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Peserta Sudah Menang Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Peserta Sudah Menang</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $group->participants->where('has_won', true)->count() }}
                        </div>
                         <div class="text-xs text-muted mt-1">
                            <span class="text-nowrap">Total pemenang</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card shadow mb-4 bg-soft-professional">
    <div class="card-header py-3 bg-transparent border-bottom-0">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-tools me-2"></i>Kelola kelompok {{ $group->name }}
        </h6>
    </div>
    <div class="card-body pt-0">
        <!-- Row 1: 3 Items -->
        <div class="row g-3 row-cols-1 row-cols-md-3 mb-3">
            <div class="col">
                <a href="{{ route('admin.groups.participants.manage', $group->id) }}" class="card action-card text-decoration-none p-3 h-100">
                    <div class="text-center">
                        <div class="icon-wrapper bg-primary-soft mb-2">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Kelola Peserta</h6>
                        <small class="text-muted d-block">Tambah/Edit Peserta</small>
                    </div>
                </a>
            </div>

             <div class="col">
                <a href="{{ route('admin.groups.periods.create', $group->id) }}" class="card action-card text-decoration-none p-3 h-100">
                    <div class="text-center">
                        <div class="icon-wrapper bg-info-soft mb-2">
                            <i class="fas fa-calendar-plus fa-lg"></i>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Periode Baru</h6>
                        <small class="text-muted d-block">Buat Bulan Baru</small>
                    </div>
                </a>
            </div>
            
            <div class="col">
                <a href="{{ route('admin.groups.cash.manage', $group->id) }}" class="card action-card text-decoration-none p-3 h-100 position-relative">
                    <div class="text-center">
                        <div class="icon-wrapper bg-success-soft mb-2">
                            <i class="fas fa-coins fa-lg"></i>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Kelola Kas</h6>
                        <small class="text-muted d-block">Laporan Keuangan</small>
                        @if(isset($unreadPaymentCount) && $unreadPaymentCount > 0)
                            <span class="position-absolute top-0 end-0 m-3 badge rounded-pill bg-danger">
                                {{ $unreadPaymentCount }}
                            </span>
                        @endif
                    </div>
                </a>
            </div>
        </div>

        <!-- Row 2: 3 Items -->
        <div class="row g-3 row-cols-1 row-cols-md-3">
            <div class="col">
                <a href="{{ route('admin.groups.auction.process', $group->id) }}" class="card action-card text-decoration-none p-3 h-100">
                    <div class="text-center">
                        <div class="icon-wrapper bg-warning-soft mb-2">
                            <i class="fas fa-gavel fa-lg"></i>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Proses Undian</h6>
                        <small class="text-muted d-block">Mulai Kocok Arisan</small>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="{{ route('admin.groups.settings', $group->id) }}" class="card action-card text-decoration-none p-3 h-100">
                    <div class="text-center">
                        <div class="icon-wrapper bg-secondary-soft mb-2" style="background-color: rgba(108, 117, 125, 0.1); color: #6c757d;">
                            <i class="fas fa-cog fa-lg"></i>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Edit Kelompok</h6>
                        <small class="text-muted d-block">Ubah Data Kelompok</small>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="{{ route('admin.drive-link.index') }}" class="card action-card text-decoration-none p-3 h-100">
                    <div class="text-center">
                        <div class="icon-wrapper bg-primary-soft mb-2" style="background-color: rgba(28, 200, 138, 0.1); color: #1cc88a;">
                            <i class="fab fa-google-drive fa-lg"></i>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Link Drive</h6>
                        <small class="text-muted d-block">Dokumentasi Google Drive</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Winners Table -->
<div class="card shadow mb-4" id="printable-area">
    <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list-ol me-2"></i>Daftar pemenang arisan {{ $group->name }}
        </h6>
        <a href="{{ route('admin.groups.winners.export-pdf', $group->id) }}" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                       
                        <th class="ps-4" style="width: 80px;">Periode</th>
                        <th class="text-center" style="width: 100px;">No Undian</th>
                        <th>Bulan</th>
                        <th>Pemenang</th>
                        <th>Tanggal</th>
                        <th>NIK</th>
                        <th>Bagian</th>
                        <th class="text-end pe-4" width="150">Sisa Kas</th>
                        <th class="text-end pe-4" width="150">Akumulasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($group->monthlyPeriods as $index => $period)
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
                                <td class="text-muted fst-italic">Belum ada pemenang</td>
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
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                                </div>
                                <h6 class="text-gray-800 fw-bold">Belum Ada Periode</h6>
                                <p class="text-muted mb-3">Kelompok ini belum memiliki periode arisan.</p>
                                <a href="{{ route('admin.groups.periods.create', $group->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Buat Periode Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection