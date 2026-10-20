# Pengukuran Pemakaian Memori: `get()` vs `lazyById()`

# Tujuan
Membandingkan penggunaan memori antara pengambilan seluruh data menggunakan `get()` dan pengambilan data secara bertahap menggunakan `lazyById()` pada Laravel.

# Data Pengujian
- Tabel: `transactions`
- Jumlah data: 52.500 baris
- Lingkungan: Laravel dengan PHP 8.4.24
- Metode pengukuran: `memory_get_peak_usage()`

# Hasil Pengukuran
| Metode        | Jumlah Baris  | Peak Memory   |
| `lazyById()`  | 52.500        | 40,2 MB       |
| `get()`       | 52.500        | 98,5 MB       |

# Analisis
Metode `lazyById()` menggunakan peak memory sebesar 40,2 MB, sedangkan `get()` menggunakan 98,5 MB pada pengujian ini. Dengan demikian, `lazyById()` menggunakan peak memory sekitar 59,2% lebih rendah dibandingkan `get()`.
Hal ini menunjukkan bahwa pengambilan data secara bertahap menggunakan `lazyById()` lebih hemat memori pada pengujian tersebut dibandingkan memuat seluruh data sekaligus menggunakan `get()`.

# Kesimpulan
Untuk pengolahan data dalam jumlah besar, `lazyById()` dapat menjadi pilihan yang lebih efisien dalam penggunaan memori. Hasil pengukuran ini berlaku untuk lingkungan dan data pengujian yang digunakan.