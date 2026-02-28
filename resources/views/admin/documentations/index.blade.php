@extends('layouts.admin')

@section('title', 'Kelola Link Google Drive')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Dashboard
        </a>
    </div>
    <div class="text-start text-sm-end">
        <h1 class="h4 h3-md mb-1 text-gray-800">
            <i class="fab fa-google-drive me-2 text-success"></i>Link Google Drive
        </h1>
        <p class="text-muted mb-0 small">Dokumentasi untuk semua kelompok</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Current Link Card --}}
        @if($driveLink)
        <div class="card shadow mb-4 border-0">
            <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border-radius: 0.35rem;">
                <i class="fab fa-google-drive text-success mb-3" style="font-size: 4rem;"></i>
                <h4 class="fw-bold text-success mb-2">Link Drive Aktif</h4>
                @if($driveCaption)
                    <p class="text-dark mb-3">{{ $driveCaption }}</p>
                @endif
                <a href="{{ $driveLink }}" target="_blank" class="btn btn-success rounded-pill px-4 shadow-sm mb-3">
                    <i class="fas fa-external-link-alt me-2"></i> Buka Google Drive
                </a>
                <p class="text-muted small mb-0 text-break px-3">{{ $driveLink }}</p>
            </div>
        </div>
        @endif

        {{-- Form Card --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fab fa-google-drive me-2"></i>
                    {{ $driveLink ? 'Ubah Link Drive' : 'Tambah Link Drive' }}
                </h6>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.drive-link.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="drive_link" class="form-label fw-bold">URL / Link Google Drive <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white"><i class="fab fa-google-drive"></i></span>
                            <input type="url" class="form-control @error('drive_link') is-invalid @enderror" id="drive_link" name="drive_link" value="{{ old('drive_link', $driveLink) }}" placeholder="https://drive.google.com/drive/folders/xxx" required>
                        </div>
                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle me-1"></i> Masukkan URL lengkap Google Drive (folder atau file), termasuk <code>https://</code>
                        </div>
                        @error('drive_link')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="drive_caption" class="form-label fw-bold">Keterangan (Opsional)</label>
                        <input type="text" class="form-control @error('drive_caption') is-invalid @enderror" id="drive_caption" name="drive_caption" value="{{ old('drive_caption', $driveCaption) }}" placeholder="Contoh: Folder Dokumentasi Arisan Primkopkar">
                        @error('drive_caption')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between border-top pt-4">
                        @if($driveLink)
                        <form action="{{ route('admin.drive-link.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus link ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash me-1"></i> Hapus Link
                            </button>
                        </form>
                        @else
                            <div></div>
                        @endif
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save me-1"></i> {{ $driveLink ? 'Perbarui Link' : 'Simpan Link' }}
                        </button>
                    </div>
                </form>

                @if($driveLink)
                <div class="mt-3">
                    <form action="{{ route('admin.drive-link.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus link Drive ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash me-1"></i> Hapus Link
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
