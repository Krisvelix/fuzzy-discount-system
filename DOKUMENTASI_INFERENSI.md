# Dokumentasi Rule Base, Inferensi Mamdani, dan Defuzzifikasi

## 1. Penanggung Jawab
Anggota 2: Rule Base (27 Aturan), Inferensi Mamdani (MIN-MAX), dan Defuzzifikasi Centroid.

## 2. File yang Dibuat
- `app/Services/Fuzzy/RuleBase.php`
- `app/Services/Fuzzy/MamdaniInference.php`
- `tests/Unit/MamdaniInferenceTest.php`

## 3. Himpunan Output Diskon
Domain: `0% – 50%`
- **Rendah:** Trapesium `(0, 0, 10, 20)`
- **Sedang:** Segitiga `(15, 25, 35)`
- **Tinggi:** Trapesium `(30, 40, 50, 50)`

## 4. Cara Menggunakan oleh Backend (Anggota 3)
Panggil satu method utama berikut pada Controller dengan mengirimkan data input mentah produk:

```php
use App\Services\Fuzzy\MamdaniInference;

$hasil = MamdaniInference::calculateDiscount($stok,          // contoh: 100
    $lamaSimpan,    // contoh: 120$penjualan      // contoh: 10
);