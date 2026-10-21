## Pengujian Manual Autentikasi dan Otorisasi

### 1. Pengujian Admin
- Login menggunakan admin@pos.test.
- Buka halaman POS, transaksi, produk, dan kategori.
- Hasil yang diharapkan: semua halaman dapat diakses.

### 2. Pengujian Kasir
- Login menggunakan kasir@pos.test.
- Buka halaman POS dan transaksi.
- Coba buka halaman produk dan kategori.
- Hasil yang diharapkan: POS dan transaksi dapat diakses, sedangkan produk dan kategori menghasilkan 403.

### 3. Pengujian Manager
- Login menggunakan manager@pos.test.
- Buka halaman transaksi.
- Coba buka halaman produk dan kategori.
- Hasil yang diharapkan: transaksi dapat diakses, sedangkan produk dan kategori menghasilkan 403.

### 4. Pengujian Guest
- Logout dari sistem.
- Buka halaman /informasi dan /login.
- Hasil yang diharapkan: kedua halaman dapat diakses.
- Login kembali, lalu buka /informasi dan /login.
- Hasil yang diharapkan: pengguna diarahkan ke /pos.



# JOBSHEET 9

## Analisis Proses Lain yang Layak Dipindahkan ke Queued Job

### 1. Pembuatan & Pengiriman Laporan Export Skala Besar (Excel / PDF Bulanan & Tahunan)
- **Proses Saat Ini:** Pembuatan file Excel (`SalesReportExport`) dan PDF (`DomPDF`) dilakukan secara *synchronous* (langsung di dalam siklus HTTP request saat tombol diklik).
- **Masalah:** Ketika data transaksi mencapai puluhan ribu hingga ratusan ribu baris (misalnya rekap tahunan), proses penulisan XML spreadsheet (`.xlsx`) atau perenderan HTML ke PDF membutuhkan waktu komputasi dan memori yang besar. Hal ini berisiko menyebabkan *HTTP Request Timeout* (504 Gateway Timeout) dan membuat browser kasir/admin menggantung.
- **Alasan Dipindah ke Queued Job:** Dengan memindahkan proses *generate* laporan ke Queued Job (например `GenerateSalesReportJob` atau menggunakan fitur `ShouldQueue` bawaan Laravel Excel), server dapat langsung merespons admin dengan pesan *"Laporan sedang disiapkan, link unduhan akan tersedia/dikirim setelah selesai"*. Worker di background akan memproses file tersebut dengan tenang tanpa memblokir *thread* web server utama.

### 2. Pengiriman Struk Digital (Email / WhatsApp) Setelah Checkout Kasir (`TransactionController::store`)
- **Proses Saat Ini:** Saat kasir menekan tombol simpan transaksi di `/pos`, sistem menyimpan transaksi ke database dan langsung mengembalikan halaman kasir.
- **Alasan Dipindah ke Queued Job:** Jika Simple POS dikembangkan untuk mengirimkan struk digital ke pelanggan (via SMTP Email atau API WhatsApp Gateway) maupun sinkronisasi stok ke marketplace eksternal, pemanggilan API pihak ketiga secara langsung akan memperlambat proses checkout kasir 2–5 detik per transaksi (atau gagal total jika jaringan API sedang lambat). Dengan mendispatch job `SendDigitalReceiptJob` ke queue setelah transaksi tersimpan di DB, kasir bisa langsung melayani antrean pelanggan berikutnya dalam hitungan milidetik, sementara pengiriman struk dikerjakan di background lengkap dengan mekanisme `$tries` dan `$backoff` jika server email/WhatsApp sedang gangguan.