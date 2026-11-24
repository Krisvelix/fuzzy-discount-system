<?php

namespace App\Services\Fuzzy;

class RuleBase
{
    /**
     * Mengembalikan 27 aturan IF-THEN untuk rekomendasi diskon.
     * Kombinasi: 3 Stok x 3 Penyimpanan x 3 Penjualan = 27 Rules.
     */
    public static function getRules(): array
    {
        return [
            // === STOK: SEDIKIT (9 Rules) ===
            ['stok' => 'sedikit', 'penyimpanan' => 'baru',   'penjualan' => 'rendah', 'diskon' => 'rendah'], // R1
            ['stok' => 'sedikit', 'penyimpanan' => 'baru',   'penjualan' => 'sedang', 'diskon' => 'rendah'], // R2
            ['stok' => 'sedikit', 'penyimpanan' => 'baru',   'penjualan' => 'tinggi', 'diskon' => 'rendah'], // R3
            ['stok' => 'sedikit', 'penyimpanan' => 'sedang', 'penjualan' => 'rendah', 'diskon' => 'sedang'], // R4
            ['stok' => 'sedikit', 'penyimpanan' => 'sedang', 'penjualan' => 'sedang', 'diskon' => 'rendah'], // R5
            ['stok' => 'sedikit', 'penyimpanan' => 'sedang', 'penjualan' => 'tinggi', 'diskon' => 'rendah'], // R6
            ['stok' => 'sedikit', 'penyimpanan' => 'lama',   'penjualan' => 'rendah', 'diskon' => 'sedang'], // R7
            ['stok' => 'sedikit', 'penyimpanan' => 'lama',   'penjualan' => 'sedang', 'diskon' => 'sedang'], // R8
            ['stok' => 'sedikit', 'penyimpanan' => 'lama',   'penjualan' => 'tinggi', 'diskon' => 'rendah'], // R9

            // === STOK: SEDANG (9 Rules) ===
            ['stok' => 'sedang',  'penyimpanan' => 'baru',   'penjualan' => 'rendah', 'diskon' => 'sedang'], // R10
            ['stok' => 'sedang',  'penyimpanan' => 'baru',   'penjualan' => 'sedang', 'diskon' => 'rendah'], // R11
            ['stok' => 'sedang',  'penyimpanan' => 'baru',   'penjualan' => 'tinggi', 'diskon' => 'rendah'], // R12
            ['stok' => 'sedang',  'penyimpanan' => 'sedang', 'penjualan' => 'rendah', 'diskon' => 'tinggi'], // R13
            ['stok' => 'sedang',  'penyimpanan' => 'sedang', 'penjualan' => 'sedang', 'diskon' => 'sedang'], // R14
            ['stok' => 'sedang',  'penyimpanan' => 'sedang', 'penjualan' => 'tinggi', 'diskon' => 'rendah'], // R15
            ['stok' => 'sedang',  'penyimpanan' => 'lama',   'penjualan' => 'rendah', 'diskon' => 'tinggi'], // R16
            ['stok' => 'sedang',  'penyimpanan' => 'lama',   'penjualan' => 'sedang', 'diskon' => 'tinggi'], // R17
            ['stok' => 'sedang',  'penyimpanan' => 'lama',   'penjualan' => 'tinggi', 'diskon' => 'sedang'], // R18

            // === STOK: BANYAK (9 Rules) ===
            ['stok' => 'banyak',  'penyimpanan' => 'baru',   'penjualan' => 'rendah', 'diskon' => 'sedang'], // R19
            ['stok' => 'banyak',  'penyimpanan' => 'baru',   'penjualan' => 'sedang', 'diskon' => 'sedang'], // R20
            ['stok' => 'banyak',  'penyimpanan' => 'baru',   'penjualan' => 'tinggi', 'diskon' => 'rendah'], // R21
            ['stok' => 'banyak',  'penyimpanan' => 'sedang', 'penjualan' => 'rendah', 'diskon' => 'tinggi'], // R22
            ['stok' => 'banyak',  'penyimpanan' => 'sedang', 'penjualan' => 'sedang', 'diskon' => 'sedang'], // R23
            ['stok' => 'banyak',  'penyimpanan' => 'sedang', 'penjualan' => 'tinggi', 'diskon' => 'sedang'], // R24
            ['stok' => 'banyak',  'penyimpanan' => 'lama',   'penjualan' => 'rendah', 'diskon' => 'tinggi'], // R25
            ['stok' => 'banyak',  'penyimpanan' => 'lama',   'penjualan' => 'sedang', 'diskon' => 'tinggi'], // R26
            ['stok' => 'banyak',  'penyimpanan' => 'lama',   'penjualan' => 'tinggi', 'diskon' => 'sedang'], // R27
        ];
    }
}