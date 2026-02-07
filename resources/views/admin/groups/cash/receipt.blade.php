<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Angsuran - {{ $payment->receipt_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { font-size: 12px; }
            .no-print { display: none !important; }
            .receipt-container { 
                border: 2px solid #000; 
                padding: 20px; 
                max-width: 100%;
                margin: 0;
            }
        }
        .receipt-container { 
            border: 2px solid #000; 
            padding: 20px; 
            max-width: 600px; 
            margin: 20px auto;
        }
        .header-center { text-align: center; margin-bottom: 30px; }
        .signature-line { 
            border-bottom: 1px solid #000; 
            height: 30px; 
            margin-top: 60px;
            width: 200px;
        }
        .notification-box {
            border: 1px solid #000;
            padding: 15px;
            margin: 20px 0;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="container mt-4">
            <div class="text-center mb-4">
                <button onclick="window.print()" class="btn btn-primary me-2">
                    <i class="fas fa-print me-2"></i>Cetak Bukti
                </button>
                <button onclick="window.close()" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="header-center">
            <h3><strong>TANDA BUKTI ANGSURAN</strong></h3>
            <h5>{{ $payment->group->name }}</h5>
            <hr>
        </div>

        <!-- Participant Information -->
        <div class="row mb-4">
            <div class="col-6">
                <p><strong>Nama Peserta:</strong><br>
                {{ $payment->participant->name }}</p>
                
                <p><strong>Bagian/Shift:</strong><br>
                {{ $payment->participant->department }} / {{ $payment->participant->shift }}</p>
            </div>
            <div class="col-6">
                <p><strong>NIK:</strong><br>
                {{ $payment->participant->nik }}</p>
                
                <p><strong>No. Undian:</strong><br>
                {{ $payment->participant->lottery_number }}</p>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="row mb-4">
            <div class="col-6">
                <p><strong>Besar Setoran:</strong><br>
                <span style="font-size: 18px;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></p>
            </div>
            <div class="col-6">
                <p><strong>Setoran Ke:</strong><br>
                <span style="font-size: 18px;">{{ $payment->installment_number }}</span></p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <p><strong>Tanggal Pembayaran:</strong><br>
                {{ $payment->payment_date->format('d F Y') }}</p>
            </div>
            <div class="col-6">
                <p><strong>No. Bukti:</strong><br>
                <strong>{{ $payment->receipt_number }}</strong></p>
            </div>
        </div>

        <!-- Notification Section -->
        <div class="notification-box">
            <h6><strong>PEMBERITAHUAN</strong></h6>
            
            <p><strong>Peserta yang menang sebelumnya:</strong> <strong>{{ $previousWinnersCount }}</strong> orang</p>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <p><strong>Tanggal terakhir undian:</strong><br>
                    {{ $lastAuctionDate ? $lastAuctionDate->format('d F Y') : 'Belum ada' }}</p>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <p><strong>Sisa kas lelang saat ini:</strong><br>
                    <strong>Rp {{ number_format($remainingCash, 0, ',', '.') }}</strong></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Harga hadiah utama (harga pokok SPM):</strong><br>
                    <strong>Rp {{ number_format($group->main_prize * ($currentPeriodWinnersCount > 0 ? $currentPeriodWinnersCount : 1), 0, ',', '.') }}</strong></p>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <p><strong>Lelang minimal:</strong><br>
                    <strong>Rp {{ number_format($group->min_bid ?? 0, 0, ',', '.') }}</strong></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Diberikan kepada:</strong><br>
                    <strong>{{ $currentPeriodWinnersCount }}</strong> pemenang di periode ini</p>
                </div>
            </div>
        </div>

        <!-- Signature -->
        <div class="row mt-5">
            <div class="col-12 mb-4">
                <p><strong>Catatan:</strong><br>
                {{ $payment->notes ?? 'Pembayaran angsuran melalui potongan gaji' }}</p>
            </div>
            
            <div class="col-6"></div>
            <div class="col-6 text-center">
                <div class="d-flex flex-column align-items-center">
                    @if($admin_signature && $admin_signature->ttd)
                        <img src="{{ asset('storage/' . $admin_signature->ttd) }}" alt="TTD Admin" style="height: 60px; max-width: 150px; object-fit: contain;">
                    @else
                        <div style="height: 60px;"></div>
                    @endif
                    <div style="border-bottom: 1px solid #000; width: 180px;"></div>
                    <p class="mt-2 mb-0"><strong>{{ $admin_signature ? ($admin_signature->position ? $admin_signature->position->name : $admin_signature->jabatan) : 'Admin' }}</strong></p>
                    <p class="text-muted small">{{ $admin_signature ? $admin_signature->nama_lengkap : 'Arbi Muhtarom' }}</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
