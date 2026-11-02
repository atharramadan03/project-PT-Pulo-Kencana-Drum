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
                <x-icon name="printer" class="w-4 h-4 text-rose-600" />
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
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3 whitespace-nowrap">No. Polisi</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Kendaraan</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Jenis Perbaikan</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Nama Bengkel</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Tanggal</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Status</th>
                        <th class="py-2.5 px-3 whitespace-nowrap text-right">Biaya</th>
                        <th class="py-2.5 px-3 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($repairs as $rep)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-3 font-mono font-bold text-[#0F172A] whitespace-nowrap">
                                {{ $rep->nomor_polisi }}
                            </td>
                            <td class="py-2.5 px-3 text-[#0F172A] whitespace-nowrap">
                                {{ $rep->jenis_kendaraan }}
                            </td>
                            <td class="py-2.5 px-3 text-[#0F172A] font-medium whitespace-nowrap">
                                {{ $rep->jenis_perbaikan }}
                            </td>
                            <td class="py-2.5 px-3 text-[#64748B] whitespace-nowrap">
                                {{ $rep->nama_bengkel }}
                            </td>
                            <td class="py-2.5 px-3 text-[#64748B] tabular-nums whitespace-nowrap">
                                {{ $rep->tanggal_perbaikan?->format('d M Y') }}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                @if ($rep->status_perbaikan === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                        Selesai
                                    </span>
                                @elseif ($rep->status_perbaikan === 'dalam_pengerjaan')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#FEF3C7] text-[#92400E]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                                        Pengerjaan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                        Dijadwalkan
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right font-bold text-[#0F172A] tabular-nums whitespace-nowrap">
                                {{ $rep->formatted_biaya }}
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                @if (auth()->user()->isAdminKeuangan())
                                    <button 
                                        wire:click="konfirmasiHapus({{ $rep->id }})" 
                                        wire:loading.attr="disabled"
                                        type="button" 
                                        class="p-1 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 hover:border-rose-300 text-slate-400 hover:text-rose-600 transition cursor-pointer shadow-2xs disabled:opacity-50"
                                        title="Hapus data perbaikan armada"
                                    >
                                        <x-icon name="trash" class="w-3.5 h-3.5" wire:loading.remove wire:target="konfirmasiHapus({{ $rep->id }})" />
                                        <svg wire:loading wire:target="konfirmasiHapus({{ $rep->id }})" class="animate-spin w-3.5 h-3.5 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-[#64748B]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-xs text-[#64748B]">
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
        <div 
            x-data 
            x-init="document.body.classList.add('overflow-hidden'); return () => document.body.classList.remove('overflow-hidden')" 
            class="fixed inset-0 z-100 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex justify-center items-center p-3 sm:p-6"
        >
            <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-[#E2E8F0] my-auto flex flex-col max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3.5rem)] animate-in fade-in zoom-in-95 duration-100">
                <div class="px-5 py-3.5 border-b border-[#E2E8F0] flex justify-between items-center bg-slate-50/75 shrink-0">
                    <div>
                        <h3 class="text-sm font-bold text-[#0F172A]">Catat Biaya Perbaikan Mobil</h3>
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

                <form wire:submit="simpanPerbaikan" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1 min-h-0">
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
                            <input 
                                wire:model="jenis_kendaraan" 
                                type="text" 
                                placeholder="Contoh: Truk Tronton Box / Pickup / Operasional" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('jenis_kendaraan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
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

                    </div>
                    <div class="px-5 py-3 border-t border-[#E2E8F0] bg-slate-50/75 flex justify-end gap-2 shrink-0">
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

    <!-- Modal Konfirmasi Hapus Biaya Perbaikan -->
    @if ($modalHapusTerbuka && $hapusRepair)
        <template x-teleport="body">
            <div 
                x-data 
                x-init="document.body.classList.add('overflow-hidden'); return () => document.body.classList.remove('overflow-hidden')" 
                class="fixed inset-0 z-100 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-3 sm:p-6 flex justify-center items-center"
            >
                <div class="relative bg-white rounded-xl max-w-md w-full border border-[#E2E8F0] shadow-2xl overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-100">
                    <div class="p-6 text-center">
                        <div class="w-12 h-12 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 mx-auto mb-4">
                            <x-icon name="trash" class="w-6 h-6" />
                        </div>
                        <h3 class="text-base font-bold text-[#0F172A] mb-1">Konfirmasi Hapus Data Perbaikan</h3>
                        <p class="text-xs text-[#64748B] mb-5 leading-relaxed">
                            Apakah Anda yakin ingin menghapus catatan servis armada <strong class="text-[#0F172A]">{{ $hapusRepair->nomor_polisi }}</strong> ({{ $hapusRepair->jenis_perbaikan }}) senilai <strong class="text-rose-600">{{ $hapusRepair->formatted_biaya }}</strong>? Data akan dihapus secara permanen.
                        </p>
                        <div class="flex items-center justify-center gap-3">
                            <button 
                                wire:click="tutupModalHapus" 
                                type="button" 
                                class="px-4 py-2 rounded-lg border border-[#E2E8F0] text-xs font-semibold text-[#0F172A] bg-white hover:bg-slate-50 transition cursor-pointer shadow-2xs"
                            >
                                Batal
                            </button>
                            <button 
                                wire:click="hapusPerbaikan" 
                                type="button" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 shadow-xs disabled:opacity-75"
                            >
                                <span wire:loading.remove wire:target="hapusPerbaikan">Ya, Hapus Data</span>
                                <span wire:loading wire:target="hapusPerbaikan" class="inline-flex items-center gap-1.5">
                                    <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Menghapus...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>

