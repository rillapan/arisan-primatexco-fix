<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #2c3e50;
            text-transform: uppercase;
        }
        .header h4 {
            margin: 5px 0 0;
            font-weight: normal;
        }
        .meta-info {
            text-align: right;
            font-size: 9pt;
            margin-bottom: 15px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            color: #2c3e50;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .group-name {
            font-weight: bold;
            color: #2980b9;
        }
        .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pemenang Arisan</h2>
        <h4>Bulan: {{ $monthName }} Tahun: {{ $selectedYear }}</h4>
    </div>

    <div class="meta-info">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20%">Kelompok</th>
                <th style="width: 10%">No. Undian</th>
                <th style="width: 25%">Nama Peserta</th>
                <th style="width: 15%">Bag/Shift</th>
                <th style="width: 15%">NIK</th>
                <th style="width: 15%">Nilai Lelang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($auctionResults as $result)
            <tr>
                <td class="group-name">{{ $result['group_name'] }}</td>
                <td style="text-align: center;">{{ $result['lottery_number'] }}</td>
                <td>{{ $result['participant_name'] }}</td>
                <td>
                    {{ $result['department'] ?? '-' }} / {{ $result['shift'] }}
                </td>
                <td>{{ $result['nik'] }}</td>
                <td class="amount">Rp {{ number_format($result['bid_amount'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    <em>Tidak ada data pemenang untuk periode ini.</em>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} Sistem Arisan Primkopkar Prima. All rights reserved.
    </div>
</body>
</html>
