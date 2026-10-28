<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Catatan Kunjungan Kendaraan
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Arsip lengkap arus keluar-masuk kendaraan dan verifikasi surat jalan B3 di pos gerbang
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold">
                <span>🛡️ Arsip Administrasi Pos Satpam</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter & Pencarian Bar -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="w-full md:w-80">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="🔍 Cari Nopol / Pengemudi / Asal.." 
                        class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm text-slate-800"
                    />
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Filter Status -->
                    <select 
                        wire:model.live="filterStatus"
                        class="rounded-xl border-slate-200 text-xs font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Semua Status Kunjungan</option>
                        <option value="aktif">Status: Aktif (Di Pabrik)</option>
                        <option value="selesai">Status: Selesai (Sudah Keluar)</option>
                    </select>

                    <!-- Filter Surat Jalan B3 -->
                    <select 
                        wire:model.live="filterB3"
                        class="rounded-xl border-slate-200 text-xs font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Semua Status Surat B3</option>
                        <option value="sesuai">B3: Sesuai Prosedur</option>
                        <option value="tidak_sesuai">B3: Tidak Sesuai</option>
                        <option value="tidak_ada_b3">Bukan Muatan B3</option>
                    </select>

                    <!-- Filter Tanggal -->
                    <input 
                        wire:model.live="filterTanggal" 
                        type="date" 
                        class="rounded-xl border-slate-200 text-xs font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500 py-1.5"
                    />
                </div>
            </div>

            <!-- Tabel Data Kendaraan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">No Kunjungan</th>
                                <th class="p-4">No Polisi</th>
                                <th class="p-4">Pengemudi & Asal</th>
                                <th class="p-4">Keperluan</th>
                                <th class="p-4">Waktu Masuk / Keluar</th>
                                <th class="p-4">Status Kunjungan</th>
                                <th class="p-4">Dokumen B3</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($visits as $v)
                                <tr class="hover:bg-slate-50/75 transition">
                                    <td class="p-4 font-mono font-bold text-blue-700">{{ $v->nomor_kunjungan }}</td>
                                    <td class="p-4 font-bold text-slate-900">{{ $v->nomor_polisi }}</td>
                                    <td class="p-4">
                                        <div class="font-semibold text-slate-800">{{ $v->nama_pengemudi }}</div>
                                        <div class="text-xs text-slate-500">{{ $v->asal_tujuan ?? '-' }}</div>
                                    </td>
                                    <td class="p-4 text-slate-700 text-xs">{{ $v->keperluan }}</td>
                                    <td class="p-4 text-xs">
                                        <div class="text-slate-800 font-medium">📥 Masuk: {{ $v->waktu_masuk?->format('d/m/Y H:i') }}</div>
                                        @if($v->waktu_keluar)
                                            <div class="text-emerald-700 font-medium mt-0.5">📤 Keluar: {{ $v->waktu_keluar->format('d/m/Y H:i') }}</div>
                                        @else
                                            <div class="text-amber-600 font-semibold mt-0.5">⏳ Di Dalam Pabrik</div>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        @if($v->waktu_keluar)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-pulse">
                                                Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $v->status_pemeriksaan_surat_jalan_b3 === 'sesuai' ? 'bg-emerald-100 text-emerald-800' : ($v->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700') }}">
                                            {{ $v->status_pemeriksaan_surat_jalan_b3 === 'sesuai' ? 'B3: Sesuai' : ($v->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai' ? 'B3: Tidak Sesuai' : 'Bukan B3') }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button 
                                            wire:click="lihatDetail({{ $v->id }})"
                                            type="button"
                                            class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs transition"
                                        >
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-10 text-center text-slate-400">
                                        <div class="text-3xl mb-2">🚛</div>
                                        <div class="font-semibold text-slate-600">Tidak ada catatan kunjungan kendaraan</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($visits->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $visits->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL DETAIL KUNJUNGAN -->
    @if ($showDetailModal && $selectedVisit)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
                <div class="p-6 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">
                            Detail Kunjungan: {{ $selectedVisit->nomor_kunjungan }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pencatatan pos satpam & surat jalan kemasan limbah B3
                        </p>
                    </div>
                    <button wire:click="tutupDetailModal" class="text-slate-400 hover:text-slate-600 text-xl font-bold">
                        &times;
                    </button>
                </div>

                <div class="p-6 space-y-4 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-xs text-slate-400 font-semibold block">Nomor Polisi:</span>
                            <span class="text-base font-bold text-slate-900">{{ $selectedVisit->nomor_polisi }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-xs text-slate-400 font-semibold block">Nama Pengemudi:</span>
                            <span class="text-base font-bold text-slate-900">{{ $selectedVisit->nama_pengemudi }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-xs text-slate-400 font-semibold block">Perusahaan / Asal-Tujuan:</span>
                            <span class="font-medium text-slate-800">{{ $selectedVisit->asal_tujuan ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-xs text-slate-400 font-semibold block">Keperluan:</span>
                            <span class="font-medium text-slate-800">{{ $selectedVisit->keperluan }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-xs text-slate-400 font-semibold block">Waktu Masuk:</span>
                            <span class="font-medium text-slate-800">{{ $selectedVisit->waktu_masuk?->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-xs text-slate-400 font-semibold block">Waktu Keluar:</span>
                            <span class="font-medium {{ $selectedVisit->waktu_keluar ? 'text-slate-800' : 'text-amber-600 font-bold' }}">
                                {{ $selectedVisit->waktu_keluar?->format('d/m/Y H:i:s') ?? 'Masih Aktif di Pabrik' }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-2">Informasi Surat Jalan & Dokumen B3</h4>
                        <div class="bg-amber-50/50 border border-amber-200 rounded-xl p-4 space-y-2">
                            <div>
                                <span class="text-xs text-slate-500 font-semibold">Status Pemeriksaan Surat Jalan B3:</span>
                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold {{ $selectedVisit->status_pemeriksaan_surat_jalan_b3 === 'sesuai' ? 'bg-emerald-100 text-emerald-800' : ($selectedVisit->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai' ? 'bg-red-100 text-red-800' : 'bg-slate-200 text-slate-800') }}">
                                    {{ $selectedVisit->status_pemeriksaan_surat_jalan_b3 === 'sesuai' ? 'B3: Sesuai Prosedur' : ($selectedVisit->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai' ? 'B3: Tidak Sesuai' : 'Bukan Muatan B3') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-semibold">Nomor / Keterangan Surat Jalan:</span>
                                <p class="text-xs text-slate-800 mt-0.5">{{ $selectedVisit->informasi_surat_jalan ?? 'Tidak ada data surat jalan fisik' }}</p>
                            </div>
                            @if($selectedVisit->keterangan_muatan)
                                <div>
                                    <span class="text-xs text-slate-500 font-semibold">Keterangan Muatan:</span>
                                    <p class="text-xs text-slate-800 mt-0.5">{{ $selectedVisit->keterangan_muatan }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-3 text-xs text-slate-500 flex justify-between">
                        <span>Pencatat Pos Gerbang: <strong>{{ $selectedVisit->user?->name ?? 'Petugas Satpam' }}</strong></span>
                        <span>Pos: {{ $selectedVisit->pos_gerbang ?? 'Gerbang Utama' }}</span>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button 
                        wire:click="tutupDetailModal"
                        type="button" 
                        class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs transition"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
