@extends('layouts.admin')

@section('title', 'Pengaturan KTA - Sistem Arisan')

@section('content')
    <div class="mb-4">
        <h2><i class="fas fa-id-card me-2"></i>Pengaturan KTA Peserta</h2>
        <p class="text-muted">Atur format tampilan Kartu Tanda Anggota (KTA) untuk peserta.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Konfigurasi Kartu</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.kta.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="header_title" class="form-label fw-bold">Header KTA <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('header_title') is-invalid @enderror" id="header_title" name="header_title" value="{{ old('header_title', $setting->header_title) }}" placeholder="Contoh: KARTU TANDA ANGGOTA ARISAN" required>
                            @error('header_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="logo" class="form-label fw-bold">Logo KTA</label>
                            @if($setting->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('uploads/kta/' . $setting->logo) }}" class="img-thumbnail" style="max-height: 80px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                            <div class="form-text mt-1">Format: jpeg, png, jpg. Max: 2MB.</div>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="signature_name" class="form-label fw-bold">Nama Penandatangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('signature_name') is-invalid @enderror" id="signature_name" name="signature_name" value="{{ old('signature_name', $setting->signature_name) }}" placeholder="Contoh: Ketua Paguyuban" required>
                                @error('signature_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="signature_image" class="form-label fw-bold">Gambar TTD</label>
                                @if($setting->signature_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('uploads/kta/' . $setting->signature_image) }}" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('signature_image') is-invalid @enderror" id="signature_image" name="signature_image" accept="image/*">
                                <div class="form-text mt-1">Format: jpeg, png, jpg. Max: 2MB.</div>
                                @error('signature_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="vision" class="form-label fw-bold">Visi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('vision') is-invalid @enderror" id="vision" name="vision" rows="3" required>{{ old('vision', $setting->vision) }}</textarea>
                            @error('vision')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="mission" class="form-label fw-bold">Misi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('mission') is-invalid @enderror" id="mission" name="mission" rows="5" required>{{ old('mission', $setting->mission) }}</textarea>
                            @error('mission')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="moto" class="form-label fw-bold">Moto</label>
                            <input type="text" class="form-control @error('moto') is-invalid @enderror" id="moto" name="moto" value="{{ old('moto', $setting->moto) }}" placeholder="Contoh: Bersatu Kita Teguh, Bercerai Kita Beruntung">
                            @error('moto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0 fw-bold">Panduan</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Pengaturan ini akan diterapkan pada semua Kartu Tanda Anggota (KTA) yang dapat diunduh oleh peserta.
                    </p>
                    <ul class="small text-muted ps-3">
                        <li class="mb-2">Gunakan logo dengan latar belakang transparan (PNG) untuk hasil terbaik.</li>
                        <li class="mb-2">TTD dapat berupa coretan ttd atau stempel resmi.</li>
                        <li class="mb-2">Visi & Misi akan ditampilkan pada sisi belakang kartu.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
