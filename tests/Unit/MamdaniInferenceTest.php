<?php

use App\Services\Fuzzy\MamdaniInference;
use App\Services\Fuzzy\RuleBase;

test('rule base contains exactly 27 unique rules', function () {
    $rules = RuleBase::getRules();
    expect($rules)->toHaveCount(27);

    $combinations = array_map(
        fn ($r) => "{$r['stok']}-{$r['penyimpanan']}-{$r['penjualan']}",
        $rules
    );
    expect(array_unique($combinations))->toHaveCount(27);
});

test('extreme high stock, old storage, low sales produces high discount', function () {
    $result = MamdaniInference::calculateDiscount(90, 160, 10);

    expect($result['aggregated_alpha']['tinggi'])->toBe(1.0);
    expect($result['discount'])->toBeGreaterThan(35.0);
    expect($result['category'])->toBe('Diskon Tinggi');
});

test('extreme low stock, new storage, high sales produces low discount', function () {
    $result = MamdaniInference::calculateDiscount(10, 15, 90);

    expect($result['aggregated_alpha']['rendah'])->toBe(1.0);
    expect($result['discount'])->toBeLessThan(15.0);
    expect($result['category'])->toBe('Diskon Rendah');
});

test('sample product Tas Wanita calculates valid recommendation', function () {
    // Contoh data Anggota 3: Stok 100, Lama penyimpanan 120 hari, Penjualan 10/bulan
    $result = MamdaniInference::calculateDiscount(100, 120, 10);

    expect($result['discount'])->toBeFloat();
    expect($result['discount'])->toBeGreaterThan(25.0);
    expect($result['active_rules'])->not->toBeEmpty();
});