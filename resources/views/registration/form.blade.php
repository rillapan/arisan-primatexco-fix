<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran - {{ $group->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --bg-light: #f1f5f9;
        }
        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            padding-bottom: 3rem;
        }
        .form-container {
            max-width: 700px;
            margin: 3rem auto;
        }
        .registration-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: white;
        }
        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 2rem;
            color: white;
            text-align: center;
        }
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
        }
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            border-color: var(--primary);
        }
        .terms-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            font-size: 0.85rem;
            max-height: 200px;
            overflow-y: auto;
        }
        .btn-register {
            border-radius: 10px;
            padding: 1rem;
            font-weight: 700;
            background: var(--primary);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-register:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }
        .btn-register:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
        }
        .photo-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f1f5f9;
            display: none;
            margin: 1rem auto;
        }
        .card-header-participants {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            padding: 1.5rem;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        
        <div class="text-center mb-4">
            <a href="{{ route('register.index') }}" class="text-decoration-none text-muted small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Pemilihan Kelompok
            </a>
        </div>

        <div class="card registration-card">
            <div class="card-header-custom">
                <h2 class="h4 fw-bold mb-1">Formulir Pendaftaran</h2>
                <p class="mb-0 opacity-90">Kelompok: {{ $group->name }}</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('register.store', $group->id) }}" method="POST" enctype="multipart/form-data" id="registrationForm">
                    @csrf
                    
                    <div class="section-title mb-4">
                        <h5 class="fw-bold"><i class="fas fa-id-card me-2 text-primary"></i>Data Identitas</h5>
                        <hr>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap Sesuai KTP</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nik" class="form-label">NIK (Nomor Induk Karyawan)</label>
                            <input type="text" class="form-control" id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="shift" class="form-label">Bagian / Shift</label>
                            <input type="text" class="form-control" id="shift" name="shift" placeholder="Contoh: Produksi / Shift A" value="{{ old('shift') }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="account_count" class="form-label">Jumlah Undian yang Didaftarkan</label>
                        <select class="form-select @error('account_count') is-invalid @enderror" id="account_count" name="account_count" required>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ old('account_count') == $i ? 'selected' : '' }}>{{ $i }} Undian</option>
                            @endfor
                        </select>
                        <div class="form-text text-info">
                            <i class="fas fa-info-circle me-1"></i> 
                            Anda dapat mendaftarkan lebih dari satu undian (akun) sekaligus menggunakan satu NIK. 
                            Setiap undian akan mendapatkan Nomor Undian unik berformat <strong>NIK-ID_Kelompok</strong> (Contoh: {{ old('nik', '12345') }}-{{ $group->id }}, {{ old('nik', '12345') }}-{{ $group->id }}A, dst).
                        </div>
                        @error('account_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="section-title mt-5 mb-4">
                        <h5 class="fw-bold"><i class="fas fa-file-contract me-2 text-primary"></i>Syarat & Ketentuan</h5>
                        <hr>
                    </div>

                    <div class="terms-box">
                        <h6 class="fw-bold">RINGKASAN ATURAN ARISAN:</h6>
                        <ul class="mb-0">
                            <li>Setiap peserta wajib membayar iuran bulanan tepat waktu melalui potong gaji.</li>
                            <li>Sistem pemenang menggunakan metode Lelang (Bid) dan Undian.</li>
                            <li>Batas minimal BID adalah Rp 2.250.000 dan maksimal Rp 6.000.000.</li>
                            <li>Pemenang akan mendapatkan Hadiah Utama dikurangi nilai BID yang diajukan.</li>
                            <li>Peserta yang sudah menang tidak dapat mengikuti lelang kembali namun tetap wajib membayar angsuran.</li>
                            <li>Keputusan admin dan hasil sistem bersifat mutlak.</li>
                        </ul>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="agreement" name="agreement" required>
                        <label class="form-check-label small fw-medium text-secondary" for="agreement">
                            Saya telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku serta bersedia mengikuti seluruh rangkaian arisan.
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-register text-white" id="submitBtn" disabled>
                            DAFTAR SEKARANG
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <p class="text-center mt-4 text-muted small">
            Kesulitan mendaftar? Hubungi bagian Administrasi Koperasi.
        </p>

        <!-- Participants List Section -->
        <div class="card registration-card mt-5">
            <div class="card-header-participants">
                <h2 class="h5 fw-bold mb-1">Daftar Peserta Terdaftar</h2>
                <p class="mb-0 opacity-90">Total: {{ $group->participants->count() }} Pendaftar</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">No Undian</th>
                                <th class="px-4 py-3">Nama Peserta</th>
                                <th class="px-4 py-3">NIK</th>
                                <th class="px-4 py-3">Bagian/Shift</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($group->participants as $participant)
                                <tr class="{{ $participant->registration_status == 'pending' ? 'table-warning' : '' }}">
                                    <td class="px-4 py-3">
                                        @if($participant->lottery_number)
                                            <span class="badge bg-primary rounded-pill">{{ $participant->lottery_number }}</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">PENDING</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 fw-medium">{{ $participant->name }}</td>
                                    <td class="px-4 py-3">{{ $participant->nik }}</td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-info text-dark rounded-pill">{{ $participant->shift }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($participant->registration_status == 'pending')
                                            <span class="badge bg-warning text-dark border"><i class="fas fa-clock me-1"></i>Menunggu</span>
                                        @elseif($participant->registration_status == 'rejected')
                                            <span class="badge bg-danger rounded-pill px-3">Ditolak</span>
                                        @else
                                            <span class="badge bg-primary rounded-pill px-3">Diterima / Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Belum ada peserta yang mendaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('registrationForm');
    const inputs = form.querySelectorAll('input[required]');
    const submitBtn = document.getElementById('submitBtn');

    function validateForm() {
        let isValid = true;
        inputs.forEach(input => {
            if (input.type === 'checkbox') {
                if (!input.checked) isValid = false;
            } else {
                if (!input.value.trim()) isValid = false;
            }
        });
        submitBtn.disabled = !isValid;
    }

    inputs.forEach(input => {
        input.addEventListener('input', validateForm);
        input.addEventListener('change', validateForm);
    });
</script>

</body>
</html>
