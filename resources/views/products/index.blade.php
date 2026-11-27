@extends('layouts.app')

@section('title', 'Manajemen Produk — FuzzyDiscount')

@section('content')
<div x-data="productManager()" x-init="fetchProducts(1)" class="space-y-6">
    {{-- Page Heading --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200">
                REST API Terintegrasi
            </span>
            <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Manajemen Produk
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Kelola data stok, lama penyimpanan, dan tingkat penjualan produk lalu jalankan rekomendasi diskon Fuzzy Mamdani.
            </p>
        </div>

        <button
            type="button"
            @click="openCreateModal()"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors"
        >
            <span>+ Tambah Produk Baru</span>
        </button>
    </div>

    {{-- Toolbar: Search & Summary --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-md">
            <input
                type="text"
                x-model="search"
                placeholder="Filter nama produk di halaman ini..."
                class="w-full rounded-xl border border-slate-300 bg-slate-50/70 px-4 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            >
        </div>

        <div class="flex items-center gap-3 text-xs text-slate-500">
            <span>
                Total: <strong class="font-mono text-slate-800" x-text="meta.total ?? products.length"></strong> produk
            </span>
            <button
                type="button"
                @click="fetchProducts(meta.current_page || 1)"
                class="rounded-lg border border-slate-200 px-3 py-1.5 font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
            >
                Muat Ulang
            </button>
        </div>
    </div>

    {{-- Product Table Card --}}
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-card">
        {{-- Loading State --}}
        <div x-show="loadingList" class="p-10 text-center text-sm text-slate-500">
            Memuat daftar produk dari server...
        </div>

        {{-- Empty State --}}
        <div x-show="!loadingList && filteredProducts.length === 0" x-cloak class="p-12 text-center">
            <p class="text-base font-bold text-slate-800" x-text="products.length === 0 ? 'Belum Ada Data Produk' : 'Produk Tidak Ditemukan'"></p>
            <p class="mt-1 text-sm text-slate-500" x-text="products.length === 0 ? 'Tambahkan produk pertama untuk mulai menghitung rekomendasi diskon Fuzzy Mamdani.' : 'Coba kata kunci pencarian lain atau hapus filter.'"></p>
            <button
                x-show="products.length === 0"
                type="button"
                @click="openCreateModal()"
                class="mt-4 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-indigo-700"
            >
                + Tambah Produk Pertama
            </button>
        </div>

        {{-- Data Table --}}
        <div x-show="!loadingList && filteredProducts.length > 0" x-cloak class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 text-left">Nama Produk</th>
                        <th class="px-5 py-3.5 text-left">Stok (0–100)</th>
                        <th class="px-5 py-3.5 text-left">Lama Penyimpanan</th>
                        <th class="px-5 py-3.5 text-left">Penjualan / Bulan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900" x-text="product.name"></div>
                                <div class="text-xs text-slate-400" x-text="'ID #' + product.id + ' • Diperbarui ' + window.FuzzyUi.formatDateTime(product.updated_at)"></div>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-mono font-bold text-slate-800 w-9" x-text="product.stock"></span>
                                    <div class="h-1.5 w-20 rounded-full bg-slate-100 overflow-hidden hidden sm:block">
                                        <div class="h-full rounded-full bg-indigo-500" :style="{ width: Math.min(100, product.stock) + '%' }"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-mono font-semibold text-slate-800" x-text="product.storage_duration"></span>
                                <span class="text-xs text-slate-500">hari</span>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-mono font-semibold text-slate-800" x-text="product.monthly_sales"></span>
                                <span class="text-xs text-slate-500">unit/bln</span>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex flex-wrap items-center justify-end gap-1.5">
                                    <button
                                        type="button"
                                        @click="calculateDiscount(product)"
                                        :disabled="calculatingId === product.id"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 disabled:opacity-60 transition-colors"
                                    >
                                        <span x-text="calculatingId === product.id ? 'Menghitung...' : 'Hitung Diskon'"></span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="openProductHistory(product)"
                                        class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors"
                                    >
                                        Riwayat
                                    </button>

                                    <button
                                        type="button"
                                        @click="openEditModal(product)"
                                        class="rounded-lg border border-amber-200 bg-amber-50/70 px-2.5 py-1.5 text-xs font-semibold text-amber-800 hover:bg-amber-100/80 transition-colors"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        @click="openDeleteModal(product)"
                                        class="rounded-lg border border-rose-200 bg-rose-50/70 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100/80 transition-colors"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination Bar --}}
        <div
            x-show="meta.last_page > 1"
            x-cloak
            class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-slate-50/60 px-5 py-3.5 text-xs text-slate-600"
        >
            <div>
                Menampilkan <strong x-text="meta.from || 0"></strong>–<strong x-text="meta.to || 0"></strong>
                dari <strong x-text="meta.total || 0"></strong> produk
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="fetchProducts(meta.current_page - 1)"
                    :disabled="meta.current_page <= 1"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                >
                    &larr; Sebelumnya
                </button>
                <span class="font-mono font-semibold text-slate-800" x-text="`Hal ${meta.current_page} / ${meta.last_page}`"></span>
                <button
                    type="button"
                    @click="fetchProducts(meta.current_page + 1)"
                    :disabled="meta.current_page >= meta.last_page"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                >
                    Berikutnya &rarr;
                </button>
            </div>
        </div>
    </div>

    {{-- Modal: Tambah / Edit Produk --}}
    <div
        x-show="formModalOpen"
        x-cloak
        x-on:keydown.escape.window="if (formModalOpen) closeFormModal()"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div @click="closeFormModal()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900" x-text="editingProduct ? 'Edit Data Produk' : 'Tambah Produk Baru'"></h2>
                        <p class="text-xs text-slate-500">Pastikan nilai sesuai domain Fuzzy Mamdani.</p>
                    </div>
                    <button type="button" @click="closeFormModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                        &times;
                    </button>
                </div>

                <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                    <div>
                        <label for="prod-name" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Nama Produk
                        </label>
                        <input
                            id="prod-name"
                            type="text"
                            required
                            x-model="form.name"
                            placeholder="Contoh: Jaket Denim Oversize"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                        <p x-show="formErrors.name" x-text="formErrors.name?.[0]" class="mt-1 text-xs font-medium text-rose-600"></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="prod-stock" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                                Stok (0–100)
                            </label>
                            <input
                                id="prod-stock"
                                type="number"
                                min="0"
                                max="100"
                                step="1"
                                required
                                x-model.number="form.stock"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 font-mono text-sm font-semibold text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >
                            <p x-show="formErrors.stock" x-text="formErrors.stock?.[0]" class="mt-1 text-xs font-medium text-rose-600"></p>
                        </div>

                        <div>
                            <label for="prod-storage" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                                Simpan (0–180 hr)
                            </label>
                            <input
                                id="prod-storage"
                                type="number"
                                min="0"
                                max="180"
                                step="1"
                                required
                                x-model.number="form.storage_duration"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 font-mono text-sm font-semibold text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >
                            <p x-show="formErrors.storage_duration" x-text="formErrors.storage_duration?.[0]" class="mt-1 text-xs font-medium text-rose-600"></p>
                        </div>

                        <div>
                            <label for="prod-sales" class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                                Penjualan (0–100)
                            </label>
                            <input
                                id="prod-sales"
                                type="number"
                                min="0"
                                max="100"
                                step="1"
                                required
                                x-model.number="form.monthly_sales"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 font-mono text-sm font-semibold text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >
                            <p x-show="formErrors.monthly_sales" x-text="formErrors.monthly_sales?.[0]" class="mt-1 text-xs font-medium text-rose-600"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            @click="closeFormModal()"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="submittingForm"
                            class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
                        >
                            <span x-text="submittingForm ? 'Menyimpan...' : (editingProduct ? 'Simpan Perubahan' : 'Simpan Produk')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Konfirmasi Hapus Produk --}}
    <div
        x-show="deleteModalOpen"
        x-cloak
        x-on:keydown.escape.window="if (deleteModalOpen) closeDeleteModal()"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div @click="closeDeleteModal()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-slate-200 space-y-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Hapus Produk?</h2>
                    <p class="mt-1.5 text-sm text-slate-600">
                        Anda akan menghapus produk <strong class="text-slate-900" x-text="deletingProduct?.name"></strong>.
                        Seluruh riwayat perhitungan diskon milik produk ini juga akan terhapus permanen.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button
                        type="button"
                        @click="closeDeleteModal()"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="confirmDelete()"
                        :disabled="deleting"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 disabled:opacity-60"
                    >
                        <span x-text="deleting ? 'Menghapus...' : 'Ya, Hapus Produk'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Riwayat Per Produk --}}
    <div
        x-show="historyModalOpen"
        x-cloak
        x-on:keydown.escape.window="if (historyModalOpen) historyModalOpen = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div @click="historyModalOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900" x-text="'Riwayat Diskon: ' + (historyProduct?.name || '')"></h2>
                        <p class="text-xs text-slate-500">Data diambil langsung dari endpoint riwayat produk.</p>
                    </div>
                    <button type="button" @click="historyModalOpen = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                        &times;
                    </button>
                </div>

                <div class="max-h-[65vh] overflow-y-auto p-6">
                    <div x-show="loadingProductHistory" class="py-8 text-center text-sm text-slate-500">
                        Memuat riwayat perhitungan...
                    </div>

                    <div x-show="!loadingProductHistory && productHistories.length === 0" x-cloak class="py-8 text-center text-sm text-slate-500">
                        Belum ada riwayat perhitungan diskon untuk produk ini.
                    </div>

                    <div x-show="!loadingProductHistory && productHistories.length > 0" x-cloak class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">ID</th>
                                    <th class="px-4 py-2.5 text-left">Waktu Perhitungan</th>
                                    <th class="px-4 py-2.5 text-right">Diskon</th>
                                    <th class="px-4 py-2.5 text-left">Kategori</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in productHistories" :key="item.id">
                                    <tr>
                                        <td class="px-4 py-2.5 font-mono text-xs text-slate-500" x-text="'#' + item.id"></td>
                                        <td class="px-4 py-2.5 text-xs text-slate-700" x-text="window.FuzzyUi.formatDateTime(item.created_at)"></td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-900" x-text="window.FuzzyUi.formatDiscount(item.discount_percentage) + '%'"></td>
                                        <td class="px-4 py-2.5">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                                :class="window.FuzzyUi.categoryBadgeClass(classifyDiscount(item.discount_percentage))"
                                                x-text="classifyDiscount(item.discount_percentage)"
                                            ></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-200 bg-slate-50 px-6 py-3">
                    <button
                        type="button"
                        @click="historyModalOpen = false"
                        class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function productManager() {
        return {
            products: [],
            meta: { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 },
            search: '',
            loadingList: true,
            calculatingId: null,

            formModalOpen: false,
            editingProduct: null,
            submittingForm: false,
            formErrors: {},
            form: {
                name: '',
                stock: 50,
                storage_duration: 60,
                monthly_sales: 40,
            },

            deleteModalOpen: false,
            deletingProduct: null,
            deleting: false,

            historyModalOpen: false,
            historyProduct: null,
            productHistories: [],
            loadingProductHistory: false,

            get filteredProducts() {
                const keyword = this.search.trim().toLowerCase();
                if (!keyword) return this.products;
                return this.products.filter((p) => p.name.toLowerCase().includes(keyword));
            },

            classifyDiscount(val) {
                const n = Number(val ?? 0);
                if (n >= 30) return 'Diskon Tinggi';
                if (n >= 15) return 'Diskon Sedang';
                return 'Diskon Rendah';
            },

            async fetchProducts(page = 1) {
                this.loadingList = true;
                try {
                    const res = await window.FuzzyUi.request(`/api/products?page=${page}`, { method: 'GET' });
                    if (!res.ok) {
                        window.FuzzyUi.notify('error', 'Gagal Memuat Produk', res.data?.message || 'Tidak dapat mengambil daftar produk.');
                        return;
                    }
                    this.products = res.data?.data || [];
                    this.meta = res.data?.meta || { current_page: 1, last_page: 1, total: this.products.length, from: 1, to: this.products.length };
                } catch (err) {
                    window.FuzzyUi.notify('error', 'Kesalahan Jaringan', 'Gagal menghubungi endpoint /api/products.');
                } finally {
                    this.loadingList = false;
                }
            },

            openCreateModal() {
                this.editingProduct = null;
                this.formErrors = {};
                this.form = { name: '', stock: 50, storage_duration: 60, monthly_sales: 40 };
                this.formModalOpen = true;
            },

            openEditModal(product) {
                this.editingProduct = product;
                this.formErrors = {};
                this.form = {
                    name: product.name,
                    stock: product.stock,
                    storage_duration: product.storage_duration,
                    monthly_sales: product.monthly_sales,
                };
                this.formModalOpen = true;
            },

            closeFormModal() {
                this.formModalOpen = false;
                this.editingProduct = null;
                this.formErrors = {};
            },

            async submitForm() {
                this.submittingForm = true;
                this.formErrors = {};

                const isEdit = Boolean(this.editingProduct);
                const url = isEdit ? `/api/products/${this.editingProduct.id}` : '/api/products';
                const method = isEdit ? 'PUT' : 'POST';

                try {
                    const res = await window.FuzzyUi.request(url, {
                        method,
                        body: JSON.stringify({
                            name: this.form.name,
                            stock: this.form.stock,
                            storage_duration: this.form.storage_duration,
                            monthly_sales: this.form.monthly_sales,
                        }),
                    });

                    if (res.status === 422 && res.data?.errors) {
                        this.formErrors = res.data.errors;
                        window.FuzzyUi.notify('error', 'Validasi Gagal', 'Periksa kembali kolom yang ditandai merah.');
                        return;
                    }

                    if (!res.ok) {
                        window.FuzzyUi.notify('error', 'Operasi Gagal', res.data?.message || 'Gagal menyimpan data produk.');
                        return;
                    }

                    this.closeFormModal();
                    window.FuzzyUi.notify(
                        'success',
                        isEdit ? 'Produk Diperbarui' : 'Produk Ditambahkan',
                        `Data "${res.data?.data?.name || this.form.name}" berhasil disimpan.`
                    );
                    await this.fetchProducts(this.meta.current_page || 1);
                } catch (err) {
                    window.FuzzyUi.notify('error', 'Kesalahan Jaringan', 'Gagal mengirim data ke server.');
                } finally {
                    this.submittingForm = false;
                }
            },

            openDeleteModal(product) {
                this.deletingProduct = product;
                this.deleteModalOpen = true;
            },

            closeDeleteModal() {
                this.deleteModalOpen = false;
                this.deletingProduct = null;
            },

            async confirmDelete() {
                if (!this.deletingProduct) return;
                this.deleting = true;
                const deletedName = this.deletingProduct.name;

                try {
                    const res = await window.FuzzyUi.request(`/api/products/${this.deletingProduct.id}`, {
                        method: 'DELETE',
                    });

                    if (!res.ok) {
                        window.FuzzyUi.notify('error', 'Gagal Menghapus', res.data?.message || 'Tidak dapat menghapus produk.');
                        return;
                    }

                    this.closeDeleteModal();
                    window.FuzzyUi.notify('success', 'Produk Dihapus', `Produk "${deletedName}" beserta riwayatnya telah dihapus.`);

                    const targetPage = this.products.length <= 1 && this.meta.current_page > 1
                        ? this.meta.current_page - 1
                        : (this.meta.current_page || 1);
                    await this.fetchProducts(targetPage);
                } catch (err) {
                    window.FuzzyUi.notify('error', 'Kesalahan Jaringan', 'Gagal menghapus produk.');
                } finally {
                    this.deleting = false;
                }
            },

            async calculateDiscount(product) {
                this.calculatingId = product.id;

                try {
                    // 1. Panggil endpoint Anggota 3 agar hasil dihitung & tersimpan ke discount_histories
                    const calcRes = await window.FuzzyUi.request(`/api/products/${product.id}/discount-calculations`, {
                        method: 'POST',
                    });

                    if (!calcRes.ok) {
                        window.FuzzyUi.notify('error', 'Perhitungan Gagal', calcRes.data?.message || 'Gagal menghitung diskon produk.');
                        return;
                    }

                    // 2. Ambil rincian lengkap Mamdani (fuzzification, active_rules, aggregated_alpha)
                    const detailRes = await window.FuzzyUi.request(`/products/${product.id}/fuzzy-detail`, {
                        method: 'GET',
                    });

                    if (!detailRes.ok || !detailRes.data?.data) {
                        window.FuzzyUi.notify('success', 'Diskon Tersimpan', `Diskon ${calcRes.data?.data?.discount_percentage}% berhasil disimpan ke riwayat.`);
                        return;
                    }

                    const fuzzyResult = detailRes.data.data;
                    const savedPct = calcRes.data?.data?.discount_percentage ?? window.FuzzyUi.formatDiscount(fuzzyResult.discount);

                    window.FuzzyUi.notify(
                        'success',
                        'Perhitungan Diskon Berhasil',
                        `${product.name}: ${savedPct}% (${fuzzyResult.category}) tersimpan di riwayat.`
                    );

                    window.FuzzyUi.openFuzzyModal({
                        title: `Hasil Rekomendasi: ${product.name}`,
                        subtitle: `Stok ${product.stock} • Penyimpanan ${product.storage_duration} hari • Penjualan ${product.monthly_sales}/bulan`,
                        savedLabel: `Tersimpan di Riwayat (#${calcRes.data?.data?.id ?? ''})`,
                        result: fuzzyResult,
                    });
                } catch (err) {
                    window.FuzzyUi.notify('error', 'Kesalahan Jaringan', 'Gagal memproses perhitungan diskon.');
                } finally {
                    this.calculatingId = null;
                }
            },

            async openProductHistory(product) {
                this.historyProduct = product;
                this.productHistories = [];
                this.historyModalOpen = true;
                this.loadingProductHistory = true;

                try {
                    const res = await window.FuzzyUi.request(`/api/products/${product.id}/discount-histories`, {
                        method: 'GET',
                    });

                    if (!res.ok) {
                        window.FuzzyUi.notify('error', 'Gagal Memuat Riwayat', res.data?.message || 'Tidak dapat mengambil riwayat produk.');
                        return;
                    }

                    this.productHistories = res.data?.data || [];
                } catch (err) {
                    window.FuzzyUi.notify('error', 'Kesalahan Jaringan', 'Gagal menghubungi server.');
                } finally {
                    this.loadingProductHistory = false;
                }
            },
        };
    }
</script>
@endpush
