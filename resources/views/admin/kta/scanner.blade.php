@extends('layouts.admin')

@section('title', 'Scan KTA - Sistem Arisan')

@section('content')
    <div class="mb-4 text-center">
        <h2><i class="fas fa-qrcode me-2"></i>Scan QR Code KTA</h2>
        <p class="text-muted">Scan QR Code pada kartu peserta untuk melihat identitas.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div id="reader" style="width: 100%; border-radius: 10px; overflow: hidden;"></div>
                    <div id="result" class="mt-4 d-none">
                        <div class="alert alert-info d-flex align-items-center mb-0">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            <span>Mencari data peserta...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <button id="start-btn" class="btn btn-primary d-none">
                    <i class="fas fa-camera me-1"></i>Mulai Kamera
                </button>
            </div>
        </div>
    </div>

    <!-- Participant Detail Modal -->
    <div class="modal fade" id="participantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-check me-2"></i>Data Peserta Ditemukan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-3x text-primary"></i>
                        </div>
                        <h4 id="p-name" class="fw-bold mb-0">Nama Peserta</h4>
                        <p id="p-lottery" class="text-muted mb-0">No Undian: 123</p>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted d-block mb-1">NIK</small>
                                <span id="p-nik" class="fw-bold">1234567890</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted d-block mb-1">Kelompok</small>
                                <span id="p-group" class="fw-bold">Kelompok A</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted d-block mb-1">Status Pemenang</small>
                                <span id="p-won" class="badge bg-secondary">Belum Menang</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted d-block mb-1">Status Akun</small>
                                <span id="p-active" class="badge bg-success">Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-secondary w-100 py-2" data-bs-dismiss="modal">Tutup & Scan Lagi</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Panduan Pilihan Kamera (Select Camera)</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="fw-bold text-dark mb-1">1. Integrated Camera</h6>
                                <p class="small text-muted mb-0">Ini adalah kamera fisik atau webcam bawaan laptop Anda. Pilih ini untuk penggunaan normal.</p>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">2. DroidCam Source</h6>
                                <p class="small text-muted mb-0">Memungkinkan Anda menggunakan Kamera HP sebagai webcam. Sangat disarankan jika kamera laptop kurang jernih atau sulit fokus.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="fw-bold text-dark mb-1">3. OBS Virtual Camera</h6>
                                <p class="small text-muted mb-0">Pilihan dari software OBS Studio. Biasanya digunakan untuk kebutuhan streaming atau simulasi input video.</p>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">4. Back vs Front Camera</h6>
                                <p class="small text-muted mb-0">Jika membuka link ini di HP, pastikan pilih <strong>Kamera Belakang</strong> karena memiliki fitur fokus otomatis yang lebih baik untuk scan QR.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrcodeScanner;
    const modal = new bootstrap.Modal(document.getElementById('participantModal'));

    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning after success
        html5QrcodeScanner.clear();
        document.getElementById('start-btn').classList.remove('d-none');
        
        // Show loading
        document.getElementById('result').classList.remove('d-none');
        
        // Search participant
        fetch('{{ route('admin.kta.search') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ lottery_number: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('result').classList.add('d-none');
            
            if (data.success) {
                const p = data.participant;
                document.getElementById('p-name').innerText = p.name;
                document.getElementById('p-lottery').innerText = 'No Undian: ' + p.lottery_number;
                document.getElementById('p-nik').innerText = p.nik;
                document.getElementById('p-group').innerText = p.group_name;
                
                const wonSpan = document.getElementById('p-won');
                wonSpan.innerText = p.has_won;
                wonSpan.className = p.has_won === 'Sudah Menang' ? 'badge bg-success' : 'badge bg-warning text-dark';
                
                const activeSpan = document.getElementById('p-active');
                activeSpan.innerText = p.is_active;
                activeSpan.className = p.is_active === 'Aktif' ? 'badge bg-success' : 'badge bg-danger';
                
                modal.show();
            } else {
                alert(data.message);
                startScanner();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mencari data.');
            startScanner();
        });
    }

    function onScanFailure(error) {
        // Optional: handle scan failure
    }

    function startScanner() {
        document.getElementById('start-btn').classList.add('d-none');
        html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
            fps: 10, 
            qrbox: {width: 250, height: 250},
            rememberLastUsedCamera: true
        });
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    document.getElementById('start-btn').addEventListener('click', startScanner);
    
    document.getElementById('participantModal').addEventListener('hidden.bs.modal', function () {
        startScanner();
    });

    window.onload = startScanner;
</script>
@endpush
