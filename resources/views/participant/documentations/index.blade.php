@extends('layouts.participant')

@section('title', 'Dokumentasi Periode - ' . $period->group->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('participant.dashboard') }}" class="btn btn-light shadow-sm btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
        <div class="text-end">
            <h1 class="h4 mb-0 fw-bold text-gradient-primary">Dokumentasi Arisan</h1>
            <p class="text-muted small mb-0">{{ $period->period_start->locale('id')->monthName }} {{ $period->period_start->year }}</p>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-images text-primary me-2"></i>Koleksi Dokumentasi</h6>
        </div>
        <div class="card-body bg-light">
            <div class="row g-4">
                @forelse($period->documentations as $doc)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-lift">
                            @if($doc->type === 'image')
                                <a href="{{ storage_url($doc->content) }}" target="_blank">
                                    <img src="{{ storage_url($doc->content) }}" class="card-img-top object-fit-cover" style="height: 220px;" alt="{{ $doc->caption }}">
                                </a>
                            @elseif($doc->type === 'video')
                                <div class="ratio ratio-16x9">
                                    <video controls poster="">
                                        <source src="{{ storage_url($doc->content) }}">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @elseif($doc->type === 'text')
                                <div class="card-body bg-white d-flex align-items-center justify-content-center" style="height: 220px; border-bottom: 1px solid #eee;">
                                    <div class="p-4 text-center">
                                        <i class="fas fa-quote-left text-primary-light fa-2x mb-3 opacity-25"></i>
                                        <p class="card-text text-dark">{{ $doc->content }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="card-body py-3 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge rounded-pill {{ $doc->type === 'image' ? 'bg-primary-soft text-primary' : ($doc->type === 'video' ? 'bg-danger-soft text-danger' : 'bg-info-soft text-info') }} small px-3">
                                        @if($doc->type === 'image') <i class="fas fa-image me-1"></i> Gambar
                                        @elseif($doc->type === 'video') <i class="fas fa-video me-1"></i> Video
                                        @else <i class="fas fa-align-left me-1"></i> Teks
                                        @endif
                                    </span>
                                    <small class="text-muted">{{ $doc->created_at->format('d M Y') }}</small>
                                </div>
                                @php
                                    $deleteDate = $doc->created_at->copy()->addMonths(6);
                                    $daysLeft = (int) floor(now()->diffInDays($deleteDate, false));
                                @endphp
                                <p class="small mb-2 {{ $daysLeft <= 30 ? 'text-danger' : 'text-warning' }}">
                                    <i class="fas fa-clock me-1"></i> 
                                    @if($daysLeft > 0)
                                        Dihapus dalam {{ $daysLeft }} hari
                                    @else
                                        Akan segera dihapus
                                    @endif
                                </p>
                                @if($doc->caption)
                                    <p class="card-text fw-bold text-dark mb-2">{{ $doc->caption }}</p>
                                @endif
                                @if($doc->type !== 'text')
                                    <a href="{{ route('admin.documentations.download', $doc->id) }}" class="btn btn-sm btn-outline-primary w-100 mt-1">
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-5 text-center">
                        <div class="empty-state">
                            <i class="fas fa-camera-retro fa-4x text-muted opacity-25 mb-3"></i>
                            <h5 class="text-muted">Belum ada dokumentasi</h5>
                            <p class="text-muted small">Admin belum mengunggah dokumentasi untuk periode ini.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-soft {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03) !important;
    }
    .bg-primary-soft { background-color: rgba(78, 115, 223, 0.1); }
    .bg-danger-soft { background-color: rgba(231, 74, 59, 0.1); }
    .bg-info-soft { background-color: rgba(54, 185, 204, 0.1); }
    .text-primary-light { color: #85a4ff; }
    .rounded-4 { border-radius: 1rem !important; }
    .hover-lift {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(18, 38, 63, 0.1) !important;
    }
    .text-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .object-fit-cover {
        object-fit: cover;
    }
</style>
@endsection
