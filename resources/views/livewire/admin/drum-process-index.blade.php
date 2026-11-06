<div class="space-y-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Catatan 4 Tahap Proses Gudang
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Data penyortiran, peniupan, pencucian, dan pengecatan drum
            </p>
        </div>
        <button 
            wire:click="bukaCreateModal"
            type="button" 
            class="px-3.5 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Input Proses Gudang</span>
        </button>
    </div>

    @if (session('success'))
        <div class="p-3.5 rounded-xl bg-[#DCFCE7] border border-green-200 text-[#166534] flex items-center gap-2 text-xs font-medium">
            <x-icon name="circle-check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @php
        $kpiSortir = (int) \App\Models\DrumProcessLog::where('jenis_proses', 'penyortiran')->sum('jumlah_diproses');
        $kpiTiup = (int) \App\Models\DrumProcessLog::where('jenis_proses', 'peniupan')->sum('jumlah_diproses');
        $kpiCuci = (int) \App\Models\DrumProcessLog::where('jenis_proses', 'pencucian')->sum('jumlah_diproses');
        $kpiCat = (int) \App\Models\DrumProcessLog::where('jenis_proses', 'pengecatan')->sum('jumlah_diproses');
    @endphp

    <!-- 4 KPI Summary Cards (4 Tahap Proses Gudang) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Tahap 1: Penyortiran</p>
                <x-icon name="layers" class="w-4 h-4 text-[#2563EB] shrink-0" />
            </div>
            <h3 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5">
                <x-animated-number :value="$kpiSortir" suffix=" Drum" />
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#2563EB] font-semibold">Seleksi fisik</span>
                <span class="text-[#64748B]">Sortir awal</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Tahap 2: Peniupan</p>
                <x-icon name="layers" class="w-4 h-4 text-[#F59E0B] shrink-0" />
            </div>
            <h3 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5">
                <x-animated-number :value="$kpiTiup" suffix=" Drum" />
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#F59E0B] font-semibold">Uji tekanan</span>
                <span class="text-[#64748B]">Deteksi bocor</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Tahap 3: Pencucian</p>
                <x-icon name="layers" class="w-4 h-4 text-[#7C3AED] shrink-0" />
            </div>
            <h3 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5">
                <x-animated-number :value="$kpiCuci" suffix=" Drum" />
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#7C3AED] font-semibold">Sterilisasi residu</span>
                <span class="text-[#64748B]">Cuci kimia & bilas</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Tahap 4: Pengecatan</p>
                <x-icon name="circle-check" class="w-4 h-4 text-[#10B981] shrink-0" />
            </div>
            <h3 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5">
                <x-animated-number :value="$kpiCat" suffix=" Drum" />
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#10B981] font-semibold">Finishing cat</span>
                <span class="text-[#64748B]">Siap distribusi</span>
            </div>
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
                placeholder="Cari pelapor, batch, atau catatan..." 
                class="w-full pl-9 pr-3 py-1.5 text-xs bg-[#F7F9FC] border border-[#E2E8F0] rounded-lg text-[#0F172A] placeholder:text-[#64748B] focus:outline-none focus:bg-white focus:border-[#2563EB]"
            />
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <select 
                wire:model.live="filterBatchId"
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 pl-3 pr-8 focus:border-[#2563EB] focus:bg-white max-w-xs"
            >
                <option value="">Semua Batch Drum</option>
                @foreach ($daftarBatch as $b)
                    <option value="{{ $b->id }}">{{ $b->kode_batch }} ({{ $b->kategori_jenis }})</option>
                @endforeach
            </select>

            <select 
                wire:model.live="filterJenisProses"
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 pl-3 pr-8 focus:border-[#2563EB] focus:bg-white"
            >
                <option value="">Semua Tahap Proses</option>
                <option value="penyortiran">Penyortiran</option>
                <option value="peniupan">Peniupan</option>
                <option value="pencucian">Pencucian</option>
                <option value="pengecatan">Pengecatan</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3 whitespace-nowrap">No. Batch</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Tahap Proses</th>
                        <th class="py-2.5 px-3 whitespace-nowrap text-center">Diproses</th>
                        <th class="py-2.5 px-3 whitespace-nowrap text-center">Reject</th>
                        <th class="py-2.5 px-3 whitespace-nowrap text-center">Hasil Baik</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Tanggal Aktivitas</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Pelapor Gudang</th>
                        <th class="py-2.5 px-3 whitespace-nowrap">Tanggal Input</th>
                        <th class="py-2.5 px-3 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-3 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                {{ $log->drumBatch->kode_batch }}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                @if ($log->jenis_proses === 'penyortiran')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#FEF3C7] text-[#92400E]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                                        Penyortiran
                                    </span>
                                @elseif ($log->jenis_proses === 'peniupan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-[#2563EB]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                                        Peniupan
                                    </span>
                                @elseif ($log->jenis_proses === 'pencucian')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#F5F3FF] text-[#7C3AED]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#7C3AED]"></span>
                                        Pencucian
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#FEE2E2] text-[#991B1B]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span>
                                        Pengecatan
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center font-semibold text-[#0F172A] whitespace-nowrap tabular-nums">
                                {{ number_format($log->jumlah_diproses, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap tabular-nums">
                                @if ($log->jumlah_afkir > 0)
                                    <span class="font-semibold text-rose-600">{{ number_format($log->jumlah_afkir, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center font-semibold text-[#10B981] whitespace-nowrap tabular-nums">
                                {{ number_format($log->jumlah_diproses - $log->jumlah_afkir, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-[#0F172A] font-medium whitespace-nowrap tabular-nums">
                                {{ $log->tanggal_aktivitas->format('d M Y') }}
                            </td>
                            <td class="py-2.5 px-3 text-[#64748B] whitespace-nowrap">
                                {{ $log->pelapor_gudang }}
                            </td>
                            <td class="py-2.5 px-3 text-[#64748B] whitespace-nowrap tabular-nums">
                                {{ $log->tanggal_input->format('d M Y H:i') }}
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <button 
                                    wire:click="konfirmasiHapus({{ $log->id }})" 
                                    wire:loading.attr="disabled"
                                    type="button" 
                                    class="p-1 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 hover:border-rose-300 text-slate-400 hover:text-rose-600 transition cursor-pointer shadow-2xs disabled:opacity-50"
                                    title="Hapus data proses gudang"
                                >
                                    <x-icon name="trash" class="w-3.5 h-3.5" wire:loading.remove wire:target="konfirmasiHapus({{ $log->id }})" />
                                    <svg wire:loading wire:target="konfirmasiHapus({{ $log->id }})" class="animate-spin w-3.5 h-3.5 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-xs text-[#64748B]">
                                Belum ada catatan proses gudang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="px-4 py-3 border-t border-[#E2E8F0] bg-white">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Input Proses Gudang (Clean White Style per Reference) -->
    @if ($showCreateModal)
        <div 
            x-data 
            x-init="document.body.classList.add('overflow-hidden'); return () => document.body.classList.remove('overflow-hidden')" 
            class="fixed inset-0 z-100 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden border border-[#E2E8F0]">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex justify-between items-center">
                    <h3 class="text-base font-bold text-[#0F172A]">Input Proses Gudang</h3>
                    <button 
                        wire:click="tutupCreateModal" 
                        type="button" 
                        class="p-1 rounded-lg text-[#64748B] hover:text-[#0F172A] hover:bg-slate-100"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanLog" class="p-5 space-y-4" novalidate>
                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Batch Drum <span class="text-rose-500">*</span></label>
                        <select 
                            wire:model="drum_batch_id" 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                            required
                        >
                            <option value="">Pilih Batch Drum</option>
                            @foreach ($daftarBatch as $b)
                                <option value="{{ $b->id }}">{{ $b->kode_batch }} ({{ $b->kategori_jenis }}, {{ $b->jumlah_diterima }} unit)</option>
                            @endforeach
                        </select>
                        @error('drum_batch_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Tahap Proses <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="jenis_proses" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required
                            >
                                <option value="penyortiran">Penyortiran</option>
                                <option value="peniupan">Peniupan</option>
                                <option value="pencucian">Pencucian</option>
                                <option value="pengecatan">Pengecatan</option>
                            </select>
                            @error('jenis_proses') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Tanggal Aktivitas <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="tanggal_aktivitas" 
                                type="date" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('tanggal_aktivitas') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jumlah Diproses <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="jumlah_diproses" 
                                type="number" 
                                min="1" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('jumlah_diproses') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jumlah Reject</label>
                            <input 
                                wire:model="jumlah_afkir" 
                                type="number" 
                                min="0" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('jumlah_afkir') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Pelapor Gudang <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model="pelapor_gudang" 
                            type="text" 
                            placeholder="Nama mandor atau petugas gudang" 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                            required 
                        />
                        @error('pelapor_gudang') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Keterangan (Opsional)</label>
                        <textarea 
                            wire:model="hasil_catatan" 
                            rows="2" 
                            placeholder="Masukkan keterangan hasil proses..." 
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
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Proses Gudang -->
    @if ($modalHapusTerbuka && $targetLogHapus)
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
                    <h3 class="text-base font-bold text-[#0F172A] mb-1">Konfirmasi Hapus Proses Gudang</h3>
                    <p class="text-xs text-[#64748B] mb-5 leading-relaxed">
                        Apakah Anda yakin ingin menghapus catatan proses <strong class="text-[#0F172A]">{{ $targetLogHapus->label_proses }}</strong> untuk Batch <strong class="text-[#0F172A]">{{ $targetLogHapus->drumBatch->kode_batch }}</strong> ({{ number_format($targetLogHapus->jumlah_diproses) }} drum)? Tindakan ini akan menghapus data proses secara permanen.
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
                            wire:click="hapusLog" 
                            type="button" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 shadow-xs disabled:opacity-75"
                        >
                            <span wire:loading.remove wire:target="hapusLog">Ya, Hapus Data</span>
                            <span wire:loading wire:target="hapusLog" class="inline-flex items-center gap-1.5">
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
    @endif
</div>
