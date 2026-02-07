@extends('layouts.admin')

@section('title', 'Kelola Kelompok - Sistem Arisan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-users me-2"></i>Kelola Kelompok</h3>
    <a href="{{ route('admin.groups.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Kelompok
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Kelompok</th>
                        <th>Deskripsi</th>
                        <th>Total Jumlah Peserta</th>
                        <th>Jumlah Peserta Aktif</th>
                        <th>Jumlah Pemenang</th>
                        <th>Akumulasi Kas Lelang</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $index => $group)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">{{ $group->id }}</span>
                        </td>
                        <td>
                            <strong>{{ $group->name }}</strong>
                            <br><small class="text-muted">Hadiah: Rp {{ number_format($group->main_prize, 0, ',', '.') }}</small>
                        </td>
                        <td>{{ $group->description ?? '-' }}</td>
                        <td>{{ $group->max_participants }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary rounded-pill me-2">{{ $group->participants->where('is_active', true)->count() - $group->monthlyPeriods->sum(function($period) { return $period->winners->count(); }) }}</span>
                                <div class="progress" style="width: 100px; height: 8px;">
                                    <div class="progress-bar" style="width: {{ (($group->participants->where('is_active', true)->count() - $group->monthlyPeriods->sum(function($period) { return $period->winners->count(); })) / $group->max_participants) * 100 }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-success rounded-pill">{{ $group->winners_count ?? $group->monthlyPeriods->sum(function($period) { return $period->winners->count(); }) }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">Rp {{ number_format($group->total_accumulation ?? 0, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.groups.manage', $group->id) }}" 
                                   class="btn btn-sm btn-info text-white" 
                                   title="Kelola Kelompok" 
                                   style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.groups.settings', $group->id) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit Pengaturan"
                                   style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.groups.delete', $group->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            title="Hapus Kelompok" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kelompok ini?')"
                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
