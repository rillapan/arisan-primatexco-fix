@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.groups.manage', $group->id) }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-calendar-plus me-2 text-primary"></i>Buat Periode Baru - {{ $group->name }}
        </h1>
        <p class="text-muted mb-0">Buat bulan baru untuk arisan</p>
    </div>
</div>


    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Buat Periode Baru - {{ $group->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.groups.periods.store', $group->id) }}" method="POST">
                            @csrf

                            <!-- Group Summary -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                            <h5>Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}</h5>
                                            <p class="mb-0">Iuran Bulanan</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <h5>{{ $group->participants->where('is_active', true)->count() }}</h5>
                                            <p class="mb-0">Total Peserta Aktif</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-gift fa-2x mb-2"></i>
                                            <h5>Rp {{ number_format($group->main_prize, 0, ',', '.') }}</h5>
                                            <p class="mb-0">Hadiah Utama</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Reference Source Section -->
                            <div class="alert alert-warning border-warning">
                                <h6 class="text-warning fw-bold"><i class="fas fa-link me-2"></i>Acuan Saldo Awal</h6>
                                <p class="mb-2 small">Pilih bulan sebelumnya untuk mengambil Saldo Akumulasi secara otomatis. Jika baru pertama kali, pilih <strong>Input Manual</strong>.</p>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="reference_source" class="form-label fw-bold">Sumber Saldo (Bulan Sebelumnya)</label>
                                        <select class="form-select @error('reference_source') is-invalid @enderror" id="reference_source" name="reference_source" required>
                                            <option value="" disabled selected>-- Pilih Acuan Saldo --</option>
                                            
                                            <optgroup label="Opsi Manual">
                                                <option value="manual" {{ old('reference_source') == 'manual' ? 'selected' : '' }}>-- Periode Pertama / Input Manual --</option>
                                            </optgroup>

                                            @if(count($referenceOptions) > 0)
                                            <optgroup label="Riwayat Kas Bulan Sebelumnya (Otomatis)">
                                                @foreach($referenceOptions as $opt)
                                                    <option value="{{ $opt['id'] }}" 
                                                            data-balance="{{ $opt['balance'] }}"
                                                            data-next-month="{{ $opt['next_month_name'] ?? '' }}"
                                                            {{ old('reference_source') == $opt['id'] ? 'selected' : '' }}>
                                                        {{ $opt['label'] }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                            @endif
                                        </select>
                                        @error('reference_source')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="manual_balance" class="form-label fw-bold">Saldo Kas Awal (Rp)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="manual_balance" name="manual_balance" 
                                                   value="{{ old('manual_balance', 0) }}" 
                                                   min="0" step="1" readonly>
                                        </div>
                                        <div class="form-text" id="balanceHelp">Otomatis terkunci jika memilih referensi.</div>
                                    </div>
                                    
                                    <!-- <div class="col-md-4">
                                        <label for="custom_month_name" class="form-label fw-bold">Bulan yang Akan Dibuat</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white">
                                                <i class="fas fa-calendar-plus"></i>
                                            </span>
                                            <input type="text" class="form-control fw-bold" id="custom_month_name" 
                                                   name="custom_month_name"
                                                   placeholder="Contoh: Desember 2025" value="{{ old('custom_month_name') }}">
                                        </div>
                                        <div class="form-text">Otomatis terisi, bisa diedit sesuai kebutuhan</div>
                                    </div> -->
                                </div>
                                
                                <!-- Custom Cash Name Field (Initially Hidden) -->
                                <div class="row mt-3" id="customCashNameRow" style="display: none;">
                                    <div class="col-md-8">
                                        <label for="custom_cash_name" class="form-label fw-bold text-primary">Nama Kas Bulanan</label>
                                        <input type="text" class="form-control" id="custom_cash_name" name="custom_cash_name" 
                                               placeholder="Contoh: Kas Awal Tahun 2026" value="{{ old('custom_cash_name') }}">
                                        <div class="form-text">Nama ini akan muncul di kartu Kelola Kas</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Period Details -->
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-calendar-alt me-2"></i>Detail Periode Baru</h6>
                                    
                                    <div class="row mb-3">
                                         <div class="col-md-6">
                                            <label for="period_name" class="form-label">Nama Periode</label>
                                            <input type="text" class="form-control" id="period_name" name="period_name" 
                                                   placeholder="Contoh: 'Januari 2026'" value="{{ old('period_name') }}" required>
                                        </div>
                                             @error('management_ids')
                                                 <div class="invalid-feedback d-block">{{ $message }}</div>
                                             @enderror
                                         </div>
                                     </div>

                                     <div class="row mb-3">
                                         <div class="col-md-6">
                                             <label for="period_start" class="form-label">Tanggal Mulai</label>
                                             <input type="date" class="form-control" id="period_start" name="period_start" 
                                                    value="{{ old('period_start') }}" required>
                                         </div>
                                         <div class="col-md-6">
                                             <label for="period_end" class="form-label">Tanggal Selesai</label>
                                             <input type="date" class="form-control" id="period_end" name="period_end" 
                                                    value="{{ old('period_end') }}" required>
                                         </div>
                                     </div>

                                     <div class="row mb-3">
                                         <div class="col-md-6">
                                             <label for="bid_deadline" class="form-label">Batas Waktu Lelang</label>
                                             <input type="datetime-local" class="form-control @error('bid_deadline') is-invalid @enderror" 
                                                    id="bid_deadline" name="bid_deadline" value="{{ old('bid_deadline') }}">
                                             <div class="alert alert-warning mt-2 mb-0">
                                                 <i class="fas fa-exclamation-triangle me-2"></i>
                                                 <strong>Peringatan:</strong> Peserta tidak dapat memasukkan nilai lelang setelah waktu ini. 
                                                 Pastikan batas waktu memberikan kesempatan cukup bagi semua peserta.
                                             </div>
                                             @error('bid_deadline')
                                                 <div class="invalid-feedback">{{ $message }}</div>
                                             @enderror
                                         </div>
                                         
                                     </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="motor_slots" class="form-label">Jumlah Slot Motor</label>
                                            <input type="text" class="form-control" id="motor_slots_display" readonly 
                                                   value="1 Motor" style="background-color: #e9ecef;">
                                            <input type="hidden" id="motor_slots" name="motor_slots" value="1">
                                            <div class="form-text">Otomatis terpilih 2 motor jika saldo > Rp 17.500.000. Otomatis terpilih 1 motor jika saldo < Rp 17.500.000.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="1">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('admin.groups.periods', $group->id) }}" class="btn btn-secondary w-100">
                                        <i class="fas fa-times me-2"></i>
                                        Batal
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-save me-2"></i>
                                        Simpan & Buka Kas Otomatis
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Kelompok
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Nama Kelompok:</strong><br>
                                {{ $group->name }}
                            </div>
                            <div class="col-md-3">
                                <strong>Total Peserta:</strong><br>
                                {{ $group->participants->count() }}
                            </div>
                            <div class="col-md-3">
                                <strong>Iuran Bulanan:</strong><br>
                                Rp {{ number_format($group->monthly_installment, 0, ',', '.') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Hadiah Utama:</strong><br>
                                Rp {{ number_format($group->main_prize, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Mode Warning Modal -->
    <div class="modal fade" id="manualModeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian: Mode Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda memilih opsi <strong>Input Manual / Periode Pertama</strong>.</p>
                    <p>Mohon pastikan angka <strong>Saldo Kas Awal</strong> yang Anda masukkan sudah benar dan sesuai dengan catatan fisik/buku.</p>
                    <p>Saldo ini akan menjadi dasar perhitungan untuk bulan-bulan berikutnya dan tidak bisa diubah dengan mudah setelah disimpan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Saya Mengerti</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date for period_start to today
            // const today = new Date().toISOString().split('T')[0];
            // document.getElementById('period_start').setAttribute('min', today);

            // Update period_end minimum date when period_start changes
            document.getElementById('period_start').addEventListener('change', function() {
                const startDate = this.value;
                document.getElementById('period_end').setAttribute('min', startDate);
            });

            // Reference Source Logic
            const referenceSelect = document.getElementById('reference_source');
            const manualBalanceInput = document.getElementById('manual_balance');
            const helpText = document.getElementById('balanceHelp');
            const manualModal = new bootstrap.Modal(document.getElementById('manualModeModal'));
            const customMonthNameInput = document.getElementById('custom_month_name');

            function handleReferenceChange() {
                const selectedOption = referenceSelect.options[referenceSelect.selectedIndex];
                const value = referenceSelect.value;
                const customNameRow = document.getElementById('customCashNameRow');
                
                if (value === 'manual') {
                    // Manual Mode
                    manualBalanceInput.readOnly = false;
                    manualBalanceInput.value = 0;
                    manualBalanceInput.focus();
                    helpText.textContent = "Silakan masukkan saldo awal secara manual.";
                    helpText.className = "form-text text-primary fw-bold";
                    
                    // Show Custom Name Field
                    if(customNameRow) customNameRow.style.display = 'flex';
                    
                    // Show warning modal
                    manualModal.show();
                    
                    // Auto-select motor slots based on manual balance
                    updateMotorSlots(0); // Default to 1 motor for manual entry
                    
                    // Update next month display
                    const today = new Date();
                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    customMonthNameInput.value = monthNames[today.getMonth()] + ' ' + today.getFullYear();
                    
                } else if (value) {
                    // Period Reference Mode
                    const balance = selectedOption.getAttribute('data-balance');
                    const nextMonth = selectedOption.getAttribute('data-next-month');
                    
                    manualBalanceInput.readOnly = true;
                    manualBalanceInput.value = balance;
                    helpText.textContent = "Saldo otomatis diambil dari periode terpilih.";
                    helpText.className = "form-text text-success";
                    
                    // Show Custom Name Field (Always allowed now)
                    if(customNameRow) customNameRow.style.display = 'flex';
                    
                    // Auto-select motor slots based on balance
                    updateMotorSlots(parseFloat(balance));
                    
                    // Update next month display
                    customMonthNameInput.value = nextMonth || '';
                } else {
                    // No selection
                    customMonthNameInput.value = '';
                }
            }
            
            // Function to update motor slots based on balance
            function updateMotorSlots(balance) {
                const motorSlotsHidden = document.getElementById('motor_slots');
                const motorSlotsDisplay = document.getElementById('motor_slots_display');
                const threshold = 17500000; // Rp 17.500.000
                
                if (balance >= threshold) {
                    motorSlotsHidden.value = '2';
                    motorSlotsDisplay.value = '2 Motor';
                } else {
                    motorSlotsHidden.value = '1';
                    motorSlotsDisplay.value = '1 Motor';
                }
            }
            
            // Also update motor slots when manual balance changes
            manualBalanceInput.addEventListener('input', function() {
                if (referenceSelect.value === 'manual') {
                    const balance = parseFloat(this.value) || 0;
                    updateMotorSlots(balance);
                }
            });

            referenceSelect.addEventListener('change', handleReferenceChange);
            
            // Trigger on load if validation failed and value persisted
            if (referenceSelect.value) {
                handleReferenceChange();
            }
        });
    </script>


@endsection

