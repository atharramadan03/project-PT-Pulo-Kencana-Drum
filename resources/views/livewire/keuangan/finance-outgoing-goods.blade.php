<div class="space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex-1 min-w-0">
            <h1 class="font-bold text-lg sm:text-xl text-[#0F172A] tracking-tight leading-snug">
                Data Barang Keluar & Penjualan
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5 leading-relaxed">
                Pencatatan distribusi dan penjualan drum rekondisi, drum olahan bersih, dan produk industri ke pelanggan
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <a 
                href="{{ route('keuangan.export-excel') }}?jenis=barang_keluar"
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
                    <span>Catat Barang Keluar</span>
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
                <span>Total Omset Penjualan</span>
                <x-icon name="arrow-up-right" class="w-4 h-4 text-emerald-600" />
            </div>
            <p class="text-base sm:text-lg font-bold text-emerald-600 mt-1 tabular-nums">
                Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Akumulasi pendapatan</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Total Unit Terjual</span>
                <x-icon name="package" class="w-4 h-4 text-[#2563EB]" />
            </div>
            <p class="text-base sm:text-lg font-bold text-[#0F172A] mt-1 tabular-nums">
                {{ number_format($totalUnitTerjual, 0, ',', '.') }} Unit
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Drum siap pakai terkirim</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Pengiriman Bulan Ini</span>
                <x-icon name="calendar" class="w-4 h-4 text-amber-500" />
            </div>
            <p class="text-base sm:text-lg font-bold text-[#0F172A] mt-1 tabular-nums">
                {{ $totalPengirimanBulanIni }} Transaksi
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">{{ now()->translatedFormat('F Y') }}</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Rata-rata Order</span>
                <x-icon name="coins" class="w-4 h-4 text-[#7C3AED]" />
            </div>
            <p class="text-base sm:text-lg font-bold text-[#0F172A] mt-1 tabular-nums">
                Rp {{ number_format($rataRataPenjualan, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Per transaksi distribusi</p>
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
                    placeholder="Cari kode, pelanggan, catatan..." 
                    class="w-full pl-9 pr-3 py-1.5 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[#0F172A] placeholder:text-[#94A3B8] focus:bg-white focus:border-[#2563EB] focus:outline-hidden"
                />
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select 
                    wire:model.live="filterMetode"
                    class="py-1.5 px-3 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[#0F172A] focus:bg-white focus:border-[#2563EB]"
                >
                    <option value="">Semua Metode Bayar</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="tunai">Tunai Kas</option>
                    <option value="tempo">Tempo / Termin Faktur</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                    <tr>
                        <th class="py-2.5 px-4 whitespace-nowrap">Kode Keluar</th>
                        <th class="py-2.5 px-4 whitespace-nowrap">Tanggal Kirim</th>
                        <th class="py-2.5 px-4 whitespace-nowrap">Pelanggan / Pembeli</th>
                        <th class="py-2.5 px-4 whitespace-nowrap">Batch Asal</th>
                        <th class="py-2.5 px-4 whitespace-nowrap text-right">Volume Keluar</th>
                        <th class="py-2.5 px-4 whitespace-nowrap text-right">Nilai Penjualan</th>
                        <th class="py-2.5 px-4 whitespace-nowrap">Metode Bayar</th>
                        <th class="py-2.5 px-4 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($barangKeluarList as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                {{ $item->kode_transaksi }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                {{ $item->tanggal_transaksi?->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-2.5 px-4 font-medium text-[#0F172A] whitespace-nowrap">
                                <div>{{ $item->pihak_terkait }}</div>
                                @if ($item->keterangan)
                                    <p class="text-[11px] text-[#64748B] truncate max-w-xs mt-0.5">{{ $item->keterangan }}</p>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap font-mono text-[11px]">
                                {{ $item->drumBatch ? $item->drumBatch->kode_batch : '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-semibold text-[#0F172A] whitespace-nowrap tabular-nums">
                                {{ number_format($item->jumlah_unit, 0, ',', '.') }} Unit
                            </td>
                            <td class="py-2.5 px-4 text-right font-bold text-emerald-600 whitespace-nowrap tabular-nums">
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
                                        title="Hapus Data Barang Keluar"
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
                                Belum ada data penjualan atau pengiriman barang keluar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($barangKeluarList->hasPages())
            <div class="p-3 border-t border-[#E2E8F0]">
                {{ $barangKeluarList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Tambah Barang Keluar -->
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
                        <h2 class="text-sm font-bold text-[#0F172A]">Catat Barang Keluar & Penjualan</h2>
                        <p class="text-xs text-[#64748B]">Kode Otomatis: {{ $kode_transaksi }}</p>
                    </div>
                    <button wire:click="tutupCreateModal" type="button" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanBarangKeluar" class="flex flex-col flex-1 min-h-0 overflow-hidden" novalidate>
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                Nama Klien / Pelanggan Pembeli <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                wire:model="pihak_terkait"
                                type="text" 
                                placeholder="Contoh: PT Agro Pelita Nusantara" 
                                class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                            />
                            <x-input-error :messages="$errors->get('pihak_terkait')" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Tanggal Pengiriman <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    wire:model="tanggal_transaksi"
                                    type="date" 
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                />
                                <x-input-error :messages="$errors->get('tanggal_transaksi')" class="mt-1" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Jumlah Drum Keluar (Unit) <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    wire:model="jumlah_unit"
                                    type="number" 
                                    min="1"
                                    placeholder="Contoh: 120" 
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                />
                                <x-input-error :messages="$errors->get('jumlah_unit')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Total Nilai Penjualan (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    wire:model="nominal"
                                    type="text" 
                                    placeholder="Contoh: 31200000" 
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                />
                                <x-input-error :messages="$errors->get('nominal')" class="mt-1" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                    Metode Pembayaran <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model="metode_pembayaran"
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                                >
                                    <option value="transfer">Transfer Bank (BCA / Mandiri)</option>
                                    <option value="tunai">Tunai Kas Langsung</option>
                                    <option value="tempo">Tempo / Termin Invoice</option>
                                </select>
                                <x-input-error :messages="$errors->get('metode_pembayaran')" class="mt-1" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                Tautkan Batch Drum Asal (Opsional)
                            </label>
                            <select 
                                wire:model="drum_batch_id"
                                class="w-full px-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] outline-hidden"
                            >
                                <option value="">-- Tanpa Tautan Batch --</option>
                                @foreach ($drumBatches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->kode_batch }} ({{ $batch->kategori_jenis }} - Stok: {{ $batch->jumlah_diterima }} unit)</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('drum_batch_id')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1">
                                Keterangan & Rincian Pengiriman
                            </label>
                            <textarea 
                                wire:model="keterangan"
                                rows="2" 
                                placeholder="Nomor PO pelanggan, spesifikasi drum rekondisi, catatan surat jalan..." 
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
                            <span>Simpan Barang Keluar</span>
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
                <h3 class="text-sm font-bold text-[#0F172A]">Hapus Catatan Barang Keluar?</h3>
                <p class="text-xs text-[#64748B] mt-1">
                    Anda akan menghapus data penjualan <span class="font-semibold text-[#0F172A]">{{ $targetHapus?->kode_transaksi }}</span> ({{ $targetHapus?->pihak_terkait }}). Tindakan ini tidak dapat dibatalkan.
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
                        wire:click="hapusBarangKeluar" 
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

