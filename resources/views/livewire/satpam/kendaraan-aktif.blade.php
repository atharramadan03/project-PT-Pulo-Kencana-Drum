<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Kendaraan Aktif di Area Pabrik
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Daftar truk yang saat ini masih berada di dalam area PT Pulo Kencana (belum memiliki waktu keluar)
                </p>
            </div>
            <a 
                href="{{ route('satpam.kendaraan-masuk') }}" 
                wire:navigate
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm transition inline-flex items-center gap-2"
            >
                <span>➕ Catat Truk Masuk Baru</span>
            </a>
        </div>
    </x-slot>

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

            <!-- Kotak Pencarian -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="w-full sm:w-80">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="🔍 Cari Nopol, Supir, Asal..." 
                        class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm text-slate-800"
                    />
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Total Kendaraan di Dalam: <span class="font-bold text-emerald-600">{{ $kendaraanAktif->total() }} Truk</span>
                </div>
            </div>

            <!-- Tabel Daftar Kendaraan Aktif -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">No. Kunjungan</th>
                                <th class="p-4">Identitas Armada</th>
                                <th class="p-4">Asal / Keperluan</th>
                                <th class="p-4">Waktu Masuk</th>
                                <th class="p-4">Pemeriksaan Dokumen B3</th>
                                <th class="p-4 text-center">Aksi Gerbang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($kendaraanAktif as $visit)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-mono font-bold text-xs text-slate-700">
                                        {{ $visit->nomor_kunjungan }}
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold font-mono text-slate-900">{{ $visit->nomor_polisi }}</div>
                                        <div class="text-xs text-slate-500">Supir: {{ $visit->nama_pengemudi }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-slate-800 font-medium">{{ $visit->asal_tujuan }}</div>
                                        <div class="text-xs text-slate-500">{{ $visit->keperluan }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-semibold text-slate-800">{{ $visit->waktu_masuk->format('d/m/Y H:i') }}</div>
                                        <div class="text-[11px] text-emerald-600 font-medium">
                                            {{ $visit->waktu_masuk->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if ($visit->status_pemeriksaan_surat_jalan_b3 === 'sesuai')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ✅ Sesuai B3
                                            </span>
                                        @elseif ($visit->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                ❌ Tidak Sesuai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                                Bukan B3
                                            </span>
                                        @endif
                                        @if ($visit->informasi_surat_jalan)
                                            <div class="text-[11px] text-slate-400 mt-1 font-mono">SJ: {{ $visit->informasi_surat_jalan }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <button 
                                            wire:click="bukaModalKeluar({{ $visit->id }})"
                                            type="button" 
                                            class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-1.5"
                                        >
                                            <span>🚪 Catat Keluar</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-slate-400">
                                        <div class="text-4xl mb-2">🚛</div>
                                        <div class="font-semibold text-slate-600">Tidak ada kendaraan aktif saat ini</div>
                                        <div class="text-xs mt-1">Semua truk yang masuk telah dicatat keluar atau belum ada kunjungan baru.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($kendaraanAktif->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $kendaraanAktif->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL CATAT KELUAR -->
    @if ($showModal && $selectedVisit)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 animate-in fade-in duration-150">
                
                <div class="bg-slate-900 text-white p-6">
                    <h3 class="text-lg font-bold">Catat Kendaraan Keluar</h3>
                    <p class="text-xs text-slate-400 mt-1">Tutup transaksi kunjungan armada yang meninggalkan area pabrik</p>
                </div>

                <form wire:submit="simpanKeluar" class="p-6 space-y-4">
                    
                    <!-- Ringkasan Kunjungan -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nomor Kunjungan:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $selectedVisit->nomor_kunjungan }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nomor Polisi:</span>
                            <span class="font-mono font-bold text-slate-800 text-sm">{{ $selectedVisit->nomor_polisi }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Supir / Asal:</span>
                            <span class="font-medium text-slate-800">{{ $selectedVisit->nama_pengemudi }} ({{ $selectedVisit->asal_tujuan }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Waktu Masuk:</span>
                            <span class="font-bold text-emerald-700">{{ $selectedVisit->waktu_masuk->format('d/m/Y H:i') }} WIB</span>
                        </div>
                    </div>

                    <!-- Input Waktu Keluar -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Waktu Keluar <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            wire:model="waktu_keluar" 
                            type="datetime-local" 
                            class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 text-slate-800 font-medium"
                            required
                        />
                        @error('waktu_keluar') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Catatan Keluar -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Catatan Keluar (Opsional)
                        </label>
                        <textarea 
                            wire:model="catatan_keluar" 
                            rows="2"
                            placeholder="Kondisi armada saat keluar / muatan yang dibawa pulang"
                            class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 text-slate-800 text-sm"
                        ></textarea>
                        @error('catatan_keluar') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tombol Aksi Modal -->
                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button 
                            wire:click="tutupModal" 
                            type="button" 
                            class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5"
                        >
                            <span>🚪 Konfirmasi Kendaraan Keluar</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif
</div>
