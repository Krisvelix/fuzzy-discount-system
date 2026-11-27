@props(['resultExpr' => 'result', 'savedBadgeExpr' => 'null'])

<div x-show="{{ $resultExpr }}" class="space-y-5">
    {{-- 1. Hero Rekomendasi Diskon --}}
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-card">
        <div
            class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r"
            :class="window.FuzzyUi.categoryAccentGradient({{ $resultExpr }}?.category)"
        ></div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Rekomendasi Diskon Mamdani
                    </span>
                    <template x-if="{{ $savedBadgeExpr }}">
                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-0.5 text-[11px] font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span x-text="{{ $savedBadgeExpr }}"></span>
                        </span>
                    </template>
                </div>

                <div class="mt-2 flex flex-wrap items-baseline gap-3">
                    <span
                        class="text-4xl sm:text-5xl font-extrabold tracking-tight font-mono"
                        :class="window.FuzzyUi.categoryTextClass({{ $resultExpr }}?.category)"
                        x-text="window.FuzzyUi.formatDiscount({{ $resultExpr }}?.discount) + '%'"
                    ></span>

                    <span
                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold"
                        :class="window.FuzzyUi.categoryBadgeClass({{ $resultExpr }}?.category)"
                    >
                        <span
                            class="h-2 w-2 rounded-full"
                            :class="{
                                'bg-rose-500': {{ $resultExpr }}?.category === 'Diskon Tinggi',
                                'bg-amber-500': {{ $resultExpr }}?.category === 'Diskon Sedang',
                                'bg-emerald-500': {{ $resultExpr }}?.category === 'Diskon Rendah',
                            }"
                        ></span>
                        <span x-text="{{ $resultExpr }}?.category"></span>
                    </span>
                </div>
            </div>

            {{-- Input Summary Pills --}}
            <template x-if="{{ $resultExpr }}?.input">
                <div class="flex flex-wrap gap-2 text-xs">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                        <span class="block text-[10px] font-medium uppercase tracking-wide text-slate-400">Stok</span>
                        <span class="font-mono font-bold text-slate-800" x-text="{{ $resultExpr }}.input.stock"></span>
                        <span class="text-slate-400">/100</span>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                        <span class="block text-[10px] font-medium uppercase tracking-wide text-slate-400">Penyimpanan</span>
                        <span class="font-mono font-bold text-slate-800" x-text="{{ $resultExpr }}.input.storage_duration"></span>
                        <span class="text-slate-400">hari</span>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                        <span class="block text-[10px] font-medium uppercase tracking-wide text-slate-400">Penjualan</span>
                        <span class="font-mono font-bold text-slate-800" x-text="{{ $resultExpr }}.input.monthly_sales"></span>
                        <span class="text-slate-400">/bln</span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Scale Gauge (0% - 50%) --}}
        <div class="mt-5">
            <div class="flex justify-between text-[11px] font-medium text-slate-400 mb-1.5">
                <span>0% (Rendah)</span>
                <span>15% (Sedang)</span>
                <span>30% (Tinggi)</span>
                <span>50% (Maks)</span>
            </div>
            <div class="relative h-3 w-full overflow-hidden rounded-full bg-slate-100">
                <div
                    class="h-full rounded-full bg-gradient-to-r transition-all duration-300"
                    :class="window.FuzzyUi.categoryAccentGradient({{ $resultExpr }}?.category)"
                    :style="{ width: window.FuzzyUi.discountPercentOfMax({{ $resultExpr }}?.discount) }"
                ></div>
            </div>
        </div>
    </div>

    {{-- 2. Detail Fuzzifikasi (Derajat Keanggotaan μ) --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">1. Detail Fuzzifikasi (Derajat Keanggotaan &mu;)</h3>
                <p class="text-xs text-slate-500">Transformasi nilai input crisp ke himpunan fuzzy [0.0000 – 1.0000]</p>
            </div>
            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                3 Variabel Input
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Stok --}}
            <div class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-700">Stok</span>
                    <span class="text-[11px] font-mono text-slate-400">0 – 100</span>
                </div>
                <div class="space-y-2.5">
                    <template x-for="key in ['sedikit', 'sedang', 'banyak']" :key="'stok-' + key">
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="capitalize font-medium text-slate-600" x-text="key"></span>
                                <span
                                    class="font-mono font-semibold"
                                    :class="({{ $resultExpr }}?.fuzzification?.stok?.[key] ?? 0) > 0 ? 'text-indigo-700' : 'text-slate-400'"
                                    x-text="window.FuzzyUi.formatMu({{ $resultExpr }}?.fuzzification?.stok?.[key])"
                                ></span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-slate-200/80 overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-indigo-500 transition-all duration-300"
                                    :style="{ width: window.FuzzyUi.muPercent({{ $resultExpr }}?.fuzzification?.stok?.[key]) }"
                                ></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Lama Penyimpanan --}}
            <div class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-700">Lama Penyimpanan</span>
                    <span class="text-[11px] font-mono text-slate-400">0 – 180 hr</span>
                </div>
                <div class="space-y-2.5">
                    <template x-for="key in ['baru', 'sedang', 'lama']" :key="'penyimpanan-' + key">
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="capitalize font-medium text-slate-600" x-text="key"></span>
                                <span
                                    class="font-mono font-semibold"
                                    :class="({{ $resultExpr }}?.fuzzification?.penyimpanan?.[key] ?? 0) > 0 ? 'text-violet-700' : 'text-slate-400'"
                                    x-text="window.FuzzyUi.formatMu({{ $resultExpr }}?.fuzzification?.penyimpanan?.[key])"
                                ></span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-slate-200/80 overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-violet-500 transition-all duration-300"
                                    :style="{ width: window.FuzzyUi.muPercent({{ $resultExpr }}?.fuzzification?.penyimpanan?.[key]) }"
                                ></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Penjualan --}}
            <div class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-700">Penjualan / Bulan</span>
                    <span class="text-[11px] font-mono text-slate-400">0 – 100</span>
                </div>
                <div class="space-y-2.5">
                    <template x-for="key in ['rendah', 'sedang', 'tinggi']" :key="'penjualan-' + key">
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="capitalize font-medium text-slate-600" x-text="key"></span>
                                <span
                                    class="font-mono font-semibold"
                                    :class="({{ $resultExpr }}?.fuzzification?.penjualan?.[key] ?? 0) > 0 ? 'text-sky-700' : 'text-slate-400'"
                                    x-text="window.FuzzyUi.formatMu({{ $resultExpr }}?.fuzzification?.penjualan?.[key])"
                                ></span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-slate-200/80 overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-sky-500 transition-all duration-300"
                                    :style="{ width: window.FuzzyUi.muPercent({{ $resultExpr }}?.fuzzification?.penjualan?.[key]) }"
                                ></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Nilai Agregasi MAX & Visualisasi Kurva Defuzzifikasi Centroid --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">2. Agregasi MAX (&alpha;) &amp; Defuzzifikasi Centroid</h3>
                <p class="text-xs text-slate-500">Nilai &alpha; maksimum per himpunan output dan area potong kurva diskon (0%–50%)</p>
            </div>
            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                Center of Gravity
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
            {{-- 3 Aggregated Alpha Cards --}}
            <div class="lg:col-span-5 space-y-3">
                {{-- Rendah --}}
                <div class="rounded-xl border border-emerald-200/70 bg-emerald-50/40 p-3.5">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-bold text-emerald-800">Diskon Rendah (0% – 20%)</span>
                        <span class="font-mono font-bold text-emerald-700" x-text="'α = ' + window.FuzzyUi.formatMu({{ $resultExpr }}?.aggregated_alpha?.rendah)"></span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-emerald-100 overflow-hidden">
                        <div
                            class="h-full rounded-full bg-emerald-500 transition-all duration-300"
                            :style="{ width: window.FuzzyUi.muPercent({{ $resultExpr }}?.aggregated_alpha?.rendah) }"
                        ></div>
                    </div>
                </div>

                {{-- Sedang --}}
                <div class="rounded-xl border border-amber-200/70 bg-amber-50/40 p-3.5">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-bold text-amber-800">Diskon Sedang (15% – 35%)</span>
                        <span class="font-mono font-bold text-amber-700" x-text="'α = ' + window.FuzzyUi.formatMu({{ $resultExpr }}?.aggregated_alpha?.sedang)"></span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-amber-100 overflow-hidden">
                        <div
                            class="h-full rounded-full bg-amber-500 transition-all duration-300"
                            :style="{ width: window.FuzzyUi.muPercent({{ $resultExpr }}?.aggregated_alpha?.sedang) }"
                        ></div>
                    </div>
                </div>

                {{-- Tinggi --}}
                <div class="rounded-xl border border-rose-200/70 bg-rose-50/40 p-3.5">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-bold text-rose-800">Diskon Tinggi (30% – 50%)</span>
                        <span class="font-mono font-bold text-rose-700" x-text="'α = ' + window.FuzzyUi.formatMu({{ $resultExpr }}?.aggregated_alpha?.tinggi)"></span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-rose-100 overflow-hidden">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-orange-500 to-rose-500 transition-all duration-300"
                            :style="{ width: window.FuzzyUi.muPercent({{ $resultExpr }}?.aggregated_alpha?.tinggi) }"
                        ></div>
                    </div>
                </div>
            </div>

            {{-- SVG Membership + Clipped Aggregated Area + Centroid Line --}}
            <div class="lg:col-span-7 rounded-xl border border-slate-200/80 bg-slate-50/60 p-3.5">
                <svg viewBox="0 0 360 120" class="w-full h-auto overflow-visible" role="img" aria-label="Grafik Kurva Agregasi Mamdani dan Titik Centroid">
                    {{-- Horizontal Grid --}}
                    <line x1="26" y1="12" x2="346" y2="12" stroke="#e2e8f0" stroke-dasharray="3 3" stroke-width="1" />
                    <line x1="26" y1="55" x2="346" y2="55" stroke="#e2e8f0" stroke-dasharray="3 3" stroke-width="1" />
                    <line x1="26" y1="98" x2="346" y2="98" stroke="#cbd5e1" stroke-width="1.2" />
                    <line x1="26" y1="12" x2="26" y2="98" stroke="#cbd5e1" stroke-width="1.2" />

                    {{-- Y Axis labels --}}
                    <text x="20" y="15" text-anchor="end" class="fill-slate-400 text-[9px] font-mono">1.0</text>
                    <text x="20" y="58" text-anchor="end" class="fill-slate-400 text-[9px] font-mono">0.5</text>
                    <text x="20" y="101" text-anchor="end" class="fill-slate-400 text-[9px] font-mono">0</text>

                    {{-- X Axis labels (0, 10, 20, 30, 40, 50) --}}
                    <text x="26" y="113" text-anchor="middle" class="fill-slate-400 text-[9px] font-mono">0%</text>
                    <text x="90" y="113" text-anchor="middle" class="fill-slate-400 text-[9px] font-mono">10%</text>
                    <text x="154" y="113" text-anchor="middle" class="fill-slate-400 text-[9px] font-mono">20%</text>
                    <text x="218" y="113" text-anchor="middle" class="fill-slate-400 text-[9px] font-mono">30%</text>
                    <text x="282" y="113" text-anchor="middle" class="fill-slate-400 text-[9px] font-mono">40%</text>
                    <text x="346" y="113" text-anchor="middle" class="fill-slate-400 text-[9px] font-mono">50%</text>

                    {{-- Outline Membership Curves --}}
                    {{-- Rendah: (0,1)->(10,1)->(20,0) => x: 26, 90, 154 --}}
                    <polyline points="26,12 90,12 154,98" fill="none" stroke="#10b981" stroke-width="1.5" stroke-opacity="0.55" />
                    {{-- Sedang: (15,0)->(25,1)->(35,0) => x: 122, 186, 250 --}}
                    <polyline points="122,98 186,12 250,98" fill="none" stroke="#f59e0b" stroke-width="1.5" stroke-opacity="0.55" />
                    {{-- Tinggi: (30,0)->(40,1)->(50,1) => x: 218, 282, 346 --}}
                    <polyline points="218,98 282,12 346,12" fill="none" stroke="#f43f5e" stroke-width="1.5" stroke-opacity="0.55" />

                    {{-- Aggregated Clipped Area --}}
                    <path
                        :d="window.FuzzyUi.buildAggregatedAreaPath({{ $resultExpr }}?.aggregated_alpha)"
                        fill="rgba(79, 70, 229, 0.22)"
                        stroke="#4f46e5"
                        stroke-width="1.8"
                    />

                    {{-- Centroid Marker Line --}}
                    <line
                        :x1="window.FuzzyUi.centroidX({{ $resultExpr }}?.discount)"
                        y1="8"
                        :x2="window.FuzzyUi.centroidX({{ $resultExpr }}?.discount)"
                        y2="98"
                        stroke="#1e1b4b"
                        stroke-width="1.8"
                        stroke-dasharray="4 2"
                    />
                    <circle
                        :cx="window.FuzzyUi.centroidX({{ $resultExpr }}?.discount)"
                        cy="98"
                        r="3.5"
                        fill="#4f46e5"
                        stroke="#ffffff"
                        stroke-width="1.5"
                    />
                </svg>
                <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Rendah</span>
                        <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-amber-500"></span>Sedang</span>
                        <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-rose-500"></span>Tinggi</span>
                    </div>
                    <span class="font-mono font-semibold text-indigo-700" x-text="'Centroid z* = ' + window.FuzzyUi.formatDiscount({{ $resultExpr }}?.discount) + '%'"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Daftar Aturan IF-THEN yang Aktif --}}
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-card">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200/80 px-5 py-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">3. Aturan IF-THEN yang Aktif (Implikasi MIN)</h3>
                <p class="text-xs text-slate-500">Daftar aturan dari 27 Rule Base dengan nilai firing strength &alpha; &gt; 0</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200">
                <span x-text="({{ $resultExpr }}?.active_rules?.length ?? 0)"></span>&nbsp;Aturan Aktif
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left w-24">Rule</th>
                        <th class="px-5 py-3 text-left">Kondisi IF-THEN</th>
                        <th class="px-5 py-3 text-left w-36">Output Diskon</th>
                        <th class="px-5 py-3 text-right w-40">Nilai Alpha (&alpha;)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <template x-if="!{{ $resultExpr }}?.active_rules || {{ $resultExpr }}.active_rules.length === 0">
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">
                                Tidak ada aturan fuzzy yang aktif (&alpha; = 0 untuk seluruh aturan).
                            </td>
                        </tr>
                    </template>

                    <template x-for="rule in ({{ $resultExpr }}?.active_rules || [])" :key="rule.rule_number">
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center justify-center rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold text-slate-700"
                                    x-text="'R' + rule.rule_number"
                                ></span>
                            </td>
                            <td class="px-5 py-3 text-xs sm:text-sm text-slate-700">
                                <span x-text="rule.condition"></span>
                                <span class="font-semibold text-indigo-600"> THEN diskon </span>
                                <span class="font-semibold capitalize text-slate-900" x-text="rule.output"></span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                                    :class="window.FuzzyUi.outputSetBadgeClass(rule.output)"
                                    x-text="'Diskon ' + rule.output"
                                ></span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right">
                                <div class="inline-flex flex-col items-end gap-1 min-w-[96px]">
                                    <span class="font-mono text-xs font-bold text-slate-900" x-text="window.FuzzyUi.formatMu(rule.alpha)"></span>
                                    <div class="h-1.5 w-24 rounded-full bg-slate-100 overflow-hidden">
                                        <div
                                            class="h-full rounded-full bg-indigo-500"
                                            :style="{ width: window.FuzzyUi.muPercent(rule.alpha) }"
                                        ></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
