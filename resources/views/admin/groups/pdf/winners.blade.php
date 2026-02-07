<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pemenang - {{ $group->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2, .header h3 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            background: #eee;
            font-size: 0.9em;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 0.9em;
        }
        .no-border-bottom { border-bottom: none; }
        .no-border-top { border-top: none; }
        .bg-light { background-color: #fafafa; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daftar Urut Arisan</h2>
        <h3>{{ $group->name }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 60px;">No Undian</th>
                <th>Bulan</th>
                <th>Pemenang</th>
                <th style="width: 80px;">Tanggal</th>
                <th>Identitas / Bagian</th>
                <th style="width: 100px;">Sisa Kas</th>
                <th style="width: 100px;">Akumulasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($group->monthlyPeriods as $index => $period)
                @php
                    $winnersCount = $period->winners->count();
                    $hasWinner = $winnersCount > 0;
                @endphp
                
                @if($hasWinner)
                    @foreach($period->winners as $wIndex => $winner)
                    <tr>
                        @if($wIndex === 0)
                        <td class="text-center @if($winnersCount > 1) no-border-bottom @endif" rowspan="{{ $winnersCount }}">{{ $index + 1 }}</td>
                        @endif
                        
                        <td class="text-center">
                            <span class="badge">
                                {{ $winner->participant->lottery_number ?? '-' }}
                            </span>
                        </td>

                        @if($wIndex === 0)
                        <td class="@if($winnersCount > 1) no-border-bottom @endif" rowspan="{{ $winnersCount }}">
                            <strong>{{ $period->period_start->locale('id')->monthName }}</strong>
                            <small>{{ $period->period_start->year }}</small>
                        </td>
                        @endif
                        
                        <td>
                            <strong>{{ $winner->participant->name ?? 'N/A' }}</strong>
                        </td>
                        
                        <td class="text-center">
                            @if($winner->draw_time)
                                {{ \Carbon\Carbon::parse($winner->draw_time)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        
                        <td>
                            {{ $winner->participant->nik ?? '-' }} / {{ $winner->participant->shift ?? '-' }}
                        </td>
                        
                        @if($wIndex === 0)
                        <td class="text-right @if($winnersCount > 1) no-border-bottom @endif" rowspan="{{ $winnersCount }}">
                            Rp {{ number_format($period->calculated_surplus ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-right @if($winnersCount > 1) no-border-bottom @endif" rowspan="{{ $winnersCount }}">
                            <strong>Rp {{ number_format($period->calculated_accumulation ?? 0, 0, ',', '.') }}</strong>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">-</td>
                        <td>
                            <strong>{{ $period->period_start->locale('id')->monthName }}</strong>
                            <small>{{ $period->period_start->year }}</small>
                        </td>
                        <td colspan="3" class="text-center" style="font-style: italic; color: #777;">
                            Belum ada pemenang
                        </td>
                        <td class="text-right">
                            Rp {{ number_format($period->calculated_surplus ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-right">
                            <strong>Rp {{ number_format($period->calculated_accumulation ?? 0, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" class="text-center">Belum ada periode.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
