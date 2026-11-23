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
php artisan migrate
npm run build
```

## Menjalankan Aplikasi

```bash
php artisan serve
```

Kemudian buka `http://127.0.0.1:8000`.

## Dokumentasi Laravel

Aplikasi ini dibangun menggunakan Laravel. Dokumentasi resmi Laravel dapat diakses melalui https://laravel.com/docs.

