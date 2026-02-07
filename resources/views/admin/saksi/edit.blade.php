@extends('layouts.admin')

@section('title', 'Edit Saksi - Sistem Arisan')

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
        <i class="fas fa-edit me-2"></i>
        Edit Saksi
    </h2>

    <!-- Edit Saksi Form -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                Form Edit Saksi
            </h5>
        </div>
        <form action="{{ route('admin.saksi.update', $saksi->id) }}" method="POST" enctype="multipart/form-data" id="saksiEditForm">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jabatan_{{ $saksi->id }}" class="form-label">
                                <i class="fas fa-user-tie me-1"></i>Jabatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" 
                                   id="jabatan_{{ $saksi->id }}" name="jabatan" 
                                   value="{{ $saksi->jabatan }}" required>
                        </div>
                    </div>
                   
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_pengurus_{{ $saksi->id }}" class="form-label">
                                <i class="fas fa-user me-1"></i>Nama Pengurus <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" 
                                   id="nama_pengurus_{{ $saksi->id }}" name="nama_pengurus" 
                                   value="{{ $saksi->nama_pengurus }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="foto_{{ $saksi->id }}" class="form-label">
                                <i class="fas fa-camera me-1"></i>Foto
                            </label>
                            <input type="file" class="form-control" 
                                   id="foto_{{ $saksi->id }}" name="foto" accept="image/*">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                            @if($saksi->foto)
                                <div class="mt-2">
                                    <small class="text-muted">Foto saat ini:</small><br>
                                    <img src="{{ asset('uploads/saksi/' . $saksi->foto) }}" 
                                         alt="Current photo" class="img-thumbnail" style="max-height: 60px;">
                                </div>
                            @endif
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
                                    <input type="file" class="form-control" id="ttd_{{ $saksi->id }}" name="ttd" accept="image/*">
                                    @if($saksi->ttd)
                                        <div class="mt-2 text-center border p-1 bg-white">
                                            <small class="text-muted d-block">TTD saat ini:</small>
                                            <img src="{{ asset('uploads/saksi/' . $saksi->ttd) }}" alt="Current TTD" style="max-height: 50px;">
                                        </div>
                                    @endif
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
                                       type="checkbox" id="is_active_{{ $saksi->id }}" name="is_active" value="1" 
                                       {{ $saksi->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active_{{ $saksi->id }}">
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
                    <i class="fas fa-save me-1"></i>Update Saksi
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

        const form = document.getElementById('saksiEditForm');
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
