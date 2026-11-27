<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DiscountHistory;
use App\Models\Product;
use App\Services\Fuzzy\MamdaniInference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DiscountUiController extends Controller
{
    public function dashboard(): View
    {
        $hasProductsTable = Schema::hasTable('products');
        $hasHistoriesTable = Schema::hasTable('discount_histories');

        $products = $hasProductsTable
            ? Product::query()->orderBy('name')->orderBy('id')->get()
            : collect();

        $totalProducts = $products->count();
        $totalHistories = $hasHistoriesTable ? DiscountHistory::query()->count() : 0;
        $averageDiscount = $hasHistoriesTable && $totalHistories > 0
            ? round((float) DiscountHistory::query()->avg('discount_percentage'), 2)
            : 0.0;
        $highDiscountCount = $hasHistoriesTable
            ? DiscountHistory::query()->where('discount_percentage', '>=', 30)->count()
            : 0;

        $recentHistories = $hasHistoriesTable
            ? DiscountHistory::query()
                ->with('product')
                ->latest('id')
                ->limit(5)
                ->get()
                ->map(function (DiscountHistory $history): array {
                    $discount = (float) $history->discount_percentage;

                    return [
                        'id' => $history->id,
                        'product_id' => $history->product_id,
                        'product_name' => $history->product?->name ?? 'Produk Dihapus',
                        'stock' => $history->product?->stock,
                        'storage_duration' => $history->product?->storage_duration,
                        'monthly_sales' => $history->product?->monthly_sales,
                        'discount_percentage' => number_format($discount, 2, '.', ''),
                        'category' => self::classifyDiscount($discount),
                        'created_at_formatted' => $history->created_at?->format('d M Y, H:i') ?? '-',
                        'created_at_human' => $history->created_at?->diffForHumans() ?? '-',
                    ];
                })
            : collect();

        $defaultInput = [
            'stock' => 85,
            'storage_duration' => 145,
            'monthly_sales' => 15,
        ];

        $initialSimulation = [
            'input' => $defaultInput,
            ...MamdaniInference::calculateDiscount(
                $defaultInput['stock'],
                $defaultInput['storage_duration'],
                $defaultInput['monthly_sales'],
            ),
        ];

        return view('dashboard', [
            'stats' => [
                'total_products' => $totalProducts,
                'total_histories' => $totalHistories,
                'average_discount' => $averageDiscount,
                'high_discount_count' => $highDiscountCount,
            ],
            'products' => $products,
            'recentHistories' => $recentHistories,
            'initialSimulation' => $initialSimulation,
        ]);
    }

    public function products(): View
    {
        $hasProductsTable = Schema::hasTable('products');
        $hasHistoriesTable = Schema::hasTable('discount_histories');

        return view('products.index', [
            'totalProducts' => $hasProductsTable ? Product::query()->count() : 0,
            'totalHistories' => $hasHistoriesTable ? DiscountHistory::query()->count() : 0,
        ]);
    }

    public function histories(Request $request): View
    {
        $hasProductsTable = Schema::hasTable('products');
        $hasHistoriesTable = Schema::hasTable('discount_histories');

        $search = trim((string) $request->query('q', ''));
        $productId = $request->query('product_id');
        $category = (string) $request->query('category', '');

        $products = $hasProductsTable
            ? Product::query()->orderBy('name')->orderBy('id')->get()
            : collect();

        if (! $hasHistoriesTable) {
            return view('histories.index', [
                'histories' => null,
                'products' => $products,
                'filters' => [
                    'q' => $search,
                    'product_id' => $productId,
                    'category' => $category,
                ],
                'summary' => [
                    'total' => 0,
                    'high' => 0,
                    'medium' => 0,
                    'low' => 0,
                    'average' => 0.0,
                ],
            ]);
        }

        $query = DiscountHistory::query()
            ->with('product')
            ->latest('id');

        if ($search !== '') {
            $query->whereHas('product', function ($productQuery) use ($search): void {
                $productQuery->where('name', 'like', "%{$search}%");
            });
        }

        if ($productId !== null && $productId !== '') {
            $query->where('product_id', (int) $productId);
        }

        if ($category === 'tinggi') {
            $query->where('discount_percentage', '>=', 30);
        } elseif ($category === 'sedang') {
            $query->where('discount_percentage', '>=', 15)
                ->where('discount_percentage', '<', 30);
        } elseif ($category === 'rendah') {
            $query->where('discount_percentage', '<', 15);
        }

        $histories = $query->paginate(15)->withQueryString();

        $total = DiscountHistory::query()->count();
        $high = DiscountHistory::query()->where('discount_percentage', '>=', 30)->count();
        $medium = DiscountHistory::query()
            ->where('discount_percentage', '>=', 15)
            ->where('discount_percentage', '<', 30)
            ->count();
        $low = DiscountHistory::query()->where('discount_percentage', '<', 15)->count();
        $average = $total > 0
            ? round((float) DiscountHistory::query()->avg('discount_percentage'), 2)
            : 0.0;

        return view('histories.index', [
            'histories' => $histories,
            'products' => $products,
            'filters' => [
                'q' => $search,
                'product_id' => $productId,
                'category' => $category,
            ],
            'summary' => [
                'total' => $total,
                'high' => $high,
                'medium' => $medium,
                'low' => $low,
                'average' => $average,
            ],
        ]);
    }

    public function simulate(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'stock' => ['required', 'numeric', 'between:0,100'],
                'storage_duration' => ['required', 'numeric', 'between:0,180'],
                'monthly_sales' => ['required', 'numeric', 'between:0,100'],
            ],
            [],
            [
                'stock' => 'jumlah stok',
                'storage_duration' => 'lama penyimpanan',
                'monthly_sales' => 'tingkat penjualan',
            ]
        );

        $stock = (float) $validated['stock'];
        $storageDuration = (float) $validated['storage_duration'];
        $monthlySales = (float) $validated['monthly_sales'];

        $result = MamdaniInference::calculateDiscount($stock, $storageDuration, $monthlySales);

        return response()->json([
            'data' => [
                'input' => [
                    'stock' => $stock,
                    'storage_duration' => $storageDuration,
                    'monthly_sales' => $monthlySales,
                ],
                ...$result,
            ],
        ]);
    }

    public function productFuzzyDetail(Product $product): JsonResponse
    {
        $result = MamdaniInference::calculateDiscount(
            (float) $product->stock,
            (float) $product->storage_duration,
            (float) $product->monthly_sales,
        );

        return response()->json([
            'data' => [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'storage_duration' => $product->storage_duration,
                    'monthly_sales' => $product->monthly_sales,
                ],
                'input' => [
                    'stock' => (float) $product->stock,
                    'storage_duration' => (float) $product->storage_duration,
                    'monthly_sales' => (float) $product->monthly_sales,
                ],
                ...$result,
            ],
        ]);
    }

    public static function classifyDiscount(float $discountPercentage): string
    {
        return match (true) {
            $discountPercentage >= 30.0 => 'Diskon Tinggi',
            $discountPercentage >= 15.0 => 'Diskon Sedang',
            default => 'Diskon Rendah',
        };
    }
}
