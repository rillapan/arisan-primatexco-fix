@extends('layouts.participant')

@section('title', 'Dokumentasi Arisan')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('participant.dashboard') }}" class="btn btn-light shadow-sm btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
        <div class="text-end">
            <h1 class="h4 mb-0 fw-bold text-primary">Galeri Dokumentasi</h1>
            <p class="text-muted small mb-0">{{ $participant->group->name }}</p>
        </div>
    </div>

    <!-- Period List -->
    <div class="row g-4">
        @forelse($periodsWithDocs as $period)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-secondary bg-opacity-10 text-primary mb-1">{{ $period->period_start->locale('id')->monthName }} {{ $period->period_start->year }}</span>
                            <h6 class="mb-0 fw-bold">{{ $period->period_name }}</h6>
                        </div>
                        <a href="{{ route('participant.periods.documentations', $period->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            Lihat <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    
                    <!-- Preview Image -->
                    @php
                        $firstImage = $period->documentations->where('type', 'image')->first();
                        $docCount = $period->documentations->count();
                    @endphp
                    
                    <div class="position-relative" style="height: 200px; background-color: #f8f9fa;">
                        @if($firstImage)
                            <img src="{{ storage_url($firstImage->content) }}" class="w-100 h-100 object-fit-cover" alt="Preview">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <div class="text-center">
                                    <i class="fas fa-images fa-3x mb-2 opacity-25"></i>
                                    <p class="small mb-0">Video/Teks Only</p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="position-absolute bottom-0 end-0 p-2">
                            <span class="badge bg-dark bg-opacity-75 rounded-pill">
                                <i class="fas fa-photo-video me-1"></i> {{ $docCount }} Item
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-camera-retro fa-4x text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">Belum ada dokumentasi</h5>
                    <p class="text-muted small">Saat ini belum ada dokumentasi kegiatan arisan yang diunggah.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .object-fit-cover {
        object-fit: cover;
    }
</style>
@endsection
