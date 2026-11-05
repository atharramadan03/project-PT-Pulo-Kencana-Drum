<div class="space-y-4">
    <!-- Header Section (Tracker Only) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <div class="flex-1 min-w-0">
            <h1 class="font-bold text-lg sm:text-xl text-[#0F172A] tracking-tight leading-snug">
                Tracker Riwayat Kunjungan Kendaraan Truk & Logistik
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5 leading-relaxed">
                Log pemantauan terpusat seluruh pergerakan armada truk dan mobil masuk-keluar di kawasan pabrik PT Pulo Kencana Drum Industri
            </p>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span class="font-medium">Total Kunjungan</span>
                <x-icon name="clipboard-list" class="w-4 h-4 text-[#2563EB] shrink-0" />
            </div>
            <p class="text-lg sm:text-xl font-bold text-[#0F172A] mt-1 tabular-nums tracking-tight">
                {{ $totalSemua }} Kunjungan
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Keseluruhan data tercatat</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span class="font-medium">Sedang di Area Pabrik</span>
                <x-icon name="clock" class="w-4 h-4 text-amber-500 shrink-0" />
            </div>
            <p class="text-lg sm:text-xl font-bold text-amber-600 mt-1 tabular-nums tracking-tight">
                {{ $totalAktif }} Truk Aktif
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Belum mencatat waktu keluar</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span class="font-medium">Selesai Keluar Pabrik</span>
                <x-icon name="circle-check" class="w-4 h-4 text-emerald-600 shrink-0" />
            </div>
            <p class="text-lg sm:text-xl font-bold text-emerald-600 mt-1 tabular-nums tracking-tight">
                {{ $totalSelesai }} Truk Selesai
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">Kunjungan resmi ditutup</p>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs text-[#64748B]">
                <span class="font-medium">Kunjungan Hari Ini</span>
                <x-icon name="calendar" class="w-4 h-4 text-[#7C3AED] shrink-0" />
            </div>
            <p class="text-lg sm:text-xl font-bold text-[#0F172A] mt-1 tabular-nums tracking-tight">
                {{ $totalHariIni }} Kendaraan
            </p>
            <p class="text-[11px] text-[#64748B] mt-1">{{ now()->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    <!-- Filter & Toolbar Data (Responsive Grid) -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-2xs p-3.5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Pencarian Cepat -->
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[#64748B]">
                    <x-icon name="search" class="w-3.5 h-3.5 shrink-0" />
                </div>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari nopol, supir, PT asal, surat..." 
                    class="w-full pl-9 pr-3 py-1.5 rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] text-xs text-[#0F172A] placeholder:text-[#94A3B8] focus:bg-white focus:border-[#2563EB] focus:outline-hidden"
                />
            </div>

            <!-- Filter Status Kunjungan -->
            <div>
                <select 
                    wire:model.live="filterStatus"
                    class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] text-xs text-[#0F172A] focus:bg-white focus:border-[#2563EB] focus:outline-hidden"
                >
                    <option value="">Semua Status Kunjungan</option>
                    <option value="aktif">Sedang di Pabrik (Aktif)</option>
                    <option value="selesai">Sudah Keluar (Selesai)</option>
                </select>
            </div>

            <!-- Filter Dokumen Limbah B3 -->
            <div>
                <select 
                    wire:model.live="filterB3"
                    class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] text-xs text-[#0F172A] focus:bg-white focus:border-[#2563EB] focus:outline-hidden"
                >
                    <option value="">Semua Status Surat B3</option>
                    <option value="sesuai">Dokumen B3 Sesuai</option>
                    <option value="tidak_sesuai">Dokumen B3 Tidak Sesuai</option>
                    <option value="tidak_ada_b3">Bukan Muatan B3 (Non-B3)</option>
                </select>
            </div>

            <!-- Filter Tanggal Kunjungan -->
            <div class="flex items-center gap-2">
                <input 
                    wire:model.live="filterTanggal" 
                    type="date" 
                    class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] text-xs text-[#0F172A] focus:bg-white focus:border-[#2563EB] focus:outline-hidden"
                    title="Pilih tanggal kunjungan"
                />
                @if ($search || $filterStatus || $filterB3 || $filterTanggal)
                    <button 
                        wire:click="resetFilter" 
                        type="button" 
                        class="px-2.5 py-1.5 text-xs text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 shrink-0 font-medium cursor-pointer whitespace-nowrap"
                        title="Reset Filter"
                    >
                        Reset
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Tracker Riwayat Kunjungan Kendaraan (Responsive) -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-2xs overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#475569] font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3.5 whitespace-nowrap">No. Kunjungan</th>
                        <th class="py-2.5 px-3.5 whitespace-nowrap">No. Polisi</th>
                        <th class="py-2.5 px-3.5 whitespace-nowrap">Pengemudi & Asal / Tujuan</th>
                        <th class="py-2.5 px-3.5 whitespace-nowrap">Waktu Masuk</th>
                        <th class="py-2.5 px-3.5 whitespace-nowrap">Waktu Keluar</th>
                        <th class="py-2.5 px-3.5 whitespace-nowrap">Status</th>
                        <th class="py-2.5 px-3.5 whitespace-nowrap">Surat Jalan B3</th>
                        <th class="py-2.5 px-3.5 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse ($kunjungan as $visit)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3.5 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                {{ $visit->nomor_kunjungan }}
                            </td>
                            <td class="py-2.5 px-3.5 font-mono font-bold text-[#2563EB] whitespace-nowrap">
                                {{ $visit->nomor_polisi }}
                            </td>
                            <td class="py-2.5 px-3.5 max-w-[240px]">
                                <p class="font-semibold text-slate-900 truncate">{{ $visit->nama_pengemudi }}</p>
                                <p class="text-[11px] text-[#64748B] truncate">{{ $visit->asal_tujuan }}</p>
                            </td>
                            <td class="py-2.5 px-3.5 text-[#64748B] whitespace-nowrap tabular-nums">
                                <span class="font-medium text-slate-800">{{ $visit->waktu_masuk->format('d/m/Y') }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $visit->waktu_masuk->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-2.5 px-3.5 whitespace-nowrap tabular-nums">
                                @if ($visit->waktu_keluar)
                                    <span class="font-medium text-slate-800">{{ $visit->waktu_keluar->format('d/m/Y') }}</span>
                                    <span class="text-[11px] text-slate-500 block">{{ $visit->waktu_keluar->format('H:i') }} WIB</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        Belum Keluar
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3.5 whitespace-nowrap">
                                @if ($visit->waktu_keluar)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCFCE7] text-[#166534]">
                                        <x-icon name="circle-check" class="w-3 h-3 text-emerald-600 shrink-0" />
                                        <span>Selesai Keluar</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                        <x-icon name="clock" class="w-3 h-3 text-amber-600 shrink-0" />
                                        <span>Aktif di Pabrik</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3.5 whitespace-nowrap">
                                @if ($visit->status_pemeriksaan_surat_jalan_b3 === 'sesuai')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <x-icon name="shield-check" class="w-3 h-3 text-emerald-600 shrink-0" />
                                        <span>Sesuai B3</span>
                                    </span>
                                @elseif ($visit->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        <x-icon name="triangle-alert" class="w-3 h-3 text-rose-600 shrink-0" />
                                        <span>Tidak Sesuai</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Non-B3
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3.5 text-center whitespace-nowrap">
                                <button 
                                    wire:click="lihatDetail({{ $visit->id }})" 
                                    type="button" 
                                    class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:text-[#2563EB] hover:bg-slate-50 hover:border-blue-200 text-xs font-semibold transition inline-flex items-center gap-1.5 cursor-pointer shadow-2xs"
                                    title="Lihat Detail Lengkap Kunjungan"
                                >
                                    <x-icon name="eye" class="w-3.5 h-3.5 text-slate-500 shrink-0" />
                                    <span>Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-icon name="truck" class="w-8 h-8 text-slate-300" />
                                    <p class="text-xs font-medium text-slate-600">Tidak ada riwayat kendaraan yang sesuai filter.</p>
                                    @if ($search || $filterStatus || $filterB3 || $filterTanggal)
                                        <button 
                                            wire:click="resetFilter" 
                                            type="button" 
                                            class="text-xs text-[#2563EB] hover:underline cursor-pointer"
                                        >
                                            Bersihkan pencarian & filter
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kunjungan->hasPages())
            <div class="p-3 border-t border-[#E2E8F0]">
                {{ $kunjungan->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Detail Kunjungan Lengkap (Pure Tracker View) -->
    @if ($showDetailModal && $selectedVisit)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-xl w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-[#F8FAFC]">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">
                            Detail Kunjungan Kendaraan
                        </h3>
                        <p class="text-[11px] text-slate-500 font-mono mt-0.5">
                            {{ $selectedVisit->nomor_kunjungan }} &bull; Plat: {{ $selectedVisit->nomor_polisi }}
                        </p>
                    </div>
                    <button 
                        wire:click="tutupDetailModal" 
                        type="button" 
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 cursor-pointer transition"
                    >
                        <x-icon name="x" class="w-4 h-4 shrink-0" />
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                    <!-- Ringkasan Status Header -->
                    <div class="p-3 rounded-xl {{ $selectedVisit->waktu_keluar ? 'bg-emerald-50 border border-emerald-100 text-emerald-900' : 'bg-amber-50 border border-amber-100 text-amber-900' }} flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <x-icon name="{{ $selectedVisit->waktu_keluar ? 'circle-check' : 'clock' }}" class="w-4 h-4 shrink-0 text-current" />
                            <span class="font-semibold">
                                {{ $selectedVisit->waktu_keluar ? 'Kunjungan Selesai (Kendaraan Telah Keluar Pabrik)' : 'Kendaraan Sedang Aktif di Dalam Area Pabrik' }}
                            </span>
                        </div>
                        <span class="font-mono font-bold text-[11px] px-2 py-0.5 rounded bg-white/70 shadow-2xs">
                            {{ $selectedVisit->pos_gerbang }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-[11px] text-slate-500 block">Waktu Masuk Gerbang:</span>
                            <span class="font-semibold text-slate-800">{{ $selectedVisit->waktu_masuk->format('d F Y, H:i') }} WIB</span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-[11px] text-slate-500 block">Waktu Keluar Gerbang:</span>
                            <span class="font-semibold text-slate-800">
                                {{ $selectedVisit->waktu_keluar ? $selectedVisit->waktu_keluar->format('d F Y, H:i') . ' WIB' : '— Belum Keluar —' }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Nama Supir / Pengemudi</span>
                            <span class="font-semibold text-slate-900">{{ $selectedVisit->nama_pengemudi }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Perusahaan Asal / Tujuan</span>
                            <span class="font-semibold text-slate-900">{{ $selectedVisit->asal_tujuan }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Keperluan Kunjungan</span>
                            <span class="font-semibold text-slate-900">{{ $selectedVisit->keperluan }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Nomor Surat Jalan Fisik</span>
                            <span class="font-mono font-semibold text-slate-900">{{ $selectedVisit->informasi_surat_jalan ?: 'Tidak Ada' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Pemeriksaan Limbah B3</span>
                            <span class="font-semibold">
                                @if ($selectedVisit->status_pemeriksaan_surat_jalan_b3 === 'sesuai')
                                    <span class="text-emerald-700">✓ Sesuai Dokumen B3</span>
                                @elseif ($selectedVisit->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai')
                                    <span class="text-rose-700">✗ Tidak Sesuai Manifest</span>
                                @else
                                    <span class="text-slate-600">Non-B3</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Petugas Satpam Pencatat</span>
                            <span class="font-semibold text-slate-900">{{ $selectedVisit->user?->name ?? 'Satpam Pos Gerbang' }}</span>
                        </div>
                    </div>

                    <!-- Keterangan Muatan & Catatan -->
                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-700 block mb-0.5">Keterangan Muatan:</span>
                            <p class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-slate-700">
                                {{ $selectedVisit->keterangan_muatan ?: 'Tidak ada keterangan muatan khusus.' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold text-slate-700 block mb-0.5">Catatan Khusus Satpam:</span>
                            <p class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-slate-700">
                                {{ $selectedVisit->catatan ?: 'Tidak ada catatan tambahan.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Batch Drum Terkait Jika Ada -->
                    @if ($selectedVisit->drumBatches && $selectedVisit->drumBatches->count() > 0)
                        <div>
                            <span class="text-[11px] font-semibold text-slate-700 block mb-1.5">Batch Drum yang Diangkut:</span>
                            <div class="space-y-1.5">
                                @foreach ($selectedVisit->drumBatches as $batch)
                                    <div class="p-2.5 rounded-lg border border-blue-100 bg-blue-50/50 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="package" class="w-3.5 h-3.5 text-[#2563EB] shrink-0" />
                                            <span class="font-mono font-semibold text-blue-900">{{ $batch->kode_batch }}</span>
                                            <span class="text-slate-600">({{ $batch->kategori_jenis }})</span>
                                        </div>
                                        <span class="font-bold text-slate-900">{{ $batch->pivot->jumlah_drum ?? $batch->jumlah_diterima }} Unit</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Modal (Tutup Only untuk Tracker) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-end items-center">
                    <button 
                        wire:click="tutupDetailModal" 
                        type="button" 
                        class="px-4 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs transition cursor-pointer shadow-2xs"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
