<?php

use App\Services\Fuzzy\Fuzzification;
use App\Services\Fuzzy\MembershipFunction;

test('triangle membership function works correctly', function () {
    expect(MembershipFunction::triangle(20, 20, 50, 80))->toBe(0.0);
    expect(MembershipFunction::triangle(50, 20, 50, 80))->toBe(1.0);
    expect(MembershipFunction::triangle(35, 20, 50, 80))->toBe(0.5);
});

test('left shoulder trapezoid works correctly', function () {
    expect(MembershipFunction::trapezoid(0, 0, 0, 20, 40))->toBe(1.0);
    expect(MembershipFunction::trapezoid(30, 0, 0, 20, 40))->toBe(0.5);
    expect(MembershipFunction::trapezoid(40, 0, 0, 20, 40))->toBe(0.0);
});

test('right shoulder trapezoid works correctly', function () {
    expect(MembershipFunction::trapezoid(60, 60, 80, 100, 100))->toBe(0.0);
    expect(MembershipFunction::trapezoid(90, 60, 80, 100, 100))->toBe(1.0);
    expect(MembershipFunction::trapezoid(100, 60, 80, 100, 100))->toBe(1.0);
});

test('stock fuzzification returns correct membership values', function () {
    $result = Fuzzification::fuzzifyStock(50);

    expect($result['sedikit'])->toBe(0.0);
    expect($result['sedang'])->toBe(1.0);
    expect($result['banyak'])->toBe(0.0);
});

test('storage fuzzification returns correct membership values', function () {
    $result = Fuzzification::fuzzifyStorage(90);

    expect($result['baru'])->toBe(0.0);
    expect($result['sedang'])->toBe(1.0);
    expect($result['lama'])->toBe(0.0);
});

test('sales fuzzification returns correct membership values', function () {
    $result = Fuzzification::fuzzifySales(50);

    expect($result['rendah'])->toBe(0.0);
    expect($result['sedang'])->toBe(1.0);
    expect($result['tinggi'])->toBe(0.0);
});

test('complete fuzzification returns all input variables', function () {
    $result = Fuzzification::fuzzify(50, 90, 50);

    expect($result)->toHaveKeys([
        'stok',
        'penyimpanan',
        'penjualan',
    ]);
});
