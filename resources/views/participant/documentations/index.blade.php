@extends('layouts.participant')

@section('title', 'Dokumentasi Google Drive')

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
            <p class="text-muted small mb-0">Google Drive</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if($driveLink)
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden hover-lift">
                <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);">
                    <i class="fab fa-google-drive text-success" style="font-size: 5rem; opacity: 0.85; margin-bottom: 1.5rem;"></i>
                    <h3 class="fw-bold text-success mb-2">Dokumentasi Google Drive</h3>
                    @if($driveCaption)
                        <p class="text-dark mb-4 fs-6">{{ $driveCaption }}</p>
                    @else
                        <p class="text-muted mb-4">Klik tombol di bawah untuk membuka folder dokumentasi.</p>
                    @endif
                    <a href="{{ $driveLink }}" target="_blank" class="btn btn-success btn-lg rounded-pill px-5 shadow">
                        <i class="fas fa-external-link-alt me-2"></i> Buka Google Drive
                    </a>
                </div>
            </div>
            @else
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="card-body text-center py-5">
                    <i class="fab fa-google-drive fa-4x text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">Belum ada link dokumentasi</h5>
                    <p class="text-muted small">Admin belum menambahkan link Google Drive.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .shadow-soft {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03) !important;
    }
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
</style>
@endsection
