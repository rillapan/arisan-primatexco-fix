@extends('layouts.admin')

@section('title', 'Kelola Customer Service - Sistem Arisan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-headset me-2"></i>Customer Service</h2>
            <p class="text-muted mb-0">Kelola data Customer Service (CS) untuk membantu peserta.</p>
        </div>
        <a href="{{ route('admin.customer-service.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah CS
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>Daftar Customer Service</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4" width="5%">No</th>
                            <th>Nama CS</th>
                            <th>No WhatsApp</th>
                            <th>Status</th>
                            <th class="text-center" width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerServices as $index => $cs)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($cs->photo)
                                            <img src="{{ asset('uploads/customer_service/' . $cs->photo) }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                        @endif
                                        <span class="fw-bold">{{ $cs->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cs->whatsapp_number) }}" target="_blank" class="text-success text-decoration-none">
                                        <i class="fab fa-whatsapp me-1"></i>{{ $cs->whatsapp_number }}
                                    </a>
                                </td>
                                <td>
                                    @if($cs->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.customer-service.edit', $cs->id) }}" class="btn btn-sm btn-outline-warning" title="Ubah">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.customer-service.destroy', $cs->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus CS ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                                        Belum ada data Customer Service.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
