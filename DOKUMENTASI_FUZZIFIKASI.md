# Dokumentasi Membership Function dan Fuzzifikasi

## 1. Penanggung Jawab

Anggota 1: Membership Function dan Fuzzification.

## 2. File yang Dibuat

- `app/Services/Fuzzy/MembershipFunction.php`
- `app/Services/Fuzzy/Fuzzification.php`
- `tests/Unit/FuzzificationTest.php`

## 3. Variabel Input dan Himpunan Fuzzy

### A. Stok

Domain: 0–100

| Himpunan | Bentuk Fungsi | Parameter |
|---|---|---|
| Sedikit | Trapesium | (0, 0, 20, 40) |
| Sedang | Segitiga | (20, 50, 80) |
| Banyak | Trapesium | (60, 80, 100, 100) |

### B. Lama Penyimpanan

Domain: 0–180 hari

| Himpunan | Bentuk Fungsi | Parameter |
|---|---|---|
| Baru | Trapesium | (0, 0, 30, 60) |
| Sedang | Segitiga | (30, 90, 150) |
| Lama | Trapesium | (120, 150, 180, 180) |

### C. Penjualan

Domain: 0–100

| Himpunan | Bentuk Fungsi | Parameter |
|---|---|---|
| Rendah | Trapesium | (0, 0, 20, 40) |
| Sedang | Segitiga | (20, 50, 80) |
| Tinggi | Trapesium | (60, 80, 100, 100) |

## 4. Method yang Tersedia

Class `MembershipFunction` menyediakan:

- `triangle()` untuk menghitung fungsi keanggotaan segitiga.
- `trapezoid()` untuk menghitung fungsi keanggotaan trapesium.

Class `Fuzzification` menyediakan:

- `fuzzifyStock($stock)`
- `fuzzifyStorage($days)`
- `fuzzifySales($sales)`
- `fuzzify($stock, $storageDays, $sales)`

## 5. Cara Menggunakan oleh Anggota 2

Gunakan method berikut untuk mendapatkan hasil fuzzifikasi seluruh variabel input:

```php
use App\Services\Fuzzy\Fuzzification;

$result = Fuzzification::fuzzify(50, 90, 50);
```

Hasilnya berupa array:

```php
[
    'stok' => [
        'sedikit' => 0.0,
        'sedang' => 1.0,
        'banyak' => 0.0,
    ],
    'penyimpanan' => [
        'baru' => 0.0,
        'sedang' => 1.0,
        'lama' => 0.0,
    ],
    'penjualan' => [
        'rendah' => 0.0,
        'sedang' => 1.0,
        'tinggi' => 0.0,
    ],
]
```

Nilai derajat keanggotaan berada pada rentang 0 sampai 1.

## 6. Pengujian

Pengujian dilakukan menggunakan perintah:

```bash
php artisan test --filter=FuzzificationTest
```

Hasil pengujian:

- 7 test berhasil.
- 21 assertions berhasil.

## 7. Catatan Integrasi

Anggota 2 dapat menggunakan hasil `Fuzzification::fuzzify()` sebagai input untuk proses Rule Base dan Inferensi Mamdani.

Nama key array dan nama himpunan fuzzy perlu dipertahankan agar integrasi antarkomponen berjalan konsisten.