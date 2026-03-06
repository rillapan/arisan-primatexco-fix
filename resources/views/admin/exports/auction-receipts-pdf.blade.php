<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak Semua Kuitansi</title>
    <style>
        @page {
            margin: 15px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
        .receipt-container {
            width: 100%;
            padding: 0;
            box-sizing: border-box;
            position: relative;
        }
        .border-box {
            border: 2px solid #000;
            padding: 10px 15px;
            box-sizing: border-box;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0;
            text-transform: uppercase;
        }
        .header h3 {
            font-size: 10pt;
            margin: 3px 0 0;
            font-weight: normal;
        }
        .receipt-no {
            position: absolute;
            top: 10px;
            right: 15px;
            font-weight: bold;
            font-size: 9pt;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .content-table td {
            vertical-align: top;
            padding: 3px 0;
        }
        .label-col {
            width: 25%;
            font-weight: bold;
        }
        .colon-col {
            width: 5%;
            text-align: center;
            font-weight: bold;
        }
        .value-col {
            width: 70%;
            border-bottom: 1px dotted #888;
        }
        .rincian-table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
        }
        .rincian-table td {
            padding: 2px 0;
            border: none;
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
        .total-box {
            font-size: 11pt;
            font-weight: bold;
            padding: 5px 10px;
            background-color: #eee;
            border: 1px solid #000;
            text-align: right;
            margin-top: 5px;
            width: 40%;
            float: left;
        }
        .signature-box {
            float: right;
            width: 30%;
            text-align: center;
            margin-top: 5px;
        }
        .signature-date {
            margin-bottom: 30px;
            font-size: 9pt;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            font-weight: bold;
            display: inline-block;
            width: 100%;
            margin-bottom: 5px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
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
    ?>

    @foreach($auctionResults as $index => $winner)
        <div class="receipt-container">
            <div class="border-box">
                <div class="receipt-no">
                    No. Kuitansi: {{ $winner->monthlyPeriod->group->id }}-{{ $winner->participant->lottery_number }}
                </div>
                
                <div class="header">
                    <h1>KUITANSI</h1>
                    <h3>Koperasi Karyawan "Prima" PT. Primatexco Indonesia</h3>
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
                            <em># {{ trim(terbilang($winner->main_prize)) }} Rupiah #</em>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Untuk Pembayaran</td>
                        <td class="colon-col">:</td>
                        <td class="value-col" style="line-height: 1.5;">
                            Perolehan arisan <strong>{{ $winner->monthlyPeriod->group->name }}</strong><br>
                            Atas nama <strong>{{ $winner->participant->name }}</strong><br>
                            NIK: {{ $winner->participant->nik }} | Bagian/Shift: {{ $winner->participant->department ?? '-' }} / {{ $winner->participant->shift ?? '-' }}
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
                                    <td colspan="2"><hr style="border: 0; border-bottom: 1px dotted #000; margin: 5px 0;"></td>
                                </tr>
                                <tr>
                                    <td class="rincian-label"><strong>Sisa Diterima / Nilai Akhir</strong></td>
                                    <td class="rincian-value" style="font-size: 11pt;"><strong>Rp {{ number_format($winner->final_prize ?? ($winner->main_prize - $winner->bid_amount), 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <div class="clearfix" style="margin-top: 20px;">
                    <div class="total-box">
                        Total: Rp {{ number_format($winner->final_prize ?? ($winner->main_prize - $winner->bid_amount), 0, ',', '.') }}
                    </div>
                    
                    <div class="signature-box">
                        <div class="signature-date">
                            Batang, ..... {{ $monthName }} {{ $selectedYear }}<br>
                            Penerima,
                        </div>
                        <br>
                        <span class="signature-line">{{ $winner->participant->name }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
