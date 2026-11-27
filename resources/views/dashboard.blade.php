@extends('layouts.app')

@section('title', 'Dashboard & Simulasi Diskon — FuzzyDiscount')

@section('content')
<div
    x-data="dashboardSimulator({
        initialResult: @js($initialSimulation),
        products: @js($products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'stock' => $p->stock,
            'storage_duration' => $p->storage_duration,
            'monthly_sales' => $p->monthly_sales,
        ])->values()),
        simulateUrl: '{{ route('fuzzy.simulate') }}',
    })"
    class="space-y-6"
>
    {{-- Page Heading --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200">
                Metode Fuzzy Mamdani
            </span>
            <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Dashboard &amp; Simulasi Diskon
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Uji rekomendasi persentase diskon produk secara instan berdasarkan stok, lama penyimpanan, dan penjualan bulanan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a
                href="{{ route('ui.products.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors"
            >
                <span>Kelola Produk</span>
            </a>
            <a
                href="{{ route('ui.histories.index') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors"
            >
                <span>Lihat Riwayat</span>
            </a>
        </div>
    </div>

    {{-- KPI Summary Strip --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Produk</p>
            <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono">{{ $stats['total_products'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Produk tersimpan di katalog</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Perhitungan</p>
            <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-indigo-600 font-mono" x-text="totalHistories"></p>
            <p class="mt-1 text-xs text-slate-500">Tercatat di riwayat diskon</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Rata-Rata Diskon</p>
            <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-amber-600 font-mono">{{ number_format($stats['average_discount'], 2) }}%</p>
            <p class="mt-1 text-xs text-slate-500">Dari domain output 0% – 50%</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diskon Tinggi (&ge;30%)</p>
            <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-rose-600 font-mono">{{ $stats['high_discount_count'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Prioritas percepatan stok</p>
        </div>
    </div>

    {{-- Simulator + Result Split Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Left: Quick Simulation Form --}}
        <div class="lg:col-span-5 rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-card space-y-5">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-slate-900">Simulasi Cepat Fuzzy Mamdani</h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    Masukkan nilai parameter produk atau pilih produk tersimpan untuk menghitung rekomendasi diskon.
                </p>
            </div>

            {{-- Load from Saved Product --}}
            <div>
                <label for="sim-product-select" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                    Muat dari Produk Tersimpan (Opsional)
                </label>
                <select
                    id="sim-product-select"
                    x-model="selectedProductId"
                    @change="applySelectedProduct()"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
                    <option value="">-- Input Manual / Simulasi Bebas --</option>
                    <template x-for="product in products" :key="product.id">
                        <option
                            :value="product.id"
                            x-text="`${product.name} (Stok ${product.stock}, ${product.storage_duration} hr, Jual ${product.monthly_sales})`"
                        ></option>
                    </template>
                </select>
            </div>

            {{-- Preset Scenarios --}}
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-2">
                    Skenario Uji Cepat
                </span>
                <div class="grid grid-cols-3 gap-2">
                    <button
                        type="button"
                        @click="applyPreset(90, 160, 10)"
                        class="rounded-xl border border-rose-200 bg-rose-50/70 px-2.5 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100/80 transition-colors"
                    >
                        Stok Penuh &amp; Sepi
                    </button>
                    <button
                        type="button"
                        @click="applyPreset(50, 90, 50)"
                        class="rounded-xl border border-amber-200 bg-amber-50/70 px-2.5 py-2 text-xs font-semibold text-amber-800 hover:bg-amber-100/80 transition-colors"
                    >
                        Kondisi Normal
                    </button>
                    <button
                        type="button"
                        @click="applyPreset(10, 15, 90)"
                        class="rounded-xl border border-emerald-200 bg-emerald-50/70 px-2.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100/80 transition-colors"
                    >
                        Stok Tipis &amp; Laris
                    </button>
                </div>
            </div>

            {{-- Input Form --}}
            <form @submit.prevent="runSimulation()" class="space-y-4">
                {{-- Stok --}}
                <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-4">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <label for="sim-stock" class="text-sm font-semibold text-slate-800">Jumlah Stok</label>
                        <div class="flex items-center gap-1.5">
                            <input
                                id="sim-stock"
                                type="number"
                                min="0"
                                max="100"
                                step="1"
                                x-model.number="form.stock"
                                class="w-20 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-right font-mono text-sm font-bold text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >
                            <span class="text-xs text-slate-500">/ 100</span>
                        </div>
                    </div>
                    <input
                        type="range"
                        min="0"
                        max="100"
                        step="1"
                        x-model.number="form.stock"
                        class="w-full cursor-pointer"
                    >
                    <div class="mt-1 flex justify-between text-[11px] text-slate-400">
                        <span>0 (Sedikit)</span>
                        <span>50 (Sedang)</span>
                        <span>100 (Banyak)</span>
                    </div>
                    <p x-show="errors.stock" x-text="errors.stock?.[0]" class="mt-1.5 text-xs font-medium text-rose-600"></p>
                </div>

                {{-- Lama Penyimpanan --}}
                <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-4">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <label for="sim-storage" class="text-sm font-semibold text-slate-800">Lama Penyimpanan</label>
                        <div class="flex items-center gap-1.5">
                            <input
                                id="sim-storage"
                                type="number"
                                min="0"
                                max="180"
                                step="1"
                                x-model.number="form.storage_duration"
                                class="w-20 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-right font-mono text-sm font-bold text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >
                            <span class="text-xs text-slate-500">hari</span>
                        </div>
                    </div>
                    <input
                        type="range"
                        min="0"
                        max="180"
                        step="1"
                        x-model.number="form.storage_duration"
                        class="w-full cursor-pointer"
                    >
                    <div class="mt-1 flex justify-between text-[11px] text-slate-400">
                        <span>0 hr (Baru)</span>
                        <span>90 hr (Sedang)</span>
                        <span>180 hr (Lama)</span>
                    </div>
                    <p x-show="errors.storage_duration" x-text="errors.storage_duration?.[0]" class="mt-1.5 text-xs font-medium text-rose-600"></p>
                </div>

                {{-- Penjualan per Bulan --}}
                <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-4">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <label for="sim-sales" class="text-sm font-semibold text-slate-800">Penjualan per Bulan</label>
                        <div class="flex items-center gap-1.5">
                            <input
                                id="sim-sales"
                                type="number"
                                min="0"
                                max="100"
                                step="1"
                                x-model.number="form.monthly_sales"
                                class="w-20 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-right font-mono text-sm font-bold text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >
                            <span class="text-xs text-slate-500">/ 100</span>
                        </div>
                    </div>
                    <input
                        type="range"
                        min="0"
                        max="100"
                        step="1"
                        x-model.number="form.monthly_sales"
                        class="w-full cursor-pointer"
                    >
                    <div class="mt-1 flex justify-between text-[11px] text-slate-400">
                        <span>0 (Rendah)</span>
                        <span>50 (Sedang)</span>
                        <span>100 (Tinggi)</span>
                    </div>
                    <p x-show="errors.monthly_sales" x-text="errors.monthly_sales?.[0]" class="mt-1.5 text-xs font-medium text-rose-600"></p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-2.5 pt-1">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-60 transition-colors"
                    >
                        <span x-show="!loading">Hitung Simulasi Fuzzy</span>
                        <span x-show="loading" x-cloak>Menghitung...</span>
                    </button>

                    <button
                        type="button"
                        x-show="selectedProductId"
                        x-cloak
                        @click="calculateAndSaveSelectedProduct()"
                        :disabled="savingProduct"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 disabled:opacity-60 transition-colors"
                    >
                        <span x-show="!savingProduct">Simpan ke Riwayat</span>
                        <span x-show="savingProduct" x-cloak>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: Complete Fuzzy Mamdani Visualization Panel --}}
        <div class="lg:col-span-7">
            <x-fuzzy-result-panel resultExpr="result" savedBadgeExpr="savedBadge" />
        </div>
    </div>

    {{-- Recent Calculation History Snapshot --}}
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-card">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200/80 px-5 py-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Riwayat Perhitungan Diskon Terbaru</h2>
                <p class="text-xs text-slate-500">5 perhitungan terakhir yang tersimpan di database</p>
            </div>
            <a href="{{ route('ui.histories.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Lihat Semua Riwayat &rarr;
            </a>
        </div>

        @if ($recentHistories->isEmpty())
            <div class="px-5 py-10 text-center">
                <p class="text-sm font-medium text-slate-600">Belum ada riwayat perhitungan diskon yang tersimpan.</p>
                <p class="mt-1 text-xs text-slate-500">Tambahkan produk di menu Manajemen Produk lalu klik "Hitung Diskon".</p>
                <a
                    href="{{ route('ui.products.index') }}"
                    class="mt-4 inline-flex items-center rounded-xl bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
                >
                    Buka Manajemen Produk
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3 text-left">Waktu</th>
                            <th class="px-5 py-3 text-left">Produk</th>
                            <th class="px-5 py-3 text-left">Input (Stok / Simpan / Jual)</th>
                            <th class="px-5 py-3 text-right">Diskon</th>
                            <th class="px-5 py-3 text-left">Kategori</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($recentHistories as $item)
                            @php
                                $badgeClass = match ($item['category']) {
                                    'Diskon Tinggi' => 'bg-rose-50 text-rose-700 ring-rose-200',
                                    'Diskon Sedang' => 'bg-amber-50 text-amber-800 ring-amber-200',
                                    default => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-5 py-3 whitespace-nowrap text-xs text-slate-600">
                                    <div class="font-medium text-slate-800">{{ $item['created_at_formatted'] }}</div>
                                    <div class="text-slate-400">{{ $item['created_at_human'] }}</div>
                                </td>
                                <td class="px-5 py-3 font-semibold text-slate-900">{{ $item['product_name'] }}</td>
                                <td class="px-5 py-3 text-xs font-mono text-slate-600">
                                    {{ $item['stock'] ?? '-' }} / {{ $item['storage_duration'] ?? '-' }} hr / {{ $item['monthly_sales'] ?? '-' }}
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right font-mono font-bold text-slate-900">
                                    {{ $item['discount_percentage'] }}%
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $badgeClass }}">
                                        {{ $item['category'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function dashboardSimulator({ initialResult, products, simulateUrl }) {
        return {
            products,
            selectedProductId: '',
            totalHistories: {{ (int) $stats['total_histories'] }},
            form: {
                stock: initialResult?.input?.stock ?? 85,
                storage_duration: initialResult?.input?.storage_duration ?? 145,
                monthly_sales: initialResult?.input?.monthly_sales ?? 15,
            },
            result: initialResult,
            savedBadge: null,
            loading: false,
            savingProduct: false,
            errors: {},

            applySelectedProduct() {
                this.savedBadge = null;
                if (!this.selectedProductId) return;

                const found = this.products.find((p) => String(p.id) === String(this.selectedProductId));
                if (!found) return;

                this.form.stock = found.stock;
                this.form.storage_duration = found.storage_duration;
                this.form.monthly_sales = found.monthly_sales;
                this.runSimulation(false);
            },

            applyPreset(stock, storageDuration, monthlySales) {
                this.selectedProductId = '';
                this.savedBadge = null;
                this.form.stock = stock;
                this.form.storage_duration = storageDuration;
                this.form.monthly_sales = monthlySales;
                this.runSimulation(false);
            },

            async runSimulation(showSuccessToast = true) {
                this.loading = true;
                this.errors = {};
                this.savedBadge = null;

                try {
                    const res = await window.FuzzyUi.request(simulateUrl, {
                        method: 'POST',
                        body: JSON.stringify({
                            stock: this.form.stock,
                            storage_duration: this.form.storage_duration,
                            monthly_sales: this.form.monthly_sales,
                        }),
                    });

                    if (res.status === 422 && res.data?.errors) {
                        this.errors = res.data.errors;
                        window.FuzzyUi.notify('error', 'Input Tidak Valid', 'Pastikan stok (0–100), penyimpanan (0–180), dan penjualan (0–100) sesuai domain.');
                        return;
                    }

                    if (!res.ok || !res.data?.data) {
                        window.FuzzyUi.notify('error', 'Simulasi Gagal', res.data?.message || 'Terjadi kesalahan saat menghitung simulasi fuzzy.');
                        return;
                    }

                    this.result = res.data.data;

                    if (showSuccessToast) {
                        window.FuzzyUi.notify(
                            'success',
                            'Simulasi Selesai',
                            `Rekomendasi diskon: ${window.FuzzyUi.formatDiscount(this.result.discount)}% (${this.result.category})`
                        );
                    }
                } catch (err) {
                    window.FuzzyUi.notify('error', 'Kesalahan Jaringan', 'Gagal menghubungi server untuk simulasi.');
                } finally {
                    this.loading = false;
                }
            },

            async calculateAndSaveSelectedProduct() {
                if (!this.selectedProductId) return;
                this.savingProduct = true;

                try {
                    const saveRes = await window.FuzzyUi.request(`/api/products/${this.selectedProductId}/discount-calculations`, {
                        method: 'POST',
                    });

                    if (!saveRes.ok) {
                        window.FuzzyUi.notify('error', 'Gagal Menyimpan Perhitungan', saveRes.data?.message || 'Tidak dapat menyimpan riwayat diskon.');
                        return;
                    }

                    const detailRes = await window.FuzzyUi.request(`/products/${this.selectedProductId}/fuzzy-detail`, {
                        method: 'GET',
                    });

                    if (detailRes.ok && detailRes.data?.data) {
                        this.result = detailRes.data.data;
                        this.form.stock = detailRes.data.data.input.stock;
                        this.form.storage_duration = detailRes.data.data.input.storage_duration;
                        this.form.monthly_sales = detailRes.data.data.input.monthly_sales;
                    }

                    this.totalHistories += 1;
                    this.savedBadge = 'Tersimpan ke Riwayat';

                    window.FuzzyUi.notify(
                        'success',
                        'Diskon Dihitung & Disimpan',
                        `Hasil ${saveRes.data?.data?.discount_percentage ?? window.FuzzyUi.formatDiscount(this.result.discount)}% telah dicatat ke riwayat.`
                    );
                } catch (err) {
                    window.FuzzyUi.notify('error', 'Kesalahan Jaringan', 'Gagal menyimpan perhitungan diskon produk.');
                } finally {
                    this.savingProduct = false;
                }
            },
        };
    }
</script>
@endpush
