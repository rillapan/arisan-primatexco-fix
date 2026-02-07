<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KTA - {{ $participant->lottery_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
        .kta-card {
            width: 8.5cm;
            height: 5.4cm;
            margin: 1cm auto;
            position: relative;
            background-color: #ffffff;
            color: #333333;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #dddddd;
        }
        .kta-header {
            background-color: #0d6efd; /* Theme Blue */
            padding: 10px;
            color: white;
            text-align: left;
        }
        .kta-logo {
            height: 25px;
            vertical-align: middle;
            margin-right: 8px;
            background: white;
            padding: 2px;
            border-radius: 3px;
        }
        .kta-title {
            font-size: 10pt;
            font-weight: bold;
            display: inline-block;
            vertical-align: middle;
            text-transform: uppercase;
        }
        .kta-body {
            padding: 15px;
            height: 100%;
        }
        .kta-info {
            width: 65%;
            float: left;
        }
        .info-label {
            font-size: 7pt;
            color: #666666;
            margin-bottom: 0px;
            font-weight: bold;
        }
        .info-value {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #0d6efd;
            display: inline-block;
            width: 90%;
            padding-bottom: 1px;
        }
        .kta-right {
            width: 30%;
            float: right;
            text-align: right;
        }
        .kta-qr {
            background: white;
            padding: 3px;
            border: 1px solid #cccccc;
            margin-bottom: 10px;
            display: inline-block;
        }
        .qr-img {
            width: 60px;
            height: 60px;
        }
        .kta-footer {
            margin-top: 5px;
        }
        .kta-signature-img {
            max-height: 25px;
            margin-bottom: 2px;
        }
        .kta-signature-name {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kta-back {
            background-image: url('{{ public_path('storage/img/logo.webp') }}');
            background-repeat: no-repeat;
            background-size: contain;
            background-position: center;
        }
        .kta-back-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255, 255, 255, 0.9);
        }
        .kta-back-content {
            position: relative;
            padding: 15px;
        }
        .kta-back-title {
            font-size: 9pt;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 3px;
            text-transform: uppercase;
            border-bottom: 1px solid #0d6efd;
            display: inline-block;
        }
        .kta-back-text {
            font-size: 7pt;
            line-height: 1.3;
            margin-bottom: 10px;
            color: #333333;
        }
    </style>
</head>
<body>
    <!-- FRONT SIDE -->
    <div class="kta-card">
        <div class="kta-header">
            @if($setting->logo)
                <img src="{{ public_path('uploads/kta/' . $setting->logo) }}" class="kta-logo">
            @endif
            <div class="kta-title">{{ $setting->header_title ?? 'KARTU TANDA ANGGOTA' }}</div>
        </div>
        <div class="kta-body">
            <div class="kta-info">
                <div class="info-label">NAMA ANGGOTA</div>
                <div class="info-value">{{ $participant->name }}</div>
                
                <div class="info-label">NOMOR UNDIAN</div>
                <div class="info-value">{{ $participant->lottery_number }}</div>
                
                <div class="info-label">KELOMPOK</div>
                <div class="info-value">{{ $participant->group->name }}</div>
            </div>
            <div class="kta-right">
                <div class="kta-qr">
                    <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={{ $participant->lottery_number }}&choe=UTF-8" class="qr-img">
                </div>
                <div class="kta-footer">
                    @if($setting->signature_image)
                        <img src="{{ public_path('uploads/kta/' . $setting->signature_image) }}" class="kta-signature-img">
                    @endif
                    <div class="kta-signature-name">{{ $setting->signature_name ?? 'TTD PENGURUS' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- BACK SIDE -->
    <div class="kta-card kta-back">
        <div class="kta-back-overlay"></div>
        <div class="kta-back-content">
            <div class="kta-back-title">VISI</div>
            <div class="kta-back-text">{!! nl2br(e($setting->vision ?? 'Visi Arisan belum diatur.')) !!}</div>
            
            <div class="kta-back-title">MISI</div>
            <div class="kta-back-text">{!! nl2br(e($setting->mission ?? 'Misi Arisan belum diatur.')) !!}</div>

            <div class="kta-back-title">MOTO</div>
            <div class="kta-back-text">{!! nl2br(e($setting->moto ?? 'Moto Arisan belum diatur.')) !!}</div>
        </div>
    </div>
</body>
</html>
