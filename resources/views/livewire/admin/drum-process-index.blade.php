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
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                    <tr>
                        <th class="py-3 px-4 whitespace-nowrap">No. Batch</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tahap Proses</th>
                        <th class="py-3 px-4 whitespace-nowrap text-center">Diproses</th>
                        <th class="py-3 px-4 whitespace-nowrap text-center">Reject</th>
                        <th class="py-3 px-4 whitespace-nowrap text-center">Hasil Baik</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tanggal Aktivitas</th>
                        <th class="py-3 px-4 whitespace-nowrap">Pelapor Gudang</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tanggal Input</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                {{ $log->drumBatch->kode_batch }}
                            </td>
                            <td class="py-2.5 px-4 whitespace-nowrap">
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
                            <td class="py-2.5 px-4 text-center font-semibold text-[#0F172A] whitespace-nowrap tabular-nums">
                                {{ number_format($log->jumlah_diproses, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-center whitespace-nowrap tabular-nums">
                                @if ($log->jumlah_afkir > 0)
                                    <span class="font-semibold text-rose-600">{{ number_format($log->jumlah_afkir, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-center font-semibold text-[#10B981] whitespace-nowrap tabular-nums">
                                {{ number_format($log->jumlah_diproses - $log->jumlah_afkir, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-[#0F172A] font-medium whitespace-nowrap tabular-nums">
                                {{ $log->tanggal_aktivitas->format('d M Y') }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap">
                                {{ $log->pelapor_gudang }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                {{ $log->tanggal_input->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-xs text-[#64748B]">
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
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
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

                <form wire:submit="simpanLog" class="p-5 space-y-4">
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
</div>
