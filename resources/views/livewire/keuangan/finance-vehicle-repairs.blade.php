<div class="space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Biaya Perbaikan & Perawatan Armada Mobil
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Pencatatan riwayat servis, penggantian suku cadang, dan biaya bengkel kendaraan operasional
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a 
                href="{{ route('keuangan.export-excel') }}?jenis=kendaraan"
                class="px-3 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
            >
                <x-icon name="file-spreadsheet" class="w-4 h-4" />
                <span>Export Excel</span>
            </a>
            <a 
                href="{{ route('keuangan.export-pdf') }}?jenis=kendaraan"
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
                    <span>Catat Biaya Servis</span>
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
            <p class="text-[11px] text-[#64748B] font-medium">Total Biaya Perbaikan</p>
            <p class="text-base font-bold text-rose-600 tabular-nums mt-0.5">
                Rp {{ number_format($totalBiaya, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <p class="text-[11px] text-[#64748B] font-medium">Servis Selesai</p>
            <p class="text-base font-bold text-[#10B981] tabular-nums mt-0.5">
                {{ $countSelesai }} Unit Kendaraan
            </p>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <p class="text-[11px] text-[#64748B] font-medium">Dalam Pengerjaan Bengkel</p>
            <p class="text-base font-bold text-[#F59E0B] tabular-nums mt-0.5">
                {{ $countProses }} Unit Kendaraan
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
                placeholder="Cari nopol, jenis mobil, atau bengkel..." 
                class="w-full pl-9 pr-3 py-1.5 text-xs bg-[#F7F9FC] border border-[#E2E8F0] rounded-lg text-[#0F172A] placeholder:text-[#64748B] focus:outline-none focus:bg-white focus:border-[#2563EB]"
            />
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <select 
                wire:model.live="filterStatus"
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 pl-3 pr-8 focus:border-[#2563EB] focus:bg-white"
            >
                <option value="">Semua Status Servis</option>
                <option value="selesai">Selesai</option>
                <option value="dalam_pengerjaan">Dalam Pengerjaan</option>
                <option value="dijadwalkan">Dijadwalkan</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                    <tr>
                        <th class="py-3 px-4 whitespace-nowrap">No. Polisi</th>
                        <th class="py-3 px-4 whitespace-nowrap">Kendaraan</th>
                        <th class="py-3 px-4 whitespace-nowrap">Jenis Perbaikan</th>
                        <th class="py-3 px-4 whitespace-nowrap">Nama Bengkel</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tanggal</th>
                        <th class="py-3 px-4 whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 whitespace-nowrap text-right">Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($repairs as $rep)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-4 font-mono font-bold text-[#0F172A] whitespace-nowrap">
                                {{ $rep->nomor_polisi }}
                            </td>
                            <td class="py-2.5 px-4 text-[#0F172A] whitespace-nowrap">
                                {{ $rep->jenis_kendaraan }}
                            </td>
                            <td class="py-2.5 px-4 text-[#0F172A] font-medium whitespace-nowrap">
                                {{ $rep->jenis_perbaikan }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap">
                                {{ $rep->nama_bengkel }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] tabular-nums whitespace-nowrap">
                                {{ $rep->tanggal_perbaikan?->format('d M Y') }}
                            </td>
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                @if ($rep->status_perbaikan === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                        Selesai
                                    </span>
                                @elseif ($rep->status_perbaikan === 'dalam_pengerjaan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#FEF3C7] text-[#92400E]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                                        Pengerjaan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                        Dijadwalkan
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right font-bold text-[#0F172A] tabular-nums whitespace-nowrap">
                                {{ $rep->formatted_biaya }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-xs text-[#64748B]">
                                Belum ada catatan biaya perbaikan armada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($repairs->hasPages())
            <div class="px-4 py-3 border-t border-[#E2E8F0] bg-white">
                {{ $repairs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Catat Biaya Perbaikan (Khusus Admin Keuangan) -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden border border-[#E2E8F0]">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-[#0F172A]">Catat Biaya Perbaikan Mobil</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">Pemeliharaan dan perbaikan armada operasional</p>
                    </div>
                    <button 
                        wire:click="tutupCreateModal" 
                        type="button" 
                        class="p-1 rounded-lg text-[#64748B] hover:text-[#0F172A] hover:bg-slate-100 cursor-pointer"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanPerbaikan" class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Nomor Polisi <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="nomor_polisi" 
                                type="text" 
                                placeholder="Contoh: B 9481 KDA" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 uppercase focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('nomor_polisi') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jenis Kendaraan <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="jenis_kendaraan" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="Truk Tronton Box">Truk Tronton Box</option>
                                <option value="Truk Engkel Bak">Truk Engkel Bak</option>
                                <option value="Truk Tangki B3">Truk Tangki B3</option>
                                <option value="Pickup Operasional">Pickup Operasional</option>
                                <option value="Mobil Operasional">Mobil Operasional</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Tanggal Perbaikan <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="tanggal_perbaikan" 
                                type="date" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('tanggal_perbaikan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Biaya Perbaikan (Rp) <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="biaya" 
                                type="number" 
                                min="1000" 
                                placeholder="Contoh: 3500000" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('biaya') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jenis Perbaikan <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="jenis_perbaikan" 
                                type="text" 
                                placeholder="Contoh: Servis Rem & Ganti Oli" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('jenis_perbaikan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Status Perbaikan <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="status_perbaikan" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="selesai">Selesai</option>
                                <option value="dalam_pengerjaan">Dalam Pengerjaan</option>
                                <option value="dijadwalkan">Dijadwalkan</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Nama Bengkel Rekanan <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model="nama_bengkel" 
                            type="text" 
                            placeholder="Contoh: Bengkel Sumber Diesel Cikarang" 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                            required 
                        />
                        @error('nama_bengkel') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Keterangan</label>
                        <textarea 
                            wire:model="keterangan" 
                            rows="2" 
                            placeholder="Rincian suku cadang atau catatan perbaikan..." 
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
                            Simpan Data Perbaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

