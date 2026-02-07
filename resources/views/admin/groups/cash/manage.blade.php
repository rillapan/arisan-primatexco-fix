<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kas - {{ $group->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Hide breadcrumb navigation */
        .breadcrumb, nav[aria-label="breadcrumb"] {
            display: none !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-gift me-2"></i>
                Sistem Arisan Admin
            </a>
        <div class="navbar-nav ms-auto">

                <a class="nav-link" href="{{ route('admin.groups.manage', $group->id) }}">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali ke Kelola Kelompok
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

    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.groups.manage', $group->id) }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-money-bill-wave me-2 text-success"></i>Kelola Kas - {{ $group->name }}
        </h1>
        <p class="text-muted mb-0">Kelola kas arisan</p>
    </div>
</div>

<!-- Remove any breadcrumb navigation -->
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <button class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#bulkInstallmentModal">
                                <i class="fas fa-plus me-1"></i>
                                Tambah Angsuran Massal
                            </button>
                        </div>
                    </div>

                        <!-- Cash Flow Summary -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-calculator me-2"></i>
                                            Perhitungan Kas Bulanan
                                        </h6>
                                        @php
                                            $currentPeriod = $group->monthlyPeriods->where('status', '!=', 'completed')->first();
                                            if($currentPeriod) {
                                                $breakdown = $currentPeriod->getCalculationBreakdown();
                                                
                                                // Calculate for 2 winners case
                                                $winnerCount = $currentPeriod->winners->count();
                                                $participantCount = $group->participants->where('is_active', true)->count();
                                                $monthlyInstallment = $group->monthly_installment;
                                                $mainPrize = $group->main_prize;
                                                $shuAmount = $group->shu ?? 500000;
                                                
                                                // Get bids only from winners for this period
                                                $winnerBids = [];
                                                if ($winnerCount > 0) {
                                                    foreach ($currentPeriod->winners as $winner) {
                                                        if ($winner->bid_amount > 0) {
                                                            $winnerBids[] = $winner->bid_amount;
                                                        }
                                                    }
                                                }
                                                $totalBids = array_sum($winnerBids);
                                                
                                                // 1. Rumus Inflow (Pemasukan Bersih Bulan Ini)
                                                // Gunakan realisasi pembayaran, bukan potensi
                                                $actualInstallments = \App\Models\Payment::where('monthly_period_id', $currentPeriod->id)->sum('amount');
                                                $netFunds = $actualInstallments - ($winnerCount * $shuAmount);
                                                
                                                // 2. Rumus Outflow (Pengeluaran Hadiah)
                                                $totalMotorPrice = $winnerCount * $mainPrize;
                                                
                                                // 3. Rumus Logika Aliran Kas (Dana Saat Ini)
                                                $currentFund = $netFunds + $totalBids;
                                                
                                                // 4. Sisa Bersih Periode Ini
                                                $finalRemainingCash = $currentFund - $totalMotorPrice;
                                                
                                                // 5. Total Kas Berjalan
                                                $totalRunningCash = $breakdown['previous_cash_balance'] + $finalRemainingCash;
                                                
                                                // Total prizes given to all winners
                                                $totalPrizesGiven = 0;
                                                if ($winnerCount > 0) {
                                                    foreach ($currentPeriod->winners as $winner) {
                                                        $totalPrizesGiven += $mainPrize - ($winner->bid_amount ?? 0);
                                                    }
                                                }
                                        @endphp
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted">Sisa Kas Bulan Lalu:</small>
                                                <h5 class="text-primary">Rp {{ number_format($breakdown['previous_cash_balance'], 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Dana Iuran Bersih:</small>
                                                <h5 class="text-success">Rp {{ number_format($netFunds, 0, ',', '.') }}</h5>
                                                <small class="text-muted">({{ $participantCount }} × Rp{{ number_format($monthlyInstallment, 0, ',', '.') }}) - ({{ $winnerCount }} × Rp{{ number_format($shuAmount, 0, ',', '.') }})</small>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Total Bid:</small>
                                                <h5 class="text-info">Rp {{ number_format($totalBids, 0, ',', '.') }}</h5>
                                                <small class="text-muted">Dari {{ $winnerCount }} pemenang</small>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Total SHU:</small>
                                                <h5 class="text-warning">Rp {{ number_format($winnerCount * $shuAmount, 0, ',', '.') }}</h5>
                                                <small class="text-muted">{{ $winnerCount }} pemenang × Rp{{ number_format($shuAmount, 0, ',', '.') }}</small>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <small class="text-muted">Dana Saat Ini:</small>
                                                <h5 class="text-success fw-bold">Rp {{ number_format($currentFund, 0, ',', '.') }}</h5>
                                                <small class="text-success">Dana Iuran Bersih + Total Bid</small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Total Harga Motor:</small>
                                                <h5 class="text-danger fw-bold">Rp {{ number_format($totalMotorPrice, 0, ',', '.') }}</h5>
                                                <small class="text-muted">{{ $winnerCount }} pemenang × Rp{{ number_format($mainPrize, 0, ',', '.') }}</small>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <small class="text-muted">Sisa Bersih Periode Ini:</small>
                                                <h5 class="text-info fw-bold">Rp {{ number_format($finalRemainingCash, 0, ',', '.') }}</h5>
                                                <small class="text-info">Dana Saat Ini - Total Harga Motor</small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Total Kas Berjalan:</small>
                                                <h5 class="text-primary fw-bold">Rp {{ number_format($totalRunningCash, 0, ',', '.') }}</h5>
                                                <small class="text-muted">Saldo Lalu + Sisa Bersih</small>
                                            </div>
                                        </div>
                                        @php
                                            }
                                        @endphp
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card" style="background-color: #f0f8ff; border: 1px solid #e0e6ff;">
                                    <div class="card-body text-center text-dark">
                                        <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($netFunds ?? 0, 0, ',', '.') }}</h5>
                                        <p class="mb-0">Dana Iuran Bersih</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card" style="background-color: #faf0e6; border: 1px solid #f5deb3;">
                                    <div class="card-body text-center text-dark">
                                        <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($totalPrizesGiven ?? 0, 0, ',', '.') }}</h5>
                                        <p class="mb-0">Total Hadiah Diberikan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card" style="background-color: {{ ($finalRemainingCash ?? 0) >= 0 ? '#f0fff0' : '#fff8dc' }}; border: 1px solid {{ ($finalRemainingCash ?? 0) >= 0 ? '#d4edda' : '#ffeaa7' }};">
                                    <div class="card-body text-center text-dark">
                                        <i class="fas fa-wallet fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($finalRemainingCash ?? 0, 0, ',', '.') }}</h5>
                                        <p class="mb-0">Sisa Kas Tersedia</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table table-striped table-hover mt-4">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Peserta</th>
                                        <th>Bag/Shift</th>
                                        <th>NIK</th>
                                        <th>Tanggal</th>
                                        <th>Angsuran Ke</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card" style="background-color: #e6e6fa; border: 1px solid #d8bfd8;">
                                    <div class="card-body text-center text-dark">
                                        <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}</h5>
                                        <p class="mb-0">Iuran Bulanan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card" style="background-color: #ffe4e1; border: 1px solid #ffb6c1;">
                                    <div class="card-body text-center text-dark">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <h5>{{ $group->participants->count() }}</h5>
                                        <p class="mb-0">Total Peserta</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card" style="background-color: #f5f5dc; border: 1px solid #eee8aa;">
                                    <div class="card-body text-center text-dark">
                                        <i class="fas fa-calculator fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($group->monthly_installment * $group->participants->count(), 0, ',', '.') }}</h5>
                                        <p class="mb-0">Total Periode</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card" style="background-color: #f0f8ff; border: 1px solid #e0e6ff;">
                                    <div class="card-body text-center text-dark">
                                        <i class="fas fa-gift fa-2x mb-2"></i>
                                        <h5>Rp {{ number_format($group->main_prize, 0, ',', '.') }}</h5>
                                        <p class="mb-0">Hadiah Utama</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                                    @forelse($cashData as $index => $data)
                                    <tr class="mt-2">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $data['participant']->name }}</strong>
                                            <br><small class="text-muted">No: {{ $data['participant']->lottery_number }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $data['participant']->shift }}</span>
                                        </td>
                                        <td>{{ $data['participant']->nik }}</td>
                                        <td>{{ $data['date'] }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $data['installment_count'] }}</span>
                                        </td>
                                        <td>
                                            @if($data['keterangan'] === '-')
                                                <span class="text-muted">-</span>
                                            @else
                                                <span class="badge bg-success">{{ $data['keterangan'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($data['payment']))
                                                <span class="text-success fw-bold">
                                                    Rp {{ number_format($data['amount'], 0, ',', '.') }}
                                                </span>
                                                <br>
                                                <a href="{{ route('admin.payments.receipt', $data['payment']->id) }}" 
                                                   target="_blank" class="btn btn-xs btn-outline-primary mt-1">
                                                    <i class="fas fa-file-invoice me-1"></i>
                                                    {{ $data['payment']->receipt_number }}
                                                </a>
                                            @else
                                                <span class="text-success fw-bold">
                                                    Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i><br>
                                            Belum ada data kas untuk kelompok ini
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>



                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-chart-line me-2"></i>
                                            Ringkasan Kas
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted">Total Transaksi:</small>
                                                <h5 class="text-primary">{{ count($cashData) }}</h5>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Total Pemenang:</small>
                                                <h5 class="text-success">{{ collect($cashData)->where('notes', 'like', 'Pemenang%')->count() }}</h5>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Total Angsuran:</small>
                                                <h5 class="text-info">{{ collect($cashData)->where('notes', 'like', 'Angsuran%')->count() }}</h5>
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

    <!-- Bulk Installment Confirmation Modal -->
    <div class="modal fade" id="bulkInstallmentModal" tabindex="-1" aria-labelledby="bulkInstallmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkInstallmentModalLabel">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Angsuran Bulanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.groups.cash.bulk.installment', $group->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Informasi Penting</h6>
                            <ul class="mb-0">
                                <li>Admin menambahkan angsuran ke angsuran berikutnya (bertambah satu).</li>
                                <li>Angsuran akan otomatis diberikan kepada seluruh peserta melalui potongan gaji.</li>
                                <li>Informasi angsuran akan otomatis dikirimkan ke akun masing-masing peserta.</li>
                                <li>Setiap peserta akan menerima bukti angsuran yang dapat diakses melalui akun mereka.</li>
                            </ul>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Jumlah Peserta Aktif:</label>
                                <input type="text" class="form-control" value="{{ $group->participants->where('is_active', true)->count() }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Besar Angsuran per Peserta:</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" value="{{ number_format($group->monthly_installment, 0, ',', '.') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Angsuran Berikutnya:</label>
                                <input type="text" class="form-control fw-bold text-warning" 
                                       value="Setiap peserta mendapat angsuran berikutnya (bertambah satu)" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Total Angsuran yang Akan Diproses:</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold text-success" 
                                           value="{{ number_format($group->participants->where('is_active', true)->count() * $group->monthly_installment, 0, ',', '.') }}" 
                                           readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Tanggal Pembayaran</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="Contoh: Angsuran bulanan {{ date('F Y') }}"></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Sistem akan otomatis melewati peserta yang sudah melakukan pembayaran untuk periode ini.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-plus me-2"></i>
                            Proses Angsuran untuk Semua Peserta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>