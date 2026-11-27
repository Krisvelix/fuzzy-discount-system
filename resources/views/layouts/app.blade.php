<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Rekomendasi Diskon Fuzzy Mamdani')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                    },
                    boxShadow: {
                        card: '0 1px 3px 0 rgb(15 23 42 / 0.05), 0 1px 2px -1px rgb(15 23 42 / 0.04)',
                        soft: '0 10px 30px -12px rgb(15 23 42 / 0.12)',
                    },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        input[type="range"] {
            accent-color: #4f46e5;
        }
    </style>

    @stack('head')
</head>
<body class="min-h-full flex flex-col text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-900">

    {{-- Top Navigation Bar --}}
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                {{-- Brand --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white shadow-sm shadow-indigo-500/25 transition-transform group-hover:scale-[1.03]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div class="flex items-center gap-2">
                            <span class="text-base font-bold tracking-tight text-slate-900">FuzzyDiscount</span>
                            <span class="hidden sm:inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200">
                                Mamdani
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Sistem Rekomendasi Diskon Produk</p>
                    </div>
                </a>

                {{-- Desktop Navigation (3 Main Menus) --}}
                <nav class="hidden md:flex items-center gap-1" aria-label="Navigasi Utama">
                    <a
                        href="{{ route('dashboard') }}"
                        @class([
                            'inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors',
                            'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-200/80' => request()->routeIs('dashboard'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs('dashboard'),
                        ])
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                        </svg>
                        <span>Dashboard / Simulasi Diskon</span>
                    </a>

                    <a
                        href="{{ route('ui.products.index') }}"
                        @class([
                            'inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors',
                            'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-200/80' => request()->routeIs('ui.products.*'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs('ui.products.*'),
                        ])
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                        <span>Manajemen Produk</span>
                    </a>

                    <a
                        href="{{ route('ui.histories.index') }}"
                        @class([
                            'inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors',
                            'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-200/80' => request()->routeIs('ui.histories.*'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs('ui.histories.*'),
                        ])
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Riwayat Perhitungan Diskon</span>
                    </a>
                </nav>

                {{-- Mobile Menu Toggle --}}
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="inline-flex md:hidden items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    aria-label="Buka menu navigasi"
                >
                    <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Dropdown Menu --}}
        <div x-show="mobileOpen" x-transition x-cloak class="border-t border-slate-200 bg-white md:hidden">
            <div class="space-y-1 px-4 py-3">
                <a
                    href="{{ route('dashboard') }}"
                    @class([
                        'flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-semibold',
                        'bg-indigo-50 text-indigo-700' => request()->routeIs('dashboard'),
                        'text-slate-700 hover:bg-slate-100' => ! request()->routeIs('dashboard'),
                    ])
                >
                    <span>Dashboard / Simulasi Diskon</span>
                </a>
                <a
                    href="{{ route('ui.products.index') }}"
                    @class([
                        'flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-semibold',
                        'bg-indigo-50 text-indigo-700' => request()->routeIs('ui.products.*'),
                        'text-slate-700 hover:bg-slate-100' => ! request()->routeIs('ui.products.*'),
                    ])
                >
                    <span>Manajemen Produk</span>
                </a>
                <a
                    href="{{ route('ui.histories.index') }}"
                    @class([
                        'flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-semibold',
                        'bg-indigo-50 text-indigo-700' => request()->routeIs('ui.histories.*'),
                        'text-slate-700 hover:bg-slate-100' => ! request()->routeIs('ui.histories.*'),
                    ])
                >
                    <span>Riwayat Perhitungan Diskon</span>
                </a>
            </div>
        </div>
    </header>

    {{-- Global Session Flash Alerts --}}
    @if (session('success') || session('error'))
        <div class="mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <svg class="h-5 w-5 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1 mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-auto border-t border-slate-200/80 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
            <p class="font-medium text-slate-600">
                FuzzyDiscount &mdash; Sistem Rekomendasi Diskon Produk Berbasis Logika Fuzzy Mamdani
            </p>
            <p>
                27 Aturan IF-THEN &bull; Implikasi MIN &bull; Agregasi MAX &bull; Defuzzifikasi Centroid (0%–50%)
            </p>
        </div>
    </footer>

    {{-- Global Toast Notification Stack --}}
    <div
        x-data="toastManager()"
        x-on:toast.window="add($event.detail)"
        class="pointer-events-none fixed bottom-4 right-4 z-50 flex w-full max-w-sm flex-col gap-2.5 sm:bottom-6 sm:right-6"
        aria-live="polite"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transform transition ease-out duration-200"
                x-transition:enter-start="translate-y-2 opacity-0 scale-95"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="pointer-events-auto flex items-start gap-3 rounded-xl border bg-white p-4 shadow-soft"
                :class="{
                    'border-emerald-200': toast.type === 'success',
                    'border-rose-200': toast.type === 'error',
                    'border-indigo-200': toast.type === 'info' || !toast.type,
                }"
            >
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                    :class="{
                        'bg-emerald-50 text-emerald-600': toast.type === 'success',
                        'bg-rose-50 text-rose-600': toast.type === 'error',
                        'bg-indigo-50 text-indigo-600': toast.type === 'info' || !toast.type,
                    }"
                >
                    <template x-if="toast.type === 'success'">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </template>
                    <template x-if="toast.type !== 'success' && toast.type !== 'error'">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                    </template>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-900" x-text="toast.title"></p>
                    <p x-show="toast.message" class="mt-0.5 text-xs leading-relaxed text-slate-600" x-text="toast.message"></p>
                </div>

                <button
                    type="button"
                    @click="dismiss(toast.id)"
                    class="rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                    aria-label="Tutup notifikasi"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    {{-- Shared Fuzzy Result Modal (can be opened from any page) --}}
    @include('components.fuzzy-result-modal')

    {{-- Shared Frontend Utilities --}}
    <script>
        window.FuzzyUi = {
            csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            },

            notify(type, title, message = '') {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { type, title, message },
                }));
            },

            openFuzzyModal(payload) {
                window.dispatchEvent(new CustomEvent('open-fuzzy-modal', {
                    detail: payload,
                }));
            },

            async request(url, options = {}) {
                const headers = {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken(),
                    ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                    ...(options.headers || {}),
                };

                const response = await fetch(url, {
                    ...options,
                    headers,
                });

                if (response.status === 204) {
                    return { ok: true, status: 204, data: null };
                }

                const json = await response.json().catch(() => null);

                return {
                    ok: response.ok,
                    status: response.status,
                    data: json,
                };
            },

            formatDiscount(val) {
                const num = Number(val ?? 0);
                return Number.isFinite(num) ? num.toFixed(2) : '0.00';
            },

            formatMu(val) {
                const num = Number(val ?? 0);
                return Number.isFinite(num) ? num.toFixed(4) : '0.0000';
            },

            muPercent(val) {
                const num = Math.max(0, Math.min(1, Number(val ?? 0)));
                return `${(num * 100).toFixed(1)}%`;
            },

            discountPercentOfMax(val) {
                const num = Math.max(0, Math.min(50, Number(val ?? 0)));
                return `${((num / 50) * 100).toFixed(1)}%`;
            },

            categoryBadgeClass(category) {
                if (category === 'Diskon Tinggi') {
                    return 'bg-gradient-to-r from-rose-50 to-orange-50 text-rose-700 ring-1 ring-inset ring-rose-200';
                }
                if (category === 'Diskon Sedang') {
                    return 'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200';
                }
                return 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200';
            },

            categoryAccentGradient(category) {
                if (category === 'Diskon Tinggi') {
                    return 'from-rose-500 to-orange-500';
                }
                if (category === 'Diskon Sedang') {
                    return 'from-amber-500 to-sky-500';
                }
                return 'from-emerald-500 to-teal-500';
            },

            categoryTextClass(category) {
                if (category === 'Diskon Tinggi') {
                    return 'text-rose-600';
                }
                if (category === 'Diskon Sedang') {
                    return 'text-amber-600';
                }
                return 'text-emerald-600';
            },

            outputSetBadgeClass(output) {
                if (output === 'tinggi') {
                    return 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200';
                }
                if (output === 'sedang') {
                    return 'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200';
                }
                return 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200';
            },

            // Exact membership functions matching App\Services\Fuzzy\MamdaniInference::getDiscountMembership()
            trapezoid(x, a, b, c, d) {
                if (x <= a) return a === b ? 1 : 0;
                if (x >= d) return c === d ? 1 : 0;
                if (x >= b && x <= c) return 1;
                if (x > a && x < b) return (x - a) / (b - a);
                if (x > c && x < d) return (d - x) / (d - c);
                return 0;
            },

            triangle(x, a, b, c) {
                if (x <= a || x >= c) return 0;
                if (x === b) return 1;
                if (x > a && x < b) return (x - a) / (b - a);
                return (c - x) / (c - b);
            },

            discountMu(z, category) {
                if (category === 'rendah') return this.trapezoid(z, 0, 0, 10, 20);
                if (category === 'sedang') return this.triangle(z, 15, 25, 35);
                if (category === 'tinggi') return this.trapezoid(z, 30, 40, 50, 50);
                return 0;
            },

            // Build SVG polygon path for clipped Mamdani aggregated output area (Domain 0..50, ViewBox 360x120)
            buildAggregatedAreaPath(aggregatedAlpha) {
                if (!aggregatedAlpha) return '';
                const alphaR = Number(aggregatedAlpha.rendah ?? 0);
                const alphaS = Number(aggregatedAlpha.sedang ?? 0);
                const alphaT = Number(aggregatedAlpha.tinggi ?? 0);

                const padL = 26;
                const padR = 14;
                const padT = 12;
                const padB = 22;
                const width = 360 - padL - padR;
                const height = 120 - padT - padB;

                const toX = (z) => padL + (z / 50) * width;
                const toY = (mu) => padT + (1 - mu) * height;

                const points = [`M ${toX(0).toFixed(1)} ${toY(0).toFixed(1)}`];
                for (let i = 0; i <= 100; i++) {
                    const z = i * 0.5;
                    const muR = Math.min(alphaR, this.discountMu(z, 'rendah'));
                    const muS = Math.min(alphaS, this.discountMu(z, 'sedang'));
                    const muT = Math.min(alphaT, this.discountMu(z, 'tinggi'));
                    const combined = Math.max(muR, muS, muT);
                    points.push(`L ${toX(z).toFixed(1)} ${toY(combined).toFixed(1)}`);
                }
                points.push(`L ${toX(50).toFixed(1)} ${toY(0).toFixed(1)} Z`);
                return points.join(' ');
            },

            centroidX(discount) {
                const z = Math.max(0, Math.min(50, Number(discount ?? 0)));
                return (26 + (z / 50) * (360 - 26 - 14)).toFixed(1);
            },

            formatDateTime(isoString) {
                if (!isoString) return '-';
                const date = new Date(isoString);
                if (Number.isNaN(date.getTime())) return isoString;
                return new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                }).format(date);
            },
        };

        function toastManager() {
            return {
                toasts: [],
                nextId: 1,
                add({ type = 'info', title = 'Informasi', message = '', duration = 4200 } = {}) {
                    const id = this.nextId++;
                    this.toasts.push({ id, type, title, message, visible: true });
                    setTimeout(() => this.dismiss(id), duration);
                },
                dismiss(id) {
                    const target = this.toasts.find((item) => item.id === id);
                    if (target) {
                        target.visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter((item) => item.id !== id);
                        }, 180);
                    }
                },
            };
        }
    </script>

    @stack('scripts')
</body>
</html>
