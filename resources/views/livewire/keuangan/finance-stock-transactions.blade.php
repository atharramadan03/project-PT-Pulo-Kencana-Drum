<div class="space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Pencatatan Keuangan Stok Gudang
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Mutasi arus kas masuk hasil penjualan drum dan beban pembelian stok bahan baku
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a 
                href="{{ route('keuangan.export-excel') }}?jenis=stok"
                class="px-3 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
            >
                <x-icon name="file-spreadsheet" class="w-4 h-4" />
                <span>Export Excel</span>
            </a>
            <a 
                href="{{ route('keuangan.export-pdf') }}?jenis=stok"
                target="_blank"
                class="px-3 py-2 rounded-lg bg-white border border-[#E2E8F0] hover:bg-slate-50 text-[#0F172A] font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
            >
                <x-icon name="file-text" class="w-4 h-4 text-rose-600" />
                <span>Cetak PDF</span>
            </a>
            @if (auth()->user()->isAdminKeuangan())
                <button 
                    wire:click="bukaCreateModal"
                    type="button" 
                    class="px-3.5 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Catat Transaksi</span>
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

    <!-- Mini KPI Summary Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <p class="text-[11px] text-[#64748B] font-medium">Pemasukan Terfilter</p>
            <p class="text-base font-bold text-emerald-600 tabular-nums mt-0.5">
                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <p class="text-[11px] text-[#64748B] font-medium">Pengeluaran Terfilter</p>
            <p class="text-base font-bold text-[#F59E0B] tabular-nums mt-0.5">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <p class="text-[11px] text-[#64748B] font-medium">Selisih Bersih</p>
            <p class="text-base font-bold {{ ($totalPemasukan - $totalPengeluaran) >= 0 ? 'text-[#2563EB]' : 'text-rose-600' }} tabular-nums mt-0.5">
                Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col md:flex-row justify-between items-center gap-3">
        <div class="relative w-full md:w-72">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#64748B]">
                <x-icon name="search" class="w-3.5 h-3.5" />
            </div>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari kode transaksi atau pihak terkait..." 
                class="w-full pl-9 pr-3 py-1.5 text-xs bg-[#F7F9FC] border border-[#E2E8F0] rounded-lg text-[#0F172A] placeholder:text-[#64748B] focus:outline-none focus:bg-white focus:border-[#2563EB]"
            />
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <select 
                wire:model.live="filterJenis"
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 pl-3 pr-8 focus:border-[#2563EB] focus:bg-white"
            >
                <option value="">Semua Jenis Transaksi</option>
                <option value="pemasukan">Pemasukan (Penjualan)</option>
                <option value="pengeluaran">Pengeluaran (Modal/Bahan)</option>
            </select>

            <select 
                wire:model.live="filterKategori"
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 pl-3 pr-8 focus:border-[#2563EB] focus:bg-white"
            >
                <option value="">Semua Kategori</option>
                <option value="penjualan_drum">Penjualan Drum</option>
                <option value="pembelian_batch">Pembelian Batch Drum</option>
                <option value="bahan_operasional">Bahan Kimia & Cat</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                    <tr>
                        <th class="py-3 px-4 whitespace-nowrap">No. Transaksi</th>
                        <th class="py-3 px-4 whitespace-nowrap">Jenis</th>
                        <th class="py-3 px-4 whitespace-nowrap">Kategori</th>
                        <th class="py-3 px-4 whitespace-nowrap">Pihak Terkait</th>
                        <th class="py-3 px-4 whitespace-nowrap text-center">Unit</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tanggal</th>
                        <th class="py-3 px-4 whitespace-nowrap">Metode</th>
                        <th class="py-3 px-4 whitespace-nowrap text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                {{ $trx->kode_transaksi }}
                            </td>
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                @if ($trx->jenis_transaksi === 'pemasukan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                        Pemasukan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#FEF3C7] text-[#92400E]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                                        Pengeluaran
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-[#0F172A] whitespace-nowrap capitalize">
                                {{ str_replace('_', ' ', $trx->kategori) }}
                            </td>
                            <td class="py-2.5 px-4 text-[#0F172A] whitespace-nowrap font-medium">
                                {{ $trx->pihak_terkait }}
                            </td>
                            <td class="py-2.5 px-4 text-center text-[#64748B] tabular-nums whitespace-nowrap">
                                {{ $trx->jumlah_unit ? number_format($trx->jumlah_unit, 0, ',', '.').' Drum' : '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] tabular-nums whitespace-nowrap">
                                {{ $trx->tanggal_transaksi?->format('d M Y') }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap uppercase text-[11px]">
                                {{ $trx->metode_pembayaran }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-bold {{ $trx->jenis_transaksi === 'pemasukan' ? 'text-emerald-600' : 'text-[#0F172A]' }} tabular-nums whitespace-nowrap">
                                {{ $trx->formatted_nominal }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-xs text-[#64748B]">
                                Belum ada catatan transaksi keuangan stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="px-4 py-3 border-t border-[#E2E8F0] bg-white">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Catat Transaksi Baru (Khusus Admin Keuangan) -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden border border-[#E2E8F0]">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-[#0F172A]">Catat Transaksi Keuangan Stok</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">Kode: <span class="font-mono font-semibold text-[#2563EB]">{{ $kode_transaksi }}</span></p>
                    </div>
                    <button 
                        wire:click="tutupCreateModal" 
                        type="button" 
                        class="p-1 rounded-lg text-[#64748B] hover:text-[#0F172A] hover:bg-slate-100 cursor-pointer"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanTransaksi" class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jenis Transaksi <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model.live="jenis_transaksi" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="pemasukan">Pemasukan (Hasil Penjualan)</option>
                                <option value="pengeluaran">Pengeluaran (Modal / Bahan)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Kategori <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="kategori" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                @if ($jenis_transaksi === 'pemasukan')
                                    <option value="penjualan_drum">Penjualan Drum Rekondisi</option>
                                    <option value="pendapatan_jasa">Jasa Cuci / Servis Drum</option>
                                    <option value="lainnya">Pemasukan Lainnya</option>
                                @else
                                    <option value="pembelian_batch">Pembelian Batch Drum Bahan Baku</option>
                                    <option value="bahan_operasional">Bahan Kimia & Cat Operasional</option>
                                    <option value="lainnya">Pengeluaran Gudang Lainnya</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="tanggal_transaksi" 
                                type="date" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('tanggal_transaksi') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="nominal" 
                                type="number" 
                                min="1000" 
                                placeholder="Contoh: 25000000" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('nominal') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jumlah Unit Drum</label>
                            <input 
                                wire:model="jumlah_unit" 
                                type="number" 
                                min="0" 
                                placeholder="Contoh: 100" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Metode Pembayaran</label>
                            <select 
                                wire:model="metode_pembayaran" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="transfer">Transfer Bank</option>
                                <option value="tunai">Tunai / Kas</option>
                                <option value="tempo">Tempo (Kredit)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Pihak Terkait (Customer / Supplier / Vendor) <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model="pihak_terkait" 
                            type="text" 
                            placeholder="Contoh: PT Sumber Kimia Abadi" 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                            required 
                        />
                        @error('pihak_terkait') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Keterangan</label>
                        <textarea 
                            wire:model="keterangan" 
                            rows="2" 
                            placeholder="Keterangan tambahan..." 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        ></textarea>
                    </div>

                    <div class="pt-3 border-t border-[#E2E8F0] flex justify-end gap-2">
                        <button 
                            wire:click="tutupCreateModal" 
                            type="button" 
                            class="px-4 py-2 rounded-lg border border-[#E2E8F0] text-[#0F172A] text-xs font-medium hover:bg-slate-50 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold transition cursor-pointer"
                        >
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

