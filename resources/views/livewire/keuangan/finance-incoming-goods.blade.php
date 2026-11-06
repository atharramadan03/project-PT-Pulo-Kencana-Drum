<div class="space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex-1 min-w-0">
            <h1 class="font-bold text-lg sm:text-xl text-[#0F172A] tracking-tight leading-snug">
                Data Barang Masuk & Pengadaan
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5 leading-relaxed">
                Pencatatan pengadaan drum baku, cat epoxy, thinner, dan bahan kimia yang dipasok dari supplier
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <a 
                href="{{ route('keuangan.export-excel') }}?jenis=barang_masuk"
                class="px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
            >
                <x-icon name="file-spreadsheet" class="w-4 h-4 shrink-0" />
                <span>Export Excel</span>
            </a>
            @if (auth()->user()->isAdminKeuangan())
                <button 
                    wire:click="bukaCreateModal"
                    type="button" 
                    class="px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
                >
                    <x-icon name="plus" class="w-4 h-4 shrink-0" />
                    <span>Catat Barang Masuk</span>
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="p-3.5 rounded-xl bg-[#DCFCE7] border border-green-200 text-[#166534] flex items-center gap-2 text-xs font-medium">
            <x-icon name="circle-check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Total Pengadaan</span>
                <x-icon name="arrow-down-left" class="w-4 h-4 text-rose-500" />
            </div>
            <p class="text-base sm:text-lg font-bold text-[#0F172A] mt-1 tabular-nums">
                <x-animated-number :value="$totalPengeluaranMasuk" prefix="Rp " />
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Akumulasi biaya beli</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Volume Masuk</span>
                <x-icon name="package" class="w-4 h-4 text-[#2563EB]" />
            </div>
            <p class="text-base sm:text-lg font-bold text-[#0F172A] mt-1 tabular-nums">
                <x-animated-number :value="$totalUnitMasuk" suffix=" Unit" />
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Total drum & item fisik</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Transaksi Bulan Ini</span>
                <x-icon name="calendar" class="w-4 h-4 text-amber-500" />
            </div>
            <p class="text-base sm:text-lg font-bold text-[#0F172A] mt-1 tabular-nums">
                <x-animated-number :value="$totalTransaksiBulanIni" suffix=" Pengiriman" />
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">{{ now()->translatedFormat('F Y') }}</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Supplier Aktif</span>
                <x-icon name="truck" class="w-4 h-4 text-emerald-600" />
            </div>
            <p class="text-base sm:text-lg font-bold text-[#0F172A] mt-1 tabular-nums">
                <x-animated-number :value="$totalSupplierAktif" suffix=" Rekanan" />
            </p>
            <a href="{{ route('keuangan.suppliers') }}" wire:navigate class="text-[11px] text-[#2563EB] hover:underline mt-1 block">Lihat data supplier &rarr;</a>
        </div>
    </div>

    <!-- Filter & Table Data -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-[#E2E8F0] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <div class="relative w-full md:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#64748B]">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    placeholder="Cari kode, supplier, keterangan..." 
                    class="w-full pl-9 pr-3 py-1.5 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[#0F172A] placeholder:text-[#94A3B8] focus:bg-white focus:border-[#2563EB] focus:outline-hidden"
                />
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select 
                    wire:model.live="filterKategori"
                    class="py-1.5 px-3 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[#0F172A] focus:bg-white focus:border-[#2563EB]"
                >
                    <option value="">Semua Kategori</option>
                    <option value="pembelian_batch">Pembelian Drum</option>
                    <option value="bahan_operasional">Bahan Cat & Chemical</option>
                    <option value="sparepart">Suku Cadang & Perlengkapan</option>
                </select>

                <select 
                    wire:model.live="filterSupplier"
                    class="py-1.5 px-3 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[#0F172A] focus:bg-white focus:border-[#2563EB]"
                >
                    <option value="">Semua Supplier</option>
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                    @endforeach
                </select>

                <select 
                    wire:model.live="filterMetode"
                    class="py-1.5 px-3 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[#0F172A] focus:bg-white focus:border-[#2563EB]"
                >
                    <option value="">Semua Metode Bayar</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="tunai">Tunai Kas</option>
                    <option value="tempo">Tempo / Termin</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                    <tr>
                        <th class="py-2.5 px-4 whitespace-nowrap">Kode Masuk</th>
                        <th class="py-2.5 px-4 whitespace-nowrap">Tanggal</th>
                        <th class="py-2.5 px-4 whitespace-nowrap">Supplier / Pemasok</th>
                        <th class="py-2.5 px-4 whitespace-nowrap">Kategori Pasokan</th>
                        <th class="py-2.5 px-4 whitespace-nowrap text-right">Kuantitas</th>
                        <th class="py-2.5 px-4 whitespace-nowrap text-right">Biaya Pengadaan</th>
                        <th class="py-2.5 px-4 whitespace-nowrap">Metode Bayar</th>
                        <th class="py-2.5 px-4 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($barangMasukList as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                {{ $item->kode_transaksi }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                {{ $item->tanggal_transaksi?->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-2.5 px-4 font-medium text-[#0F172A] whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $item->pihak_terkait }}</span>
                                    @if ($item->supplier)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] bg-blue-50 text-blue-700 font-medium">Rekanan</span>
                                    @endif
                                </div>
                                @if ($item->keterangan)
                                    <p class="text-[11px] text-[#64748B] truncate max-w-xs mt-0.5">{{ $item->keterangan }}</p>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap">
                                <span class="capitalize">{{ str_replace('_', ' ', $item->kategori) }}</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-[#0F172A] whitespace-nowrap tabular-nums">
                                {{ $item->jumlah_unit > 0 ? number_format($item->jumlah_unit, 0, ',', '.') . ' Unit' : '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-semibold text-rose-600 whitespace-nowrap tabular-nums">
                                {{ $item->formatted_nominal }}
                            </td>
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $item->metode_pembayaran === 'transfer' ? 'bg-blue-50 text-[#2563EB]' : ($item->metode_pembayaran === 'tunai' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ ucfirst($item->metode_pembayaran) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                @if (auth()->user()->isAdminKeuangan())
                                    <button 
                                        wire:click="konfirmasiHapus({{ $item->id }})"
                                        type="button" 
                                        title="Hapus Data Barang Masuk"
                                        class="p-1 rounded-md text-[#64748B] hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                    >
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-xs text-[#64748B]">
                                Tidak ada data barang masuk yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($barangMasukList->hasPages())
            <div class="p-3 border-t border-[#E2E8F0]">
                {{ $barangMasukList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Tambah Barang Masuk -->
    <div 
        x-data="{ show: @entangle('showCreateModal') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
    >
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="$wire.tutupCreateModal()"></div>

        <div class="min-h-screen px-4 flex items-center justify-center py-6">
            <div class="relative bg-white rounded-2xl max-w-xl w-full border border-[#E2E8F0] shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-[#0F172A]">Catat Barang Masuk dari Supplier</h2>
                        <p class="text-xs text-[#64748B]">Kode Otomatis: {{ $kode_transaksi }}</p>
                    </div>
                    <button wire:click="tutupCreateModal" type="button" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanBarangMasuk" class="flex flex-col flex-1 min-h-0 overflow-hidden" novalidate>
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <!-- Pilih Supplier Rekanan -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                Supplier Rekanan (Pilihan Cepat)
                            </label>
                            <select 
                                wire:model.live="supplier_id"
                                class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                            >
                                <option value="">-- Pilih dari Master Supplier (Opsional) --</option>
                                @foreach ($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->nama_supplier }} ({{ $sup->kategori_pasokan }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('supplier_id')" class="mt-1" />
                        </div>

                        <!-- Nama Pihak Terkait / Supplier -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                Nama Supplier / Vendor <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                wire:model="pihak_terkait"
                                type="text" 
                                placeholder="Contoh: PT Mitra Logam Bersama" 
                                class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                            />
                            <x-input-error :messages="$errors->get('pihak_terkait')" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Kategori Pasokan <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model="kategori"
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                >
                                    <option value="pembelian_batch">Pembelian Drum Baku</option>
                                    <option value="bahan_operasional">Bahan Cat & Chemical</option>
                                    <option value="sparepart">Suku Cadang & Pelumas</option>
                                </select>
                                <x-input-error :messages="$errors->get('kategori')" class="mt-1" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Tanggal Masuk <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    wire:model="tanggal_transaksi"
                                    type="date" 
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                />
                                <x-input-error :messages="$errors->get('tanggal_transaksi')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Jumlah Kuantitas Fisik (Unit)
                                </label>
                                <input 
                                    wire:model="jumlah_unit"
                                    type="number" 
                                    min="0"
                                    placeholder="Contoh: 150" 
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                />
                                <x-input-error :messages="$errors->get('jumlah_unit')" class="mt-1" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Total Biaya Pembelian (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    wire:model="nominal"
                                    type="text" 
                                    placeholder="Contoh: 18750000" 
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                />
                                <x-input-error :messages="$errors->get('nominal')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Metode Pembayaran <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model="metode_pembayaran"
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                >
                                    <option value="transfer">Transfer Bank (BCA / Mandiri)</option>
                                    <option value="tunai">Tunai Kas Kecil</option>
                                    <option value="tempo">Tempo / Termin Faktur</option>
                                </select>
                                <x-input-error :messages="$errors->get('metode_pembayaran')" class="mt-1" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Tautkan Batch Drum (Opsional)
                                </label>
                                <select 
                                    wire:model="drum_batch_id"
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                >
                                    <option value="">-- Tanpa Tautan Batch --</option>
                                    @foreach ($drumBatches as $batch)
                                        <option value="{{ $batch->id }}">{{ $batch->kode_batch }} ({{ $batch->kategori_jenis }} - {{ $batch->jumlah_diterima }} unit)</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('drum_batch_id')" class="mt-1" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                Catatan / Keterangan Muatan
                            </label>
                            <textarea 
                                wire:model="keterangan"
                                rows="2" 
                                placeholder="Rincian nota, nomor surat jalan, spesifikasi bahan drum..." 
                                class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden resize-none"
                            ></textarea>
                            <x-input-error :messages="$errors->get('keterangan')" class="mt-1" />
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-[#E2E8F0] flex items-center justify-end gap-2 shrink-0">
                        <button 
                            wire:click="tutupCreateModal" 
                            type="button" 
                            class="px-4 py-2 rounded-lg border border-[#E2E8F0] bg-white text-xs font-semibold text-[#0F172A] hover:bg-slate-100 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-xs font-semibold text-white transition inline-flex items-center gap-1.5 cursor-pointer shadow-xs"
                        >
                            <x-icon name="check" class="w-4 h-4" />
                            <span>Simpan Barang Masuk</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div 
        x-data="{ showHapus: @entangle('modalHapusTerbuka') }" 
        x-show="showHapus" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
    >
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="$wire.tutupModalHapus()"></div>
        <div class="min-h-screen px-4 flex items-center justify-center py-6">
            <div class="relative bg-white rounded-2xl max-w-sm w-full p-6 text-center border border-[#E2E8F0] shadow-xl">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-3">
                    <x-icon name="trash" class="w-6 h-6" />
                </div>
                <h3 class="text-sm font-bold text-[#0F172A]">Hapus Catatan Barang Masuk?</h3>
                <p class="text-xs text-[#64748B] mt-1">
                    Anda akan menghapus data pengadaan <span class="font-semibold text-[#0F172A]">{{ $targetHapus?->kode_transaksi }}</span> ({{ $targetHapus?->pihak_terkait }}). Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="mt-5 flex items-center justify-center gap-2">
                    <button 
                        wire:click="tutupModalHapus" 
                        type="button" 
                        class="px-4 py-2 rounded-lg border border-[#E2E8F0] bg-white text-xs font-semibold text-[#0F172A] hover:bg-slate-50 transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        wire:click="hapusBarangMasuk" 
                        type="button" 
                        class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-xs font-semibold text-white transition cursor-pointer"
                    >
                        Ya, Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

