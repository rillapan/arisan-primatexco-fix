<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak Semua Kuitansi</title>
    <style>
        @page {
            margin: 5mm 10mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .page-container {
            width: 100%;
            page-break-after: always;
        }
        .page-container:last-child {
            page-break-after: auto;
        }
        .receipt-block {
            width: 100%;
            margin-bottom: 0mm;
        }
        .outer-table {
            width: 100%;
            height: 91mm;
            border: 2px solid #000;
            border-collapse: collapse;
        }
        .outer-table > tr > td,
        .outer-table > tbody > tr > td {
            padding: 4px 10px;
            vertical-align: top;
        }
        .header {
            background-color: #1a3a6b;
            color: #ffffff;
            text-align: center;
            padding: 2px 8px;
            margin-bottom: 4px;
            border-bottom: 3px solid #f5a623;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .header-logo {
            width: 45px;
        }
        .header-logo img {
            width: 32px;
            height: 32px;
            background: #ffffff;
            border-radius: 5px;
            padding: 2px;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0;
            text-transform: uppercase;
            color: #ffffff;
            letter-spacing: 2px;
        }
        .header h3 {
            font-size: 8pt;
            margin: 2px 0 0;
            font-weight: normal;
            color: #e0d9c8;
            letter-spacing: 0.5px;
        }
        .receipt-no {
            font-weight: bold;
            font-size: 8pt;
            color: #ffffff;
            text-align: right;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .content-table td {
            vertical-align: top;
            padding: 1px 0;
            font-size: 9pt;
        }
        .label-col {
            width: 22%;
            font-weight: bold;
        }
        .colon-col {
            width: 3%;
            text-align: center;
            font-weight: bold;
        }
        .value-col {
            width: 75%;
            border-bottom: 1px dotted #888;
        }
        .rincian-table {
            width: 100%;
            margin-top: 3px;
            border-collapse: collapse;
        }
        .rincian-table td {
            padding: 2px 0;
            border: none;
            font-size: 9pt;
        }
        .rincian-label {
            width: 60%;
        }
        .rincian-value {
            width: 40%;
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .footer-section {
            margin-top: 2px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            vertical-align: top;
        }
        .total-box {
            font-size: 10pt;
            font-weight: bold;
            padding: 4px 10px;
            background-color: #eee;
            border: 1px solid #000;
            text-align: right;
            display: inline-block;
        }
        .signature-box {
            text-align: center;
        }
        .signature-date {
            margin-bottom: 55px;
            font-size: 8pt;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            font-weight: bold;
            display: inline-block;
            width: 80%;
            padding-bottom: 3px;
            font-size: 8pt;
        }
        .cut-line {
            border-bottom: 1px dashed #999;
            margin: 1mm 0;
            text-align: center;
            font-size: 7pt;
            color: #999;
        }
    </style>
</head>
<body>
    <?php
        function terbilang($angka) {
            $angka = abs($angka);
            $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
            $terbilang = "";
            if ($angka < 12) {
                $terbilang = " " . $baca[$angka];
            } else if ($angka < 20) {
                $terbilang = terbilang($angka - 10) . " Belas";
            } else if ($angka < 100) {
                $terbilang = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
            } else if ($angka < 200) {
                $terbilang = " Seratus" . terbilang($angka - 100);
            } else if ($angka < 1000) {
                $terbilang = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
            } else if ($angka < 2000) {
                $terbilang = " Seribu" . terbilang($angka - 1000);
            } else if ($angka < 1000000) {
                $terbilang = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
            } else if ($angka < 1000000000) {
                $terbilang = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
            } else if ($angka < 1000000000000) {
                $terbilang = terbilang($angka / 1000000000) . " Milyar" . terbilang(fmod($angka, 1000000000));
            } else if ($angka < 1000000000000000) {
                $terbilang = terbilang($angka / 1000000000000) . " Trilyun" . terbilang(fmod($angka, 1000000000000));
            }
            return $terbilang;
        }

        // Group results into chunks of 3 for A4 pages
        $chunks = $auctionResults->chunk(3);
    ?>

    @foreach($chunks as $chunkIndex => $chunk)
        <div class="page-container">
            @foreach($chunk as $receiptIndex => $winner)
                <div class="receipt-block">
                    <table class="outer-table">
                        <tr>
                            <td>
                                <div class="header">
                                    <table class="header-table">
                                        <tr>
                                            <td class="header-logo">
                                                <img src="{{ public_path('img/logo.png') }}" alt="Logo">
                                            </td>
                                            <td>
                                                <h1>KUITANSI</h1>
                                                <h3>Primkopkar "Prima" PT. Primatexco Indonesia</h3>
                                            </td>
                                            <td>
                                                <h3 style="font-size: 10pt; margin-top: 1px;">www.arisanprimkopkar.com</h3>
                                            </td>
                                            <td style="width: 120px; text-align: right; vertical-align: middle;">
                                                <div class="receipt-no">
                                                    No. Kuitansi: {{ $winner->monthlyPeriod->group->id }}-{{ $winner->receipt_sequence }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <table class="content-table">
                                    <tr>
                                        <td class="label-col">Telah Terima Dari</td>
                                        <td class="colon-col">:</td>
                                        <td class="value-col"><strong>PRIMKOPKAR "PRIMA" PT. PRIMATEXCO INDONESIA BATANG</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Uang Sejumlah</td>
                                        <td class="colon-col">:</td>
                                        <td class="value-col">
                                            <em># {{ trim(terbilang($winner->final_prize ?? ($winner->main_prize - $winner->bid_amount))) }} Rupiah #</em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Untuk Pembayaran</td>
                                        <td class="colon-col">:</td>
                                        <td class="value-col" style="line-height: 1.5;">
                                            Perolehan arisan <strong>{{ $winner->monthlyPeriod->group->name }}</strong><br>
                                            Atas nama <strong>{{ $winner->participant->name }}</strong><br>
                                            NIK: {{ $winner->participant->nik }} | Bagian/Shift: {{ $winner->participant->shift ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Rincian Pembayaran</td>
                                        <td class="colon-col">:</td>
                                        <td class="value-col" style="border-bottom: none;">
                                            <table class="rincian-table">
                                                <tr>
                                                    <td class="rincian-label">&bull; Harga Sepeda Motor</td>
                                                    <td class="rincian-value">Rp {{ number_format($winner->main_prize, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="rincian-label">&bull; Potongan Uang Lelang</td>
                                                    <td class="rincian-value">Rp {{ number_format($winner->bid_amount, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"><hr style="border: 0; border-bottom: 1px dotted #000; margin: 3px 0;"></td>
                                                </tr>
                                                <tr>
                                                    <td class="rincian-label"><strong>Sisa Diterima / Nilai Akhir</strong></td>
                                                    <td class="rincian-value" style="font-size: 10pt;"><strong>Rp {{ number_format($winner->final_prize ?? ($winner->main_prize - $winner->bid_amount), 0, ',', '.') }}</strong></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                <div class="footer-section">
                                    <table class="footer-table">
                                        <tr>
                                            <td style="width: 45%; vertical-align: middle;">
                                                <div class="total-box">
                                                    Total: Rp {{ number_format($winner->final_prize ?? ($winner->main_prize - $winner->bid_amount), 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td style="width: 20%;"></td>
                                            <td style="width: 35%;">
                                                <div class="signature-box">
                                                    <div class="signature-date">
                                                        Batang, ..... {{ $monthName }} {{ $selectedYear }}<br>
                                                        Penerima,
                                                    </div>
                                                    <span class="signature-line">{{ $winner->participant->name }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                @if(!$loop->last)
                    <div class="cut-line">✂ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</div>
                @endif
            @endforeach
        </div>
    @endforeach
</body>
</html>
