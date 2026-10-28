<div>
    <!-- Page Heading Didalam Lingkup Livewire -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Data Penerimaan & Transit Batch Drum
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Pencatatan batch drum masuk, kategori jenis, serta pemantauan masa pengendapan
                    </p>
                </div>
                <button 
                    wire:click="bukaCreateModal"
                    type="button" 
                    class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition inline-flex items-center gap-2 cursor-pointer"
                >
                    <span>➕ Input Batch Baru</span>
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

            <!-- Baris Pencarian & Filter -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="w-full md:w-80">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="🔍 Cari Kode Batch / Jenis..." 
                        class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm text-slate-800"
                    />
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Filter Status Pengendapan -->
                    <select 
                        wire:model.live="filterPengendapan"
                        class="rounded-xl border-slate-200 text-xs font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Semua Status Pengendapan</option>
                        <option value="Transit">Status: Transit</option>
                        <option value="Mengendap">Status: Mengendap</option>
                        <option value="Selesai">Status: Selesai</option>
                    </select>

                    <!-- Filter Status Proses -->
                    <select 
                        wire:model.live="filterProses"
                        class="rounded-xl border-slate-200 text-xs font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Semua Status Proses</option>
                        <option value="Diterima">Proses: Diterima</option>
                        <option value="Dalam Proses">Proses: Dalam Proses</option>
                        <option value="Selesai">Proses: Selesai</option>
                    </select>
                </div>
            </div>

            <!-- Tabel Data Batch Drum -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">Kode Batch</th>
                                <th class="p-4">Tgl Terima</th>
                                <th class="p-4">Kategori Jenis</th>
                                <th class="p-4 text-center">Jumlah</th>
                                <th class="p-4">Status Pengendapan</th>
                                <th class="p-4">Status Proses</th>
                                <th class="p-4">Sumber Laporan</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($batches as $batch)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-mono font-bold text-xs text-blue-700">
                                        {{ $batch->kode_batch }}
                                    </td>
                                    <td class="p-4 text-slate-700 font-medium">
                                        {{ $batch->tanggal_penerimaan->format('d/m/Y') }}
                                    </td>
                                    <td class="p-4 text-slate-800">
                                        {{ $batch->kategori_jenis ?? '-' }}
                                    </td>
                                    <td class="p-4 text-center font-bold text-slate-900">
                                        {{ number_format($batch->jumlah_diterima) }}
                                    </td>
                                    <td class="p-4">
                                        @if ($batch->status_pengendapan === 'Transit')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                ⏳ Transit
                                            </span>
                                        @elseif ($batch->status_pengendapan === 'Mengendap')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                💧 Mengendap
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ✅ Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        @if ($batch->status_proses === 'Diterima')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                                Diterima
                                            </span>
                                        @elseif ($batch->status_proses === 'Dalam Proses')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                                ⚙️ Dalam Proses
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-700">
                                                🏁 Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-xs text-slate-500">
                                        {{ $batch->sumber_laporan ?? '-' }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <button 
                                            wire:click="bukaStatusModal({{ $batch->id }})"
                                            type="button" 
                                            class="px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition"
                                        >
                                            ✏️ Update
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-slate-400">
                                        <div class="text-4xl mb-2">📦</div>
                                        <div class="font-semibold text-slate-600">Belum ada data batch drum</div>
                                        <div class="text-xs mt-1">Klik tombol "Input Batch Baru" di atas untuk mencatat penerimaan drum dari gudang.</div>
                                        <div class="mt-4">
                                            <button 
                                                wire:click="bukaCreateModal"
                                                type="button" 
                                                class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer"
                                            >
                                                <span>➕ Input Batch Baru Sekarang</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($batches->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $batches->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL 1: INPUT BATCH BARU -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 animate-in fade-in duration-150">
                <div class="bg-blue-900 text-white p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold">Input Batch Drum Penerimaan</h3>
                        <p class="text-xs text-blue-200 mt-0.5">Mencatat data fisik drum masuk dari laporan petugas gudang</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-blue-300 block uppercase">Kode Batch</span>
                        <span class="font-mono font-bold text-sm text-white">{{ $kode_batch }}</span>
                    </div>
                </div>

                <form wire:submit="simpanBatch" class="p-6 space-y-4">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Terima <span class="text-rose-500">*</span></label>
                            <input wire:model="tanggal_penerimaan" type="date" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" required />
                            @error('tanggal_penerimaan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Drum (Pcs) <span class="text-rose-500">*</span></label>
                            <input wire:model="jumlah_diterima" type="number" min="1" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" required />
                            @error('jumlah_diterima') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Jenis Drum <span class="text-rose-500">*</span></label>
                        <select wire:model="kategori_jenis" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Besi Tight Head">Besi Tight Head (Bung Ring / Buntu)</option>
                            <option value="Besi Open Top">Besi Open Top (Tutup Terbuka)</option>
                            <option value="Plastik HDPE">Plastik HDPE 200 Liter</option>
                            <option value="Drum Galvanis">Drum Galvanis</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pengendapan</label>
                            <select wire:model="status_pengendapan" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Transit">Transit (Baru Tiba)</option>
                                <option value="Mengendap">Masa Pengendapan</option>
                                <option value="Selesai">Pengendapan Selesai</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Proses Awal</label>
                            <select wire:model="status_proses" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Diterima">Diterima</option>
                                <option value="Dalam Proses">Dalam Proses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Sumber Laporan Fisik Gudang</label>
                        <input wire:model="sumber_laporan" type="text" placeholder="Contoh: Nota Fisik Mandor Pak Joko" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Kondisi Awal (Opsional)</label>
                        <textarea wire:model="catatan" rows="2" placeholder="Contoh: Drum karat ringan, sisa residu cat" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button wire:click="tutupCreateModal" type="button" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm">Simpan Batch</button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: UPDATE STATUS CEPAT -->
    @if ($showStatusModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-100 animate-in fade-in duration-150">
                <div class="bg-slate-900 text-white p-5">
                    <h3 class="text-base font-bold">Update Status Batch Drum</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Perbarui status masa transit dan status alur proses</p>
                </div>

                <form wire:submit="simpanStatus" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pengendapan</label>
                        <select wire:model="edit_status_pengendapan" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Transit">Transit (Menunggu Antrean)</option>
                            <option value="Mengendap">Masa Pengendapan Residu</option>
                            <option value="Selesai">Pengendapan Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Proses Rekondisi</label>
                        <select wire:model="edit_status_proses" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Diterima">Diterima</option>
                            <option value="Dalam Proses">Dalam Proses (Sedang Dikerjakan)</option>
                            <option value="Selesai">Selesai (Siap Distribusi)</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button wire:click="tutupStatusModal" type="button" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
