@extends('layouts.participant')

@section('title', 'Kartu Tanda Anggota - Sistem Arisan')

@push('styles')
<style>
    .kta-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 10px;
    }
    .kta-card {
        width: 100%;
        aspect-ratio: 1.58 / 1; /* Standard card ratio */
        background: #fff;
        color: #333;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        margin-bottom: 25px;
        border: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
    }
    /* Fixed height for capture stability */
    @media (min-width: 576px) {
        .kta-card {
            height: 316px; 
        }
    }
    .kta-header {
        background: #0d6efd; /* Primary Blue */
        padding: 12px 15px;
        display: flex;
        align-items: center;
        color: white;
        height: 60px;
    }
    .kta-logo {
        height: 35px;
        margin-right: 12px;
        background: white;
        padding: 2px;
        border-radius: 4px;
        object-fit: contain;
    }
    .kta-title {
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        line-height: 1.2;
    }
    .kta-body {
        flex: 1;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        position: relative;
        background: white;
    }
    .kta-info {
        flex: 1;
        z-index: 2;
    }
    .info-label {
        font-size: 9px;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 700;
        margin-bottom: 0px;
    }
    .info-value {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
        line-height: 1.2;
        color: #212529;
        border-bottom: 2px solid #0d6efd; /* Blue underline */
        display: inline-block;
        padding-bottom: 2px;
        min-width: 180px;
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kta-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: space-between;
        width: 120px;
        height: 100%;
        z-index: 2;
    }
    .kta-qr {
        background: white;
        padding: 5px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin-bottom: 5px;
    }
    .kta-footer {
        text-align: right;
        width: 100%;
    }
    .kta-signature-img {
        height: 35px;
        max-width: 100px;
        object-fit: contain;
        margin-bottom: 2px;
    }
    .kta-signature-name {
        font-size: 10px;
        font-weight: 700;
        color: #212529;
        text-transform: uppercase;
        line-height: 1.1;
        word-wrap: break-word;
        max-width: 120px;
    }

    .kta-back {
        background-color: #fff;
        background-image: url('{{ asset('storage/img/logo.webp') }}');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
    }
    .kta-back-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.92);
        z-index: 1;
    }
    .kta-back-content {
        position: relative;
        z-index: 2;
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .kta-back-section {
        margin-bottom: 12px;
    }
    .kta-back-title {
        font-size: 12px;
        font-weight: 800;
        color: #0d6efd;
        margin-bottom: 4px;
        text-transform: uppercase;
        border-bottom: 2px solid #0d6efd;
        display: inline-block;
    }
    .kta-back-text {
        font-size: 10px;
        line-height: 1.4;
        color: #333;
        font-weight: 500;
        white-space: pre-line;
    }

    @media (max-width: 480px) {
        .kta-card {
            aspect-ratio: auto;
            height: auto;
            min-height: 280px;
        }
        .info-value {
            font-size: 13px;
            min-width: 140px;
            max-width: 160px;
        }
        .kta-right {
            width: 100px;
        }
        .kta-signature-img {
            height: 30px;
        }
    }
</style>
@endpush

@section('content')
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-id-card me-2"></i>Kartu Tanda Anggota</h2>
            <p class="text-muted mb-0">Identitas resmi Keanggotaan Arisan Anda.</p>
        </div>
        <button onclick="downloadPng()" class="btn btn-primary d-none d-md-inline-block" id="btn-download-desktop">
            <i class="fas fa-download me-1"></i>Unduh KTA (PNG)
        </button>
    </div>

    <div class="kta-container">
        <!-- FRONT SIDE -->
        <h6 class="text-center text-muted mb-3">Tampak Depan</h6>
        <div class="kta-card shadow" id="kta-front">
            <div class="kta-header">
                @if($setting->logo)
                    <img src="{{ asset('uploads/kta/' . $setting->logo) }}" class="kta-logo">
                @else
                    <div class="bg-white rounded p-1 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="fas fa-id-card-alt text-primary fa-lg"></i>
                    </div>
                @endif
                <h1 class="kta-title">{{ $setting->header_title ?? 'KARTU TANDA ANGGOTA' }}</h1>
            </div>
            <div class="kta-body">
                <div class="kta-info">
                    <div class="info-label">Nama Anggota</div>
                    <div class="info-value">{{ $participant->name }}</div>
                    
                    <div class="info-label">Nomor Undian</div>
                    <div class="info-value">{{ $participant->lottery_number }}</div>
                    
                    <div class="info-label">Kelompok</div>
                    <div class="info-value">{{ $participant->group->name }}</div>
                </div>
                <div class="kta-right">
                    <div class="kta-qr">
                        <div id="qrcode"></div>
                    </div>
                    <div class="kta-footer">
                        @if($setting->signature_image)
                            <img src="{{ asset('uploads/kta/' . $setting->signature_image) }}" class="kta-signature-img">
                        @endif
                        <div class="kta-signature-name">{{ $setting->signature_name ?? 'TTD PENGURUS' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BACK SIDE -->
        <h6 class="text-center text-muted mb-3 mt-5">Tampak Belakang</h6>
        <div class="kta-card kta-back shadow" id="kta-back">
            <div class="kta-back-overlay"></div>
            <div class="kta-back-content">
                <div class="kta-back-section">
                    <div class="kta-back-title">Visi</div>
                    <div class="kta-back-text">{{ $setting->vision ?? 'Visi Arisan belum diatur.' }}</div>
                </div>
                <div class="kta-back-section">
                    <div class="kta-back-title">Misi</div>
                    <div class="kta-back-text">{{ $setting->mission ?? 'Misi Arisan belum diatur.' }}</div>
                </div>
                <div class="kta-back-section">
                    <div class="kta-back-title">Moto</div>
                    <div class="kta-back-text">{{ $setting->moto ?? 'Moto Arisan belum diatur.' }}</div>
                </div>
            
            </div>
        </div>

        <!-- MOBILE DOWNLOAD BUTTON -->
        <div class="mt-4 d-md-none">
            <button onclick="downloadPng()" class="btn btn-primary w-100 py-3 rounded-pill shadow" id="btn-download-mobile">
                <i class="fas fa-download me-2"></i>Unduh KTA Sekarang (PNG)
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    // Initialize QR Code
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $participant->lottery_number }}",
        width: 80,
        height: 80,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    async function downloadPng() {
        const btnDesktop = document.getElementById('btn-download-desktop');
        const btnMobile = document.getElementById('btn-download-mobile');
        
        const setLoading = (loading) => {
            const text = loading ? '<i class="fas fa-spinner fa-spin me-1"></i>Proses...' : '<i class="fas fa-download me-1"></i>Unduh KTA (PNG)';
            if(btnDesktop) { btnDesktop.innerHTML = text; btnDesktop.disabled = loading; }
            if(btnMobile) { btnMobile.innerHTML = loading ? '<i class="fas fa-spinner fa-spin me-1"></i>Proses...' : '<i class="fas fa-download me-2"></i>Unduh KTA Sekarang (PNG)'; btnMobile.disabled = loading; }
        };

        setLoading(true);

        try {
            const options = {
                scale: 3, // Higher scale for high quality PNG
                useCORS: true,
                allowTaint: false,
                backgroundColor: "#ffffff",
            };

            // Capture Front
            const canvasFront = await html2canvas(document.getElementById('kta-front'), options);
            const linkFront = document.createElement('a');
            linkFront.download = 'KTA_Depan_{{ $participant->lottery_number }}.png';
            linkFront.href = canvasFront.toDataURL('image/png');
            linkFront.click();

            // Capture Back after small delay
            setTimeout(async () => {
                const canvasBack = await html2canvas(document.getElementById('kta-back'), options);
                const linkBack = document.createElement('a');
                linkBack.download = 'KTA_Belakang_{{ $participant->lottery_number }}.png';
                linkBack.href = canvasBack.toDataURL('image/png');
                linkBack.click();
                
                setLoading(false);
            }, 800);

        } catch (error) {
            console.error('Capture failed:', error);
            alert('Gagal mengunduh gambar. Silakan coba lagi.');
            setLoading(false);
        }
    }
</script>
@endpush
