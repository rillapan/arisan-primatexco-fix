@extends('layouts.admin')

@section('title', 'Profil Admin - Sistem Arisan')

@push('styles')
<style>
    .signature-pad-container {
        border: 1px solid #dee2e6;
        border-radius: .25rem;
        background-color: #f8f9fa;
        position: relative;
        width: 100%;
        height: 200px;
    }
    .signature-pad-container canvas {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        touch-action: none;
    }
</style>
@endpush

@section('content')
    <h2>
        <i class="fas fa-user-cog me-2"></i>
        Profil Admin
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Profile Photo Upload -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-camera me-2"></i>
                Foto Profil
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="position-relative">
                        @if($admin->profile_photo)
                            <img src="{{ asset('storage/' . $admin->profile_photo) }}" 
                                 alt="Foto Profil" 
                                 class="rounded-circle shadow"
                                 id="profilePreview"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #0d6efd;">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white shadow"
                                 id="profilePlaceholder"
                                 style="width: 120px; height: 120px; font-size: 3rem; border: 4px solid #0d6efd;">
                                <i class="fas fa-user"></i>
                            </div>
                            <img src="" alt="Foto Profil" class="rounded-circle shadow d-none"
                                 id="profilePreview"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #0d6efd;">
                        @endif
                    </div>
                </div>
                <div class="col">
                    <form action="{{ route('admin.profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoUploadForm">
                        @csrf
                        <div class="mb-2">
                            <label for="photo" class="form-label">
                                <i class="fas fa-upload me-1"></i>Pilih Foto Baru
                            </label>
                            <input type="file" 
                                   class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" 
                                   name="photo" 
                                   accept="image/jpg,image/jpeg,image/png">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 5MB. Akan di-resize ke 300x300 pixel.</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm" id="uploadBtn" disabled>
                            <i class="fas fa-cloud-upload-alt me-1"></i>
                            Upload Foto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-user me-2"></i>
                Informasi Profil
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-1"></i>Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">
                    <i class="fas fa-lock me-2"></i>
                    Ubah Password
                </h5>
                <p class="text-muted mb-3">Biarkan kosong jika tidak ingin mengubah password</p>
                
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong> Untuk keamanan, pastikan hanya Anda yang memiliki akses ke halaman ini. Perubahan password akan langsung berlaku.
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password Baru
                            </label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" name="new_password">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimal 6 karakter. Gunakan kombinasi huruf besar, huruf kecil, dan angka.</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1"></i>Konfirmasi Password
                            </label>
                            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                   id="new_password_confirmation" name="new_password_confirmation">
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>
                <i class="fas fa-users-cog me-2"></i>
                Kelola Pengurus
            </h3>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#managePositionsModal">
                    <i class="fas fa-briefcase me-1"></i> Kelola Jabatan
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addManagementModal">
                    <i class="fas fa-plus me-1"></i> Tambah Pengurus
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">Foto</th>
                                <th>Nama Lengkap</th>
                                <th>Jabatan</th>
                                <th>TTD</th>
                                <th class="text-end px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($managements as $item)
                                <tr>
                                    <td class="px-4">
                                        @if($item->foto_profil)
                                            <img src="{{ storage_url($item->foto_profil) }}" alt="Foto" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="align-middle"><strong>{{ $item->nama_lengkap }}</strong></td>
                                    <td class="align-middle">
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1">
                                            {{ $item->position ? $item->position->name : $item->jabatan }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if($item->ttd)
                                            <img src="{{ storage_url($item->ttd) }}" alt="TTD" style="height: 30px;">
                                        @else
                                            <span class="text-muted small">Belum ada TTD</span>
                                        @endif
                                    </td>
                                    <td class="text-end px-4 align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editManagementModal{{ $item->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.management.delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengurus ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Belum ada data pengurus.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Management Modals (placed outside table to avoid z-index issues) -->
    @foreach($managements as $item)
        <div class="modal fade" id="editManagementModal{{ $item->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.management.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="management-form">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Pengurus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="{{ $item->nama_lengkap }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select name="position_id" class="form-select" required>
                                    <option value="" disabled>Pilih Jabatan...</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}" {{ $item->position_id == $pos->id ? 'selected' : '' }}>
                                            {{ $pos->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto Profil (Kosongkan jika tidak diubah)</label>
                                <input type="file" name="foto_profil" class="form-control" accept="image/*">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label d-block">Tanda Tangan</label>
                                <ul class="nav nav-tabs mb-2" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active btn-sm py-1" data-bs-toggle="tab" data-bs-target="#upload-ttd-edit-{{ $item->id }}" type="button">Upload</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link btn-sm py-1" data-bs-toggle="tab" data-bs-target="#draw-ttd-edit-{{ $item->id }}" type="button">Gambar</button>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="upload-ttd-edit-{{ $item->id }}">
                                        <input type="file" name="ttd" class="form-control" accept="image/*">
                                    </div>
                                    <div class="tab-pane fade" id="draw-ttd-edit-{{ $item->id }}">
                                        <div class="signature-pad-container">
                                            <canvas id="canvas-edit-{{ $item->id }}"></canvas>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary mt-1 clear-canvas" data-canvas="canvas-edit-{{ $item->id }}">Hapus Gambar</button>
                                    </div>
                                </div>
                                <input type="hidden" name="ttd_drawing" class="ttd-drawing-input">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Modal -->
    <div class="modal fade" id="addManagementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.management.store') }}" method="POST" enctype="multipart/form-data" id="addManagementForm" class="management-form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pengurus Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <select name="position_id" class="form-select" required>
                                <option value="" selected disabled>Pilih Jabatan...</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">
                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#managePositionsModal" class="text-primary text-decoration-none">
                                    <i class="fas fa-plus-circle me-1"></i>Kelola daftar jabatan
                                </a>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="foto_profil" class="form-control" accept="image/*">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label d-block">Tanda Tangan</label>
                            <ul class="nav nav-tabs mb-2" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active btn-sm py-1" data-bs-toggle="tab" data-bs-target="#upload-ttd-add" type="button">Upload</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link btn-sm py-1" data-bs-toggle="tab" data-bs-target="#draw-ttd-add" type="button">Gambar</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="upload-ttd-add">
                                    <input type="file" name="ttd" class="form-control" accept="image/*">
                                </div>
                                <div class="tab-pane fade" id="draw-ttd-add">
                                    <div class="signature-pad-container">
                                        <canvas id="canvas-add"></canvas>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1 clear-canvas" data-canvas="canvas-add">Hapus Gambar</button>
                                </div>
                            </div>
                            <input type="hidden" name="ttd_drawing" class="ttd-drawing-input">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manage Positions Modal -->
    <div class="modal fade" id="managePositionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-briefcase me-2"></i>Kelola Daftar Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Daftar Jabatan Pengurus</h6>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addPositionForm">
                            <i class="fas fa-plus me-1"></i> Tambah Jabatan
                        </button>
                    </div>

                    <!-- Add Position Collapse -->
                    <div class="collapse mb-4" id="addPositionForm">
                        <div class="card card-body bg-light">
                            <form action="{{ route('admin.positions.store') }}" method="POST">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Nama Jabatan</label>
                                        <input type="text" name="name" class="form-control form-control-sm" required placeholder="Contoh: Manager, Ketua, dll">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Deskripsi</label>
                                        <input type="text" name="description" class="form-control form-control-sm" placeholder="Deskripsi tugas jabatan...">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Jabatan</th>
                                    <th>Deskripsi</th>
                                    <th>Anggota</th>
                                    <th width="120" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($positions as $index => $pos)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $pos->name }}</td>
                                        <td class="small">{{ $pos->description ?: '-' }}</td>
                                        <td>
                                            @if($pos->managements->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($pos->managements as $m)
                                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size: 0.7rem;">
                                                            {{ $m->nama_lengkap }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted smaller">Bebas</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-xs btn-outline-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#editPositionModal{{ $pos->id }}">
                                                <i class="fas fa-edit fa-xs"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#deletePositionModal{{ $pos->id }}">
                                                <i class="fas fa-trash fa-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">Belum ada data jabatan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @foreach($positions as $pos)
        <!-- Edit Position Modal -->
        <div class="modal fade" id="editPositionModal{{ $pos->id }}" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog">
                <div class="modal-content shadow-lg">
                    <form action="{{ route('admin.positions.update', $pos->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Jabatan</h5>
                            <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#managePositionsModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Jabatan</label>
                                <input type="text" name="name" class="form-control" value="{{ $pos->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="2">{{ $pos->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#managePositionsModal">Batal</button>
                            <button type="submit" class="btn btn-primary">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Position Modal -->
        <div class="modal fade" id="deletePositionModal{{ $pos->id }}" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog">
                <div class="modal-content shadow-lg">
                    <form action="{{ route('admin.positions.delete', $pos->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal" data-bs-target="#managePositionsModal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Yakin ingin menghapus jabatan <strong>{{ $pos->name }}</strong>?</p>
                            @if($pos->managements_count > 0)
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Jabatan ini masih memiliki {{ $pos->managements_count }} anggota.
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#managePositionsModal">Batal</button>
                            @if($pos->managements_count == 0)
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Profile photo preview functionality
    const photoInput = document.getElementById('photo');
    const uploadBtn = document.getElementById('uploadBtn');
    const profilePreview = document.getElementById('profilePreview');
    const profilePlaceholder = document.getElementById('profilePlaceholder');

    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Enable upload button
                uploadBtn.disabled = false;
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(event) {
                    if (profilePlaceholder) {
                        profilePlaceholder.classList.add('d-none');
                    }
                    profilePreview.src = event.target.result;
                    profilePreview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                uploadBtn.disabled = true;
            }
        });
    }

    // Real-time password validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');

    function validatePasswords() {
        if (newPassword.value && confirmPassword.value) {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Password konfirmasi tidak cocok');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
    }

    if (newPassword && confirmPassword) {
        newPassword.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);
    }

    // Signature Pad logic
    const pads = {};
    
    function resizeCanvas(canvas) {
        if (!canvas || canvas.offsetWidth === 0) return;
        
        const pad = pads[canvas.id];
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        
        // Save drawing before resize
        const data = pad.toData();
        
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        
        pad.clear(); // clear is required after width/height change
        pad.fromData(data); // restore drawing
    }

    function initPad(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        const pad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1.5,
            maxWidth: 4
        });
        
        pads[canvasId] = pad;

        // Resize when window resizes
        window.addEventListener("resize", () => resizeCanvas(canvas));
        
        // Initial resize attempt (might be 0 if hidden)
        resizeCanvas(canvas);
    }

    // Initialize all pads
    initPad('canvas-add');
    @foreach($managements as $item)
        initPad('canvas-edit-{{ $item->id }}');
    @endforeach

    // Fix for Bootstrap Modals and Tabs
    // 1. When modal is shown
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            const canvas = this.querySelector('.tab-pane.active canvas');
            if (canvas) resizeCanvas(canvas);
        });
    });

    // 2. When tab is switched (CRITICAL)
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tabBtn => {
        tabBtn.addEventListener('shown.bs.tab', function (e) {
            const targetId = e.target.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            const canvas = targetPane.querySelector('canvas');
            if (canvas && pads[canvas.id]) {
                resizeCanvas(canvas);
            }
        });
    });

    // Clear canvas buttons
    document.querySelectorAll('.clear-canvas').forEach(btn => {
        btn.addEventListener('click', function() {
            const canvasId = this.getAttribute('data-canvas');
            if (pads[canvasId]) {
                pads[canvasId].clear();
            }
        });
    });

    // Handle form submission to attach signature data
    document.querySelectorAll('.management-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const drawTab = this.querySelector('.tab-pane[id*="draw"]');
            const canvas = drawTab.querySelector('canvas');
            const pad = pads[canvas.id];
            
            // Only use drawing if the "Gambar" tab is currently active OR if there's no file chosen
            const tabBtnGambar = this.querySelector('button[data-bs-target*="draw"]');
            const isGambarTabActive = tabBtnGambar.classList.contains('active');
            
            if (isGambarTabActive && pad && !pad.isEmpty()) {
                this.querySelector('.ttd-drawing-input').value = pad.toDataURL();
            }
        });
    });
});
</script>
@endpush
