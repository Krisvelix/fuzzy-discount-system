<?php

namespace App\Services\Fuzzy;

class Fuzzification
{
    /**
     * Menghitung derajat keanggotaan untuk variabel stok.
     * Domain: 0 - 100
     */
    public static function fuzzifyStock(float $stock): array
    {
        return [
            'sedikit' => MembershipFunction::trapezoid(
                $stock, 0, 0, 20, 40
            ),
            'sedang' => MembershipFunction::triangle(
                $stock, 20, 50, 80
            ),
            'banyak' => MembershipFunction::trapezoid(
                $stock, 60, 80, 100, 100
            ),
        ];
    }

    /**
     * Menghitung derajat keanggotaan untuk lama penyimpanan.
     * Domain: 0 - 180 hari
     */
    public static function fuzzifyStorage(float $days): array
    {
        return [
            'baru' => MembershipFunction::trapezoid(
                $days, 0, 0, 30, 60
            ),
            'sedang' => MembershipFunction::triangle(
                $days, 30, 90, 150
            ),
            'lama' => MembershipFunction::trapezoid(
                $days, 120, 150, 180, 180
            ),
        ];
    }

    /**
     * Menghitung derajat keanggotaan untuk tingkat penjualan.
     * Domain: 0 - 100
     */
    public static function fuzzifySales(float $sales): array
    {
        return [
            'rendah' => MembershipFunction::trapezoid(
                $sales, 0, 0, 20, 40
            ),
            'sedang' => MembershipFunction::triangle(
                $sales, 20, 50, 80
            ),
            'tinggi' => MembershipFunction::trapezoid(
                $sales, 60, 80, 100, 100
            ),
        ];
    }

    /**
     * Melakukan fuzzifikasi seluruh variabel input.
     */
    public static function fuzzify(
        float $stock,
        float $storageDays,
        float $sales
    ): array {
        return [
            'stok' => self::fuzzifyStock($stock),
            'penyimpanan' => self::fuzzifyStorage($storageDays),
            'penjualan' => self::fuzzifySales($sales),
        ];
    }
}
