@extends('layouts.participant')

@section('title', 'Syarat & Ketentuan - Sistem Arisan')

@push('styles')
<style>
    .terms-wrapper {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    .terms-paper {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .terms-header-top {
        padding: 3rem 2rem;
        background-color: #f8fafc;
        border-bottom: 2px solid #334155;
        text-align: center;
    }
    .company-name {
        letter-spacing: 2px;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .doc-title {
        color: #1e293b;
        font-weight: 800;
        margin-top: 0.5rem;
        font-size: 1.75rem;
        text-transform: uppercase;
    }
    .doc-subtitle {
        color: #475569;
        font-weight: 500;
        margin-bottom: 0;
    }
    .terms-content {
        padding: 3rem;
    }
    .section-block {
        margin-bottom: 3.5rem;
    }
    .section-header {
        display: flex;
        align-items: baseline;
        border-bottom: 1px solid #cbd5e1;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
    }
    .section-letter {
        font-weight: 800;
        color: #334155;
        font-size: 1.25rem;
        margin-right: 1rem;
    }
    .section-name {
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }
    .terms-ol {
        padding-left: 1.5rem;
        margin-bottom: 0;
    }
    .terms-ol li {
        margin-bottom: 1rem;
        color: #334155;
        line-height: 1.7;
    }
    .nested-ul {
        list-style-type: none;
        padding-left: 0;
        margin-top: 0.75rem;
    }
    .nested-ul li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        color: #475569;
    }
    .nested-ul li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #94a3b8;
    }
    .data-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin: 1.5rem 0;
    }
    .data-card {
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #fdfdfd;
    }
    .data-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        display: block;
    }
    .data-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }
    .formula-box {
        background: #f1f5f9;
        padding: 1rem;
        border-radius: 4px;
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        color: #1e293b;
        text-align: center;
        border: 1px dashed #cbd5e1;
        margin-top: 1rem;
    }
    .footer-note {
        text-align: center;
        padding: 2rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        color: #94a3b8;
        font-size: 0.85rem;
    }
    @media (max-width: 768px) {
        .terms-content { padding: 1.5rem; }
        .data-grid { grid-template-columns: 1fr; }
        .doc-title { font-size: 1.4rem; }
    }
</style>
@endpush

