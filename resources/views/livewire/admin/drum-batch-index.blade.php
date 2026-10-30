<div class="space-y-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Data Penerimaan & Transit Batch Drum
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Daftar batch penerimaan drum dan status pengendapan gudang
            </p>
        </div>
        <button 
            wire:click="bukaCreateModal"
            type="button" 
            class="px-3.5 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Tambah Data Drum</span>
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
                placeholder="Cari kode batch atau jenis..." 
                class="w-full pl-9 pr-3 py-1.5 text-xs bg-[#F7F9FC] border border-[#E2E8F0] rounded-lg text-[#0F172A] placeholder:text-[#64748B] focus:outline-none focus:bg-white focus:border-[#2563EB]"
            />
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <select 
                wire:model.live="filterPengendapan"
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 pl-3 pr-8 focus:border-[#2563EB] focus:bg-white"
            >
                <option value="">Semua Pengendapan</option>
                <option value="Transit">Transit</option>
                <option value="Mengendap">Mengendap</option>
                <option value="Selesai">Selesai</option>
            </select>

            <select 
                wire:model.live="filterProses"
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 pl-3 pr-8 focus:border-[#2563EB] focus:bg-white"
            >
                <option value="">Semua Status Proses</option>
                <option value="Diterima">Diterima</option>
                <option value="Dalam Proses">Dalam Proses</option>
                <option value="Selesai">Selesai</option>
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
                        <th class="py-3 px-4 whitespace-nowrap">Jenis Drum</th>
                        <th class="py-3 px-4 whitespace-nowrap text-center">Jumlah</th>
                        <th class="py-3 px-4 whitespace-nowrap">Pengendapan</th>
                        <th class="py-3 px-4 whitespace-nowrap">Status Proses</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tanggal Masuk</th>
                        <th class="py-3 px-4 whitespace-nowrap">Sumber Laporan</th>
                        <th class="py-3 px-4 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($batches as $batch)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                {{ $batch->kode_batch }}
                            </td>
                            <td class="py-2.5 px-4 text-[#0F172A] whitespace-nowrap">
                                {{ $batch->kategori_jenis ?? '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-center font-semibold text-[#0F172A] whitespace-nowrap tabular-nums">
                                {{ number_format($batch->jumlah_diterima, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                @if ($batch->status_pengendapan === 'Transit')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Transit
                                    </span>
                                @elseif ($batch->status_pengendapan === 'Mengendap')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#FEF3C7] text-[#92400E]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                                        Mengendap
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                @if ($batch->status_proses === 'Diterima')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                        Diterima
                                    </span>
                                @elseif ($batch->status_proses === 'Dalam Proses')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-[#2563EB]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                                        Diproses
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                {{ $batch->tanggal_penerimaan->format('d M Y') }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] max-w-[180px] truncate">
                                {{ $batch->sumber_laporan ?? '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                <button 
                                    wire:click="bukaStatusModal({{ $batch->id }})"
                                    type="button" 
                                    class="px-2.5 py-1 rounded-lg border border-[#E2E8F0] hover:bg-slate-50 text-[#0F172A] text-xs font-medium transition cursor-pointer"
                                >
                                    Ubah Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-xs text-[#64748B]">
                                Belum ada data batch drum.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($batches->hasPages())
            <div class="px-4 py-3 border-t border-[#E2E8F0] bg-white">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    <!-- Modal 1: Tambah Data Drum (Clean White Style per Reference) -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden border border-[#E2E8F0]">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-[#0F172A]">Tambah Data Drum</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">Kode Otomatis: <span class="font-mono font-semibold text-[#2563EB]">{{ $kode_batch }}</span></p>
                    </div>
                    <button 
                        wire:click="tutupCreateModal" 
                        type="button" 
                        class="p-1 rounded-lg text-[#64748B] hover:text-[#0F172A] hover:bg-slate-100"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanBatch" class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Tanggal Masuk <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="tanggal_penerimaan" 
                                type="date" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('tanggal_penerimaan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jumlah Drum <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="jumlah_diterima" 
                                type="number" 
                                min="1" 
                                placeholder="Contoh: 150"
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('jumlah_diterima') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jenis Drum <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="kategori_jenis" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="Besi Tight Head">Besi Tight Head</option>
                                <option value="Besi Open Top">Besi Open Top</option>
                                <option value="Plastik HDPE">Plastik HDPE</option>
                                <option value="Drum Galvanis">Drum Galvanis</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Status Pengendapan</label>
                            <select 
                                wire:model.live="status_pengendapan" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="Transit">Transit</option>
                                <option value="Mengendap">Mengendap</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Status Proses</label>
                            <select 
                                wire:model.live="status_proses" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="Diterima">Diterima</option>
                                <option value="Dalam Proses">Dalam Proses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Sumber Laporan</label>
                            <input 
                                wire:model="sumber_laporan" 
                                type="text" 
                                placeholder="Contoh: Mandor Gudang" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Keterangan</label>
                        <textarea 
                            wire:model="catatan" 
                            rows="2" 
                            placeholder="Masukkan keterangan tambahan..." 
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

    <!-- Modal 2: Ubah Status Batch -->
    @if ($showStatusModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden border border-[#E2E8F0]">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex justify-between items-center">
                    <h3 class="text-base font-bold text-[#0F172A]">Ubah Status Batch</h3>
                    <button 
                        wire:click="tutupStatusModal" 
                        type="button" 
                        class="p-1 rounded-lg text-[#64748B] hover:text-[#0F172A] hover:bg-slate-100"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanStatus" class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Status Pengendapan</label>
                        <select 
                            wire:model.live="edit_status_pengendapan" 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        >
                            <option value="Transit">Transit</option>
                            <option value="Mengendap">Mengendap</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Status Proses</label>
                        <select 
                            wire:model.live="edit_status_proses" 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        >
                            <option value="Diterima">Diterima</option>
                            <option value="Dalam Proses">Dalam Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <div class="pt-3 border-t border-[#E2E8F0] flex justify-end gap-2">
                        <button 
                            wire:click="tutupStatusModal" 
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
