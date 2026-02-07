@extends('layouts.admin')

@section('title', 'Ubah Dokumentasi - ' . $group->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.groups.documentations.index', $group->id) }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-edit me-2 text-primary"></i>Ubah Dokumentasi
        </h1>
        <p class="text-muted mb-0">{{ $group->name }}</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Formulir Ubah Dokumentasi</h6>
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

                <div class="text-center mb-4 p-3 bg-light rounded">
                    <p class="mb-2 text-muted small text-uppercase fw-bold">Pratinjau Saat Ini</p>
                    @if($documentation->type === 'image')
                        <img src="{{ asset('storage/' . $documentation->content) }}" class="img-fluid rounded shadow-sm" style="max-height: 200px;" alt="">
                    @elseif($documentation->type === 'video')
                        <div class="ratio ratio-16x9 mx-auto" style="max-width: 400px;">
                            <video controls>
                                <source src="{{ asset('storage/' . $documentation->content) }}" type="video/mp4">
                            </video>
                        </div>
                    @else
                        <div class="p-3 bg-white border text-start rounded" style="max-height: 200px; overflow-y: auto;">
                            {{ $documentation->content }}
                        </div>
                    @endif
                    <div class="mt-2">
                        <span class="badge bg-secondary">
                            {{ ucfirst($documentation->type) }} - 
                            {{ $documentation->monthlyPeriod->period_start->locale('id')->monthName }} {{ $documentation->monthlyPeriod->period_start->year }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('admin.documentations.update', $documentation->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    @if($documentation->type === 'text')
                    <div class="mb-4">
                        <label for="text_content" class="form-label fw-bold">Isi Teks <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('text_content') is-invalid @enderror" id="text_content" name="text_content" rows="8" required>{{ old('text_content', $documentation->content) }}</textarea>
                        @error('text_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="mb-4">
                        <label for="caption" class="form-label fw-bold">Keterangan</label>
                        <input type="text" class="form-control @error('caption') is-invalid @enderror" id="caption" name="caption" value="{{ old('caption', $documentation->caption) }}" placeholder="Judul atau keterangan singkat">
                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle me-1"></i> Catatan: Berkas (Gambar/Video) tidak dapat diubah di sini. Jika ingin mengganti berkas, silakan hapus dan tambah baru.
                        </div>
                        @error('caption')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end border-top pt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Perbarui Dokumentasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
