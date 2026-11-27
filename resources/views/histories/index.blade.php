@extends('layouts.app')

@section('title', 'Riwayat Perhitungan Diskon — FuzzyDiscount')

@section('content')
<div x-data="historyViewer()" class="space-y-6">
    {{-- Page Heading --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200">
                Log Evaluasi Mamdani
            </span>
            <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Riwayat Perhitungan Diskon
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Seluruh hasil rekomendasi diskon yang telah dihitung dan disimpan pada tabel <code class="font-mono text-xs bg-slate-100 px-1.5 py-0.5 rounded">discount_histories</code>.
            </p>
        </div>

        <a
            href="{{ route('ui.products.index') }}"
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors"
        >
            Hitung Diskon Produk Baru
        </a>
    </div>

    {{-- Summary Strip --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Riwayat</p>
            <p class="mt-1.5 text-2xl font-extrabold font-mono text-slate-900">{{ $summary['total'] }}</p>
        </div>

        <div class="rounded-2xl border border-rose-200/70 bg-rose-50/40 p-4 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">Diskon Tinggi</p>
            <p class="mt-1.5 text-2xl font-extrabold font-mono text-rose-700">{{ $summary['high'] }}</p>
            <p class="text-[11px] text-rose-600/80">&ge; 30.00%</p>
        </div>

        <div class="rounded-2xl border border-amber-200/70 bg-amber-50/40 p-4 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">Diskon Sedang</p>
            <p class="mt-1.5 text-2xl font-extrabold font-mono text-amber-700">{{ $summary['medium'] }}</p>
            <p class="text-[11px] text-amber-700/80">15.00% – 29.99%</p>
        </div>

        <div class="rounded-2xl border border-emerald-200/70 bg-emerald-50/40 p-4 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Diskon Rendah</p>
            <p class="mt-1.5 text-2xl font-extrabold font-mono text-emerald-700">{{ $summary['low'] }}</p>
            <p class="text-[11px] text-emerald-600/80">&lt; 15.00%</p>
        </div>

        <div class="col-span-2 lg:col-span-1 rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Rata-Rata</p>
            <p class="mt-1.5 text-2xl font-extrabold font-mono text-indigo-600">{{ number_format($summary['average'], 2) }}%</p>
            <p class="text-[11px] text-slate-400">Domain 0% – 50%</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('ui.histories.index') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <div class="lg:col-span-4">
                <label for="filter-q" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1">
                    Cari Nama Produk
                </label>
                <input
                    id="filter-q"
                    type="text"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="Ketik nama produk..."
                    class="w-full rounded-xl border border-slate-300 bg-slate-50/60 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>

            <div class="lg:col-span-3">
                <label for="filter-product" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1">
                    Produk
                </label>
                <select
                    id="filter-product"
                    name="product_id"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
                    <option value="">Semua Produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected((string) $filters['product_id'] === (string) $product->id)>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="filter-category" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1">
                    Kategori Diskon
                </label>
                <select
                    id="filter-category"
                    name="category"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
                    <option value="">Semua Kategori</option>
                    <option value="tinggi" @selected($filters['category'] === 'tinggi')>Diskon Tinggi (&ge; 30%)</option>
                    <option value="sedang" @selected($filters['category'] === 'sedang')>Diskon Sedang (15% – 29.99%)</option>
                    <option value="rendah" @selected($filters['category'] === 'rendah')>Diskon Rendah (&lt; 15%)</option>
                </select>
            </div>

            <div class="lg:col-span-2 flex gap-2">
                <button
                    type="submit"
                    class="flex-1 rounded-xl bg-slate-900 px-3.5 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition-colors"
                >
                    Filter
                </button>
                @if ($filters['q'] !== '' || ! empty($filters['product_id']) || $filters['category'] !== '')
                    <a
                        href="{{ route('ui.histories.index') }}"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- History Table Card --}}
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-card">
        @if (! $histories || $histories->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-base font-bold text-slate-800">Belum Ada Riwayat Perhitungan Diskon</p>
                <p class="mt-1 text-sm text-slate-500">
                    Jalankan perhitungan diskon dari halaman Manajemen Produk agar hasil tersimpan di riwayat ini.
                </p>
                <a
                    href="{{ route('ui.products.index') }}"
                    class="mt-4 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-indigo-700"
                >
                    Pergi ke Manajemen Produk
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Waktu Perhitungan</th>
                            <th class="px-5 py-3.5 text-left">Produk</th>
                            <th class="px-5 py-3.5 text-left">Detail Input Produk</th>
                            <th class="px-5 py-3.5 text-left">Persentase Diskon</th>
                            <th class="px-5 py-3.5 text-left">Kategori</th>
                            <th class="px-5 py-3.5 text-right">Analisis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($histories as $history)
                            @php
                                $discountValue = (float) $history->discount_percentage;
                                $category = \App\Http\Controllers\Web\DiscountUiController::classifyDiscount($discountValue);
                                $badgeClass = match ($category) {
                                    'Diskon Tinggi' => 'bg-gradient-to-r from-rose-50 to-orange-50 text-rose-700 ring-rose-200',
                                    'Diskon Sedang' => 'bg-amber-50 text-amber-800 ring-amber-200',
                                    default => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                };
                                $barClass = match ($category) {
                                    'Diskon Tinggi' => 'from-orange-500 to-rose-500',
                                    'Diskon Sedang' => 'from-amber-500 to-sky-500',
                                    default => 'from-emerald-500 to-teal-500',
                                };
                                $barWidth = min(100, max(0, ($discountValue / 50) * 100));
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-4 whitespace-nowrap text-xs">
                                    <div class="font-semibold text-slate-800">
                                        {{ $history->created_at?->format('d M Y, H:i') ?? '-' }}
                                    </div>
                                    <div class="text-slate-400">
                                        {{ $history->created_at?->diffForHumans() ?? '' }} &bull; #{{ $history->id }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900">
                                        {{ $history->product?->name ?? 'Produk Dihapus' }}
                                    </div>
                                    @if ($history->product)
                                        <div class="text-xs text-slate-400">ID Produk #{{ $history->product->id }}</div>
                                    @endif
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($history->product)
                                        <div class="flex flex-wrap gap-1.5 text-xs">
                                            <span class="rounded-lg bg-slate-100 px-2 py-1 font-mono text-slate-700">
                                                Stok: <strong>{{ $history->product->stock }}</strong>
                                            </span>
                                            <span class="rounded-lg bg-slate-100 px-2 py-1 font-mono text-slate-700">
                                                Simpan: <strong>{{ $history->product->storage_duration }} hr</strong>
                                            </span>
                                            <span class="rounded-lg bg-slate-100 px-2 py-1 font-mono text-slate-700">
                                                Jual: <strong>{{ $history->product->monthly_sales }}/bln</strong>
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Data produk tidak tersedia</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono text-base font-extrabold text-slate-900 w-16">
                                            {{ number_format($discountValue, 2) }}%
                                        </span>
                                        <div class="h-2 w-24 rounded-full bg-slate-100 overflow-hidden hidden sm:block">
                                            <div
                                                class="h-full rounded-full bg-gradient-to-r {{ $barClass }}"
                                                style="width: {{ number_format($barWidth, 1) }}%"
                                            ></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $badgeClass }}">
                                        {{ $category }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    @if ($history->product)
                                        <button
                                            type="button"
                                            @click="inspectProduct({{ $history->product->id }}, @js($history->product->name), '{{ $history->created_at?->format('d M Y, H:i') }}')"
                                            :disabled="loadingId === {{ $history->product->id }}"
                                            class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50/70 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 disabled:opacity-60 transition-colors"
                                        >
                                            <span x-text="loadingId === {{ $history->product->id }} ? 'Memuat...' : 'Detail Fuzzy'"></span>
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($histories->hasPages())
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-slate-50/60 px-5 py-3.5 text-xs text-slate-600">
                    <div>
                        Menampilkan <strong>{{ $histories->firstItem() }}</strong>–<strong>{{ $histories->lastItem() }}</strong>
                        dari <strong>{{ $histories->total() }}</strong> riwayat
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($histories->onFirstPage())
                            <span class="rounded-lg border border-slate-200 bg-slate-100 px-3 py-1.5 font-semibold text-slate-400 cursor-not-allowed">
                                &larr; Sebelumnya
                            </span>
                        @else
                            <a href="{{ $histories->previousPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-semibold text-slate-700 hover:bg-slate-50">
                                &larr; Sebelumnya
                            </a>
                        @endif

                        <span class="font-mono font-semibold text-slate-800">
                            Hal {{ $histories->currentPage() }} / {{ $histories->lastPage() }}
                        </span>

                        @if ($histories->hasMorePages())
                            <a href="{{ $histories->nextPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-semibold text-slate-700 hover:bg-slate-50">
                                Berikutnya &rarr;
                            </a>
                        @else
                            <span class="rounded-lg border border-slate-200 bg-slate-100 px-3 py-1.5 font-semibold text-slate-400 cursor-not-allowed">
                                Berikutnya &rarr;
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function historyViewer() {
        return {
            loadingId: null,

            async inspectProduct(productId, productName, calculatedAt) {
                this.loadingId = productId;
                try {
                    const res = await window.FuzzyUi.request(`/products/${productId}/fuzzy-detail`, {
                        method: 'GET',
                    });

                    if (!res.ok || !res.data?.data) {
                        window.FuzzyUi.notify('error', 'Gagal Memuat Analisis', res.data?.message || 'Tidak dapat memuat rincian Fuzzy Mamdani.');
                        return;
                    }

                    const data = res.data.data;
                    window.FuzzyUi.openFuzzyModal({
                        title: `Analisis Fuzzy Mamdani: ${productName}`,
                        subtitle: `Dicatat pada ${calculatedAt} • Analisis berdasarkan parameter produk saat ini`,
                        savedLabel: 'Data Riwayat',
                        result: data,
                    });
                } catch (err) {
                    window.FuzzyUi.notify('error', 'Kesalahan Jaringan', 'Gagal menghubungi server.');
                } finally {
                    this.loadingId = null;
                }
            },
        };
    }
</script>
@endpush
