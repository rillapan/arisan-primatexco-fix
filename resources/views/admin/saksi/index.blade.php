@extends('layouts.admin')

@section('title', 'Daftar Saksi - Sistem Arisan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-users-cog me-2"></i>Daftar Saksi</h2>
            <p class="text-muted mb-0">Lihat daftar saksi berdasarkan asal kelompok </p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.saksi') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-5">
                    <label for="group_id" class="form-label fw-bold">Filter Kelompok Asal:</label>
                    <select class="form-select select2" id="group_id" name="group_id" onchange="this.form.submit()">
                        <option value="">-- Semua Kelompok --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $selectedGroupId == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="search" class="form-label fw-bold">Cari Nama atau NIK:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" id="search" placeholder="Ketik nama atau NIK saksi..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.saksi') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($selectedGroupId && $selectedGroup)
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-list me-2"></i>Daftar Saksi dari {{ $selectedGroup->name }}
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="ps-4" width="5%">No</th>
                                <th width="30%">Nama</th>
                                <th width="20%">NIK</th>
                                <th width="45%">Telah Menjadi Saksi di Kelompok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($saksis as $index => $saksi)
                                <tr>
                                    <td class="ps-4">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user text-muted me-2"></i>
                                            <span class="fw-bold">{{ $saksi->nama_pengurus }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $saksi->participant->nik ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            // Get unique group names served by this saksi
                                            $servedGroups = $saksi->monthlyPeriods->map(function($period) {
                                                return $period->group->name . ' (Periode: ' . $period->period_name . ')';
                                            }); // ->unique() - removed unique to show details if needed, or stick to group names only
                                            
                                            // Or simplified as requested: "Kelompok A, Kelompok B"
                                            $servedGroupNames = $saksi->monthlyPeriods->pluck('group.name')->unique();
                                        @endphp
                                        
                                        @if($servedGroupNames->count() > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($servedGroupNames as $name)
                                                    <span class="badge bg-info text-light">{{ $name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small fst-italic">Belum bertugas</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                                            Belum ada peserta dari kelompok <strong>{{ $selectedGroup->name }}</strong> yang menjadi saksi.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif(!$selectedGroupId)
        <div class="text-center py-5 bg-white rounded shadow-sm border border-dashed">
            <div class="py-5">
                <i class="fas fa-hand-point-up fa-3x text-primary mb-3"></i>
                <h5 class="text-muted">Silakan Pilih Kelompok</h5>
                <p class="text-muted mb-0">Pilih kelompok di atas untuk menampilkan daftar saksi.</p>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    // Optional: Add Select2 if available in the layout
    // $(document).ready(function() {
    //     $('.select2').select2({ theme: 'bootstrap-5' });
    // });
</script>
@endpush
