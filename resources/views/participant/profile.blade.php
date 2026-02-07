@extends('layouts.participant')

@section('title', 'Profil - Sistem Arisan')

@section('content')
    <h2>
        <i class="fas fa-user-cog me-2"></i>
        Profil Peserta
    </h2>

    <!-- Profile Information -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-user me-2"></i>
                Informasi Pribadi
            </h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
    
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi Pribadi:</strong> Beberapa data tidak dapat diedit. Jika ingin mengubah data NIK/Shift, harap hubungi admin.
            </div>

            <div class="row mb-4">
                <div class="col-12 text-center">
                    <div class="position-relative d-inline-block">
                        @if($participant->photo)
                            <img src="{{ storage_url($participant->photo) }}" alt="Foto Profil" 
                                 class="rounded-circle shadow border" 
                                 id="profile-img-preview"
                                 style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #fff;">
                            <div class="position-absolute bottom-0 end-0">
                                <button type="button" class="btn btn-danger btn-sm rounded-circle shadow" 
                                        onclick="if(confirm('Hapus foto profil?')) document.getElementById('delete-photo-form').submit();"
                                        title="Hapus Foto">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <form id="delete-photo-form" action="{{ route('participant.profile.delete-photo') }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border shadow-sm mx-auto" 
                                 id="profile-placeholder"
                                 style="width: 150px; height: 150px; background-color: #f8fafc !important;">
                                <i class="fas fa-user fa-4x text-muted opacity-30"></i>
                            </div>
                            <img src="" alt="Foto Profil" 
                                 class="rounded-circle shadow border d-none" 
                                 id="profile-img-preview"
                                 style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #fff;">
                            <div class="mt-2 small text-muted" id="no-photo-text">Belum ada foto profil</div>
                        @endif
                        
                        <!-- Camera Button -->
                        <div class="position-absolute top-0 end-0">
                            <button type="button" class="btn btn-primary btn-sm rounded-circle shadow" 
                                    data-bs-toggle="modal" data-bs-target="#cameraModal"
                                    title="Ambil Foto">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-user me-1"></i>Nama Lengkap
                        </label>
                        <input type="text" class="form-control" 
                               id="name" value="{{ $participant->name }}" readonly>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nik" class="form-label">
                            <i class="fas fa-id-card me-1"></i>NIK
                        </label>
                        <input type="text" class="form-control" 
                               id="nik" value="{{ $participant->nik }}" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="shift" class="form-label">
                            <i class="fas fa-clock me-1"></i>Shift
                        </label>
                        <input type="text" class="form-control" 
                               id="shift" value="{{ $participant->shift }}" readonly>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-hashtag me-1"></i>No. Undian
                        </label>
                        <input type="text" class="form-control" value="{{ $participant->lottery_number }}" readonly>
                    </div>
                </div>
            </div>

            <hr>

            <form action="{{ route('participant.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <h5 class="mb-3">
                    <i class="fas fa-upload me-2 text-primary"></i>
                    Update Foto Profil
                </h5>
                <div class="mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <label for="photo" class="form-label small fw-bold">Unggah dari File</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Hidden input for captured image -->
                            <input type="hidden" id="captured_photo" name="captured_photo">
                            
                            <div class="form-text mt-2 small">
                                <i class="fas fa-info-circle me-1"></i> Anda bisa mengunggah file atau menggunakan kamera di tombol biru atas.
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">
                    <i class="fas fa-lock me-2 text-primary"></i>
                    Ubah Password
                </h5>
                
                <!-- Hidden fields for required data -->
                <input type="hidden" name="name" value="{{ $participant->name }}">
                <input type="hidden" name="department" value="{{ $participant->department }}">
                <input type="hidden" name="shift" value="{{ $participant->shift }}">
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-key me-1"></i>Password Saat Ini
                            </label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password Baru
                            </label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" name="new_password">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1"></i>Konfirmasi Password
                            </label>
                            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                   id="new_password_confirmation" name="new_password_confirmation">
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold">
                        <i class="fas fa-save me-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Status -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-info">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Status Akun
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status Keanggotaan:</span>
                        @if($participant->has_won)
                            <span class="badge bg-warning">SUDAH MENANG</span>
                        @else
                            <span class="badge bg-success">MASIH AKTIF</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Kelompok:</span>
                        <strong class="text-primary">{{ $participant->group->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Tanggal Bergabung:</span>
                        <strong>{{ $participant->created_at->format('d M Y') }}</strong>
                    </div>
                    
                    @if($participant->has_won && $participant->winner)
                        <hr>
                        <h6 class="text-success mb-3">
                            <i class="fas fa-trophy me-2"></i>Informasi Kemenangan
                        </h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Tanggal Menang:</span>
                            <strong class="text-warning">{{ $participant->winner->draw_time ? $participant->winner->draw_time->format('d M Y H:i') : '-' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Periode:</span>
                            <strong class="text-info">{{ $participant->winner->monthlyPeriod->period_start->format('M Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Hadiah Akhir:</span>
                            <strong class="text-success">Rp {{ number_format($participant->winner->final_prize, 0, ',', '.') }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Statistik
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Bid:</span>
                        <strong class="text-primary">{{ $participant->bids()->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Periode Aktif:</span>
                        <strong class="text-success">{{ $participant->group->monthlyPeriods()->where('status', 'active')->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cameraModalLabel"><i class="fas fa-camera me-2"></i>Ambil Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopCamera()"></button>
                </div>
                <div class="modal-body text-center p-0 overflow-hidden bg-black" style="min-height: 300px;">
                    <video id="video" class="w-100 h-100" autoplay playsinline></video>
                    <canvas id="canvas" class="d-none"></canvas>
                    <img id="photo-preview" class="w-100 h-100 d-none" style="object-fit: contain;">
                </div>
                <div class="modal-footer justify-content-center">
                    <div id="camera-controls">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" onclick="switchCamera()">
                            <i class="fas fa-sync me-1"></i>Putar Kamera
                        </button>
                        <button type="button" class="btn btn-primary rounded-pill px-4" onclick="takePhoto()">
                            <i class="fas fa-circle me-1"></i>Ambil Foto
                        </button>
                    </div>
                    <div id="preview-controls" class="d-none">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" onclick="retakePhoto()">
                            <i class="fas fa-redo me-1"></i>Ulangi
                        </button>
                        <button type="button" class="btn btn-success rounded-pill px-4" onclick="usePhoto()">
                            <i class="fas fa-check me-1"></i>Gunakan Foto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Real-time password validation
    const currentPassword = document.getElementById('current_password');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');

    function validatePasswords() {
        if (newPassword.value && confirmPassword.value) {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Password konfirmasi tidak cocok');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
    }

    if (newPassword && confirmPassword) {
        newPassword.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);
    }
});

// Camera Functionality
let stream = null;
let currentFacingMode = 'user'; // Start with front camera
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const photoPreview = document.getElementById('photo-preview');
const capturedPhotoInput = document.getElementById('captured_photo');
const fileInput = document.getElementById('photo');
const cameraControls = document.getElementById('camera-controls');
const previewControls = document.getElementById('preview-controls');

// Clear captured photo if user selects a file
if (fileInput) {
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            capturedPhotoInput.value = '';
            // Reset main preview if possible (optional, might require re-reading file)
        }
    });
}

async function startCamera() {
    try {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        const constraints = {
            video: {
                facingMode: currentFacingMode,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };
        
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = stream;
        
        video.classList.remove('d-none');
        photoPreview.classList.add('d-none');
        cameraControls.classList.remove('d-none');
        previewControls.classList.add('d-none');
    } catch (err) {
        console.error("Camera error:", err);
        alert("Gagal mengakses kamera. Pastikan izin kamera telah diberikan.");
    }
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

function switchCamera() {
    currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
    startCamera();
}

function retakePhoto() {
    video.classList.remove('d-none');
    photoPreview.classList.add('d-none');
    cameraControls.classList.remove('d-none');
    previewControls.classList.add('d-none');
}

function takePhoto() {
    // Set canvas dimensions
    let width = video.videoWidth;
    let height = video.videoHeight;
    
    // Resize if too large (max 800px width) to prevent POST size issues
    const MAX_WIDTH = 800;
    if (width > MAX_WIDTH) {
        height = height * (MAX_WIDTH / width);
        width = MAX_WIDTH;
    }
    
    canvas.width = width;
    canvas.height = height;
    
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, width, height);
    
    // Reduced quality to 0.8 to save bandwidth
    const data = canvas.toDataURL('image/jpeg', 0.8);
    photoPreview.src = data;
    
    video.classList.add('d-none');
    photoPreview.classList.remove('d-none');
    cameraControls.classList.add('d-none');
    previewControls.classList.remove('d-none');
}

function usePhoto() {
    // Get data again from canvas (already resized)
    const data = canvas.toDataURL('image/jpeg', 0.8);
    capturedPhotoInput.value = data;
    
    // Clear file input so controller uses captured photo
    if (fileInput) fileInput.value = '';
    
    // Hide placeholder and "no photo" text if exists
    const placeholder = document.getElementById('profile-placeholder');
    const noPhotoText = document.getElementById('no-photo-text');
    if (placeholder) placeholder.classList.add('d-none');
    if (noPhotoText) noPhotoText.classList.add('d-none');
    
    // Show preview on main profile image
    const mainPreview = document.getElementById('profile-img-preview');
    if (mainPreview) {
        mainPreview.src = data;
        mainPreview.classList.remove('d-none');
    }
    
    stopCamera();
    const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
    modal.hide();
    
    alert("Foto berhasil diambil. Klik 'Simpan Perubahan' di bawah untuk menyimpan.");
}

// Start camera when modal is shown
document.getElementById('cameraModal').addEventListener('shown.bs.modal', function () {
    startCamera();
});

document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function () {
    stopCamera();
});
</script>
@endpush
