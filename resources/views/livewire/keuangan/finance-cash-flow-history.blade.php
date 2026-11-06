<div class="space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex-1 min-w-0">
            <h1 class="font-bold text-lg sm:text-xl text-[#0F172A] tracking-tight leading-snug">
                Riwayat Keuangan & Buku Kas (Cash Flow)
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5 leading-relaxed">
                Buku kas terpadu mencatat seluruh mutasi penerimaan kas dari barang keluar dan pengeluaran barang masuk, mobil, serta operasional pabrik
            </p>
        </div>
        <div class="shrink-0 grid grid-cols-2 gap-2 w-full sm:w-auto">
            <a 
                href="{{ route('keuangan.laporan-cetak') }}"
                class="px-3.5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer text-center whitespace-nowrap shrink-0"
            >
                <x-icon name="printer" class="w-4 h-4 shrink-0" />
                <span>Format Cetak</span>
            </a>
            <a 
                href="{{ route('keuangan.export-excel') }}"
                class="px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer text-center whitespace-nowrap shrink-0"
            >
                <x-icon name="file-spreadsheet" class="w-4 h-4 shrink-0" />
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Total Mutasi Masuk</span>
                <x-icon name="arrow-up-right" class="w-4 h-4 text-emerald-600" />
            </div>
            <p class="text-base sm:text-lg font-bold text-emerald-700 mt-1 tabular-nums">
                <x-animated-number :value="$totalMutasiMasuk" prefix="+Rp " />
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Penjualan barang keluar</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Total Mutasi Keluar</span>
                <x-icon name="arrow-down-left" class="w-4 h-4 text-rose-500" />
            </div>
            <p class="text-base sm:text-lg font-bold text-rose-600 mt-1 tabular-nums">
                <x-animated-number :value="$totalMutasiKeluar" prefix="-Rp " />
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Pengadaan, mobil, & operasional</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Selisih Kas Bersih</span>
                <x-icon name="wallet" class="w-4 h-4 text-[#2563EB]" />
            </div>
            <p class="text-base sm:text-lg font-bold {{ $selisihBersih >= 0 ? 'text-[#2563EB]' : 'text-rose-600' }} mt-1 tabular-nums">
                <x-animated-number :value="$selisihBersih" prefix="Rp " />
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">{{ $selisihBersih >= 0 ? 'Surplus Kas' : 'Defisit Kas' }}</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span>Total Catatan Mutasi</span>
                <x-icon name="receipt" class="w-4 h-4 text-slate-500" />
            </div>
            <p class="text-base sm:text-lg font-bold text-slate-800 mt-1 tabular-nums">
                <x-animated-number :value="$totalTransaksi" suffix=" Transaksi" />
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Pada periode yang dipilih</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5">
            <div class="lg:col-span-2 relative">
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    placeholder="Cari transaksi, keterangan, no ref, atau PIC..." 
                    class="w-full pl-9 pr-3 py-1.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB] placeholder:text-[#94A3B8]"
                >
                <div class="absolute left-2.5 top-2 text-[#94A3B8]">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
            </div>

            <div>
                <select 
                    wire:model.live="filterJenis"
                    class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                >
                    <option value="">Semua Jenis Mutasi</option>
                    <option value="masuk">Pemasukan (+)</option>
                    <option value="keluar">Pengeluaran (-)</option>
                </select>
            </div>

            <div>
                <select 
                    wire:model.live="filterSumber"
                    class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                >
                    <option value="">Semua Sumber Biaya</option>
                    <option value="barang_masuk">Pengadaan Barang Masuk</option>
                    <option value="barang_keluar">Penjualan Barang Keluar</option>
                    <option value="mobil">Biaya Armada & Mobil</option>
                    <option value="operasional">Operasional Pabrik</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button 
                    wire:click="resetFilter" 
                    type="button" 
                    class="w-full py-1.5 px-3 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition"
                >
                    Reset Filter
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-2.5 pt-2.5 border-t border-slate-100">
            <div>
                <label class="block text-[11px] font-medium text-slate-500 mb-1">Dari Tanggal:</label>
                <input 
                    type="date" 
                    wire:model.live="startDate" 
                    class="w-full py-1 px-2 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                >
            </div>
            <div>
                <label class="block text-[11px] font-medium text-slate-500 mb-1">Sampai Tanggal:</label>
                <input 
                    type="date" 
                    wire:model.live="endDate" 
                    class="w-full py-1 px-2 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                >
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#475569] font-semibold">
                        <th class="py-2.5 px-3">Tanggal</th>
                        <th class="py-2.5 px-3 text-center">Arus</th>
                        <th class="py-2.5 px-3">Sumber Mutasi</th>
                        <th class="py-2.5 px-3">No. Referensi</th>
                        <th class="py-2.5 px-3">Uraian / Deskripsi</th>
                        <th class="py-2.5 px-3">Petugas / PIC</th>
                        <th class="py-2.5 px-3 text-right">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse($mutasiItems as $item)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-2.5 px-3 font-mono text-slate-700 whitespace-nowrap">
                                {{ $item->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                @if($item->jenis === 'masuk')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <x-icon name="arrow-up-right" class="w-3 h-3" />
                                        Masuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        <x-icon name="arrow-down-left" class="w-3 h-3" />
                                        Keluar
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium 
                                    {{ $item->sumber === 'barang_keluar' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : '' }}
                                    {{ $item->sumber === 'barang_masuk' ? 'bg-blue-50 text-blue-800 border border-blue-200' : '' }}
                                    {{ $item->sumber === 'mobil' ? 'bg-amber-50 text-amber-800 border border-amber-200' : '' }}
                                    {{ $item->sumber === 'operasional' ? 'bg-purple-50 text-purple-800 border border-purple-200' : '' }}
                                ">
                                    {{ $item->sumber_label }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-mono text-slate-600 whitespace-nowrap">
                                {{ $item->kode_ref }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-800 max-w-xs md:max-w-md">
                                <span class="line-clamp-2">{{ $item->keterangan }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 whitespace-nowrap">
                                {{ $item->petugas }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold whitespace-nowrap {{ $item->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $item->jenis === 'masuk' ? '+' : '-' }}Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-[#64748B]">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <x-icon name="inbox" class="w-8 h-8 text-slate-300" />
                                    <p class="font-medium text-xs">Belum ada catatan mutasi kas pada kriteria yang dicari</p>
                                    <p class="text-[11px] text-slate-400">Silakan ubah rentang tanggal atau kata kunci filter</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mutasiItems->hasPages())
            <div class="p-3 border-t border-[#E2E8F0]">
                {{ $mutasiItems->links() }}
            </div>
        @endif
    </div>
</div>

