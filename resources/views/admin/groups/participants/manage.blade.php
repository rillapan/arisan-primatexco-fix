@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.groups.manage', $group->id) }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-users me-2 text-primary"></i>Kelola Peserta - {{ $group->name }}
        </h1>
        <p class="text-muted mb-0">Kelola peserta arisan</p>
    </div>
</div>

    <div class="container-fluid mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{!! session('warning') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Kelola Peserta - {{ $group->name }}
                        </h4>
                        <div class="d-flex align-items-center gap-2">
                            <form action="{{ route('admin.groups.registration.toggle', $group->id) }}" method="POST" class="bg-white text-dark px-3 py-1 rounded shadow-sm d-flex align-items-center">
                                @csrf
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="registrationToggle" name="is_registration_active" onchange="this.form.submit()" {{ $group->is_registration_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="registrationToggle">Buka Pendaftaran</label>
                                </div>
                            </form>
                            <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                                <i class="fas fa-qrcode me-1"></i>
                                Scan QR Bukti
                            </button>
                            <a href="{{ route('admin.groups.participants.export', $group->id) }}" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i>
                                Export Excel
                            </a>
                            <button class="btn btn-warning btn-sm" 
                                    data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import me-1"></i>
                                Import Excel
                            </button>
                            @php
                                $pendingCount = $participantsData->where('participant.registration_status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                            <button class="btn btn-info btn-sm" 
                                    data-bs-toggle="modal" data-bs-target="#approveAllModal">
                                <i class="fas fa-check-double me-1"></i>
                                Setujui Semua Pendaftar ({{ $pendingCount }})
                            </button>
                            @endif
                            @if($participantsData->count() > 0)
                            <button class="btn btn-danger btn-sm" 
                                    data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                                <i class="fas fa-trash-alt me-1"></i>
                                Hapus Semua
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                    <div class="card-body">
                    
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>No Undian</th>
                                        <th>Nama Peserta</th>
                                        <th>Bag/Shift</th>
                                        <th>NIK</th>
                                        <th>Besar Lelang</th>
                                        <th>Tanggal Lelang</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($participantsData as $index => $data)
                                    <tr class="{{ $data['participant']->registration_status == 'pending' ? 'table-warning' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($data['participant']->lottery_number)
                                                <span class="badge bg-primary">{{ $data['participant']->lottery_number }}</span>
                                            @else
                                                <span class="badge bg-secondary">BELUM ADA</span>
                                            @endif
                                        </td>
                                        <td>{{ $data['participant']->name }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $data['participant']->shift }}</span>
                                        </td>
                                        <td>{{ $data['participant']->nik }}</td>
                                        <td>
                                            @if($data['current_bid'])
                                                <span class="text-success fw-bold">
                                                    Rp {{ number_format($data['current_bid']->bid_amount, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data['has_won'])
                                                <span class="badge bg-success">{{ $data['won_date'] }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data['participant']->registration_status == 'pending')
                                                <span class="badge bg-warning text-dark border"><i class="fas fa-clock me-1"></i>Pending</span>
                                            @elseif($data['has_won'])
                                                <span class="badge bg-success">Sudah Menang</span>
                                            @else
                                                <span class="badge bg-primary">Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                             <div class="d-flex justify-content-center gap-1">
                                                @if($data['participant']->registration_status == 'pending')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success approve-participant-btn"
                                                            data-id="{{ $data['participant']->id }}"
                                                            data-name="{{ $data['participant']->name }}"
                                                            data-nik="{{ $data['participant']->nik }}"
                                                            data-shift="{{ $data['participant']->shift }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#approveParticipantModal"
                                                            title="Setujui Pendaftaran">
                                                        <i class="fas fa-check me-1"></i>Setujui
                                                    </button>

                                                    <form action="{{ route('admin.participants.delete', $data['participant']->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak pendaftaran {{ $data['participant']->name }}? Data pendaftaran akan dihapus.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Tolak Pendaftaran">
                                                            <i class="fas fa-times me-1"></i>Tolak
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('admin.participants.show', $data['participant']->id) }}" 
                                                       class="btn btn-sm btn-info text-white" 
                                                       title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @php
                                                        $linkedCount = $participantsData->filter(fn($p) => $p['participant']->nik === $data['participant']->nik && $p['participant']->id !== $data['participant']->id)->count();
                                                    @endphp
                                                    <button type="button" 
                                                            class="btn btn-sm btn-warning text-white edit-participant-btn"
                                                            data-id="{{ $data['participant']->id }}"
                                                            data-name="{{ $data['participant']->name }}"
                                                            data-lottery="{{ $data['participant']->lottery_number }}"
                                                            data-nik="{{ $data['participant']->nik }}"
                                                            data-shift="{{ $data['participant']->shift }}"
                                                            data-active="{{ $data['participant']->is_active }}"
                                                            data-group-id="{{ $group->id }}"
                                                            data-linked-count="{{ $linkedCount }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editParticipantModal"
                                                            title="Edit Peserta">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    @if($data['participant']->is_password_changed)
                                                    @php
                                                        $resetLinkedCount = $participantsData->filter(fn($p) => $p['participant']->nik === $data['participant']->nik && $p['participant']->id !== $data['participant']->id)->count();
                                                        $resetConfirmMsg = 'Apakah Anda yakin ingin mereset password peserta ' . $data['participant']->name . ' ke default (No. Undian)?';
                                                        if ($resetLinkedCount > 0) {
                                                            $resetConfirmMsg .= '\\n\\n⚠️ PERHATIAN: ' . $resetLinkedCount . ' akun terhubung dengan NIK yang sama juga akan direset!';
                                                        }
                                                    @endphp
                                                    <form action="{{ route('admin.participants.reset-password', $data['participant']->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-secondary {{ $resetLinkedCount > 0 ? 'btn-warning' : '' }}" title="Reset Password{{ $resetLinkedCount > 0 ? ' (' . ($resetLinkedCount + 1) . ' akun)' : '' }}" onclick="return confirm('{{ $resetConfirmMsg }}')">
                                                            <i class="fas fa-key"></i>
                                                            @if($resetLinkedCount > 0)
                                                            <span class="badge bg-light text-dark ms-1" style="font-size: 0.6rem;">{{ $resetLinkedCount + 1 }}</span>
                                                            @endif
                                                        </button>
                                                    </form>
                                                    @endif
                                                    
                                                    <form action="{{ route('admin.participants.delete', $data['participant']->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta {{ $data['participant']->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Peserta">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-2x mb-2"></i><br>
                                            Belum ada peserta dalam kelompok ini
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-chart-bar me-2"></i>
                                            Ringkasan Peserta
                                        </h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Total Peserta:</small>
                                                <h5 class="text-primary">{{ $participantsData->count() }}</h5>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Sudah Menang:</small>
                                                <h5 class="text-success">{{ $participantsData->where('has_won', true)->count() }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-gavel me-2"></i>
                                            Ringkasan Lelang
                                        </h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Bid Aktif:</small>
                                                <h5 class="text-warning">{{ $participantsData->where('current_bid', '!=', null)->count() }}</h5>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Belum Bid:</small>
                                                <h5 class="text-info">{{ $participantsData->where('current_bid', null)->count() }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="fas fa-file-import me-2"></i>
                        Import Peserta dari Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.groups.participants.import', $group->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" 
                                   accept=".xlsx,.xls" required>
                            <div class="form-text">
                                Format file: .xlsx atau .xls
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('admin.groups.participants.template', $group->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-download me-1"></i>Unduh Template Excel
                                </a>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Format Excel:</h6>
                            <ul class="mb-0">
                                <li><strong>NAMA</strong> - Nama lengkap peserta</li>
                                <li><strong>NIK</strong> - Nomor Induk Karyawan</li>
                                <li><strong>BAG/SHIFT</strong> - Bagian atau Shift peserta</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Pastikan data NIK tidak duplikat dengan peserta yang sudah ada.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>
                            Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete All Confirmation Modal -->
    <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteAllModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Konfirmasi Hapus Semua Peserta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>PERINGATAN:</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>
                    
                    <p>Apakah Anda yakin ingin menghapus <strong>semua peserta</strong> dari grup <strong>{{ $group->name }}</strong>?</p>
                    
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-muted mb-2">Detail yang akan dihapus:</h6>
                        <ul class="mb-0">
                            <li><strong>{{ $participantsData->count() }}</strong> peserta</li>
                            <li>Semua data peserta termasuk nomor undian, NIK, dan informasi lainnya</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Catatan:</strong> Penghapusan tidak akan dilakukan jika ada data pembayaran, lelang, atau pemenang yang terkait dengan peserta.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Batal
                    </button>
                    <form action="{{ route('admin.groups.participants.delete-all', $group->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-1"></i>
                            Hapus Semua Peserta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve All Confirmation Modal -->
    <div class="modal fade" id="approveAllModal" tabindex="-1" aria-labelledby="approveAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="approveAllModalLabel">
                        <i class="fas fa-check-double me-2"></i>
                        Konfirmasi Setujui Semua Pendaftar
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>INFO:</strong> Semua pendaftar yang pending akan disetujui!
                    </div>
                    
                    <p>Apakah Anda yakin ingin menyetujui <strong>semua pendaftar yang pending</strong> di grup <strong>{{ $group->name }}</strong>?</p>
                    
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-muted mb-2">Detail yang akan disetujui:</h6>
                        <ul class="mb-0">
                            <li><strong>@php echo $participantsData->where('participant.registration_status', 'pending')->count(); @endphp</strong> pendaftar yang pending</li>
                            <li>Setiap pendaftar sudah memiliki Nomor Undian yang dibuat otomatis saat pendaftaran</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Batal
                    </button>
                    <form action="{{ route('admin.groups.participants.approve-all', $group->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-info text-white">
                            <i class="fas fa-check-double me-1"></i>
                            Setujui Semua Pendaftar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Edit Participant Modal -->
    <div class="modal fade" id="editParticipantModal" tabindex="-1" aria-labelledby="editParticipantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editParticipantModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Data Peserta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editParticipantForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Linked Accounts Warning -->
                        <div id="linked_accounts_warning" class="alert alert-info d-none mb-3">
                            <i class="fas fa-link me-2"></i>
                            <strong>Akun Terhubung:</strong> <span id="linked_count_text"></span>
                            <hr class="my-2">
                            <small class="text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Perubahan Nama, NIK, dan Bagian/Shift akan diterapkan ke <strong>semua akun</strong> yang terhubung!</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_lottery_number" class="form-label">No. Undian</label>
                            <input type="text" class="form-control bg-light" id="edit_lottery_number" readonly disabled>
                            <input type="hidden" id="edit_lottery_number_hidden" name="lottery_number">
                            <small class="text-muted">No. Undian akan otomatis terganti jika NIK diubah</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Peserta</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="edit_nik" name="nik">
                        </div>
                        <div class="mb-3">
                            <label for="edit_shift" class="form-label">Bagian / Shift</label>
                            <input type="text" class="form-control" id="edit_shift" name="shift">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Approve Participant Modal -->
    <div class="modal fade" id="approveParticipantModal" tabindex="-1" aria-labelledby="approveParticipantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="approveParticipantModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Setujui Pendaftaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="approveParticipantForm" action="" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="card border-0 bg-light rounded-4 mb-0">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12 border-bottom pb-2">
                                        <label class="text-muted small text-uppercase fw-bold mb-1">Nama Lengkap</label>
                                        <div class="h5 mb-0 fw-bold text-dark" id="approve_info_name"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small text-uppercase fw-bold mb-1">NIK</label>
                                        <div class="fw-bold text-primary" id="approve_info_nik"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small text-uppercase fw-bold mb-1">Bagian/Shift</label>
                                        <div class="fw-bold text-dark" id="approve_info_shift"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4 rounded-3 fw-bold">
                            <i class="fas fa-check-circle me-2"></i>Setujui Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="qrScannerModalLabel"><i class="fas fa-camera me-2"></i>Scan QR Bukti Pendaftaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto; border-radius: 12px; overflow: hidden; background: #f8f9fa;"></div>
                    
                    <div class="mt-4 border-top pt-3">
                        <p class="text-muted small mb-2">Atau masukkan NIK secara manual:</p>
                        <div class="input-group">
                            <input type="text" id="manualNikInput" class="form-control" placeholder="Masukkan NIK Peserta...">
                            <button class="btn btn-primary" type="button" id="manualSearchBtn">
                                <i class="fas fa-search me-1"></i>Cari
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Result Modal -->
    <div class="modal fade" id="qrResultModal" tabindex="-1" aria-labelledby="qrResultModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="qrResultModalLabel"><i class="fas fa-user-check me-2"></i>Detail Pendaftaran (Scan)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="qrResultBody" class="modal-body p-4">
                    <!-- Dynamic content will be injected here -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing modal logic for Edit & Approve
            var editModal = document.getElementById('editParticipantModal');
            var currentGroupId = null;
            var originalNik = null;
            var originalLottery = null;
            
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var name = button.getAttribute('data-name');
                    var lottery = button.getAttribute('data-lottery');
                    var nik = button.getAttribute('data-nik');
                    var shift = button.getAttribute('data-shift');
                    var linkedCount = parseInt(button.getAttribute('data-linked-count')) || 0;
                    currentGroupId = button.getAttribute('data-group-id');
                    originalNik = nik;
                    originalLottery = lottery;
                    
                    var form = document.getElementById('editParticipantForm');
                    form.action = '/admin/participants/' + id;
                    
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_lottery_number').value = lottery;
                    document.getElementById('edit_lottery_number_hidden').value = lottery;
                    document.getElementById('edit_nik').value = nik;
                    document.getElementById('edit_shift').value = shift;
                    
                    // Show/hide linked accounts warning
                    var warningDiv = document.getElementById('linked_accounts_warning');
                    var linkedText = document.getElementById('linked_count_text');
                    if (linkedCount > 0) {
                        linkedText.textContent = 'Peserta ini memiliki ' + linkedCount + ' akun lain dengan NIK yang sama.';
                        warningDiv.classList.remove('d-none');
                    } else {
                        warningDiv.classList.add('d-none');
                    }
                });
                
                // Auto-update lottery_number when NIK changes
                document.getElementById('edit_nik').addEventListener('input', function() {
                    var newNik = this.value.trim();
                    if (newNik && newNik !== originalNik) {
                        // Parse original lottery number to extract suffix (groupId-accountNumber)
                        var parts = originalLottery.split('-');
                        var suffix = '';
                        if (parts.length >= 2) {
                            // Keep everything after the first part (NIK)
                            suffix = '-' + parts.slice(1).join('-');
                        }
                        var newLottery = newNik + suffix;
                        document.getElementById('edit_lottery_number').value = newLottery;
                        document.getElementById('edit_lottery_number_hidden').value = newLottery;
                    } else if (newNik === originalNik) {
                        // Reset to original if NIK is reverted
                        document.getElementById('edit_lottery_number').value = originalLottery;
                        document.getElementById('edit_lottery_number_hidden').value = originalLottery;
                    }
                });
            }

            var approveModal = document.getElementById('approveParticipantModal');
            if (approveModal) {
                approveModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var name = button.getAttribute('data-name');
                    var nik = button.getAttribute('data-nik');
                    var shift = button.getAttribute('data-shift');
                    
                    var form = document.getElementById('approveParticipantForm');
                    form.action = '/admin/participants/' + id + '/approve';
                    
                    document.getElementById('approve_info_name').textContent = name;
                    document.getElementById('approve_info_nik').textContent = nik;
                    document.getElementById('approve_info_shift').textContent = shift;
                });
            }

            // QR Scanner Logic - Improved
            let html5QrCode = null;
            const qrScannerModal = document.getElementById('qrScannerModal');
            const qrResultModal = new bootstrap.Modal(document.getElementById('qrResultModal'));
            const qrResultBody = document.getElementById('qrResultBody');
            const readerElement = document.getElementById('reader');

            // Check for Secure Context
            if (!window.isSecureContext && location.hostname !== "localhost" && location.hostname !== "127.0.0.1") {
                console.warn("Camera access usually requires HTTPS (Secure Context).");
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning small mt-2';
                alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Mode Scan membutuhkan koneksi HTTPS jika diakses bukan dari localhost.';
                readerElement.parentNode.insertBefore(alertDiv, readerElement);
            }

            qrScannerModal.addEventListener('shown.bs.modal', function () {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }
                
                const config = { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0 
                };

                // Start scanning
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    config, 
                    (decodedText) => {
                        // Success!
                        html5QrCode.stop().then(() => {
                            const scanModal = bootstrap.Modal.getInstance(qrScannerModal);
                            scanModal.hide();
                            handleScannedData(decodedText);
                        }).catch(err => console.error("Stop error", err));
                    },
                    (errorMessage) => {
                        // This fires for every frame where QR is not found, silent is better
                        // console.log("Scanning...");
                    }
                ).catch((err) => {
                    console.error("Camera access error:", err);
                    readerElement.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-video-slash fa-2x mb-2"></i>
                            <p class="small mb-0">Gagal mengakses kamera: ${err}</p>
                            <small class="d-block mt-2">Pastikan izin kamera sudah diberikan dan Anda menggunakan koneksi aman (HTTPS).</small>
                        </div>
                    `;
                });
            });

            qrScannerModal.addEventListener('hidden.bs.modal', function () {
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        console.log("Scanner stopped.");
                    }).catch(err => console.error("Stop error", err));
                }
                document.getElementById('manualNikInput').value = '';
            });

            // Manual Search Logic
            document.getElementById('manualSearchBtn').addEventListener('click', function() {
                const nik = document.getElementById('manualNikInput').value.trim();
                if (nik) {
                    const scanModal = bootstrap.Modal.getInstance(qrScannerModal);
                    scanModal.hide();
                    handleScannedData(JSON.stringify({ nik: nik }));
                } else {
                    alert('Silakan masukkan NIK terlebih dahulu.');
                }
            });

            // Allow enter key on manual search
            document.getElementById('manualNikInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('manualSearchBtn').click();
                }
            });

            function handleScannedData(data) {
                console.log("Scanned Data:", data);
                try {
                    let parsed;
                    try {
                        parsed = JSON.parse(data);
                    } catch (e) {
                        // Fallback if data is just the NIK string
                        parsed = { nik: data };
                    }

                    const scannedNik = parsed.nik;
                    if (!scannedNik) throw new Error("NIK tidak ditemukan dalam QR");
                    
                    // Match with participantsData currently on page
                    const participants = @json($participantsData);
                    const matches = participants.filter(d => 
                        d.participant.nik == scannedNik && 
                        d.participant.registration_status === 'pending'
                    );

                    if (matches.length === 0) {
                        qrResultBody.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h6 class="fw-bold">Pendaftar Tidak Ditemukan</h6>
                                <p class="text-muted small">Tidak ada akun pending dengan NIK <strong>${scannedNik}</strong> di kelompok ini.</p>
                                <hr>
                                <p class="small text-warning"><i class="fas fa-info-circle me-1"></i> Pastikan peserta mendaftar di kelompok <strong>{{ $group->name }}</strong>.</p>
                                <button class="btn btn-secondary btn-sm mt-3 w-100" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        `;
                    } else {
                        const first = matches[0].participant;
                        let accountsHtml = '';
                        matches.forEach((m, idx) => {
                            accountsHtml += `
                                <div class="d-flex justify-content-between align-items-center mb-2 p-3 bg-white rounded border shadow-sm">
                                    <div>
                                        <div class="small fw-bold text-primary">${m.participant.lottery_number}</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Status: PENDING</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <form action="/admin/participants/${m.participant.id}/approve" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success px-3">Setujui</button>
                                        </form>
                                        <form action="/admin/participants/${m.participant.id}" method="POST" class="d-inline" onsubmit="return confirm('Tolak pendaftaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Tolak</button>
                                        </form>
                                    </div>
                                </div>
                            `;
                        });

                        qrResultBody.innerHTML = `
                            <div class="card border-0 bg-light rounded-4 mb-4">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="text-muted small text-uppercase fw-bold mb-0">Nama Peserta</label>
                                        <div class="h5 fw-bold text-dark mb-0">${first.name}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="text-muted small text-uppercase fw-bold mb-0">NIK</label>
                                            <div class="fw-bold text-primary">${first.nik}</div>
                                        </div>
                                        <div class="col-6">
                                            <label class="text-muted small text-uppercase fw-bold mb-0">Bagian/Shift</label>
                                            <div class="fw-bold text-dark">${first.shift}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="fw-bold mb-3 small d-flex justify-content-between">
                                <span>Daftar Akun (Undian)</span>
                                <span class="badge bg-info">${matches.length} Total</span>
                            </h6>
                            <div class="account-list" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                                ${accountsHtml}
                            </div>

                            <div class="mt-4 border-top pt-3">
                                <button class="btn btn-outline-secondary w-100 rounded-3" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        `;
                    }
                    qrResultModal.show();
                } catch (e) {
                    console.error("Invalid QR Data", e);
                    alert("Format QR Code tidak valid. Pastikan Anda men-scan QR Code dari Bukti Pendaftaran PRIMKOPKAR PRIMA.");
                }
            }
        });
    </script>
    @endpush

@endsection

