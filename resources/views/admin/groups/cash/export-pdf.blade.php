<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kas Arisan</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
            orientation: portrait;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }
        
        .table-main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        .table-main th,
        .table-main td {
            border: 1px solid #666;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }
        
        .table-main th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #000;
        }
        
        .table-main td:nth-child(2) {
            text-align: left;
        }
        
        .amount {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
        }
        
        /* Calculation Box Styles */
        .calc-container {
            width: 100%;
            margin-top: 10px;
            page-break-inside: avoid; /* Mencoba mencegah container terpotong */
        }
        
        .calc-box {
            border: 1px solid #999;
            margin-bottom: 10px;
            padding: 0;
            page-break-inside: avoid; /* Mencegah box individual terpotong */
            break-inside: avoid;
            background-color: white; /* Memperjelas batas */
        }
        
        .calc-header {
            background-color: #e0e0e0;
            padding: 5px 8px;
            font-weight: bold;
            border-bottom: 1px solid #999;
            font-size: 11px;
        }
        
        .calc-row {
            padding: 3px 8px;
            border-bottom: 1px solid #eee;
        }
        .calc-row:last-child {
            border-bottom: none;
        }
        
        .calc-label {
            display: inline-block;
            width: 60%;
            color: #555;
        }
        .calc-value {
            display: inline-block;
            width: 38%;
            text-align: right;
            font-weight: bold;
        }
        
        .text-success { color: #27ae60; }
        .text-danger { color: #c0392b; }
        .text-primary { color: #2980b9; }
        .text-muted { color: #7f8c8d; font-style: italic; font-size: 9px; }

        .winner-section {
            margin-top: 15px;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
            page-break-inside: avoid;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        .col-left {
            width: 48%;
            float: left;
        }
        .col-right {
            width: 48%;
            float: right;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PRIMKOPKAR "PRIMA" PT.PRIMATEXCO INDONESIA</h1>
        <h2>Laporan Arisan: {{ strtoupper($monthName) }}</h2>
        @if(isset($generatingPeriod) && $generatingPeriod)
             <div style="font-size: 12px; margin-top: 5px;">Periode Pembukuan: {{ $generatingPeriod->period_start->format('d M Y') }}</div>
        @endif
    </div>

    <!-- Main Table -->
    <table class="table-main">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Peserta</th>
                <th width="10%">Shift</th>
                <th width="15%">NIK</th>
                <th width="10%">Tanggal</th>
                <th width="10%">Angsuran Ke</th>
                <th width="15%">Ket. Menang</th>
                <th width="10%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cashData as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $data['participant']->name }}</td>
                <td>{{ $data['participant']->shift }}</td>
                <td>{{ $data['participant']->nik }}</td>
                <td>{{ $data['date'] }}</td>
                <td>{{ $data['installment_count'] }}</td>
                <td class="text-left" style="font-size: 9px;">
                    {{ $data['keterangan'] === '-' ? '-' : $data['keterangan'] }}
                </td>
                <td class="amount">Rp {{ number_format($data['amount'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding: 15px;">
                    Tidak ada data transaksi untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if(count($cashData) > 0)
        <tfoot>
            <tr>
                <td colspan="7" style="text-align: right; font-weight: bold; background-color: #f9f9f9;">Total Iuran Masuk (Aktual)</td>
                <td class="amount" style="font-weight: bold; background-color: #f9f9f9;">Rp {{ number_format($totalInstallments, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="calc-container clearfix">
        <!-- Left Calculation Column -->
        <div class="col-left">
            <div class="calc-box">
                <div class="calc-header">1. INFLOW (Pemasukan)</div>
                <div class="calc-row">
                    <span class="calc-label">Target Setoran (Potensi)</span>
                    <span class="calc-value">Rp {{ number_format($potentialInstallment, 0, ',', '.') }}</span>
                    <br><span class="text-muted">({{ $participantCount }} Peserta x Rp {{ number_format($monthlyInstallment, 0, ',', '.') }})</span>
                </div>
                <div class="calc-row">
                     <span class="calc-label">Akumulasi Kas Lalu</span>
                     <span class="calc-value text-primary">Rp {{ number_format($previousCashBalance, 0, ',', '.') }}</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Potongan SHU ({{ $winnerCount }})</span>
                    <span class="calc-value text-danger">(Rp {{ number_format($totalShuDeduction, 0, ',', '.') }})</span>
                </div>
                <div class="calc-row" style="background-color: #fafffa;">
                    <span class="calc-label" style="font-weight: bold;">Dana Iuran Bersih</span>
                    <span class="calc-value text-success">Rp {{ number_format($netFunds, 0, ',', '.') }}</span>
                </div>
                 <div class="calc-row">
                    <span class="calc-label">Total Bid Masuk</span>
                    <span class="calc-value text-success">Rp {{ number_format($totalBids, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="calc-box">
                <div class="calc-header">2. OUTFLOW (Pengeluaran)</div>
                <div class="calc-row">
                    <span class="calc-label">Total Harga Motor</span>
                    <span class="calc-value text-danger">Rp {{ number_format($totalPrizesGiven, 0, ',', '.') }}</span>
                    <br><span class="text-muted">({{ $winnerCount }} Pemenang x Rp {{ number_format($group->main_prize, 0, ',', '.') }})</span>
                </div>
            </div>
        </div>

        <!-- Right Calculation Column -->
        <div class="col-right">
             <div class="calc-box">
                <div class="calc-header">3. HASIL PERHITUNGAN</div>
                <div class="calc-row">
                    <span class="calc-label">Dana Saat Ini</span>
                    <span class="calc-value text-primary">Rp {{ number_format($currentFund, 0, ',', '.') }}</span>
                    <br><span class="text-muted">[Dana Iuran Bersih] + [Total Bid]</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Sisa Bersih Periode Ini</span>
                    <span class="calc-value text-success">Rp {{ number_format($surplus, 0, ',', '.') }}</span>
                    <br><span class="text-muted">[Dana Saat Ini] - [Total Harga Motor]</span>
                </div>
                <div class="calc-row" style="background-color: #f0f8ff; padding: 8px;">
                     <span class="calc-label" style="font-weight: bold; font-size: 12px;">TOTAL KAS BERJALAN</span>
                     <span class="calc-value" style="font-size: 12px; color: #000;">Rp {{ number_format($remainingCash, 0, ',', '.') }}</span>
                     <br><span class="text-muted">[Saldo Lalu] + [Sisa Bersih Periode Ini]</span>
                </div>
            </div>

            <!-- Winner Info Box -->
             @if(isset($winnersInMonth) && count($winnersInMonth) > 0)
            <div class="winner-section">
                <div style="font-weight: bold; margin-bottom: 5px; border-bottom: 1px solid #ddd;">Info Pemenang Periode Ini</div>
                <table style="width: 100%; font-size: 10px;">
                    @foreach($winnersInMonth as $winner)
                    <tr>
                        <td width="20">{{ $loop->iteration }}.</td>
                        <td>{{ $winner['participant_name'] }} ({{ $winner['lottery_number'] }})</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endif
        </div>
    </div>
    
    <div style="margin-top: 20px; font-size: 10px; color: #777; border-top: 1px solid #ccc; pt-2">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
