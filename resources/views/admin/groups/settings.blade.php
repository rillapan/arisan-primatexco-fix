@extends('layouts.admin')

@section('title', 'Pengaturan Kelompok - ' . $group->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h3 class="mb-0"><i class="fas fa-cog me-2"></i>Pengaturan Kelompok</h3>
        <p class="text-muted mb-0">{{ $group->name }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Terjadi kesalahan!</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Ubah Informasi Kelompok
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.groups.settings.update', $group->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Kelompok</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ $group->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="max_participants" class="form-label">Maksimal Peserta</label>
                                        <input type="number" class="form-control" id="max_participants" name="max_participants" 
                                               value="{{ $group->max_participants }}" min="1" max="200" required>
                                        <div class="form-text">Jumlah maksimal peserta dalam kelompok (default: 90)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi Kelompok</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $group->description ?? '' }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="monthly_installment" class="form-label">Angsuran Bulanan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="monthly_installment" name="monthly_installment" 
                                                   value="{{ $group->monthly_installment }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="main_prize" class="form-label">Hadiah Utama</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="main_prize" name="main_prize" 
                                                   value="{{ $group->main_prize }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="shu" class="form-label">SHU</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="shu" name="shu" 
                                                   value="{{ $group->shu }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="min_bid" class="form-label">Lelang Minimum</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="min_bid" name="min_bid" 
                                                   value="{{ $group->min_bid }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="max_bid" class="form-label">Lelang Maksimum</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="max_bid" name="max_bid" 
                                                   value="{{ $group->max_bid }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               {{ $group->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Kelompok Aktif
                                        </label>
                                        <div class="form-text">Nonaktifkan jika kelompok tidak lagi digunakan</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        Simpan Perubahan
                                    </button>
                                    <a href="{{ route('admin.groups.manage', $group->id) }}" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times me-2"></i>
                                        Batal
                                    </a>
                                </div>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteGroupModal">
                                    <i class="fas fa-trash-alt me-2"></i>
                                    Hapus Grup
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Grup
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">ID Grup:</small>
                            <h6 class="text-primary">{{ $group->id }}</h6>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Total Peserta:</small>
                            <h6 class="text-success">{{ $group->participants->count() }} / {{ $group->max_participants }}</h6>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Total Periode:</small>
                            <h6 class="text-info">{{ $group->monthlyPeriods->count() }}</h6>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Total Pemenang:</small>
                            <h6 class="text-warning">{{ $group->winners->count() }}</h6>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Dibuat:</small>
                            <h6 class="text-muted">{{ $group->created_at->format('d/m/Y H:i') }}</h6>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Terakhir Diubah:</small>
                            <h6 class="text-muted">{{ $group->updated_at->format('d/m/Y H:i') }}</h6>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Peringatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <small>
                                <strong>Catatan Penting:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Mengubah pengaturan dapat memengaruhi perhitungan yang sedang berjalan</li>
                                    <li>Lelang minimum dan maksimum akan memengaruhi proses undian</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Group Confirmation Modal -->
    <div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteGroupModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Konfirmasi Hapus Grup
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>PERINGATAN:</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>
                    
                    <p>Apakah Anda yakin ingin menghapus grup <strong>{{ $group->name }}</strong>?</p>
                    
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-muted mb-2">Data yang akan dihapus:</h6>
                        <ul class="mb-0">
                            <li><strong>{{ $group->participants->count() }}</strong> peserta</li>
                            <li><strong>{{ $group->monthlyPeriods->count() }}</strong> periode bulanan</li>
                            <li><strong>{{ $group->payments->count() }}</strong> data pembayaran</li>
                            <li><strong>{{ $group->bids->count() }}</strong> data lelang</li>
                            <li><strong>{{ $group->winners->count() }}</strong> data pemenang</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Catatan:</strong> Semua data terkait grup akan dihapus permanen dari sistem.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Batal
                    </button>
                    <form action="{{ route('admin.groups.delete', $group->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-1"></i>
                            Hapus Grup
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>
@endsection
