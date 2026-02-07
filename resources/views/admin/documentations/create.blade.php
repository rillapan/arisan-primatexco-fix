@extends('layouts.admin')

@section('title', 'Tambah Dokumentasi - ' . $group->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.groups.documentations.index', $group->id) }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Dokumentasi
        </h1>
        <p class="text-muted mb-0">{{ $group->name }}</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Formulir Dokumentasi Baru</h6>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.groups.documentations.store', $group->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="monthly_period_id" class="form-label fw-bold">Pilih Periode <span class="text-danger">*</span></label>
                        <select class="form-select @error('monthly_period_id') is-invalid @enderror" id="monthly_period_id" name="monthly_period_id" required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach($group->monthlyPeriods as $period)
                                <option value="{{ $period->id }}" {{ old('monthly_period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->period_start->locale('id')->monthName }} {{ $period->period_start->year }}
                                </option>
                            @endforeach
                        </select>
                        @error('monthly_period_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Jenis Dokumentasi <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_image" value="image" {{ old('type', 'image') == 'image' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="type_image">
                                    <i class="fas fa-image me-1 text-primary"></i> Gambar
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_video" value="video" {{ old('type') == 'video' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_video">
                                    <i class="fas fa-video me-1 text-danger"></i> Video
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_text" value="text" {{ old('type') == 'text' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_text">
                                    <i class="fas fa-align-left me-1 text-info"></i> Teks
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="file_input_container" class="mb-4 {{ old('type') == 'text' ? 'd-none' : '' }}">
                        <label for="file" class="form-label fw-bold">Pilih Berkas <span class="text-danger">*</span></label>
                        <input class="form-control @error('file') is-invalid @enderror" type="file" id="file" name="file" accept="image/*,video/*">
                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle me-1"></i> Format didukung: Image (JPG, PNG, GIF) atau Video (MP4). Maksimal 50MB.
                        </div>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="text_input_container" class="mb-4 {{ old('type') == 'text' ? '' : 'd-none' }}">
                        <label for="text_content" class="form-label fw-bold">Isi Teks <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('text_content') is-invalid @enderror" id="text_content" name="text_content" rows="6" placeholder="Masukkan konten teks di sini...">{{ old('text_content') }}</textarea>
                        @error('text_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="caption" class="form-label fw-bold">Keterangan (Opsional)</label>
                        <input type="text" class="form-control @error('caption') is-invalid @enderror" id="caption" name="caption" value="{{ old('caption') }}" placeholder="Judul atau keterangan singkat">
                        @error('caption')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="progress_container" class="mb-4 d-none">
                        <label class="form-label fw-bold">Progres Upload</label>
                        <div class="progress" style="height: 25px;">
                            <div id="upload_progress_bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small id="upload_status" class="text-muted">Mengupload berkas...</small>
                            <small id="upload_percentage" class="fw-bold text-primary">0%</small>
                        </div>
                    </div>

                    <div class="text-end border-top pt-4">
                        <button type="submit" id="submit_btn" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> <span id="btn_text">Simpan Dokumentasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const typeRadios = document.querySelectorAll('input[name="type"]');
        const fileContainer = document.getElementById('file_input_container');
        const textContainer = document.getElementById('text_input_container');
        const fileInput = document.getElementById('file');
        const textInput = document.getElementById('text_content');
        const submitBtn = document.getElementById('submit_btn');
        const btnText = document.getElementById('btn_text');
        
        const progressContainer = document.getElementById('progress_container');
        const progressBar = document.getElementById('upload_progress_bar');
        const uploadPercentage = document.getElementById('upload_percentage');
        const uploadStatus = document.getElementById('upload_status');

        // Create warning element for file type mismatch
        const fileWarning = document.createElement('div');
        fileWarning.id = 'file_type_warning';
        fileWarning.className = 'alert alert-warning mt-2 d-none';
        fileWarning.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i><span id="file_warning_text"></span>';
        fileInput.parentNode.insertBefore(fileWarning, fileInput.nextSibling.nextSibling);

        // Function to validate file type and size
        function validateFileType() {
            const selectedType = document.querySelector('input[name="type"]:checked').value;
            const file = fileInput.files[0];
            
            if (!file || selectedType === 'text') {
                fileWarning.classList.add('d-none');
                fileInput.classList.remove('is-invalid');
                return true;
            }
            
            const fileName = file.name.toLowerCase();
            const fileType = file.type.toLowerCase();
            const fileSize = file.size;
            const maxSize = 50 * 1024 * 1024; // 50MB in bytes
            const warningText = document.getElementById('file_warning_text');
            
            // Check file size first
            if (fileSize > maxSize) {
                const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);
                warningText.textContent = 'Ukuran file terlalu besar! File Anda: ' + fileSizeMB + 'MB. Maksimal yang diperbolehkan adalah 50MB.';
                fileWarning.classList.remove('d-none');
                fileInput.classList.add('is-invalid');
                return false;
            }
            
            // Check image types
            const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp'];
            const videoExtensions = ['.mp4', '.mov', '.avi', '.webm', '.mkv', '.wmv'];
            
            const isImage = imageExtensions.some(ext => fileName.endsWith(ext)) || fileType.startsWith('image/');
            const isVideo = videoExtensions.some(ext => fileName.endsWith(ext)) || fileType.startsWith('video/');
            
            if (selectedType === 'image' && !isImage) {
                warningText.textContent = 'File yang dipilih bukan gambar! Anda memilih jenis "Gambar" tetapi mengupload file video atau format lain.';
                fileWarning.classList.remove('d-none');
                fileInput.classList.add('is-invalid');
                return false;
            } else if (selectedType === 'video' && !isVideo) {
                warningText.textContent = 'File yang dipilih bukan video! Anda memilih jenis "Video" tetapi mengupload file gambar atau format lain.';
                fileWarning.classList.remove('d-none');
                fileInput.classList.add('is-invalid');
                return false;
            }
            
            fileWarning.classList.add('d-none');
            fileInput.classList.remove('is-invalid');
            return true;
        }

        // Validate on file change
        fileInput.addEventListener('change', validateFileType);

        typeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'text') {
                    fileContainer.classList.add('d-none');
                    textContainer.classList.remove('d-none');
                    fileInput.removeAttribute('required');
                    textInput.setAttribute('required', 'required');
                    fileWarning.classList.add('d-none');
                    fileInput.classList.remove('is-invalid');
                } else {
                    fileContainer.classList.remove('d-none');
                    textContainer.classList.add('d-none');
                    textInput.removeAttribute('required');
                    fileInput.setAttribute('required', 'required');
                    
                    if (this.value === 'image') {
                        fileInput.setAttribute('accept', 'image/*');
                    } else {
                        fileInput.setAttribute('accept', 'video/*');
                    }
                    
                    if (fileInput.files.length > 0) {
                        validateFileType();
                    }
                }
            });
        });
        
        const initialType = document.querySelector('input[name="type"]:checked').value;
        if (initialType === 'text') {
            fileInput.removeAttribute('required');
            textInput.setAttribute('required', 'required');
        } else {
            textInput.removeAttribute('required');
            fileInput.setAttribute('required', 'required');
        }

        // Handle AJAX form submission
        form.addEventListener('submit', function(e) {
            const selectedType = document.querySelector('input[name="type"]:checked').value;
            
            // Standard validation
            if (selectedType !== 'text' && !validateFileType()) {
                e.preventDefault();
                fileInput.focus();
                return;
            }

            // For image/video, use AJAX to show progress
            if (selectedType !== 'text') {
                e.preventDefault();
                
                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();
                
                // Set up progress tracking
                progressContainer.classList.remove('d-none');
                submitBtn.disabled = true;
                btnText.textContent = 'Sedang Mengupload...';
                
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percent + '%';
                        progressBar.setAttribute('aria-valuenow', percent);
                        progressBar.textContent = percent + '%';
                        uploadPercentage.textContent = percent + '%';
                        
                        if (percent === 100) {
                            uploadStatus.textContent = 'Memproses berkas di server...';
                        }
                    }
                });
                
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                window.location.href = response.redirect;
                            } else {
                                throw new Error(response.message || 'Gagal menyimpan data.');
                            }
                        } catch(err) {
                            alert('Upload selesai tetapi terjadi kesalahan: ' + err.message);
                            resetForm();
                        }
                    } else {
                        let errorMessage = 'Terjadi kesalahan sistem (' + xhr.status + ').';
                        try {
                            const errorData = JSON.parse(xhr.responseText);
                            if (errorData.message) errorMessage = errorData.message;
                            if (errorData.errors) {
                                errorMessage += "\n" + Object.values(errorData.errors).flat().join("\n");
                            }
                        } catch(e) {}
                        alert(errorMessage);
                        resetForm();
                    }
                };
                
                xhr.onerror = function() {
                    alert('Koneksi terputus atau server tidak merespon.');
                    resetForm();
                };
                
                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            }
        });

        function resetForm() {
            progressContainer.classList.add('d-none');
            submitBtn.disabled = false;
            btnText.textContent = 'Simpan Dokumentasi';
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            uploadPercentage.textContent = '0%';
            uploadStatus.textContent = 'Mengupload berkas...';
        }
    });
</script>
@endpush
@endsection
