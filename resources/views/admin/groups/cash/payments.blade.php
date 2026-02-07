<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Angsuran - {{ $group->name }}</title>
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
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali ke Dashboard
                </a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link text-decoration-none">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-hand-holding-usd me-2"></i>
                            Proses Angsuran - {{ $group->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi Periode
                            </h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Periode:</strong> {{ $currentPeriod->period_name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Angsuran Ke:</strong> {{ $participantsWithPayments->first()['installment_number'] }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Besar Angsuran:</strong> Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Total Peserta:</strong> {{ $participantsWithPayments->count() }}
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>No Undian</th>
                                        <th>Nama Peserta</th>
                                        <th>Bag/Shift</th>
                                        <th>NIK</th>
                                        <th>Status Pembayaran</th>
                                        <th>Bukti Angsuran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($participantsWithPayments as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $data['participant']->lottery_number }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $data['participant']->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $data['participant']->shift }}</span>
                                        </td>
                                        <td>{{ $data['participant']->nik }}</td>
                                        <td>
                                            @if($data['has_paid'])
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>
                                                    Sudah Bayar
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>
                                                    Belum Bayar
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data['has_paid'])
                                                <a href="{{ route('payments.receipt', $data['payment']->id) }}" 
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-file-invoice me-1"></i>
                                                    {{ $data['payment']->receipt_number }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$data['has_paid'])
                                                <button class="btn btn-success btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#paymentModal"
                                                        data-participant-id="{{ $data['participant']->id }}"
                                                        data-participant-name="{{ $data['participant']->name }}"
                                                        data-participant-nik="{{ $data['participant']->nik }}"
                                                        data-amount="{{ $group->monthly_installment }}">
                                                    <i class="fas fa-plus me-1"></i>
                                                    Catat Pembayaran
                                                </button>
                                            @else
                                                <a href="{{ route('payments.receipt', $data['payment']->id) }}" 
                                                   target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-print me-1"></i>
                                                    Cetak Bukti
                                                </a>
                                            @endif
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
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <h5>{{ $participantsWithPayments->where('has_paid', true)->count() }}</h5>
                                        <p class="mb-0">Sudah Bayar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                                        <h5>{{ $participantsWithPayments->where('has_paid', false)->count() }}</h5>
                                        <p class="mb-0">Belum Bayar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-percentage fa-2x mb-2"></i>
                                        <h5>{{ round(($participantsWithPayments->where('has_paid', true)->count() / $participantsWithPayments->count()) * 100, 1) }}%</h5>
                                        <p class="mb-0">Persentase</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($participantsWithPayments->where('has_paid', true)->count() * $group->monthly_installment, 0, ',', '.') }}</h5>
                                        <p class="mb-0">Total Terkumpul</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-hand-holding-usd me-2"></i>
                        Catat Pembayaran Angsuran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.groups.cash.payments.store', $group->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Peserta</label>
                            <input type="text" class="form-control" id="modalParticipantName" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control" id="modalParticipantNik" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Besar Setoran</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Tanggal Pembayaran</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                        
                        <input type="hidden" id="participant_id" name="participant_id">
                        <input type="hidden" id="monthly_period_id" name="monthly_period_id" value="{{ $currentPeriod->id }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentModal = document.getElementById('paymentModal');
            
            paymentModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const participantId = button.getAttribute('data-participant-id');
                const participantName = button.getAttribute('data-participant-name');
                const participantNik = button.getAttribute('data-participant-nik');
                const amount = button.getAttribute('data-amount');
                
                document.getElementById('participant_id').value = participantId;
                document.getElementById('modalParticipantName').value = participantName;
                document.getElementById('modalParticipantNik').value = participantNik;
                document.getElementById('amount').value = amount;
            });
        });
    </script>
</body>
</html>
