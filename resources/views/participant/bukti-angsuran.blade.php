@extends('layouts.participant')

@section('title', 'Bukti Angsuran - Sistem Arisan')

@section('content')
    <h2>
        <i class="fas fa-file-invoice me-2"></i>
        Bukti Angsuran
    </h2>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-receipt me-2"></i>
                Riwayat Pembayaran
            </h5>
        </div>
        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th>Kelompok</th>
                                <th>Periode</th>
                                <th class="text-center">Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $payment->participant && $payment->participant->group ? $payment->participant->group->name : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $payment->monthlyPeriod ? $payment->monthlyPeriod->period_name : '-' }}</div>
                                        @if($payment->cash_flow_name)
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                Kas: {{ $payment->cash_flow_name }}
                                            </small>
                                        @endif
                                        <div class="text-muted small">
                                            {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->locale('id')->translatedFormat('F Y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($payment->receipt_number)
                                            <a href="{{ route('participant.participant.receipt', $payment->id) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-primary"
                                               title="Lihat Bukti">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-chart-pie me-2"></i>Ringkasan
                                    </h6>
                                    <p class="mb-1">
                                        <strong>Total Pembayaran:</strong> 
                                        <span class="text-success">{{ $payments->count() }} kali</span>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Total Jumlah:</strong> 
                                        <span class="text-primary">Rp {{ number_format($payments->sum('amount'), 0, ',', '.') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle me-2"></i>Informasi
                                    </h6>
                                    <p class="mb-1">
                                        <strong>Nama:</strong> {{ $participant->name }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>No. Undian:</strong> {{ $participant->lottery_number }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>Kelompok:</strong> {{ $participant->group->name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Bukti Angsuran</h5>
                    <p class="text-muted">Anda belum memiliki riwayat pembayaran yang dikonfirmasi.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
