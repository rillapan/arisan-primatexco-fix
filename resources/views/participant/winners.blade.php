@extends('layouts.participant')

@section('title', 'Daftar Pemenang - Sistem Arisan')

@section('content')
    <h2>
        <i class="fas fa-trophy me-2"></i>
        Daftar Pemenang
    </h2>

    <!-- Winners List -->
    <div class="card shadow-sm border-0">
        <div class="card-header  py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-trophy me-2 text-warning"></i>
                Daftar Pemenang Arisan
            </h5>
        </div>
        <div class="card-body p-0">
            @if(count($winners) > 0)
                <!-- Desktop Table -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Periode</th>
                                <th>No. Undian</th>
                                <th>Nama Pemenang</th>
                                <th>Shift</th>
                                <th>Hadiah</th>
                                <th class="pe-4">Waktu Undi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($winners as $winner)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-primary px-3">{{ $winner->monthlyPeriod->period_name }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $winner->participant->lottery_number }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $winner->participant->name }}</div>
                                    @if($winner->participant->id === Auth::guard('participant')->user()->id)
                                        <span class="badge bg-success small">Anda</span>
                                    @endif
                                </td>
                                <td>{{ $winner->participant->shift }}</td>
                                <td>
                                    <span class="text-success fw-bold">
                                        Rp {{ number_format($winner->final_prize, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <div class="small fw-bold">{{ $winner->draw_time->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $winner->draw_time->format('H:i') }} WIB</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View -->
                <div class="d-md-none">
                    @foreach($winners as $winner)
                        <div class="p-3 border-bottom {{ $winner->participant->id === Auth::guard('participant')->user()->id ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary">{{ $winner->monthlyPeriod->period_name }}</span>
                                <span class="badge bg-light text-dark border">{{ $winner->participant->lottery_number }}</span>
                            </div>
                            <div class="mb-2">
                                <div class="fw-bold fs-6 text-dark">
                                    {{ $winner->participant->name }}
                                    @if($winner->participant->id === Auth::guard('participant')->user()->id)
                                        <span class="badge bg-success ms-1 small">Anda</span>
                                    @endif
                                </div>
                                <small class="text-muted">Shift: {{ $winner->participant->shift }}</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-success fw-bold">
                                    Rp {{ number_format($winner->final_prize, 0, ',', '.') }}
                                </div>
                                <div class="text-end small text-muted">
                                    {{ $winner->draw_time->format('d M Y') }}<br>
                                    {{ $winner->draw_time->format('H:i') }} WIB
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state py-5 text-center">
                    <i class="fas fa-trophy fa-3x text-light mb-3"></i><br>
                    <p class="text-muted">Belum ada pemenang yang diumumkan</p>
                </div>
            @endif
        </div>
    </div>

@endsection
