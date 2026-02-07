@extends('layouts.admin')

@section('title', 'Ubah Customer Service - Sistem Arisan')

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.customer-service.index') }}">Customer Service</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah CS</li>
            </ol>
        </nav>
        <h2><i class="fas fa-edit me-2"></i>Ubah Customer Service</h2>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Form Update Data CS</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.customer-service.update', $customerService->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nama CS <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $customerService->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="whatsapp_number" class="form-label fw-bold">No WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('whatsapp_number') is-invalid @enderror" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $customerService->whatsapp_number) }}" placeholder="Contoh: 6281234567890" required>
                            <div class="form-text text-muted">Gunakan format angka saja diawali kode negara (misal: 628xxx).</div>
                            @error('whatsapp_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label fw-bold">Foto CS</label>
                            @if($customerService->photo)
                                <div class="mb-2">
                                    <img src="{{ asset('uploads/customer_service/' . $customerService->photo) }}" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                            <div class="form-text text-muted">Format: jpeg, png, jpg, gif. Max: 2MB. Kosongkan jika tidak ingin mengubah.</div>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_active" id="active" value="1" {{ old('is_active', $customerService->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">Aktif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_active" id="inactive" value="0" {{ !old('is_active', $customerService->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inactive">Tidak Aktif</label>
                                </div>
                            </div>
                            @error('is_active')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.customer-service.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Perbarui Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
