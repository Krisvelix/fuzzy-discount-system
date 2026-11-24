<?php

use App\Models\DiscountHistory;
use App\Models\Product;
use App\Services\Fuzzy\FuzzyDiscountService;
use App\Services\Fuzzy\MamdaniInference;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('products can be listed with stable pagination', function () {
    Product::create([
        'name' => 'Teh',
        'stock' => 40,
        'storage_duration' => 90,
        'monthly_sales' => 50,
    ]);

    $response = $this->getJson('/api/products');

    $response->assertOk()
        ->assertJsonPath('data.0.name', 'Teh');
});

test('a product can be created with valid domain values', function () {
    $response = $this->postJson('/api/products', [
        'name' => 'Teh',
        'stock' => 0,
        'storage_duration' => 180,
        'monthly_sales' => 100,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Teh')
        ->assertJsonPath('data.stock', 0);

    $this->assertDatabaseHas('products', [
        'name' => 'Teh',
        'stock' => 0,
        'storage_duration' => 180,
        'monthly_sales' => 100,
    ]);
});

test('required product fields are rejected when missing', function () {
    $response = $this->postJson('/api/products', []);

    $response->assertUnprocessable()
        ->assertInvalid(['name', 'stock', 'storage_duration', 'monthly_sales']);

    $this->assertDatabaseCount('products', 0);
});

test('product values outside their domains are rejected', function (string $field, mixed $value) {
    $response = $this->postJson('/api/products', [
        'name' => 'Teh',
        'stock' => 40,
        'storage_duration' => 90,
        'monthly_sales' => 50,
        $field => $value,
    ]);

    $response->assertUnprocessable()
        ->assertInvalid([$field]);

    $this->assertDatabaseCount('products', 0);
})->with([
    'negative stock' => ['stock', -1],
    'stock above maximum' => ['stock', 101],
    'negative storage duration' => ['storage_duration', -1],
    'storage above maximum' => ['storage_duration', 181],
    'negative sales' => ['monthly_sales', -1],
    'sales above maximum' => ['monthly_sales', 101],
    'fractional stock' => ['stock', 12.5],
]);

test('a product can be updated with a partial payload', function () {
    $product = Product::create([
        'name' => 'Teh',
        'stock' => 40,
        'storage_duration' => 90,
        'monthly_sales' => 50,
    ]);

    $response = $this->patchJson("/api/products/{$product->id}", ['stock' => 70]);

    $response->assertOk()
        ->assertJsonPath('data.stock', 70)
        ->assertJsonPath('data.name', 'Teh');

    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 70]);
});

test('an update with an out-of-domain value is rejected without changing the product', function () {
    $product = Product::create([
        'name' => 'Teh',
        'stock' => 40,
        'storage_duration' => 90,
        'monthly_sales' => 50,
    ]);

    $response = $this->patchJson("/api/products/{$product->id}", ['stock' => 101]);

    $response->assertUnprocessable()
        ->assertInvalid(['stock']);

    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 40]);
});

test('a product can be shown by route model binding', function () {
    $product = Product::create([
        'name' => 'Teh',
        'stock' => 40,
        'storage_duration' => 90,
        'monthly_sales' => 50,
    ]);

    $response = $this->getJson("/api/products/{$product->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('data.name', 'Teh');
});

test('a product can be deleted together with its calculation history', function () {
    $product = Product::create([
        'name' => 'Teh',
        'stock' => 40,
        'storage_duration' => 90,
        'monthly_sales' => 50,
    ]);
    $history = $product->discountHistories()->create(['discount_percentage' => 20]);

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
    $this->assertDatabaseMissing('discount_histories', ['id' => $history->id]);
});

test('a calculation invokes the fuzzy service and persists its actual result', function () {
    $product = Product::create([
        'name' => 'Teh',
        'stock' => 90,
        'storage_duration' => 160,
        'monthly_sales' => 10,
    ]);
    $expectedDiscount = MamdaniInference::calculateDiscount(90, 160, 10)['discount'];

    $response = $this->postJson("/api/products/{$product->id}/discount-calculations");

    $response->assertCreated()
        ->assertJsonPath('data.product_id', $product->id)
        ->assertJsonPath('data.discount_percentage', number_format($expectedDiscount, 2, '.', ''));

    $history = DiscountHistory::query()->sole();
    expect((float) $history->discount_percentage)->toBe($expectedDiscount);
    $this->assertModelExists($history);
});

test('an invalid fuzzy service result is not saved', function () {
    $product = Product::create([
        'name' => 'Teh',
        'stock' => 40,
        'storage_duration' => 90,
        'monthly_sales' => 50,
    ]);
    $this->app->bind(FuzzyDiscountService::class, fn () => new class implements FuzzyDiscountService
    {
        public function calculate(float $stock, float $storageDuration, float $monthlySales): float
        {
            return 51.0;
        }
    });

    $response = $this->postJson("/api/products/{$product->id}/discount-calculations");

    $response->assertStatus(500);
    $this->assertDatabaseCount('discount_histories', 0);
});

test('a product calculation history can be listed', function () {
    $product = Product::create([
        'name' => 'Teh',
        'stock' => 40,
        'storage_duration' => 90,
        'monthly_sales' => 50,
    ]);
    $product->discountHistories()->create(['discount_percentage' => 20]);

    $response = $this->getJson("/api/products/{$product->id}/discount-histories");

    $response->assertOk()
        ->assertJsonPath('data.0.product_id', $product->id)
        ->assertJsonPath('data.0.discount_percentage', '20.00');
});
