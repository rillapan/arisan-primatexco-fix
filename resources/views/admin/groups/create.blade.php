@extends('layouts.admin')

@section('title', 'Tambah Kelompok - Sistem Arisan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-plus me-2"></i>
            Tambah Kelompok Baru
        </h2>
        <a href="{{ route('admin.groups') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>
                Informasi Kelompok
            </h5>
        </div>
        <div class="card-body">
                        <form action="{{ route('admin.groups.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="id" class="form-label">
                                <i class="fas fa-hashtag me-1"></i>ID Kelompok <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('id') is-invalid @enderror" 
                                   id="id" name="id" value="{{ old('id') }}" 
                                   min="1" required placeholder="Contoh: 1, 2, 3...">
                            @error('id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                ID unik kelompok 
                            </small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag me-1"></i>Nama Kelompok <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="max_participants" class="form-label">
                                <i class="fas fa-users me-1"></i>Maksimal Peserta <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                                   id="max_participants" name="max_participants" value="{{ old('max_participants', 90) }}" 
                                   min="2"  required>
                            @error('max_participants')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimal 2 peserta</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="monthly_installment" class="form-label">
                                <i class="fas fa-money-bill-wave me-1"></i>Angsuran Bulanan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('monthly_installment') is-invalid @enderror" 
                                       id="monthly_installment" name="monthly_installment" 
                                       value="{{ old('monthly_installment', 175000) }}" min="10000" step="1000" required>
                            </div>
                            @error('monthly_installment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimal Rp 10.000</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="main_prize" class="form-label">
                                <i class="fas fa-gift me-1"></i>Hadiah Utama <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('main_prize') is-invalid @enderror" 
                                       id="main_prize" name="main_prize" 
                                       value="{{ old('main_prize', 17500000) }}" min="100000" step="10000" required>
                            </div>
                            @error('main_prize')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimal Rp 100.000</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="shu" class="form-label">
                                <i class="fas fa-hand-holding-usd me-1"></i>SHU <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('shu') is-invalid @enderror" 
                                       id="shu" name="shu" 
                                       value="{{ old('shu', 500000) }}" min="0" step="10000" required>
                            </div>
                            @error('shu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Sisa Hasil Usaha</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="min_bid" class="form-label">
                                <i class="fas fa-arrow-down me-1"></i>Lelang Minimum <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('min_bid') is-invalid @enderror" 
                                       id="min_bid" name="min_bid" 
                                       value="{{ old('min_bid', 2250000) }}" min="0" step="10000" required>
                            </div>
                            @error('min_bid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Penawaran minimum</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="max_bid" class="form-label">
                                <i class="fas fa-arrow-up me-1"></i>Lelang Maksimum <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('max_bid') is-invalid @enderror" 
                                       id="max_bid" name="max_bid" 
                                       value="{{ old('max_bid', 6000000) }}" min="0" step="10000" required>
                            </div>
                            @error('max_bid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Penawaran maksimum</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left me-1"></i>Deskripsi
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Opsional: Deskripsi singkat tentang kelompok</small>
                </div>


                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.groups') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan Kelompok
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
