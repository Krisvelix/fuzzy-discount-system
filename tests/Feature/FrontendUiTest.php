<?php

use App\Models\Product;
use App\Services\Fuzzy\MamdaniInference;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('dashboard page renders successfully with fuzzy simulation form', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Dashboard &amp; Simulasi Diskon', false)
        ->assertSee('Simulasi Cepat Fuzzy Mamdani');
});

test('product management page renders successfully', function () {
    $response = $this->get('/products');

    $response->assertOk()
        ->assertSee('Manajemen Produk')
        ->assertSee('Tambah Produk Baru');
});

test('discount histories page renders saved calculations with category', function () {
    $product = Product::create([
        'name' => 'Jaket Musim Dingin',
        'stock' => 90,
        'storage_duration' => 160,
        'monthly_sales' => 10,
    ]);

    $product->discountHistories()->create([
        'discount_percentage' => 41.32,
    ]);

    $response = $this->get('/histories');

    $response->assertOk()
        ->assertSee('Riwayat Perhitungan Diskon')
        ->assertSee('Jaket Musim Dingin')
        ->assertSee('41.32%')
        ->assertSee('Diskon Tinggi');
});

test('fuzzy simulation endpoint returns full mamdani breakdown', function () {
    $expected = MamdaniInference::calculateDiscount(90, 160, 10);

    $response = $this->postJson('/simulate', [
        'stock' => 90,
        'storage_duration' => 160,
        'monthly_sales' => 10,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.discount', $expected['discount'])
        ->assertJsonPath('data.category', $expected['category'])
        ->assertJsonStructure([
            'data' => [
                'input' => ['stock', 'storage_duration', 'monthly_sales'],
                'fuzzification' => ['stok', 'penyimpanan', 'penjualan'],
                'aggregated_alpha' => ['rendah', 'sedang', 'tinggi'],
                'active_rules',
                'discount',
                'category',
            ],
        ]);
});

test('fuzzy simulation endpoint rejects out-of-domain values', function () {
    $response = $this->postJson('/simulate', [
        'stock' => 150,
        'storage_duration' => 250,
        'monthly_sales' => -5,
    ]);

    $response->assertUnprocessable()
        ->assertInvalid(['stock', 'storage_duration', 'monthly_sales']);
});

test('product fuzzy detail endpoint returns product inputs and mamdani result', function () {
    $product = Product::create([
        'name' => 'Tas Wanita',
        'stock' => 100,
        'storage_duration' => 120,
        'monthly_sales' => 10,
    ]);
    $expected = MamdaniInference::calculateDiscount(100, 120, 10);

    $response = $this->getJson("/products/{$product->id}/fuzzy-detail");

    $response->assertOk()
        ->assertJsonPath('data.product.name', 'Tas Wanita')
        ->assertJsonPath('data.discount', $expected['discount'])
        ->assertJsonPath('data.category', $expected['category']);
});
