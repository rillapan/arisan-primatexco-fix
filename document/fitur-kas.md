fitur kelola kas

1. Halaman Utama: Daftar Arus Kas Bulanan
Bentuknya adalah tabel kronologis (seperti yang sudah dibuat, tapi dengan kolom yang lebih bermakna).
Header Tabel: Bulan, Peserta (90), Total Iuran, Nilai Lelang, Pengeluaran (Motor + SHU), Sisa Bersih, dan Akumulasi Saldo.
Visualisasi: Setiap baris mewakili satu bulan. Jika sebuah bulan memiliki akumulasi saldo di atas Rp17.500.000, baris tersebut akan berwarna hijau muda dengan ikon 🔥 (Indikator Percepatan).

2. Panel Detail Kas (Modal Pop-up)
Ketika Admin klik "Lihat Detail" di salah satu bulan, muncul jendela yang membagi arus uang menjadi dua sisi agar mudah dipahami:
Sisi Kiri (Inflow/Masuk):
Iuran Kolektif: Rp15.750.000 (Otomatis).
Nilai Lelang (Bid): Rp6.000.000 (Input saat undian).
Subtotal Masuk: Rp21.750.000.

Sisi Kanan (Outflow/Keluar):
Harga Motor: Rp17.500.000 (Otomatis).
Biaya Admin (SHU): Rp500.000 (Otomatis).
Subtotal Keluar: Rp18.000.000.

Bagian Bawah (Hasil Akhir):
Sisa Bersih: Rp3.750.000.
Total Kas Berjalan: (Saldo Bulan Lalu + Rp3.750.000).

3. Logika "The Smart Notification"
Ini adalah bentuk fitur yang paling penting untuk membantu Admin mengambil keputusan:

Kondisi A (Normal): Saldo akumulasi masih Rp11.000.000. Sistem menampilkan pesan: "Status: Normal. Target percepatan kurang Rp6.500.000."

Kondisi B (Siap Percepatan): Saldo akumulasi mencapai Rp18.500.000. Sistem menampilkan pesan besar berwarna biru: "🔥 KAS MENCUKUPI! Bulan depan Anda bisa mengeluarkan 2 motor."

Kondisi C (Eksekusi 2 Motor): Saat admin membuat periode baru di bulan berikutnya, sistem akan secara otomatis memunculkan dua slot input pemenang (Pemenang A dan Pemenang B).

4. Ringkasan di Sidebar (Widget Kas)
Agar admin tidak perlu selalu masuk ke menu Kelola Kas, di sidebar atau pojok dashboard selalu tampil "Tabungan Kas" saat ini.

Contoh Tampilan Widget: 💰 Tabungan Kas Lelang Rp11.250.000 (Butuh Rp6.250.000 lagi untuk 2 motor)

Mengapa Bentuk Ini Sangat Efektif?
Transparan: Pengurus bisa melihat dengan jelas dari mana asal uang "motor tambahan" tersebut.

Otomatis: Admin tidak perlu menghitung pakai kalkulator manual; cukup input nilai lelang, semua angka akumulasi di bulan-bulan berikutnya akan bergeser otomatis.

Meminimalisir Human Error: Karena iuran (90 orang), biaya SHU (500rb), dan harga motor (17,5jt) sudah dikunci sebagai variabel, Admin hanya perlu fokus pada satu angka variabel saja: Nilai Lelang.

Apakah Anda ingin saya buatkan draf "Logic Flow" dalam bentuk diagram sederhana agar Anda bisa menjelaskan ini ke atasan atau tim Anda?