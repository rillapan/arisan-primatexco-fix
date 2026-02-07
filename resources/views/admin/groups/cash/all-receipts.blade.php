<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Semua Bukti Angsuran - {{ $group->name }}</title>
    <style>
        @page {
            margin: 0;
        }
        body { 
            font-family: sans-serif;
            font-size: 12px; 
            margin: 0;
            padding: 0;
        }
        .receipt-page { 
            border: 2px solid #000; 
            padding: 30px; 
            box-sizing: border-box;
            position: relative;
        }
        .page-break {
            page-break-after: always;
        }
        .header-center { text-align: center; margin-bottom: 20px; }
        .header-center h3 { margin: 0; font-size: 18px; }
        .header-center h5 { margin: 5px 0; font-size: 14px; }
        hr { border: 0; border-top: 1px solid #000; margin: 10px 0; }
        
        .row { width: 100%; display: block; clear: both; margin-bottom: 15px; }
        .col-6 { width: 48%; float: left; }
        .mb-4 { margin-bottom: 20px; }
        .text-center { text-align: center; }
        
        .notification-box {
            border: 1px solid #000;
            padding: 15px;
            margin: 20px 0;
            background-color: #f8f9fa;
            clear: both;
        }
        .notification-box h6 { margin: 0 0 10px 0; font-size: 13px; }
        
        .mt-3 { margin-top: 15px; }
        .mt-5 { margin-top: 30px; }
        
        .signature-container {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-img {
            height: 60px;
            max-width: 150px;
            margin-bottom: 5px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto;
        }
        .small { font-size: 10px; }
        .text-muted { color: #6c757d; }
        
        /* Utility for clearing floats */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    @foreach($payments as $payment)
        <div class="receipt-page">
            <div class="header-center">
                <h3><strong>TANDA BUKTI ANGSURAN</strong></h3>
                <h5>{{ $payment->group->name }}</h5>
                <hr>
            </div>

            <div class="row clearfix">
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

            <div class="row clearfix">
                <div class="col-6">
                    <p><strong>Besar Setoran:</strong><br>
                    <span style="font-size: 16px;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></p>
                </div>
                <div class="col-6">
                    <p><strong>Setoran Ke:</strong><br>
                    <span style="font-size: 16px;">{{ $payment->installment_number }}</span></p>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-6">
                    <p><strong>Tanggal Pembayaran:</strong><br>
                    {{ $payment->payment_date->format('d F Y') }}</p>
                </div>
                <div class="col-6">
                    <p><strong>No. Bukti:</strong><br>
                    <strong>{{ $payment->receipt_number }}</strong></p>
                </div>
            </div>

            <div class="notification-box">
                <h6><strong>PEMBERITAHUAN</strong></h6>
                
                <p><strong>Peserta yang menang sebelumnya:</strong> <strong>{{ $previousWinnersCount }}</strong> orang</p>
                
                <div class="row clearfix mt-3">
                    <div class="col-6">
                        <p><strong>Tanggal terakhir undian:</strong><br>
                        {{ $lastAuctionDate ? $lastAuctionDate->format('d F Y') : 'Belum ada' }}</p>
                    </div>
                </div>
                
                <div class="row clearfix mt-3">
                    <div class="col-6">
                        <p><strong>Sisa kas lelang saat ini:</strong><br>
                        <strong>Rp {{ number_format($remainingCash, 0, ',', '.') }}</strong></p>
                    </div>
                    <div class="col-6">
                        <p><strong>Harga hadiah utama:</strong><br>
                        <strong>Rp {{ number_format($group->main_prize * ($currentPeriodWinnersCount > 0 ? $currentPeriodWinnersCount : 1), 0, ',', '.') }}</strong></p>
                    </div>
                </div>
                
                <div class="row clearfix mt-3">
                    <div class="col-6">
                        <p><strong>Lelang minimal:</strong><br>
                        <strong>Rp {{ number_format($group->min_bid ?? 0, 0, ',', '.') }}</strong></p>
                    </div>
                    <div class="col-6">
                        <p><strong>Diberikan kepada:</strong><br>
                        <strong>{{ $currentPeriodWinnersCount }}</strong> pemenang di periode ini</p>
                    </div>
                </div>
            </div>

            <div class="row clearfix mt-5">
                <div class="col-6">
                    <p><strong>Catatan:</strong><br>
                    {{ $payment->notes ?? 'Pembayaran angsuran melalui potongan gaji' }}</p>
                </div>
                
                <div class="signature-container">
                    @if($admin_signature && $admin_signature->ttd)
                        <img src="{{ public_path('storage/' . $admin_signature->ttd) }}" class="signature-img">
                    @else
                        <div style="height: 60px;"></div>
                    @endif
                    <div class="signature-line"></div>
                    <p style="margin: 5px 0 0 0;"><strong>{{ $admin_signature ? ($admin_signature->position ? $admin_signature->position->name : $admin_signature->jabatan) : 'Admin' }}</strong></p>
                    <p class="text-muted small" style="margin: 0;">{{ $admin_signature ? $admin_signature->nama_lengkap : 'Arbi Muhtarom' }}</p>
                </div>
            </div>
        </div>
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
