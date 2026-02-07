<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Kelompok Arisan - PRIMKOPKAR PRIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --bg-light: #f8fafc;
        }
        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }
        .registration-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 4rem 0;
            color: white;
            text-align: center;
            margin-bottom: 3rem;
        }
        .group-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .group-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
        }
        .quota-info {
            font-size: 0.9rem;
            color: #64748b;
        }
        .btn-select {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
        }
    </style>
</head>
<body>

<div class="registration-header shadow-sm">
    <div class="container">
        <h1 class="fw-bold mb-2">Pendaftaran Peserta Arisan</h1>
        <p class="lead opacity-90">Silakan pilih kelompok arisan yang sedang membuka pendaftaran</p>
        <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm mt-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
        </a>
    </div>
</div>

<div class="container mb-5">
    @if(session('error'))
        <div class="alert alert-danger mb-4 border-0 shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        @forelse($activeGroups as $group)
            @php
                $isFull = $group->participants_count >= $group->max_participants;
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card group-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h3 class="h5 fw-bold mb-0">{{ $group->name }}</h3>
                            @if($isFull)
                                <span class="status-badge bg-danger text-white">Penuh</span>
                            @else
                                <span class="status-badge bg-success text-white">Terbuka</span>
                            @endif
                        </div>
                        
                        <p class="text-muted small mb-4">
                            {{ $group->description ?: 'Kelompok arisan PT. PRIMATEXCO INDONESIA.' }}
                        </p>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="quota-info">Kapasitas Peserta</span>
                                <span class="fw-bold">{{ $group->participants_count }} / {{ $group->max_participants }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $isFull ? 'bg-danger' : 'bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ ($group->participants_count / $group->max_participants) * 100 }}%">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            @if($isFull)
                                <button class="btn btn-secondary btn-select" disabled>Pendaftaran Ditutup</button>
                            @else
                                <a href="{{ route('register.form', $group->id) }}" class="btn btn-primary btn-select">
                                    Pilih Kelompok <i class="fas fa-chevron-right ms-2"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="bg-white p-5 rounded-4 shadow-sm">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-4 opacity-20"></i>
                    <h2 class="h4 fw-bold">Belum Ada Pendaftaran Terbuka</h2>
                    <p class="text-muted">Saat ini belum ada kelompok arisan yang membuka pendaftaran online. Silakan hubungi admin untuk informasi lebih lanjut.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary px-4 mt-3">Kembali ke Beranda</a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<footer class="text-center py-4 text-muted mt-auto">
    <small>© 2026 PT. PRIMATEXCO INDONESIA. All rights reserved.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
