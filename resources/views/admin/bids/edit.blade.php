@extends('layouts.admin')

@section('title', 'Edit Bid - ' . $bid->participant->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-edit me-2"></i>
            Edit Bid
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.periods.edit', $bid->monthly_period_id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-calendar-edit me-1"></i>
                    Edit Periode
                </a>
                <a href="{{ route('admin.groups.auction.process', [$bid->monthlyPeriod->group_id, 'period_id' => $bid->monthly_period_id]) }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>
                    Proses Undian
                </a>
                <a href="{{ route('admin.bids.show', $bid->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye me-1"></i>
                    Detail Bid
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Current Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white text-dark py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user me-2 text-primary"></i>
                        Informasi Peserta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Nama Peserta</small>
                            <span class="fw-bold">{{ $bid->participant->name }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">No Undian</small>
                            <span class="badge bg-primary px-3 rounded-pill">{{ $bid->participant->lottery_number }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Bag/Shift</small>
                            <span class="badge bg-info">{{ $bid->participant->shift }}</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">NIK</small>
                            <span class="fw-bold">{{ $bid->participant->nik }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white text-dark py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle me-2 text-success"></i>
                        Informasi Saat Ini
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Nilai Bid Saat Ini</small>
                            <span class="fw-bold text-success">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Status Saat Ini</small>
                            @if($bid->status === 'submitted')
                                <span class="badge bg-success">Disubmit</span>
                            @elseif($bid->status === 'accepted')
                                <span class="badge bg-primary">Diterima</span>
                            @else
                                <span class="badge bg-secondary">{{ $bid->status }}</span>
                            @endif
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Waktu Bid</small>
                            <span class="fw-bold">{{ $bid->bid_time->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Periode</small>
                            <span class="fw-bold">{{ $bid->monthlyPeriod->period_name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success py-3">
            <h5 class="mb-0 text-white fw-bold">
                <i class="fas fa-edit me-2"></i>
                Form Edit Bid
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.bids.update', $bid->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bid_amount" class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave me-1 text-success"></i>
                                Nilai Bid (Rp)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       class="form-control @error('bid_amount') is-invalid @enderror" 
                                       id="bid_amount" 
                                       name="bid_amount" 
                                       value="{{ old('bid_amount', $bid->bid_amount) }}" 
                                       min="0" 
                                       step="1000"
                                       required>
                            </div>
                            <div class="form-text">Masukkan nilai bid dalam Rupiah (kelipatan 1.000)</div>
                            @error('bid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">
                                <i class="fas fa-flag me-1 text-primary"></i>
                                Status Bid
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="submitted" {{ old('status', $bid->status) === 'submitted' ? 'selected' : '' }}>
                                    Disubmit
                                </option>
                                <option value="accepted" {{ old('status', $bid->status) === 'accepted' ? 'selected' : '' }}>
                                    Diterima
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm my-4">
                    <div class="d-flex">
                        <i class="fas fa-info-circle mt-1 me-3 fs-4"></i>
                        <div>
                            <strong class="d-block mb-2">Informasi Penting:</strong>
                            <ul class="mb-0 small">
                                <li>Perubahan akan langsung disimpan dan mempengaruhi proses undian</li>
                                <li>Nilai bid yang lebih tinggi memiliki peluang menang lebih besar</li>
                                <li>Status "Diterima" berarti bid telah diverifikasi dan sah</li>
                                <li>Pastikan nilai bid sesuai dengan ketentuan kelompok</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-end gap-2">
                    <a href="{{ route('admin.bids.show', $bid->id) }}" class="btn btn-outline-secondary py-2 px-4 order-2 order-md-1">
                        <i class="fas fa-times me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-success py-2 px-4 order-1 order-md-2">
                        <i class="fas fa-save me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning py-3">
            <h5 class="mb-0 text-dark fw-bold">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Peringatan
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning border-0 shadow-sm mb-0">
                <div class="d-flex">
                    <i class="fas fa-shield-alt mt-1 me-3 fs-4"></i>
                    <div>
                        <strong class="d-block mb-2">Keamanan Data:</strong>
                        <p class="mb-0 small">Perubahan pada bid akan tercatat dalam log sistem. Pastikan perubahan yang dilakukan sesuai dengan persetujuan peserta atau kebijakan kelompok. Jika periode sudah selesai, edit bid tidak akan lagi diizinkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
