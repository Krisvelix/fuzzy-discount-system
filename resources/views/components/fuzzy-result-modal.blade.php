<div
    x-data="fuzzyResultModal()"
    x-on:open-fuzzy-modal.window="open($event.detail)"
    x-on:keydown.escape.window="if (isOpen) close()"
    x-show="isOpen"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 bg-slate-900/55 backdrop-blur-sm"
    ></div>

    {{-- Dialog Panel --}}
    <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
        <div
            x-show="isOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 sm:scale-95"
            class="relative w-full max-w-4xl rounded-2xl bg-slate-50 shadow-2xl ring-1 ring-slate-200 overflow-hidden"
        >
            {{-- Modal Header --}}
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-white px-6 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900" x-text="title"></h2>
                    <p x-show="subtitle" class="mt-0.5 text-xs text-slate-500" x-text="subtitle"></p>
                </div>
                <button
                    type="button"
                    @click="close()"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors"
                    aria-label="Tutup modal hasil fuzzy"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="max-h-[78vh] overflow-y-auto p-5 sm:p-6">
                <x-fuzzy-result-panel resultExpr="result" savedBadgeExpr="savedLabel" />
            </div>

            {{-- Modal Footer --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-white px-6 py-3.5">
                <a
                    href="{{ route('ui.histories.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                >
                    <span>Lihat Semua Riwayat Perhitungan</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <button
                    type="button"
                    @click="close()"
                    class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition-colors"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function fuzzyResultModal() {
        return {
            isOpen: false,
            title: 'Hasil Rekomendasi Fuzzy Mamdani',
            subtitle: '',
            savedLabel: null,
            result: null,

            open(payload = {}) {
                this.title = payload.title || 'Hasil Rekomendasi Fuzzy Mamdani';
                this.subtitle = payload.subtitle || '';
                this.savedLabel = payload.savedLabel || null;
                this.result = payload.result || null;
                this.isOpen = true;
            },

            close() {
                this.isOpen = false;
            },
        };
    }
</script>
