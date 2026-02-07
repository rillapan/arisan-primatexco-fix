@extends('layouts.participant')

@section('title', 'Hubungi Kami - Sistem Arisan')

@section('content')
    <div class="mb-4">
        <h2><i class="fas fa-headset me-2"></i>Hubungi Kami</h2>
        <p class="text-muted">Pilih Customer Service yang ingin Anda hubungi via WhatsApp.</p>
    </div>

    <div class="row">
        @forelse($customerServices as $cs)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0 border-top border-primary border-4">
                    <div class="card-body text-center py-4">
                        @if($cs->photo)
                            <div class="mb-3">
                                <img src="{{ asset('uploads/customer_service/' . $cs->photo) }}" class="rounded-circle shadow-sm border" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        @else
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-user-tie fa-3x text-primary"></i>
                            </div>
                        @endif
                        <h5 class="fw-bold mb-1">{{ $cs->name }}</h5>
                        <p class="text-muted mb-4">Customer Service Aktif</p>
                        
                        @php
                            $waNumber = preg_replace('/[^0-9]/', '', $cs->whatsapp_number);
                            $message = "Halo, saya " . $participant->name . "\n" .
                                      "NIK          : " . $participant->nik . "\n" .
                                      "No Undian    : " . $participant->lottery_number . "\n" .
                                      "Kelompok     : " . $participant->group->name . "\n" .
                                      "Ingin menyampaikan:";
                            $encodedMessage = rawurlencode($message);
                            $waLink = "https://wa.me/" . $waNumber . "?text=" . $encodedMessage;
                        @endphp

                        <a href="{{ $waLink }}" target="_blank" class="btn btn-success w-100 py-2">
                            <i class="fab fa-whatsapp me-2"></i>Hubungi via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <h5>Belum Ada Customer Service Tersedia</h5>
                        <p class="text-muted mb-0">Maaf, saat ini belum ada layanan Customer Service yang aktif.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="card shadow-sm border-0 mt-4 bg-light">
        <div class="card-body">
            <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-1"></i>Informasi</h6>
            <p class="small text-muted mb-0">
                Klik tombol di atas untuk membuka chat WhatsApp dengan Customer Service kami. 
                Pesan otomatis akan berisi informasi akun Anda untuk memudahkan proses layanan.
            </p>
        </div>
    </div>
@endsection
