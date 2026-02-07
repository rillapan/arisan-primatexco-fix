@extends('layouts.admin')

@section('title', 'Daftar Pemenang - Sistem Arisan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3><i class="fas fa-trophy me-2"></i>Daftar Pemenang</h3>
        @if(isset($selectedGroup))
            <p class="text-muted mb-0">Kelompok: {{ $selectedGroup->name }}</p>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif



<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list me-2"></i>Semua Pemenang</h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Cari pemenang..." style="width: 200px;">
            <select class="form-select form-select-sm" id="groupFilter" style="width: 150px;">
                <option value="">Semua Kelompok</option>
                @foreach($winners->pluck('monthlyPeriod.group.name')->unique()->filter() as $groupName)
                    <option value="{{ $groupName }}">{{ $groupName }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body">
        @if($winners->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="winnersTable">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Pemenang</th>
                            <th>Nomor Undian</th>
                            <th>Kelompok</th>
                            <th>Periode</th>
                            <th>Hadiah Utama</th>
                            <th>Penawaran</th>
                            <th>Hadiah Akhir</th>
                            <th>Perlu Undi</th>
                            <th>Waktu Menang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($winners as $index => $winner)
                        <tr data-group="{{ $winner->monthlyPeriod?->group?->name ?? '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $winner->participant->name ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $winner->participant->email ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $winner->participant->lottery_number ?? '-' }}</span>
                            </td>
                            <td>{{ $winner->monthlyPeriod?->group?->name ?? '-' }}</td>
                            <td>{{ $winner->monthlyPeriod?->period_name ?? '-' }}</td>
                            <td class="fw-bold">Rp {{ number_format($winner->main_prize, 0, ',', '.') }}</td>
                            <td class="fw-bold text-primary">Rp {{ number_format($winner->bid_amount, 0, ',', '.') }}</td>
                            <td class="fw-bold text-success">Rp {{ number_format($winner->final_prize, 0, ',', '.') }}</td>
                            <td>
                                @if($winner->needs_draw)
                                    <span class="badge bg-warning">Ya</span>
                                    @if($winner->draw_time)
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($winner->draw_time)->format('d/m/Y H:i') }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-success">Tidak</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($winner->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.periods.show', $winner->monthly_period_id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Periode">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-trophy fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Belum Ada Pemenang</h4>
                <p class="text-muted">Belum ada pemenang yang tercatat dalam sistem</p>
                <a href="{{ route('admin.periods') }}" class="btn btn-primary">
                    <i class="fas fa-calendar me-1"></i>Lihat Periode
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('modals')
    <!-- Winner Details Modal -->
    <div class="modal fade" id="winnerDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detail Pemenang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="winnerDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#winnersTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    // Group filter functionality
    document.getElementById('groupFilter').addEventListener('change', function() {
        const filterValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#winnersTable tbody tr');
        
        rows.forEach(row => {
            const groupName = row.getAttribute('data-group').toLowerCase();
            row.style.display = filterValue === '' || groupName.includes(filterValue) ? '' : 'none';
        });
    });

    // View winner details
    function viewWinnerDetails(winnerId) {
        // In a real application, you would fetch this data via AJAX
        // For now, we'll show a placeholder
        const modal = new bootstrap.Modal(document.getElementById('winnerDetailsModal'));
        document.getElementById('winnerDetailsContent').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat detail pemenang...</p>
            </div>
        `;
        modal.show();
        
        // Simulate loading
        setTimeout(() => {
            document.getElementById('winnerDetailsContent').innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Detail pemenang akan ditampilkan di sini. Implementasi AJAX diperlukan untuk mengambil data dinamis.
                </div>
            `;
        }, 1000);
    }

    // Export winners to Excel
    function exportWinners() {
        // In a real application, this would generate and download an Excel file
        alert('Fungsi export Excel akan segera tersedia. Fitur ini memerlukan library tambahan seperti Laravel Excel.');
    }

    // Sort table functionality
    function sortTable(columnIndex) {
        const table = document.getElementById('winnersTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aText = a.cells[columnIndex].textContent.trim();
            const bText = b.cells[columnIndex].textContent.trim();
            return aText.localeCompare(bText);
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }
</script>
@endpush
