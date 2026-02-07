<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta Kelompok - Sistem Arisan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-gift me-2"></i>
                Sistem Arisan Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-bars me-2"></i>Menu</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.groups') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2"></i>Kelola Kelompok
                        </a>
                        <a href="{{ route('admin.periods') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar me-2"></i>Periode Bulanan
                        </a>
                        <a href="{{ route('admin.winners') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-trophy me-2"></i>Daftar Pemenang
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3><i class="fas fa-users me-2"></i>Peserta Kelompok</h3>
                        <p class="text-muted mb-0">{{ $group->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.groups') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-excel me-1"></i>Import Excel
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Group Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Informasi Kelompok</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted">Total Peserta</h6>
                                    <h4 class="text-primary">{{ $participants->count() }} / {{ $group->max_participants }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted">Peserta Aktif</h6>
                                    <h4 class="text-success">{{ $participants->where('is_active', true)->count() }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted">Angsuran/Bulan</h6>
                                    <h4 class="text-info">Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted">Hadiah Utama</h6>
                                    <h4 class="text-warning">Rp {{ number_format($group->main_prize, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar" style="width: {{ ($participants->count() / $group->max_participants) * 100 }}%"></div>
                        </div>
                        <small class="text-muted">Kapasitas terisi: {{ round(($participants->count() / $group->max_participants) * 100, 1) }}%</small>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-list me-2"></i>Daftar Peserta</h5>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Cari peserta..." style="width: 200px;">
                            <select class="form-select form-select-sm" id="statusFilter" style="width: 120px;">
                                <option value="">Semua</option>
                                <option value="1">Aktif</option>
                                <option value="0">Non-aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($participants->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="participantsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Nomor Undian</th>
                                            <th>Email</th>
                                            <th>Telepon</th>
                                            <th>Status</th>
                                            <th>Pernah Menang</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $index => $participant)
                                        <tr data-status="{{ $participant->is_active ? '1' : '0' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $participant->name }}</strong>
                                                @if($participant->is_active)
                                                    <br><small class="text-success"><i class="fas fa-check-circle"></i> Aktif</small>
                                                @else
                                                    <br><small class="text-muted"><i class="fas fa-times-circle"></i> Non-aktif</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $participant->is_active ? 'primary' : 'secondary' }} fs-6">
                                                    {{ $participant->lottery_number }}
                                                </span>
                                            </td>
                                            <td>{{ $participant->email ?? '-' }}</td>
                                            <td>{{ $participant->phone ?? '-' }}</td>
                                            <td>
                                                @if($participant->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Non-aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($participant->has_won)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-trophy"></i> Ya
                                                        @if($participant->won_at)
                                                            <br><small>{{ \Carbon\Carbon::parse($participant->won_at)->format('d/m/Y') }}</small>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">Belum</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($participant->created_at)->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewParticipant({{ $participant->id }})" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus({{ $participant->id }})" title="Ubah Status">
                                                        <i class="fas fa-toggle-on"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteParticipant({{ $participant->id }})" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum Ada Peserta</h4>
                                <p class="text-muted">Kelompok ini belum memiliki peserta</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="fas fa-file-excel me-1"></i>Import Peserta
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-excel me-2"></i>Import Peserta dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.participants.import', $group->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">
                                <i class="fas fa-file-excel me-1"></i>Pilih File Excel
                            </label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                                   id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Format file: .xlsx atau .xls<br>
                                Kolom yang diperlukan: Nama, Email, Telepon
                            </small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Panduan Import:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Buat file Excel dengan kolom: Nama, Email, Telepon</li>
                                <li>Nomor undian akan dibuat otomatis</li>
                                <li>Pastikan email unik untuk setiap peserta</li>
                                <li>Maksimal {{ $group->max_participants - $participants->count() }} peserta dapat ditambahkan</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Participant Details Modal -->
    <div class="modal fade" id="participantDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user me-2"></i>Detail Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="participantDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#participantsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Status filter functionality
        document.getElementById('statusFilter').addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('#participantsTable tbody tr');
            
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                row.style.display = filterValue === '' || status === filterValue ? '' : 'none';
            });
        });

        // View participant details
        function viewParticipant(participantId) {
            const modal = new bootstrap.Modal(document.getElementById('participantDetailsModal'));
            document.getElementById('participantDetailsContent').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat detail peserta...</p>
                </div>
            `;
            modal.show();
            
            // Simulate loading
            setTimeout(() => {
                document.getElementById('participantDetailsContent').innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Detail peserta akan ditampilkan di sini. Implementasi AJAX diperlukan untuk mengambil data dinamis.
                    </div>
                `;
            }, 1000);
        }

        // Toggle participant status
        function toggleStatus(participantId) {
            if (confirm('Apakah Anda yakin ingin mengubah status peserta ini?')) {
                // In a real application, this would make an AJAX call
                console.log('Toggle status for participant:', participantId);
                alert('Fitur ini akan segera tersedia. Implementasi AJAX diperlukan.');
            }
        }

        // Delete participant
        function deleteParticipant(participantId) {
            if (confirm('Apakah Anda yakin ingin menghapus peserta ini? Tindakan ini tidak dapat dibatalkan.')) {
                // In a real application, this would make an AJAX call
                console.log('Delete participant:', participantId);
                alert('Fitur ini akan segera tersedia. Implementasi AJAX diperlukan.');
            }
        }
    </script>
</body>
</html>
