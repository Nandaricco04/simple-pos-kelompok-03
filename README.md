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