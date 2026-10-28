<div>
    <!-- Page Heading Didalam Lingkup Livewire -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Catatan 4 Tahap Proses Gudang
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Rekapitulasi aktivitas penyortiran, peniupan, pencucian, dan pengecatan drum rekondisi
                    </p>
                </div>
                <button 
                    wire:click="bukaCreateModal"
                    type="button" 
                    class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition inline-flex items-center gap-2 cursor-pointer"
                >
                    <span>➕ Input Laporan Proses</span>
                </button>
            </div>
        </div>
    </header>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Message Sukses -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span>✅</span>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Pencarian & Filter -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="w-full md:w-80">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="🔍 Cari Pelapor / Batch / Catatan..." 
                        class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm text-slate-800"
                    />
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Filter Batch -->
                    <select 
                        wire:model.live="filterBatchId"
                        class="rounded-xl border-slate-200 text-xs font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Semua Batch Drum</option>
                        @foreach ($daftarBatch as $b)
                            <option value="{{ $b->id }}">{{ $b->kode_batch }} ({{ $b->kategori_jenis }})</option>
                        @endforeach
                    </select>

                    <!-- Filter Jenis Proses -->
                    <select 
                        wire:model.live="filterJenisProses"
                        class="rounded-xl border-slate-200 text-xs font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Semua Tahap Proses</option>
                        <option value="penyortiran">Penyortiran (Sorting)</option>
                        <option value="peniupan">Peniupan (Denting)</option>
                        <option value="pencucian">Pencucian (Washing)</option>
                        <option value="pengecatan">Pengecatan (Painting)</option>
                    </select>
                </div>
            </div>

            <!-- Tabel Log Proses -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">Tanggal Aktivitas</th>
                                <th class="p-4">Batch Drum</th>
                                <th class="p-4">Tahap Proses</th>
                                <th class="p-4 text-center">Diproses</th>
                                <th class="p-4 text-center">Afkir (Reject)</th>
                                <th class="p-4 text-center">Hasil Baik</th>
                                <th class="p-4">Pelapor Gudang</th>
                                <th class="p-4">Tgl Input Sistem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 text-slate-800 font-semibold">
                                        {{ $log->tanggal_aktivitas->format('d/m/Y') }}
                                        <div class="text-[11px] text-slate-400 font-normal">Fisik di gudang</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="font-mono font-bold text-blue-700 text-xs">{{ $log->drumBatch->kode_batch }}</span>
                                        <div class="text-xs text-slate-500">{{ $log->drumBatch->kategori_jenis }}</div>
                                    </td>
                                    <td class="p-4">
                                        @if ($log->jenis_proses === 'penyortiran')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                🔍 Penyortiran
                                            </span>
                                        @elseif ($log->jenis_proses === 'peniupan')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                💨 Peniupan
                                            </span>
                                        @elseif ($log->jenis_proses === 'pencucian')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">
                                                🧼 Pencucian
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                                🎨 Pengecatan
                                            </span>
                                        @endif
                                        @if ($log->hasil_catatan)
                                            <div class="text-xs text-slate-500 mt-1 italic">"{{ $log->hasil_catatan }}"</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center font-bold text-slate-900">
                                        {{ number_format($log->jumlah_diproses) }}
                                    </td>
                                    <td class="p-4 text-center font-semibold text-rose-600">
                                        {{ number_format($log->jumlah_afkir) }}
                                    </td>
                                    <td class="p-4 text-center font-bold text-emerald-600">
                                        {{ number_format($log->jumlah_diproses - $log->jumlah_afkir) }}
                                    </td>
                                    <td class="p-4 text-slate-700 text-xs">
                                        {{ $log->pelapor_gudang }}
                                    </td>
                                    <td class="p-4 text-slate-500 text-xs font-mono">
                                        {{ $log->tanggal_input->format('d/m/Y H:i') }}
                                        <div class="text-[10px] text-slate-400">Oleh: {{ $log->user->name }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-slate-400">
                                        <div class="text-4xl mb-2">⚙️</div>
                                        <div class="font-semibold text-slate-600">Belum ada catatan log proses gudang</div>
                                        <div class="text-xs mt-1">Klik tombol "Input Laporan Proses" untuk mencatat aktivitas sortir, tiup, cuci, atau cat.</div>
                                        <div class="mt-4">
                                            <button 
                                                wire:click="bukaCreateModal"
                                                type="button" 
                                                class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer"
                                            >
                                                <span>➕ Input Laporan Proses Sekarang</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($logs->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL INPUT LOG PROSES -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 animate-in fade-in duration-150">
                <div class="bg-blue-900 text-white p-6">
                    <h3 class="text-lg font-bold">Input Laporan Proses Rekondisi</h3>
                    <p class="text-xs text-blue-200 mt-0.5">Mencatat data aktivitas fisik gudang berdasarkan nota/laporan petugas</p>
                </div>

                <form wire:submit="simpanLog" class="p-6 space-y-4">
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Batch Drum <span class="text-rose-500">*</span></label>
                        <select wire:model="drum_batch_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Pilih Batch Drum --</option>
                            @foreach ($daftarBatch as $b)
                                <option value="{{ $b->id }}">{{ $b->kode_batch }} — {{ $b->kategori_jenis }} (Total: {{ $b->jumlah_diterima }} drum)</option>
                            @endforeach
                        </select>
                        @error('drum_batch_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tahap Proses <span class="text-rose-500">*</span></label>
                            <select wire:model="jenis_proses" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="penyortiran">1. Penyortiran (Sorting)</option>
                                <option value="peniupan">2. Peniupan (Denting)</option>
                                <option value="pencucian">3. Pencucian (Washing)</option>
                                <option value="pengecatan">4. Pengecatan (Painting)</option>
                            </select>
                            @error('jenis_proses') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Aktivitas Fisik <span class="text-rose-500">*</span></label>
                            <input wire:model="tanggal_aktivitas" type="date" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" required />
                            @error('tanggal_aktivitas') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Diproses (Pcs) <span class="text-rose-500">*</span></label>
                            <input wire:model="jumlah_diproses" type="number" min="1" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" required />
                            @error('jumlah_diproses') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Afkir / Reject (Pcs)</label>
                            <input wire:model="jumlah_afkir" type="number" min="0" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" required />
                            @error('jumlah_afkir') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Mandor / Petugas Gudang Pelapor <span class="text-rose-500">*</span></label>
                        <input wire:model="pelapor_gudang" type="text" placeholder="Contoh: Mandor Joko / Regu Cuci Shift Pagi" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" required />
                        @error('pelapor_gudang') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hasil / Catatan Teknis (Opsional)</label>
                        <textarea wire:model="hasil_catatan" rows="2" placeholder="Contoh: Pengecatan warna biru standar PT PKDI, 2 drum afkir penyok parah" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button wire:click="tutupCreateModal" type="button" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm">Simpan Log Proses</button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
