@extends('layouts.admin')

@section('title', 'Tambah Saksi - Sistem Arisan')

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
        <i class="fas fa-plus me-2"></i>
        Tambah Saksi Baru
    </h2>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Error Message -->
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

    <!-- Add Saksi Form -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-plus me-2"></i>
                Form Tambah Saksi
            </h5>
        </div>
        <form action="{{ route('admin.saksi.store') }}" method="POST" enctype="multipart/form-data" id="saksiForm">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">
                                <i class="fas fa-user-tie me-1"></i>Jabatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" 
                                   id="jabatan" name="jabatan" 
                                   value="{{ old('jabatan') }}" required>
                        </div>
                    </div>
                   
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_pengurus" class="form-label">
                                <i class="fas fa-user me-1"></i>Nama Pengurus <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" 
                                   id="nama_pengurus" name="nama_pengurus" 
                                   value="{{ old('nama_pengurus') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="foto" class="form-label">
                                <i class="fas fa-camera me-1"></i>Foto
                            </label>
                            <input type="file" class="form-control" 
                                   id="foto" name="foto" accept="image/*">
                        </div>
                    </div>
                   
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label d-block">
                                <i class="fas fa-signature me-1"></i>Tanda Tangan
                            </label>
                            <ul class="nav nav-tabs mb-2" id="ttdTabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active btn-sm py-1" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-ttd" type="button" role="tab">Upload</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link btn-sm py-1" id="draw-tab" data-bs-toggle="tab" data-bs-target="#draw-ttd" type="button" role="tab">Gambar</button>
                                </li>
                            </ul>
                            <div class="tab-content border rounded p-2 bg-light">
                                <div class="tab-pane fade show active" id="upload-ttd" role="tabpanel">
                                    <input type="file" class="form-control" id="ttd" name="ttd" accept="image/*">
                                </div>
                                <div class="tab-pane fade" id="draw-ttd" role="tabpanel">
                                    <div class="signature-pad-container mb-2">
                                        <canvas id="signature-canvas" style="border: 1px solid #ddd; width: 100%; height: 200px;"></canvas>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="clear-signature">
                                        <i class="fas fa-eraser me-1"></i>Hapus Gambar
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="ttd_drawing" id="ttd_drawing">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="form-check mt-1">
                                <input class="form-check-input" 
                                       type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-toggle-on me-1"></i>Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.saksi') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan Saksi
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('signature-canvas');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1.5,
            maxWidth: 4
        });

        function resizeCanvas() {
            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
            const data = signaturePad.toData();
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
            signaturePad.fromData(data);
        }

        window.addEventListener("resize", resizeCanvas);

        // Resize when draw tab is shown
        const drawTab = document.getElementById('draw-tab');
        drawTab.addEventListener('shown.bs.tab', function () {
            resizeCanvas();
        });

        document.getElementById('clear-signature').addEventListener('click', function () {
            signaturePad.clear();
        });

        const form = document.getElementById('saksiForm');
        form.addEventListener('submit', function (e) {
            const activeTab = document.querySelector('#ttdTabs .nav-link.active').id;
            if (activeTab === 'draw-tab') {
                if (!signaturePad.isEmpty()) {
                    document.getElementById('ttd_drawing').value = signaturePad.toDataURL();
                }
            }
        });
    });
</script>
@endpush
