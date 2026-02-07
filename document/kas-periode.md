				Acuan Saldo Awal
1. Mekanisme Dropdown Dinamis (Cross-Year)
Dropdown "Bulan Sebelumnya" tidak lagi berisi daftar statis, melainkan daftar dinamis yang mengambil data dari seluruh riwayat kas yang tersimpan di database.
Pengelompokan (Grouping): Daftar diurutkan berdasarkan tahun terbaru ke terlama (misal: 2026, 2025, 2024) untuk memudahkan pencarian lintas tahun.
Informasi Terintegrasi: Setiap pilihan bulan menampilkan Nama Bulan, Tahun, dan Total Akumulasi Terakhir secara otomatis.
Otomatisasi: Saat Admin memilih satu entri (misal: "Desember 2025"), sistem akan langsung mengunci nilai tersebut sebagai saldo pembuka untuk periode baru (Januari 2026).

2. Fitur Kustomisasi "Input Manual"
Untuk memberikan fleksibilitas penuh (misal: memulai data dari tahun yang jauh di belakang atau migrasi data manual), sistem menyediakan opsi kustom:
Opsi "Input Saldo Baru": Admin dapat memilih opsi ini jika bulan/tahun yang diinginkan belum ada di sistem.
Pembukaan Kunci (Unlock Field): Memilih opsi manual akan membuka kolom "Saldo Kas Awal" yang tadinya terkunci (ReadOnly) menjadi bisa diisi secara bebas oleh Admin.
Penamaan Kustom: Admin tetap bisa mengetikkan Nama Periode (Bulan dan Tahun) secara manual pada kolom nama periode.

Meskipun bulan dan tahun dipilih secara kustom, rumus akumulasi tetap mengikuti standar yang telah disepakati:
Dana Iuran Bersih: (Setoran Kotor - SHU).
Sisa Kas Tersedia: (Iuran Bersih + Bid) - Harga Motor.
Akumulasi Akhir: Saldo Acuan (dari dropdown kustom) + Sisa Kas Tersedia.

Dengan pendekatan ini, sistem memiliki "jangkauan luas" karena tidak terpaku pada satu tahun kalender saja dan memungkinkan Admin untuk menyambungkan saldo dari periode mana pun di masa lalu ke periode baru mana pun di masa depan.


Berikut adalah urutan kerjanya secara ringkas:

jika acuan saldo awal sudah ada
1.Admin Membuat Periode Baru
Admin masuk ke menu Buat Periode Baru.
Admin mengisi nama periode (misal: Januari 2026).
Admin menentukan "Acuan Saldo Awal" dengan memilih kas dari bulan sebelumnya (misal: Desember 2025).
Pada tahap ini, sistem secara otomatis menarik nilai Total Akumulasi dari bulan Desember 2025 untuk dijadikan saldo pembuka yang terkunci (ReadOnly).

2.Sistem Membuat Kas Otomatis
Begitu tombol "Simpan & Buka Kas" diklik, sistem secara cerdas langsung membuatkan satu baris data baru di tabel Kelola Kas untuk bulan tersebut.
Admin tidak perlu lagi menginput bulan secara manual di menu Kas.

3.Fleksibilitas Kustomisasi
Dropdown acuan saldo dibuat dinamis agar bisa memilih bulan dan tahun secara bebas (lintas tahun).
Jika arisan baru dimulai, tersedia opsi "Input Manual/Periode Awal" yang membuka kunci kolom saldo awal agar bisa diisi mulai dari Rp 0 atau angka kustom lainnya.


jika saldo awal belum ada
1. Memilih Opsi "Periode Awal / Manual"
Pada dropdown Bulan Sebelumnya, sistem menyediakan opsi khusus di baris paling atas atau paling bawah:
Opsi: -- Periode Pertama / Input Manual --.
Fungsi: Opsi ini dipilih jika tidak ada riwayat kas di database yang bisa dijadikan acuan.

2. Membuka Kunci (Unlock) Saldo Kas Awal
Secara default, kolom Saldo Kas Awal bersifat Read-Only (terkunci). Namun, jika opsi "Periode Pertama" dipilih:
Input Menjadi Editable: Kolom Saldo Kas Awal akan terbuka secara otomatis.
Input Angka: Admin bisa mengisi angka 0 (jika benar-benar baru) atau angka tertentu (jika migrasi dari buku manual).


3. Pengisian Nama Periode Secara Kustom
Karena tidak ada acuan otomatis, Admin bebas menentukan:
Nama Periode: Misal "Januari 2026".
Tahun: (Input atau dropdown tahun bisa dipilih secara bebas).

4. Eksekusi "Simpan & Buka Kas"
Setelah data disimpan:
Tabel Periode: Terbentuk sebagai periode nomor 1 (Root).
Tabel Kelola Kas: Terbentuk otomatis dengan saldo awal sesuai yang diketik Admin.
Rantai Dimulai: Periode ini akan menjadi "Acuan Saldo Awal" untuk bulan-bulan berikutnya (Februari 2026, dst).

Ringkasan Logika untuk Admin:
"Jika bulan lalu ada di sistem, pilih saja namanya. Jika bulan lalu tidak ada (karena baru pakai aplikasi), pilih opsi manual lalu ketik sendiri saldo awalnya."
Apakah Anda ingin saya buatkan tampilan peringatan (Alert) yang muncul jika Admin memilih opsi manual agar mereka memastikan angka yang dimasukkan sudah benar?