@section('content')
<div class="terms-wrapper">
    <div class="terms-paper">
        <div class="terms-header-top">
            <div class="company-name">PT. PRIMATEXCO INDONESIA</div>
            <h1 class="doc-title">Syarat & Ketentuan</h1>
            <p class="doc-subtitle">Arisan Bulanan Sistem Lelang</p>
        </div>
        
        <div class="terms-content">
            
            <!-- A -->
            <div class="section-block">
                <div class="section-header">
                    <span class="section-letter">A.</span>
                    <span class="section-name">Ketentuan Umum</span>
                </div>
                <ol class="terms-ol">
                    <li>Arisan bulanan ini diselenggarakan oleh PT. PRIMATEXCO INDONESIA dengan sistem lelang (bid).</li>
                    <li>Peserta arisan adalah karyawan aktif PT. PRIMATEXCO INDONESIA yang telah terdaftar secara resmi.</li>
                    <li>Setiap peserta akan mendapatkan Nomor Undian (No. Undian) yang bersifat tetap selama periode arisan.</li>
                    <li>Program arisan berlangsung hingga seluruh peserta dalam satu kelompok memperoleh giliran menang.</li>
                </ol>
            </div>

            <!-- B -->
            <div class="section-block">
                <div class="section-header">
                    <span class="section-letter">B.</span>
                    <span class="section-name">Jumlah Peserta dan Pemenang</span>
                </div>
                <ol class="terms-ol">
                    <li>Jumlah peserta dalam satu kelompok adalah maksimal 90 (sembilan puluh) orang.</li>
                    <li>Jumlah pemenang setiap bulan ditentukan berdasarkan kondisi kas bulan sebelumnya:
                        <ul class="nested-ul">
                            <li>1 (satu) pemenang, apabila jumlah sisa kas bulan lalu lebih kecil dari Hadiah Utama.</li>
                            <li>2 (dua) pemenang, apabila jumlah sisa kas bulan lalu lebih besar dari Hadiah Utama.</li>
                        </ul>
                    </li>
                </ol>
            </div>

            <!-- C -->
            <div class="section-block">
                <div class="section-header">
                    <span class="section-letter">C.</span>
                    <span class="section-name">Sistem Lelang (Bid)</span>
                </div>
                <ol class="terms-ol">
                    <li>Setiap bulan, peserta diberi kesempatan untuk mengajukan harga lelang (bid) guna mengikuti proses undian.</li>
                    <li>Ketentuan nominal bid:
                        <ul class="nested-ul">
                            <li>Minimal bid: <strong>Rp 2.250.000</strong></li>
                            <li>Maksimal bid: <strong>Rp 6.000.000</strong></li>
                        </ul>
                    </li>
                    <li>Nilai bid akan mengurangi Hadiah Utama yang diterima pemenang.</li>
                    <div class="formula-box">
                        HADIAH DITERIMA = HADIAH UTAMA – NILAI BID
                    </div>
                </ol>
            </div>

            <!-- D -->
            <div class="section-block">
                <div class="section-header">
                    <span class="section-letter">D.</span>
                    <span class="section-name">Penentuan Pemenang</span>
                </div>
                <ol class="terms-ol">
                    <li>Admin akan mengurutkan seluruh bid dari nilai tertinggi ke terendah.</li>
                    <li>Nilai bid yang paling tinggi akan menjadi pemenang.</li>
                    <li>Jika hanya 1 peserta memiliki bid tertinggi, maka langsung ditetapkan sebagai pemenang.</li>
                    <li>Jika terdapat lebih dari 1 peserta dengan bid tertinggi yang sama, maka dilakukan proses undian.</li>
                    <li>Proses undian dilakukan secara transparan menggunakan sistem spinner digital.</li>
                    <li>Keputusan pemenang bersifat final dan tidak dapat diganggu gugat.</li>
                </ol>
            </div>

            <!-- E -->
            <div class="section-block">
                <div class="section-header">
                    <span class="section-letter">E.</span>
                    <span class="section-name">Ketentuan Peserta Pemenang</span>
                </div>
                <ol class="terms-ol">
                    <li>Peserta yang telah menang tidak dapat mengikuti lelang kembali di bulan berikutnya.</li>
                    <li>Pemenang tetap wajib membayar angsuran bulanan hingga seluruh periode arisan berakhir.</li>
                    <li>Hak menang hanya berlaku satu kali untuk setiap nomor undian selama satu periode arisan.</li>
                </ol>
            </div>

            <!-- F -->
            <div class="section-block">
                <div class="section-header">
                    <span class="section-letter">F.</span>
                    <span class="section-name">Komponen Keuangan</span>
                </div>
                <div class="data-grid">
                    <div class="data-card">
                        <span class="data-label">Hadiah Utama</span>
                        <div class="data-value">Rp 17.500.000</div>
                    </div>
                    <div class="data-card">
                        <span class="data-label">Angsuran Bulanan</span>
                        <div class="data-value">Rp 175.000</div>
                    </div>
                </div>
                <ol class="terms-ol">
                    <li>SHU (Sisa Hasil Usaha) sebesar Rp 500.000 per pemenang.</li>
                    <li>Total setoran angsuran per bulan: 90 peserta × Rp 175.000.</li>
                    <li>Mekanisme pembayaran dilakukan melalui sistem potong gaji otomatis.</li>
                </ol>
            </div>

            <!-- G & H -->
            <div class="section-block">
                <div class="section-header">
                    <span class="section-letter">G.</span>
                    <span class="section-name">Hak & Kewajiban Peserta</span>
                </div>
                <ol class="terms-ol">
                    <li>Setiap peserta berhak mendapatkan informasi yang transparan terkait rincian kas, bid, dan pemenang.</li>
                    <li>Peserta wajib menjaga kerahasiaan akun dan password masing-masing.</li>
                    <li>Dilarang keras melakukan manipulasi data, kecurangan dalam bid, atau tindakan yang merugikan kelancaran program.</li>
                </ol>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('participant.dashboard') }}" class="btn btn-dark px-5">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
        
        <div class="footer-note">
            Dokumen ini merupakan standar operasional resmi Arisan PT. PRIMATEXCO INDONESIA.
        </div>
    </div>
</div>
@endsection
