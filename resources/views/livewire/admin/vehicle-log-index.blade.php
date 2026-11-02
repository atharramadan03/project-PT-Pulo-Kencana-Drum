<div class="space-y-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Catatan Kunjungan Kendaraan
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Arsip keluar masuk kendaraan dan pemeriksaan dokumen di pos gerbang
            </p>
        </div>
    </div>

    <!-- Filter & Pencarian Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col lg:flex-row justify-between items-center gap-3">
        <div class="relative w-full lg:w-72">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#64748B]">
                <x-icon name="search" class="w-3.5 h-3.5" />
            </div>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari nopol, pengemudi, atau surat jalan..." 
                class="w-full pl-9 pr-3 py-1.5 text-xs bg-[#F7F9FC] border border-[#E2E8F0] rounded-lg text-[#0F172A] placeholder:text-[#64748B] focus:outline-none focus:bg-white focus:border-[#2563EB]"
            />
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
            <select 
                wire:model.live="filterStatus"
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 pl-3 pr-8 focus:border-[#2563EB] focus:bg-white"
            >
                <option value="">Semua Status</option>
                <option value="aktif">Aktif di Pabrik</option>
                <option value="selesai">Selesai</option>
            </select>

            <select 
                wire:model.live="filterB3"
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 pl-3 pr-8 focus:border-[#2563EB] focus:bg-white"
            >
                <option value="">Semua Surat B3</option>
                <option value="sesuai">Sesuai B3</option>
                <option value="tidak_sesuai">Tidak Sesuai</option>
                <option value="tidak_ada_b3">Non-B3</option>
            </select>

            <input 
                wire:model.live="filterTanggal" 
                type="date" 
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 px-3 focus:border-[#2563EB] focus:bg-white"
            />
        </div>
    </div>

    <!-- Tabel Data Kunjungan Kendaraan -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3 whitespace-nowrap">No. Kunjungan</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">No. Polisi</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Pengemudi</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Asal / Tujuan</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Waktu Masuk</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Status</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Dokumen B3</th>
                        <th class="py-2.5 px-3 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($visits as $v)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-3 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                {{ $v->nomor_kunjungan }}
                            </td>
                            <td class="py-2.5 px-3 font-mono font-semibold text-[#2563EB] whitespace-nowrap">
                                {{ $v->nomor_polisi }}
                            </td>
                            <td class="py-2.5 px-3 text-[#0F172A] font-medium whitespace-nowrap">
                                {{ $v->nama_pengemudi }}
                            </td>
                            <td class="py-2.5 px-3 text-[#64748B] max-w-[180px] truncate">
                                {{ $v->asal_tujuan ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-[#64748B] whitespace-nowrap tabular-nums">
                                {{ $v->waktu_masuk?->format('d M Y H:i') }}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                @if ($v->waktu_keluar)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-[#2563EB]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                @php $statusB3 = strtolower($v->status_pemeriksaan_surat_jalan_b3 ?? ''); @endphp
                                @if ($statusB3 === 'sesuai' || $statusB3 === 'lolos')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                        Sesuai B3
                                    </span>
                                @elseif ($statusB3 === 'tidak_sesuai' || $statusB3 === 'tidak sesuai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#FEE2E2] text-[#991B1B]">
                                        Tidak Sesuai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                        Non-B3
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button 
                                        wire:click="lihatDetail({{ $v->id }})"
                                        type="button"
                                        class="px-2.5 py-1 rounded-lg border border-[#E2E8F0] hover:bg-slate-50 text-[#0F172A] font-medium text-xs transition cursor-pointer"
                                    >
                                        Detail
                                    </button>
                                    <button 
                                        wire:click="konfirmasiHapus({{ $v->id }})" 
                                        wire:loading.attr="disabled"
                                        type="button" 
                                        class="p-1 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 hover:border-rose-300 text-slate-400 hover:text-rose-600 transition cursor-pointer shadow-2xs disabled:opacity-50"
                                        title="Hapus data kunjungan armada"
                                    >
                                        <x-icon name="trash" class="w-3.5 h-3.5" wire:loading.remove wire:target="konfirmasiHapus({{ $v->id }})" />
                                        <svg wire:loading wire:target="konfirmasiHapus({{ $v->id }})" class="animate-spin w-3.5 h-3.5 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-xs text-[#64748B]">
                                Tidak ada catatan kunjungan kendaraan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($visits->hasPages())
            <div class="px-4 py-3 border-t border-[#E2E8F0] bg-white">
                {{ $visits->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Detail Kunjungan (Clean White Style per Reference) -->
    @if ($showDetailModal && $selectedVisit)
        <div 
            x-data 
            x-init="document.body.classList.add('overflow-hidden'); return () => document.body.classList.remove('overflow-hidden')" 
            class="fixed inset-0 z-100 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6"
        >
            <div class="bg-white rounded-xl max-w-lg w-full shadow-xl border border-[#E2E8F0] overflow-hidden">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-[#0F172A]">Detail Kunjungan Kendaraan</h3>
                        <p class="text-xs text-[#64748B] font-mono mt-0.5">{{ $selectedVisit->nomor_kunjungan }}</p>
                    </div>
                    <button 
                        wire:click="tutupDetailModal" 
                        type="button"
                        class="p-1 rounded-lg text-[#64748B] hover:text-[#0F172A] hover:bg-slate-100"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <div class="p-5 space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg">
                            <span class="text-[11px] text-[#64748B] block">Nomor Polisi</span>
                            <span class="font-mono font-bold text-[#0F172A] mt-0.5 block">{{ $selectedVisit->nomor_polisi }}</span>
                        </div>
                        <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg">
                            <span class="text-[11px] text-[#64748B] block">Nama Pengemudi</span>
                            <span class="font-semibold text-[#0F172A] mt-0.5 block">{{ $selectedVisit->nama_pengemudi }}</span>
                        </div>
                        <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg">
                            <span class="text-[11px] text-[#64748B] block">Asal / Tujuan</span>
                            <span class="font-medium text-[#0F172A] mt-0.5 block">{{ $selectedVisit->asal_tujuan ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg">
                            <span class="text-[11px] text-[#64748B] block">Keperluan</span>
                            <span class="font-medium text-[#0F172A] mt-0.5 block">{{ $selectedVisit->keperluan }}</span>
                        </div>
                        <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg">
                            <span class="text-[11px] text-[#64748B] block">Waktu Masuk</span>
                            <span class="font-mono text-[#0F172A] mt-0.5 block">{{ $selectedVisit->waktu_masuk?->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg">
                            <span class="text-[11px] text-[#64748B] block">Waktu Keluar</span>
                            @if ($selectedVisit->waktu_keluar)
                                <span class="font-mono text-[#166534] font-semibold mt-0.5 block">{{ $selectedVisit->waktu_keluar->format('d/m/Y H:i') }}</span>
                            @else
                                <span class="font-semibold text-[#F59E0B] mt-0.5 block">Aktif di Pabrik</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[#64748B]">Surat Jalan B3</span>
                            <span class="font-semibold text-[#0F172A]">{{ $selectedVisit->informasi_surat_jalan ?? 'Tidak ada' }}</span>
                        </div>
                        @if ($selectedVisit->keterangan_muatan)
                            <div class="flex items-center justify-between">
                                <span class="text-[#64748B]">Muatan</span>
                                <span class="font-medium text-[#0F172A]">{{ $selectedVisit->keterangan_muatan }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-[#64748B]">Petugas Gerbang</span>
                            <span class="font-medium text-[#0F172A]">{{ $selectedVisit->user?->name ?? 'Satpam' }}</span>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 bg-[#F8FAFC] border-t border-[#E2E8F0] flex justify-end">
                    <button 
                        wire:click="tutupDetailModal"
                        type="button" 
                        class="px-4 py-1.5 rounded-lg border border-[#E2E8F0] bg-white hover:bg-slate-50 text-[#0F172A] font-medium text-xs transition cursor-pointer"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Kunjungan Armada -->
    @if ($modalHapusTerbuka && $hapusVisit)
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
                        <h3 class="text-base font-bold text-[#0F172A] mb-1">Konfirmasi Hapus Data Kunjungan</h3>
                        <p class="text-xs text-[#64748B] mb-5 leading-relaxed">
                            Apakah Anda yakin ingin menghapus data kunjungan armada <strong class="text-[#0F172A]">{{ $hapusVisit->nomor_polisi }}</strong> (No: {{ $hapusVisit->nomor_kunjungan }}) atas nama <strong class="text-[#0F172A]">{{ $hapusVisit->nama_pengemudi }}</strong>? Tindakan ini akan menghapus data kunjungan secara permanen.
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
                                wire:click="hapusVisit" 
                                type="button" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 shadow-xs disabled:opacity-75"
                            >
                                <span wire:loading.remove wire:target="hapusVisit">Ya, Hapus Data</span>
                                <span wire:loading wire:target="hapusVisit" class="inline-flex items-center gap-1.5">
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
