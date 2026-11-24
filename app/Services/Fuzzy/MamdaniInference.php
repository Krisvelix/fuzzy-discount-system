<?php

namespace App\Services\Fuzzy;

class MamdaniInference
{
    /**
     * Fungsi keanggotaan variabel output Diskon (Domain: 0% - 50%).
     */
    public static function getDiscountMembership(float $z, string $category): float
    {
        return match ($category) {
            'rendah' => MembershipFunction::trapezoid($z, 0, 0, 10, 20),
            'sedang' => MembershipFunction::triangle($z, 15, 25, 35),
            'tinggi' => MembershipFunction::trapezoid($z, 30, 40, 50, 50),
            default  => 0.0,
        };
    }

    /**
     * Proses Inferensi Mamdani:
     * 1. Implikasi menggunakan operator MIN
     * 2. Agregasi menggunakan operator MAX
     */
    public static function infer(array $fuzzified): array
    {
        $rules = RuleBase::getRules();

        $aggregatedAlpha = [
            'rendah' => 0.0,
            'sedang' => 0.0,
            'tinggi' => 0.0,
        ];

        $activeRules = [];

        foreach ($rules as $index => $rule) {
            $muStok        = $fuzzified['stok'][$rule['stok']] ?? 0.0;
            $muPenyimpanan = $fuzzified['penyimpanan'][$rule['penyimpanan']] ?? 0.0;
            $muPenjualan   = $fuzzified['penjualan'][$rule['penjualan']] ?? 0.0;

            // Operator MIN untuk implikasi
            $alpha = min($muStok, $muPenyimpanan, $muPenjualan);

            if ($alpha > 0) {
                $activeRules[] = [
                    'rule_number' => $index + 1,
                    'condition'   => "IF stok {$rule['stok']} AND penyimpanan {$rule['penyimpanan']} AND penjualan {$rule['penjualan']}",
                    'output'      => $rule['diskon'],
                    'alpha'       => round($alpha, 4),
                ];

                // Operator MAX untuk agregasi himpunan output yang sama
                $outputSet = $rule['diskon'];
                if ($alpha > $aggregatedAlpha[$outputSet]) {
                    $aggregatedAlpha[$outputSet] = $alpha;
                }
            }
        }

        return [
            'aggregated_alpha' => $aggregatedAlpha,
            'active_rules'     => $activeRules,
        ];
    }

    /**
     * Proses Defuzzifikasi menggunakan metode Centroid (Center of Gravity).
     */
    public static function defuzzifyCentroid(array $aggregatedAlpha, int $samples = 100): float
    {
        $minDomain = 0.0;
        $maxDomain = 50.0;
        $step      = ($maxDomain - $minDomain) / $samples;

        $numerator   = 0.0;
        $denominator = 0.0;

        for ($i = 0; $i <= $samples; $i++) {
            $z = $minDomain + ($i * $step);

            $muRendah = min($aggregatedAlpha['rendah'], self::getDiscountMembership($z, 'rendah'));
            $muSedang = min($aggregatedAlpha['sedang'], self::getDiscountMembership($z, 'sedang'));
            $muTinggi = min($aggregatedAlpha['tinggi'], self::getDiscountMembership($z, 'tinggi'));

            $muCombined = max($muRendah, $muSedang, $muTinggi);

            $numerator   += $z * $muCombined;
            $denominator += $muCombined;
        }

        if ($denominator == 0.0) {
            return 0.0;
        }

        return round($numerator / $denominator, 2);
    }

    /**
     * Method utama untuk dipanggil oleh Backend (Anggota 3).
     * Menerima input mentah produk dan mengembalikan hasil perhitungan lengkap.
     */
    public static function calculateDiscount(float $stock, float $storageDays, float $sales): array
    {
        // Amankan input agar selalu berada dalam batas domain Anggota 1
        $clampedStock   = max(0.0, min(100.0, $stock));
        $clampedStorage = max(0.0, min(180.0, $storageDays));
        $clampedSales   = max(0.0, min(100.0, $sales));

        // 1. Fuzzifikasi (Modul Anggota 1)
        $fuzzified = Fuzzification::fuzzify($clampedStock, $clampedStorage, $clampedSales);

        // 2. Inferensi MIN & Agregasi MAX (Modul Anggota 2)
        $inferenceResult = self::infer($fuzzified);

        // 3. Defuzzifikasi Centroid (Modul Anggota 2)
        $discountPercentage = self::defuzzifyCentroid($inferenceResult['aggregated_alpha']);

        // Label kategori untuk mempermudah tampilan UI (Anggota 4)
        $category = match (true) {
            $discountPercentage >= 30.0 => 'Diskon Tinggi',
            $discountPercentage >= 15.0 => 'Diskon Sedang',
            default                     => 'Diskon Rendah',
        };

        return [
            'fuzzification'    => $fuzzified,
            'aggregated_alpha' => $inferenceResult['aggregated_alpha'],
            'active_rules'     => $inferenceResult['active_rules'],
            'discount'         => $discountPercentage,
            'category'         => $category,
        ];
    }
}