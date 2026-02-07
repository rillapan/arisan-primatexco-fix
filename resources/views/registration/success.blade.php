<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - PRIMKOPKAR PRIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --success: #10b981;
        }
        body {
            background-color: #f1f5f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .success-card {
            max-width: 500px;
            width: 90%;
            background: white;
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.1);
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background: #ecfdf5;
            color: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 2rem;
        }
        .btn-home {
            background: var(--primary);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            color: white;
        }
        .proof-container {
            position: fixed;
            left: -9999px;
            top: 0;
        }
        .registration-ticket {
            width: 500px;
            background: white;
            padding: 2rem;
            border-radius: 20px;
            border: 2px dashed #cbd5e1;
            position: relative;
        }
        .ticket-header {
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .ticket-body {
            text-align: left;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .info-label {
            color: #64748b;
            font-size: 0.85rem;
        }
        .info-value {
            font-weight: 700;
            color: #1e293b;
        }
        .lottery-list {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 12px;
            margin-top: 1rem;
        }
        .qr-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #qrcode {
            background: white;
            padding: 5px;
        }
        .btn-download-proof {
            background: #10b981;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            border: none;
            margin-top: 1rem;
            width: 100%;
        }
        .btn-download-proof:hover {
            background: #059669;
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>
<body>

<div class="success-card shadow-lg">
    <div class="icon-box">
        <i class="fas fa-check-circle"></i>
    </div>
    
    <h1 class="h3 fw-bold mb-3">Pendaftaran Berhasil!</h1>
    <p class="text-muted mb-2">Terima kasih, <strong>{{ $participants[0]->name }}</strong>.</p>
    <p class="text-muted mb-4">
        Anda telah berhasil mendaftarkan <strong>{{ count($participants) }} undian</strong> untuk kelompok <strong>{{ $group->name }}</strong>. 
    </p>

    <div class="bg-light p-3 rounded-3 mb-4 text-start">
        <h6 class="fw-bold mb-3 small border-bottom pb-2">Daftar Nomor Undian Anda:</h6>
        <div class="table-responsive">
            <table class="table table-sm table-borderless mb-0">
                <thead>
                    <tr class="small text-muted">
                        <th>No</th>
                        <th>Nomor Undian</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $index => $p)
                    <tr>
                        <td class="small">{{ $index + 1 }}</td>
                        <td class="small fw-bold text-primary">{{ $p->lottery_number }}</td>
                        <td><span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Pending</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <button type="button" class="btn-download-proof" id="downloadBtn">
        <i class="fas fa-download me-2"></i> Simpan Bukti Pendaftaran (JPG)
    </button>

    <a href="{{ route('home') }}" class="btn-home mt-3 w-100">
        Kembali ke Beranda
    </a>
</div>

<!-- Hidden section for image generation -->
<div class="proof-container">
    <div id="proofVoucher" class="registration-ticket">
        <div class="ticket-header">
            <h5 class="fw-bold mb-0" style="color: #2563eb;">PRIMKOPKAR PRIMA</h5>
            <small class="text-muted">BUKTI PENDAFTARAN ARISAN</small>
        </div>
        <div class="ticket-body">
            <div class="info-row">
                <span class="info-label">Nama Lengkap</span>
                <span class="info-value">{{ $participants[0]->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NIK</span>
                <span class="info-value">{{ $participants[0]->nik }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelompok</span>
                <span class="info-value">{{ $group->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Daftar</span>
                <span class="info-value">{{ date('d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jumlah Undian</span>
                <span class="info-value">{{ count($participants) }} Akun</span>
            </div>

            <div class="lottery-list">
                <h6 class="fw-bold small mb-2">Daftar Nomor Undian:</h6>
                <div style="column-count: 2; column-gap: 10px;">
                    @foreach($participants as $p)
                        <div class="small fw-bold mb-1" style="color: #2563eb;">• {{ $p->lottery_number }}</div>
                    @endforeach
                </div>
            </div>

            <div class="qr-section">
                <div>
                    <h6 class="fw-bold mb-1">Status: PENDING</h6>
                    <small class="text-muted d-block" style="width: 250px;">Bukti ini sah dikeluarkan oleh sistem pendaftaran online PRIMKOPKAR PRIMA.</small>
                </div>
                <div id="qrcode"></div>
            </div>
            
            <div class="mt-4 text-center">
                <small class="text-muted" style="font-size: 0.7rem;">&copy; {{ date('Y') }} PRIMKOPKAR PRIMA - Sistem Arisan Digital</small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate QR Code
        const nik = "{{ $participants[0]->nik }}";
        const lotteryRaw = {
            nik: nik,
            group: "{{ $group->name }}",
            count: "{{ count($participants) }}",
            date: "{{ date('Y-m-d') }}"
        };
        
        new QRCode(document.getElementById("qrcode"), {
            text: JSON.stringify(lotteryRaw),
            width: 80,
            height: 80,
            colorDark : "#1e293b",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Download as Image
        document.getElementById('downloadBtn').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengolah Gambar...';

            const proof = document.getElementById('proofVoucher');
            
            // Temporary show the container for html2canvas
            const container = document.querySelector('.proof-container');
            container.style.position = 'static';
            container.style.left = '0';
            
            html2canvas(proof, {
                scale: 2, // Better quality
                backgroundColor: '#ffffff',
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Bukti_Daftar_Arisan_' + nik + '.jpg';
                link.href = canvas.toDataURL('image/jpeg', 0.9);
                link.click();
                
                // Restore styles
                container.style.position = 'fixed';
                container.style.left = '-9999px';
                
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    });
</script>

</body>
</html>
