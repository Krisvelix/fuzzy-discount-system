# Fuzzy Discount System

**Web Application Sistem Rekomendasi Diskon Produk Menggunakan Metode Fuzzy Mamdani**

Project ini merupakan aplikasi rekomendasi diskon produk menggunakan metode Fuzzy Mamdani untuk membantu menentukan besaran diskon berdasarkan kondisi stok, lama penyimpanan, dan tingkat penjualan produk.

## Teknologi yang Digunakan

- Laravel
- PHP
- SQLite
- JavaScript
- Vite
- Bootstrap (untuk antarmuka aplikasi)

## Metode

Fuzzy Mamdani digunakan untuk mengolah variabel input menjadi rekomendasi persentase diskon berdasarkan aturan fuzzy yang telah ditentukan.

## Instalasi Project

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm run build
```

Jika menggunakan Windows PowerShell, ganti `cp` dengan `Copy-Item .env.example .env` dan `touch` dengan `New-Item database/database.sqlite -ItemType File`.

Gunakan PHP CLI 8.4.1 atau lebih baru sesuai versi paket di `composer.lock`, dan pastikan ekstensi `pdo_sqlite` aktif. Aplikasi memakai koneksi SQLite dan file `database/database.sqlite` secara default.

## Backend Produk dan Rekomendasi

Backend menyediakan API JSON dengan prefix `/api`:

| Method | Endpoint | Fungsi |
| --- | --- | --- |
| GET | `/api/products` | Daftar produk (15 per halaman) |
| POST | `/api/products` | Membuat produk |
| GET | `/api/products/{product}` | Detail produk |
| PUT/PATCH | `/api/products/{product}` | Mengubah produk |
| DELETE | `/api/products/{product}` | Menghapus produk beserta riwayatnya |
| POST | `/api/products/{product}/discount-calculations` | Menghitung diskon dan menyimpan riwayat |
| GET | `/api/products/{product}/discount-histories` | Riwayat perhitungan produk (15 per halaman) |

Payload produk menggunakan `name`, `stock` (0–100), `storage_duration` (0–180 hari), dan `monthly_sales` (0–100). Respons mengikuti format resource Laravel (`data`), sedangkan kegagalan validasi API mengembalikan HTTP 422 dengan rincian error per field.

`FuzzyDiscountService` adalah batas integrasi backend. Binding Laravel saat ini memakai `MamdaniFuzzyDiscountService`, adaptor yang memanggil `MamdaniInference::calculateDiscount()` yang sudah ada dan mengambil nilai `discount`. Algoritma fuzzy tidak diduplikasi atau diubah. Jika implementasi anggota kelompok kelak dipindahkan, hubungkan implementasinya melalui interface/binding tersebut; nilai di luar 0–50 ditolak dan tidak disimpan sebagai riwayat.

## Menjalankan Aplikasi

```bash
php artisan serve
```

Kemudian buka `http://127.0.0.1:8000`.

Contoh membuat produk:

```json
{
  "name": "Produk Contoh",
  "stock": 40,
  "storage_duration": 90,
  "monthly_sales": 50
}
```

## Dokumentasi Laravel

Aplikasi ini dibangun menggunakan Laravel. Dokumentasi resmi Laravel dapat diakses melalui https://laravel.com/docs.

