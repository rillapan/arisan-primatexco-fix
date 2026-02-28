@extends('layouts.participant')

@section('title', 'Dashboard Peserta - Sistem Arisan')

@section('content')
    <h2>
        <i class="fas fa-tachometer-alt me-2"></i>
        Dashboard Peserta
    </h2>

    <!-- @if(!$participant->photo)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
            <div class="d-flex align-items-center">
                <i class="fas fa-camera-retro fs-3 me-3 text-warning"></i>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1">Foto Profil Belum Terisi!</h6>
                    <p class="small mb-0 opacity-75">Untuk kelengkapan data, Anda <strong>wajib</strong> mengunggah foto profil di halaman profil.</p>
                </div>
                <a href="{{ route('participant.profile') }}" class="btn btn-warning btn-sm fw-bold rounded-pill px-3">
                    Upload Sekarang
                </a>
            </div>
        </div>
    @endif -->

    {{-- Alert moved to Welcome Card --}}

    <!-- Welcome Card -->
    <div class="card shadow-sm border-0 mb-4" style="overflow: visible;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
              
                Informasi Peserta
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    @if($participant->photo)
                        <img src="{{ storage_url($participant->photo) }}" alt="Foto" class="rounded-circle shadow border" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border shadow-sm mx-auto" style="width: 100px; height: 100px;">
                            <i class="fas fa-user fa-3x text-muted opacity-30"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-primary fw-bold mb-3">Selamat Datang, {{ $participant->name }}!</h4>
                    
                    @if(isset($relatedParticipants) && $relatedParticipants->count() > 0)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Beralih Akun:</small>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="switchAccountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-exchange-alt me-1"></i> Pilih Akun Lain ({{ $relatedParticipants->count() }})
                                </button>
                                <ul class="dropdown-menu shadow border-0" aria-labelledby="switchAccountDropdown" style="max-height: 300px; overflow-y: auto; z-index: 1050;">
                                    @foreach($relatedParticipants as $relAccount)
                                        <li>
                                            <a class="dropdown-item d-flex justify-content-between align-items-center py-2" href="#" onclick="event.preventDefault(); document.getElementById('card-switch-account-{{ $relAccount->id }}').submit();">
                                                <span>
                                                    <strong class="text-primary">{{ $relAccount->lottery_number }}</strong>
                                                    <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $relAccount->group->name }}</small>
                                                </span>
                                                <i class="fas fa-chevron-right small text-muted ms-3"></i>
                                            </a>
                                            <form id="card-switch-account-{{ $relAccount->id }}" action="{{ route('participant.switch-account', $relAccount->id) }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-6 col-sm-4">
                            <small class="text-muted d-block">No. Undian</small>
                            <span class="fw-bold">{{ $participant->lottery_number }}</span>
                        </div>
                        <div class="col-6 col-sm-4">
                            <small class="text-muted d-block">Shift</small>
                            <span class="fw-bold">{{ $participant->shift }}</span>
                        </div>
                        <div class="col-12 col-sm-4">
                            <small class="text-muted d-block">Kelompok</small>
                            <span class="fw-bold">{{ $participant->group->name }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-start text-md-end mt-4 mt-md-0">
                    @if($participant->has_won)
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                            <i class="fas fa-trophy me-1"></i>SUDAH MENANG
                        </span>
                    @else
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="fas fa-check-circle me-1"></i>MASIH AKTIF
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Menu Buttons (Sidebar Items) -->
    <div class="row g-2 mb-4 row-cols-3 row-cols-md-6">
        <div class="col">
            <a href="{{ route('participant.results') }}" class="btn btn-outline-primary w-100 py-3 h-100 d-flex flex-column align-items-center justify-content-center shadow-sm px-1">
                <i class="fas fa-chart-bar fa-lg mb-2"></i>
                <span class="fw-bold d-none d-md-inline" style="font-size: 0.85rem;">Hasil Arisan</span>
                <span class="fw-bold d-md-none" style="font-size: 0.7rem;">Hasil</span>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('participant.winners') }}" class="btn btn-outline-success w-100 py-3 h-100 d-flex flex-column align-items-center justify-content-center shadow-sm px-1">
                <i class="fas fa-trophy fa-lg mb-2"></i>
                <span class="fw-bold d-none d-md-inline" style="font-size: 0.85rem;">Daftar Pemenang</span>
                <span class="fw-bold d-md-none" style="font-size: 0.7rem;">Pemenang</span>
            </a>
        </div>
        @php
            $hasUnread = isset($unreadBuktiAngsuranCount) && $unreadBuktiAngsuranCount > 0;
        @endphp
        <div class="col">
            <a href="{{ route('participant.bukti.angsuran') }}" class="btn btn-outline-info w-100 py-3 h-100 d-flex flex-column align-items-center justify-content-center shadow-sm position-relative px-1">
                <i class="fas fa-file-invoice fa-lg mb-2 {{ $hasUnread ? 'animate__animated animate__swing animate__infinite infinite' : '' }}"></i>
                <span class="fw-bold d-none d-md-inline" style="font-size: 0.85rem;">Bukti Angsuran</span>
                <span class="fw-bold d-md-none" style="font-size: 0.7rem;">Bukti</span>
                @if($hasUnread)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        {{ $unreadBuktiAngsuranCount }}
                    </span>
                @endif
            </a>
        </div>
        <div class="col">
            <a href="{{ route('participant.kta') }}" class="btn btn-outline-danger w-100 py-3 h-100 d-flex flex-column align-items-center justify-content-center shadow-sm px-1">
                <i class="fas fa-id-card fa-lg mb-2"></i>
                <span class="fw-bold d-none d-md-inline" style="font-size: 0.85rem;">Kartu Anggota</span>
                <span class="fw-bold d-md-none" style="font-size: 0.7rem;">KTA</span>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('participant.hubungi-kami') }}" class="btn btn-outline-warning w-100 py-3 h-100 d-flex flex-column align-items-center justify-content-center shadow-sm px-1">
                <i class="fas fa-headset fa-lg mb-2"></i>
                <span class="fw-bold d-none d-md-inline" style="font-size: 0.85rem;">Hubungi Kami</span>
                <span class="fw-bold d-md-none" style="font-size: 0.7rem;">Kontak</span>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('participant.drive-link') }}" class="btn btn-outline-secondary w-100 py-3 h-100 d-flex flex-column align-items-center justify-content-center shadow-sm px-1">
                <i class="fab fa-google-drive fa-lg mb-2"></i>
                <span class="fw-bold d-none d-md-inline" style="font-size: 0.85rem;">Link Drive</span>
                <span class="fw-bold d-md-none" style="font-size: 0.7rem;">Drive</span>
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-list-ol me-2 text-primary"></i>
            Daftar Pemenang Arisan
        </h4>
        <small class="text-muted">{{ $participant->group->name }}</small>
    </div>

    <!-- Winners Table (Desktop) -->
    @php
        $displayPeriods = $allPeriods->sortByDesc('period_start');
        $recentPeriods = $displayPeriods->take(5);
        $showAllPeriods = request()->query('show_all', false);
        $periodsToShow = $showAllPeriods ? $displayPeriods : $recentPeriods;
    @endphp

    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 table-hover align-middle" style="min-width: 1000px;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">Prd</th>
                            <th class="text-center" style="width: 100px;">No Undian</th>
                            <th>Bulan</th>
                            <th>Pemenang</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">NIK</th>
                            <th class="text-center">Bagian</th>
                            <th class="text-end pe-4">Sisa Kas</th>
                            <th class="text-end pe-4">Akumulasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- PHP Logic moved above --}}
                        @forelse($periodsToShow as $index => $period)
                            @php
                                $winnersCount = $period->winners->count();
                                $hasWinner = $winnersCount > 0;
                                $actualIndex = $displayPeriods->search($period) + 1;
                            @endphp
                            
                            @if($hasWinner)
                                <tr>
                                    <td class="ps-4 fw-bold text-center">{{ $actualIndex }}</td>
                                    <td class="text-center">
                                        @foreach($period->winners as $winner)
                                            <div class="winner-item mb-1">
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
                                        @foreach($period->winners as $winner)
                                            <div class="winner-item mb-1">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1">
                                                        <span class="d-block text-dark fw-bold">{{ $winner->participant->name ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($period->winners as $winner)
                                            <div class="winner-item mb-1">
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
                                        @foreach($period->winners as $winner)
                                            <div class="winner-item mb-1">
                                                <div><i class="fas fa-id-card text-muted me-1"></i> {{ $winner->participant->nik ?? '-' }}</div>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($period->winners as $winner)
                                            <div class="winner-item mb-1">
                                                <div><i class="fas fa-briefcase text-muted me-1"></i> {{ $winner->participant->shift ?? '-' }}</div>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="text-end pe-4">
                                        @if(($period->calculated_surplus ?? 0) == 0)
                                            <span class="fw-bold text-muted">-</span>
                                        @else
                                            <span class="fw-bold text-success">Rp {{ number_format($period->calculated_surplus, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if(($period->calculated_accumulation ?? 0) == 0)
                                            <span class="fw-bold text-muted">-</span>
                                        @else
                                            <span class="fw-bold text-primary">Rp {{ number_format($period->calculated_accumulation, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="ps-4 fw-bold text-center">{{ $actualIndex }}</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="fw-bold text-dark">{{ $period->period_name }}</td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="text-muted fst-italic">{{ $period->period_name }}</div>
                                                <div class="text-muted fst-italic small">Belum ada pemenang</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted text-center">-</td>
                                    <td class="text-muted text-center">-</td>
                                    <td class="text-muted text-center">-</td>
                                    <td class="text-end pe-4">
                                        @if(($period->calculated_surplus ?? 0) == 0)
                                            <span class="fw-bold text-muted">-</span>
                                        @else
                                            <span class="fw-bold text-success">Rp {{ number_format($period->calculated_surplus, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if(($period->calculated_accumulation ?? 0) == 0)
                                            <span class="fw-bold text-muted">-</span>
                                        @else
                                            <span class="fw-bold text-primary">Rp {{ number_format($period->calculated_accumulation, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada data pemenang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Show More/Less Button -->
        @if($displayPeriods->count() > 5)
            <div class="card-footer bg-white border-0 py-3">
                <div class="text-center">
                    @if(!$showAllPeriods)
                        <a href="{{ route('participant.dashboard', ['show_all' => true]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-chevron-down me-1"></i>
                            Lihat Selengkapnya
                            <span class="badge bg-primary ms-1">{{ $displayPeriods->count() - 5 }}</span>
                        </a>
                    @else
                        <a href="{{ route('participant.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-chevron-up me-1"></i>
                            Tampilkan 5 Periode Terakhir
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Mobile View removed --}}

    <!-- All Periods Cards Title -->
    <h4 class="mt-5 mb-3 fw-bold">
        <i class="fas fa-calendar-alt me-2 text-primary"></i>
        Semua Periode Arisan
    </h4>

    <div class="row">
        @php
            $showAllPeriodsList = request()->query('show_all_list', false);
            $sortedPeriodsList = $allPeriods->sortByDesc('period_start');
            $periodsListToShow = $showAllPeriodsList ? $sortedPeriodsList : $sortedPeriodsList->take(3);
        @endphp
        @forelse($periodsListToShow as $period)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $period->period_name }}</h6>
                        <div>
                            <span class="badge bg-{{ $period->status === 'bidding' ? 'success' : ($period->status === 'completed' ? 'primary' : 'secondary') }}">
                                {{ ucfirst($period->status) }}
                            </span>
                            <!-- <small class="text-muted ms-2">ID: {{ $period->id }}</small> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Deadline Calculation (available throughout the loop) -->
                        @php
                            $now = \Carbon\Carbon::now();
                            // Use bid_deadline if set, otherwise fall back to period_end
                            $deadline = $period->bid_deadline ? $period->bid_deadline : $period->period_end->endOfDay();
                            $diff = $now->diff($deadline);
                            
                            $daysLeft = $diff->days;
                            $hoursLeft = $diff->h;
                            $minutesLeft = $diff->i;
                            
                            $isExpired = $now->isAfter($deadline);
                            $isUrgent = $daysLeft < 2;
                            $isCritical = $daysLeft < 1;
                        @endphp

                        <!-- Period Information -->
                        <div class="mb-3">
                            <small class="text-muted">Tanggal:</small><br>
                            <strong>{{ $period->period_start->format('d M Y') }} - {{ $period->period_end->format('d M Y') }}</strong>
                        </div>

                        <!-- Bid Deadline Alert (for bidding status) -->
                        @if($period->status === 'bidding')
                            <div class="alert alert-{{ $isExpired ? 'danger' : ($isCritical ? 'danger' : ($isUrgent ? 'warning' : 'info')) }} py-2 px-3 mb-3 border-0 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <h2 class="h5 mb-0 me-3">
                                        <i class="fas fa-{{ $isExpired ? 'times-circle' : 'exclamation-circle' }}"></i>
                                    </h2>
                                    <div class="flex-grow-1">
                                        @if($isExpired)
                                            <strong class="d-block">Waktu Input Lelang Anda Habis</strong>
                                            <small>Waktu input lelang Anda telah habis. Anda tidak dapat melakukan perubahan bid lagi.</small>
                                        @elseif($isCritical)
                                            <strong class="d-block">❗ Waktu Hampir Habis!</strong>
                                            <small>Lelang akan segera ditutup. Pastikan nilai bid Anda sudah final dan merupakan tawaran terbaik.</small>
                                        @elseif($isUrgent)
                                            <strong class="d-block">Segera Lakukan Penawaran</strong>
                                            <small>Waktu tersisa kurang dari 2 hari. Jangan lupa cek status bid Anda secara berkala.</small>
                                        @else
                                            <strong class="d-block">Periode Lelang Aktif</strong>
                                            <small>Silakan masukkan penawaran terbaik Anda sebelum batas waktu berakhir.</small>
                                        @endif
                                        
                                        @if(!$isExpired)
                                            <div class="mt-1 d-inline-block bg-white bg-opacity-50 px-2 rounded small fw-bold">
                                                <i class="far fa-clock me-1"></i>
                                                Sisa: {{ $daysLeft }} hari, {{ $hoursLeft }} jam, {{ $minutesLeft }} menit
                                            </div>
                                            <div class="small text-muted mt-1">
                                                Deadline: {{ $deadline->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Financial Information -->
                        <div class="mb-3">
                            <small class="text-muted">Informasi Keuangan:</small>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <small>Kas Digunakan:</small><br>
                                    <strong class="text-info">
                                        @if(!empty($period->period_start))
                                            {{ $period->period_start->copy()->subMonthNoOverflow()->locale('id')->monthName }} {{ $period->period_start->copy()->subMonthNoOverflow()->year }}
                                        @else
                                            -
                                        @endif
                                    </strong>
                                </div>
                                <div class="col-6">
                                    <small>Sisa Kas:</small><br>
                                    <strong class="text-success">Rp {{ number_format($period->calculated_surplus ?? 0, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <small>SHU:</small><br>
                                    <strong class="text-warning">Rp {{ number_format($period->shu_amount ?? 0, 0, ',', '.') }}</strong>
                                </div>
                                <div class="col-6">
                                    <small>Total Hadiah:</small><br>
                                    <strong class="text-primary">Rp {{ number_format($period->group->main_prize, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Bid Information -->
                        <div class="mb-3">
                            <small class="text-muted">Status Bid Anda:</small><br>
                            @php
                                $participantBid = $period->bids->where('participant_id', $participant->id)->first();
                            @endphp
                            @if($participantBid)
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-success">Rp {{ number_format($participantBid->bid_amount, 0, ',', '.') }}</strong>
                                        <small class="text-muted d-block">({{ $participantBid->created_at->format('d M H:i') }})</small>
                                    </div>
                                    @if($period->status === 'bidding' && !$participant->has_won && !$isExpired)
                                        <button type="button" class="btn btn-outline-warning btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editBidModal{{ $period->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @elseif($isExpired && $period->status === 'bidding')
                                        <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i>Terkunci</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">Belum bid</span>
                            @endif
                        </div>

                        <!-- Winner Information (if completed) -->
                        @if($period->status === 'completed' && $period->winners->isNotEmpty())
                            <div class="mb-3">
                                <small class="text-muted">Pemenang:</small><br>
                                @foreach($period->winners->take(2) as $winner)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ $winner->participant->name }}</span>
                                        <span class="badge bg-success">Rp {{ number_format($winner->final_prize, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                                @if($period->winners->count() > 2)
                                    <small class="text-muted">+{{ $period->winners->count() - 2 }} lainnya</small>
                                @endif
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-2 mt-auto">
                            @if($period->status === 'bidding')
                                <a href="{{ route('participant.auction.view', $period->id) }}" class="btn btn-outline-info btn-sm flex-grow-1">
                                    <i class="fas fa-gavel me-1"></i>Lelang
                                </a>
                                @if(!$participant->has_won)
                                    @if($participantBid)
                                        @if(!$isExpired)
                                            <a href="{{ route('participant.bid.create') }}" class="btn btn-outline-warning btn-sm flex-grow-1">
                                                <i class="fas fa-edit me-1"></i>Ubah Bid
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm flex-grow-1" disabled>
                                                <i class="fas fa-clock me-1"></i>Waktu Habis
                                            </button>
                                        @endif
                                    @else
                                        @if(!$isExpired)
                                            <a href="{{ route('participant.bid.create') }}" class="btn btn-success btn-sm flex-grow-1">
                                                <i class="fas fa-hand-holding-usd me-1"></i>Masukkan Lelang
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm flex-grow-1" disabled>
                                                <i class="fas fa-clock me-1"></i>Waktu Habis
                                            </button>
                                        @endif
                                    @endif
                                @else
                                    <button class="btn btn-secondary btn-sm flex-grow-1" disabled>
                                        <i class="fas fa-check me-1"></i>Sudah Menang
                                    </button>
                                @endif
                            @endif
                            
                            @if($period->status === 'completed')
                                <a href="{{ route('participant.results', ['period_id' => $period->id]) }}" class="btn btn-outline-info btn-sm flex-grow-1">
                                    <i class="fas fa-chart-bar me-1"></i>Hasil
                                </a>
                            @endif

                            <a href="{{ route('participant.drive-link') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                                <i class="fab fa-google-drive me-1"></i>Drive
                            </a>

                            <!-- Financial Detail Button -->
                            <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1" data-bs-toggle="modal" data-bs-target="#financialDetailModal{{ $period->id }}">
                                <i class="fas fa-file-invoice-dollar me-1"></i>Detail Keuangan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Detail Modal -->
            <div class="modal fade" id="financialDetailModal{{ $period->id }}" tabindex="-1" aria-labelledby="financialDetailModalLabel{{ $period->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="financialDetailModalLabel{{ $period->id }}">
                                <i class="fas fa-calculator me-2"></i>Detail Keuangan - {{ $period->period_name }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            <!-- Period Context -->
                            <div class="alert alert-info border-0 shadow-sm mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-7">
                                        <h6 class="mb-1 fw-bold text-info-emphasis"><i class="fas fa-calendar-alt me-2"></i>Informasi Periode</h6>
                                        <p class="mb-0 small">{{ $period->period_name }} ({{ $period->period_start->format('d/m/Y') }} - {{ $period->period_end->format('d/m/Y') }})</p>
                                        <p class="mb-0 small text-info"><i class="fas fa-trophy me-1"></i>Jumlah Pemenang: {{ $period->calc_winner_count }} orang</p>
                                    </div>
                                    <div class="col-md-5 text-md-end mt-2 mt-md-0">
                                        <h6 class="mb-1 fw-bold text-info-emphasis"><i class="fas fa-database me-2"></i>Kas Acuan</h6>
                                        <p class="mb-0 small">Bulan {{ $period->calc_prev_month_name }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Cash Flow Summary Cards -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Saldo Awal</h6>
                                            <h4 class="mb-0">Rp {{ number_format($period->calc_previous_cash_balance, 0, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Iuran</h6>
                                            @if(($period->calc_actual_installments ?? 0) == 0)
                                                <h4 class="mb-0">-</h4>
                                            @else
                                                <h4 class="mb-0">Rp {{ number_format($period->calc_actual_installments, 0, ',', '.') }}</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Dana Tersedia</h6>
                                            @if((($period->calc_net_funds + $period->calc_highest_bid) ?? 0) == 0)
                                                <h4 class="mb-0">-</h4>
                                            @else
                                                <h4 class="mb-0">Rp {{ number_format($period->calc_net_funds + $period->calc_highest_bid, 0, ',', '.') }}</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Sisa Kas</h6>
                                            @if(($period->calc_final_remaining_cash ?? 0) == 0)
                                                <h4 class="mb-0">-</h4>
                                            @else
                                                <h4 class="mb-0">Rp {{ number_format($period->calc_final_remaining_cash, 0, ',', '.') }}</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Cash Flow Table -->
                            <div class="card border-0 shadow-sm border-start border-primary border-4 mb-4">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-calculator me-2"></i>Detail Perhitungan Kas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Deskripsi</th>
                                                    <th>Perhitungan</th>
                                                    <th>Hasil</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Saldo Awal</td>
                                                    <td>(Dari Periode Lalu)</td>
                                                    <td class="text-end fw-bold">Rp {{ number_format($period->calc_previous_cash_balance, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Total Iuran</td>
                                                    <td>
                                                        @if(($period->calc_actual_installments ?? 0) > 0)
                                                            ({{ $period->calc_paid_participant_count }} × Rp{{ number_format($period->calc_monthly_installment, 0, ',', '.') }})
                                                        @else
                                                            (0 × Rp{{ number_format($period->calc_monthly_installment, 0, ',', '.') }})
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-success">
                                                        @if(($period->calc_actual_installments ?? 0) == 0)
                                                            + -
                                                        @else
                                                            + Rp {{ number_format($period->calc_actual_installments, 0, ',', '.') }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Nilai Bid</td>
                                                    <td>({{ $period->calc_winner_count }} × Rp{{ number_format($period->calc_winner_count > 0 ? ($period->calc_highest_bid / $period->calc_winner_count) : 0, 0, ',', '.') }})</td>
                                                    <td class="text-end text-success">
                                                        @if(($period->calc_highest_bid ?? 0) == 0)
                                                            + -
                                                        @else
                                                            + Rp {{ number_format($period->calc_highest_bid, 0, ',', '.') }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Harga Motor</td>
                                                    <td>({{ $period->calc_winner_count }} × Rp{{ number_format($period->calc_winner_count > 0 ? ($period->calc_main_prize / $period->calc_winner_count) : 0, 0, ',', '.') }})</td>
                                                    <td class="text-end text-danger">- Rp {{ number_format($period->calc_main_prize, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Potongan SHU</td>
                                                    <td>({{ $period->calc_winner_count }} × Rp{{ number_format($period->calc_winner_count > 0 ? ($period->calc_shu_amount / $period->calc_winner_count) : 0, 0, ',', '.') }})</td>
                                                    <td class="text-end text-danger">- Rp {{ number_format($period->calc_shu_amount, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr class="table-warning">
                                                    <td><strong>Sisa Bersih (Kas Lelang)</strong></td>
                                                    <td><strong>Saldo setelah hadiah diberikan</strong></td>
                                                    <td class="text-end fw-bold">
                                                        @if(($period->calc_final_remaining_cash ?? 0) == 0)
                                                            -
                                                        @else
                                                            Rp {{ number_format($period->calc_final_remaining_cash, 0, ',', '.') }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr class="table-success">
                                                    <td><strong>TOTAL AKUMULASI</strong></td>
                                                    <td><strong>Saldo Awal + Sisa Bulan Ini</strong></td>
                                                    <td class="text-end fw-bold">
                                                        @if(($period->calc_total_running_cash ?? 0) == 0)
                                                            -
                                                        @else
                                                            Rp {{ number_format($period->calc_total_running_cash, 0, ',', '.') }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Final Result Cards -->
                            <div class="card border-0 shadow modal-result-card">
                                <div class="card-body p-0 overflow-hidden rounded">
                                    <div class="row g-0">
                                        <div class="col-md-6 bg-success bg-gradient text-white p-4">
                                            <small class="text-white-80 d-block mb-1">Sisa Bersih (Kas Lelang)</small>
                                            @if(($period->calc_final_remaining_cash ?? 0) == 0)
                                                <h4 class="fw-bold mb-0">-</h4>
                                            @else
                                                <h4 class="fw-bold mb-0">Rp {{ number_format($period->calc_final_remaining_cash, 0, ',', '.') }}</h4>
                                            @endif
                                            <p class="small mt-2 mb-0 text-white-80">Saldo setelah hadiah diberikan</p>
                                        </div>
                                        <div class="col-md-6 bg-primary bg-gradient text-white p-4">
                                            <small class="text-white-80 d-block mb-1">Total Kas Berjalan</small>
                                            @if(($period->calc_total_running_cash ?? 0) == 0)
                                                <h4 class="fw-bold mb-0">-</h4>
                                            @else
                                                <h4 class="fw-bold mb-0">Rp {{ number_format($period->calc_total_running_cash, 0, ',', '.') }}</h4>
                                            @endif
                                            <p class="small mt-2 mb-0 text-white-80">Akumulasi saldo akhir periode</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-white">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Bid Modal -->
            @if($participantBid && $period->status === 'bidding' && !$participant->has_won)
            <div class="modal fade" id="editBidModal{{ $period->id }}" tabindex="-1" aria-labelledby="editBidModalLabel{{ $period->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="editBidModalLabel{{ $period->id }}">
                                <i class="fas fa-edit me-2"></i>Ubah Bid - {{ $period->period_name }}
                            </h5>
                            <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('participant.bid.update', $participantBid->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">

                            <!-- jika peserta sudah menyimpan lelang permanen maka tidak bisa mengubah lelang -->
                                @if($participantBid->is_permanent)
                                    <div class="alert alert-danger border-0 shadow-sm mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-lock fs-4 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1 status-message">Nilai Lelelang Permanen</h6>
                                                <p class="small mb-0 opacity-75">Nilai lelang sudah disimpan permanen, tidak bisa diubah lagi.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="edit_bid_amount_{{ $period->id }}" class="form-label">
                                        <i class="fas fa-money-bill-wave me-1"></i>Nilai Bid Baru
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control" 
                                               id="edit_bid_amount_{{ $period->id }}"
                                               name="bid_amount" 
                                               value="{{ $participantBid->bid_amount }}"
                                               min="{{ $period->group->min_bid }}" 
                                               max="{{ $period->group->max_bid }}" 
                                               step="10000"
                                               required
                                               {{ $participantBid->is_permanent ? 'disabled' : '' }}>
                                    </div>
                                    <small class="text-muted">Min: Rp {{ number_format($period->group->min_bid, 0, ',', '.') }} - Max: Rp {{ number_format($period->group->max_bid, 0, ',', '.') }}</small>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Bid saat ini:</strong> Rp {{ number_format($participantBid->bid_amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $participantBid->is_permanent ? 'Tutup' : 'Batal' }}</button>
                                @if(!$participantBid->is_permanent)
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i>Perbarui Bid
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-calendar"></i><br>
                    Belum ada periode yang tersedia
                </div>
            </div>
        @endforelse
    </div>

    <!-- Show More/Less Button for Periods List -->
    @if($allPeriods->count() > 3)
        <div class="text-center mb-5">
            @if(!$showAllPeriodsList)
                <a href="{{ request()->fullUrlWithQuery(['show_all_list' => true]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-chevron-down me-1"></i>
                    Tampilkan Semua Periode
                </a>
            @else
                <a href="{{ request()->fullUrlWithQuery(['show_all_list' => null]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-chevron-up me-1"></i>
                    Tampilkan 3 Periode Terbaru
                </a>
            @endif
        </div>
    @endif



@endsection

@push('styles')
<style>
    .modal-result-card {
        transition: transform 0.3s ease;
    }
    .modal-result-card:hover {
        transform: scale(1.01);
    }
    .bg-gradient {
        background-image: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0)) !important;
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
    .table thead th {
        background-color: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .table tbody td {
        vertical-align: middle;
    }
    .bid-form {
        margin-top: 0.5rem;
    }
    .bid-form .input-group-sm {
        max-width: 300px;
    }
    .bid-form .btn-success {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    .winner-mobile-item:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
@endpush
