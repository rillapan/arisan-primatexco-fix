@extends('layouts.participant')

@section('title', 'Hasil & Peringkat - Sistem Arisan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-chart-bar me-2 text-primary"></i>
            {{ $selectedPeriod ? 'Hasil Peringkat: ' . $selectedPeriod->period_name : 'Pilih Periode Hasil Arisan' }}
        </h2>
        @if($selectedPeriod)
            <a href="{{ route('participant.results') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        @endif
    </div>

    @if(!$selectedPeriod)
        <!-- List View: Period Cards -->
        <div class="row g-4">
            @forelse($allPeriods as $period)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('participant.results', ['period_id' => $period->id]) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="icon-box bg-primary-soft p-3 rounded-circle">
                                        <i class="fas fa-calendar-alt text-primary fa-lg"></i>
                                    </div>
                                    <span class="badge rounded-pill bg-{{ $period->status === 'active' ? 'success' : ($period->status === 'completed' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($period->status) }}
                                    </span>
                                </div>
                                <h5 class="card-title text-dark fw-bold mb-2">{{ $period->period_name }}</h5>
                                <p class="text-muted small mb-3">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $period->period_start->locale('id')->format('M Y') }}
                                </p>
                                
                                <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                    <span class="text-primary fw-medium small">Lihat hasil</span>
                                    <i class="fas fa-chevron-right text-primary small"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm p-5 text-center">
                        <div class="empty-state">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h5 class="text-dark">Belum ada periode yang tersedia</h5>
                            <p class="text-muted">Hasil arisan akan tampil di sini setelah periode dimulai.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    @else
        <!-- Detail View: Rankings for Selected Period -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-list-ol me-2 text-primary"></i>
                            Hasil Lelang 
                        </h5>
                    </div>
                    <div class="col-auto">
                        <span class="text-muted small">
                            {{ $selectedPeriod->period_start->format('d M Y') }} - {{ $selectedPeriod->period_end->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Managers Information Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-users-cog me-2"></i>
                        Saksi- {{ $selectedPeriod->period_name }}
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Debug information - remove later --}}
                    @php
                        $managerCount = $selectedPeriod->saksis ? $selectedPeriod->saksis->count() : 0;
                        $periodId = $selectedPeriod->id;
                    @endphp
                    @if($selectedPeriod->saksis && $selectedPeriod->saksis->isNotEmpty())
                        <div class="row g-3">
                            @foreach($selectedPeriod->saksis as $manager)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                {{-- Photo Section --}}
                                                <div class="me-3">
                                                    @if($manager->foto)
                                                        <img src="{{ asset('uploads/saksi/' . $manager->foto) }}" alt="{{ $manager->nama_pengurus }}" 
                                                             class="rounded" style="width: 80px; height: 100px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                             style="width: 80px; height: 100px;">
                                                            <i class="fas fa-user text-muted fa-2x"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                {{-- Info Section --}}
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold text-dark">{{ $manager->nama_pengurus }}</h6>
                                                    <p class="mb-0 small text-muted">{{ $manager->jabatan }}</p>
                                                    <p class="mb-2 small text-primary fw-medium">
                                                        <i class="fas fa-users-viewfinder me-1"></i>
                                                        Asal: {{ $manager->participant->group->name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada saksi yang ditugaskan untuk periode ini.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card-body">
                @if($allGroupParticipants->isNotEmpty())
                    @php
                        // Map and sort participants
                        $participantsList = $allGroupParticipants->map(function($p) use ($selectedPeriod, $maxBidAmount) {
                            // Find bid
                            $bid = $p->bids->first();
                            $p->bid_amount = $bid ? $bid->bid_amount : 0;
                            $p->has_bid = ($bid && $bid->bid_amount > 0);
                            
                            // Determine statuses
                            $p->is_winner = $selectedPeriod->winners->contains('participant_id', $p->id);
                            
                            // Candidate: Has bid amount equal to max bid amount AND max bid > 0
                            // Note: Winners are also candidates technically, but we label them Winner.
                            // If draw happened, they were candidates.
                            $p->is_candidate = ($p->bid_amount == $maxBidAmount && $maxBidAmount > 0);
                            
                            return $p;
                        })->sort(function($a, $b) {
                            // Sort Order:
                            // 1. Winner
                            // 2. Candidate (Highest Bid)
                            // 3. Bidder (Other Bids desc)
                            // 4. Non-Bidder (Lottery Number asc)
                            
                            if ($a->is_winner && !$b->is_winner) return -1;
                            if (!$a->is_winner && $b->is_winner) return 1;
                            
                            if ($a->is_candidate && !$b->is_candidate) return -1;
                            if (!$a->is_candidate && $b->is_candidate) return 1;
                            
                            if ($a->has_bid && !$b->has_bid) return -1;
                            if (!$a->has_bid && $b->has_bid) return 1;
                            
                            if ($a->has_bid && $b->has_bid) {
                                return $b->bid_amount <=> $a->bid_amount;
                            }
                            
                            return $a->lottery_number <=> $b->lottery_number; // Default to lottery number
                        })->values();
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th width="80" class="text-center">No</th>
                                    <th>Peserta Arisan</th>
                                    <th class="text-end">Nilai Lelang (Bid)</th>
                                    <th class="text-center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($participantsList as $index => $p)
                                <tr class="{{ $p->id === Auth::guard('participant')->user()->id ? 'table-primary-soft' : '' }} {{ $p->is_winner ? 'table-success-soft' : '' }}">
                                    <td class="text-center">
                                    
                                            <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                        
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                @if($p->photo)
                                                    <img src="{{ storage_url($p->photo) }}" alt="{{ $p->name }}" 
                                                         class="rounded-circle w-100 h-100" style="object-fit: cover; border: 2px solid {{ $p->is_winner ? '#22c55e' : '#e2e8f0' }};">
                                                @else
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center w-100 h-100">
                                                        <i class="fas fa-user {{ $p->is_winner ? 'text-success' : 'text-muted' }}"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="d-block fw-bold text-dark">
                                                    {{ $p->name }}
                                                    @if($p->id === Auth::guard('participant')->user()->id)
                                                        <span class="badge bg-primary ms-1">Anda</span>
                                                    @endif
                                                    @if($p->is_winner)
                                                        <span class="badge bg-success ms-1"><i class="fas fa-medal me-1"></i>Pemenang</span>
                                                    @endif
                                                </span>
                                                <small class="text-muted">No. {{ $p->lottery_number }} • {{ $p->shift }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if($p->has_bid)
                                            <span class="{{ $p->is_winner ? 'text-success' : 'text-dark' }} fw-bold">
                                                Rp {{ number_format($p->bid_amount, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($p->is_winner)
                                            <span class="badge bg-success rounded-pill px-3">
                                                <i class="fas fa-check-circle me-1"></i> TELAH MENANG
                                            </span>
                                        @elseif($p->is_candidate)
                                            <span class="badge bg-warning text-dark rounded-pill px-3">
                                                <i class="fas fa-star me-1"></i> KANDIDAT UNDIAN
                                            </span>
                                        @elseif($p->has_bid)
                                            <span class="badge bg-secondary rounded-pill px-3">
                                                <i class="fas fa-gavel me-1"></i> PESERTA LELANG
                                            </span>
                                        @else
                                            <span class="text-muted small fst-italic">Tidak Lelang</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($selectedPeriod->winners->isNotEmpty())
                        <div class="mt-5">
                            <h6 class="fw-bold text-dark mb-4 fst-italic">
                                <i class="fas fa-award me-2 text-warning"></i>
                                PEMENANG PERIODE {{ strtoupper($selectedPeriod->period_name) }}
                            </h6>
                            <div class="row g-4">
                                @foreach($selectedPeriod->winners as $winner)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border-0 shadow-sm winner-glow">
                                        <div class="card-body p-4 text-center">
                                            <div class="winner-icon mb-3">
                                                <i class="fas fa-crown fa-2x text-warning"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-1">{{ $winner->participant->name }}</h5>
                                            <p class="text-muted small mb-3">No. {{ $winner->participant->lottery_number }}</p>
                                            <div class="bg-success-soft py-2 rounded-3">
                                                <span class="text-success fw-bold fs-5">Rp {{ number_format($winner->final_prize, 0, ',', '.') }}</span>
                                                <br>
                                                <small class="text-success-emphasis">Total Hadiah</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="p-5 text-center">
                        <i class="fas fa-gavel fa-3x text-light mb-3"></i>
                        <h5 class="text-muted">Belum ada penawaran bid untuk periode ini.</h5>
                    </div>
                @endif
            </div>
        </div>
    @endif

@endsection

@push('styles')
<style>
    .bg-primary-soft { background-color: rgba(37, 99, 235, 0.1); }
    .bg-success-soft { background-color: rgba(22, 163, 74, 0.1); }
    .hover-shadow:hover { 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        transform: translateY(-2px);
    }
    .transition-all { transition: all 0.3s ease; }
    
    .rank-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        margin: 0 auto;
    }
    .gold { background: linear-gradient(45deg, #ffd700, #ffae00); box-shadow: 0 2px 4px rgba(255, 215, 0, 0.4); }
    .silver { background: linear-gradient(45deg, #c0c0c0, #939393); box-shadow: 0 2px 4px rgba(192, 192, 192, 0.4); }
    .bronze { background: linear-gradient(45deg, #cd7f32, #a0522d); box-shadow: 0 2px 4px rgba(205, 127, 50, 0.4); }
    
    .table-primary-soft { background-color: rgba(37, 99, 235, 0.05); }
    .table-success-soft { background-color: rgba(22, 163, 74, 0.05); }
    
    .winner-glow {
        border: 2px solid transparent;
        background-clip: padding-box;
        position: relative;
    }
    .winner-glow:after {
        position: absolute;
        top: -2px; bottom: -2px;
        left: -2px; right: -2px;
        background: linear-gradient(45deg, #fbbf24, #f59e0b, #fbbf24);
        content: '';
        z-index: -1;
        border-radius: calc(var(--bs-border-radius) + 2px);
    }
    .winner-icon {
        width: 60px;
        height: 60px;
        background: rgba(251, 191, 36, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    .avatar-sm { width: 40px; height: 40px; }
</style>
@endpush
