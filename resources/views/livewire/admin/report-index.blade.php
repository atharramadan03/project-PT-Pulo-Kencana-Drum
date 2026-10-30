<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Rekapitulasi Laporan
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Ringkasan operasional gudang dan ekspor dokumen laporan
            </p>
        </div>
    </div>

    <!-- 1. Filter Periode & Tombol Export (Excel Dropdown + PDF) -->
    <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-medium text-[#64748B]">Periode:</span>
            <input 
                wire:model.live="startDate" 
                type="date" 
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 px-2.5 focus:border-[#2563EB] focus:bg-white"
            />
            <span class="text-xs text-[#64748B]">s/d</span>
            <input 
                wire:model.live="endDate" 
                type="date" 
                class="rounded-lg border-[#E2E8F0] bg-[#F7F9FC] text-xs font-medium text-[#0F172A] py-1.5 px-2.5 focus:border-[#2563EB] focus:bg-white"
            />
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Satu Tombol Dropdown Export Excel (.xlsx) -->
            <div x-data="{ openExcelMenu: false }" class="relative">
                <button 
                    @click="openExcelMenu = !openExcelMenu" 
                    type="button"
                    class="px-3.5 py-2 rounded-lg bg-[#10B981] hover:bg-emerald-600 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
                >
                    <x-icon name="file-spreadsheet" class="w-4 h-4" />
                    <span>Export Excel</span>
                    <x-icon name="chevron-down" class="w-3.5 h-3.5" />
                </button>

                <div 
                    x-show="openExcelMenu" 
                    @click.outside="openExcelMenu = false"
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-1.5 w-56 bg-white rounded-xl shadow-lg border border-[#E2E8F0] py-1.5 z-40 divide-y divide-slate-100"
                >
                    <div class="py-1">
                        <a 
                            href="{{ route('admin.reports.export-all', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                            @click="openExcelMenu = false"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-[#0F172A] hover:bg-slate-50 transition-colors"
                        >
                            <x-icon name="file-spreadsheet" class="w-4 h-4 text-[#10B981] shrink-0" />
                            <span>Export Semua Data (All)</span>
                        </a>
                    </div>
                    <div class="py-1">
                        <a 
                            href="{{ route('admin.reports.export-batch', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                            @click="openExcelMenu = false"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                        >
                            <x-icon name="package" class="w-4 h-4 text-[#2563EB] shrink-0" />
                            <span>Export Batch Drum</span>
                        </a>
                        <a 
                            href="{{ route('admin.reports.export-process', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                            @click="openExcelMenu = false"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                        >
                            <x-icon name="layers" class="w-4 h-4 text-[#F59E0B] shrink-0" />
                            <span>Export Proses Gudang</span>
                        </a>
                        <a 
                            href="{{ route('admin.reports.export-vehicle', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                            @click="openExcelMenu = false"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                        >
                            <x-icon name="truck" class="w-4 h-4 text-[#7C3AED] shrink-0" />
                            <span>Export Kunjungan Kendaraan</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tombol Export PDF -->
            <a 
                href="{{ route('admin.reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                target="_blank"
                class="px-3.5 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5"
            >
                <x-icon name="file-down" class="w-4 h-4" />
                <span>Export PDF</span>
            </a>
        </div>
    </div>

    <!-- 2. 4 KPI Cards Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-blue-50 text-[#2563EB] flex items-center justify-center shrink-0">
                <x-icon name="package" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium">Penerimaan Drum</p>
                <h4 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-0.5">{{ number_format($totalDrumDiterima, 0, ',', '.') }}</h4>
                <p class="text-[11px] text-[#64748B]">{{ $totalBatches }} Batch</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-amber-50 text-[#F59E0B] flex items-center justify-center shrink-0">
                <x-icon name="clock" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium">Masa Pengendapan</p>
                <h4 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-0.5">{{ number_format($drumTransit, 0, ',', '.') }}</h4>
                <p class="text-[11px] text-[#64748B]">Transit & Mengendap</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-emerald-50 text-[#10B981] flex items-center justify-center shrink-0">
                <x-icon name="layers" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium">Total Diproses</p>
                <h4 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-0.5">{{ number_format($drumSortir + $drumTiup + $drumCuci + $drumCat, 0, ',', '.') }}</h4>
                <p class="text-[11px] text-[#64748B]">{{ number_format($totalAfkir, 0, ',', '.') }} Reject</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-purple-50 text-[#7C3AED] flex items-center justify-center shrink-0">
                <x-icon name="truck" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs text-[#64748B] font-medium">Kunjungan Truk</p>
                <h4 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-0.5">{{ number_format($totalKunjungan, 0, ',', '.') }}</h4>
                <p class="text-[11px] text-[#64748B]">{{ $kunjunganB3 }} Sesuai B3</p>
            </div>
        </div>
    </div>

    <!-- 3. Tab & Tabel Laporan -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
        <div class="border-b border-[#E2E8F0] p-3.5 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div class="flex flex-wrap items-center gap-1.5">
                <button 
                    wire:click="setTab('batch')" 
                    type="button"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition cursor-pointer {{ $activeTab === 'batch' ? 'bg-[#2563EB] text-white font-semibold' : 'text-[#64748B] hover:bg-slate-100 hover:text-[#0F172A]' }}"
                >
                    Data Batch Drum
                </button>
                <button 
                    wire:click="setTab('proses')" 
                    type="button"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition cursor-pointer {{ $activeTab === 'proses' ? 'bg-[#2563EB] text-white font-semibold' : 'text-[#64748B] hover:bg-slate-100 hover:text-[#0F172A]' }}"
                >
                    Proses Gudang
                </button>
                <button 
                    wire:click="setTab('kendaraan')" 
                    type="button"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition cursor-pointer {{ $activeTab === 'kendaraan' ? 'bg-[#2563EB] text-white font-semibold' : 'text-[#64748B] hover:bg-slate-100 hover:text-[#0F172A]' }}"
                >
                    Kunjungan Kendaraan
                </button>
            </div>

            <div class="relative w-full md:w-60">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#64748B]">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari data..." 
                    class="w-full pl-8 pr-3 py-1.5 text-xs bg-[#F7F9FC] border border-[#E2E8F0] rounded-lg text-[#0F172A] placeholder:text-[#64748B] focus:outline-none focus:bg-white focus:border-[#2563EB]"
                />
            </div>
        </div>

        @if ($activeTab === 'batch')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                        <tr>
                            <th class="py-3 px-4 whitespace-nowrap">Kode Batch</th>
                            <th class="py-3 px-4 whitespace-nowrap">Tanggal</th>
                            <th class="py-3 px-4 whitespace-nowrap">Jenis</th>
                            <th class="py-3 px-4 whitespace-nowrap">Pengendapan</th>
                            <th class="py-3 px-4 whitespace-nowrap">Status</th>
                            <th class="py-3 px-4 whitespace-nowrap text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($batches as $b)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                    {{ $b->kode_batch }}
                                </td>
                                <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                    {{ $b->tanggal_penerimaan?->format('Y-m-d') }}
                                </td>
                                <td class="py-2.5 px-4 text-[#0F172A] whitespace-nowrap">
                                    {{ $b->kategori_jenis }}
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                        {{ $b->status_pengendapan }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    @if ($b->status_proses === 'Selesai')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                            Selesai
                                        </span>
                                    @elseif ($b->status_proses === 'Dalam Proses')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-[#2563EB]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                                            Diproses
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                            {{ $b->status_proses }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-right font-semibold text-[#0F172A] whitespace-nowrap tabular-nums">
                                    {{ number_format($b->jumlah_diterima, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-xs text-[#64748B]">
                                    Tidak ada data batch pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($batches && $batches->hasPages())
                <div class="px-4 py-3 border-t border-[#E2E8F0] bg-white">
                    {{ $batches->links() }}
                </div>
            @endif

        @elseif ($activeTab === 'proses')
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                        <tr>
                            <th class="py-3 px-4 whitespace-nowrap">Batch</th>
                            <th class="py-3 px-4 whitespace-nowrap">Tahap Proses</th>
                            <th class="py-3 px-4 whitespace-nowrap">Tanggal Aktivitas</th>
                            <th class="py-3 px-4 whitespace-nowrap text-center">Diproses</th>
                            <th class="py-3 px-4 whitespace-nowrap text-center">Reject</th>
                            <th class="py-3 px-4 whitespace-nowrap text-center">Hasil Baik</th>
                            <th class="py-3 px-4 whitespace-nowrap">Pelapor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($processLogs as $log)
                            @php $lolos = max(0, $log->jumlah_diproses - $log->jumlah_afkir); @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                    {{ $log->drumBatch?->kode_batch ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    @if ($log->jenis_proses === 'penyortiran')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#FEF3C7] text-[#92400E]">Penyortiran</span>
                                    @elseif ($log->jenis_proses === 'peniupan')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-[#2563EB]">Peniupan</span>
                                    @elseif ($log->jenis_proses === 'pencucian')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#F5F3FF] text-[#7C3AED]">Pencucian</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#FEE2E2] text-[#991B1B]">Pengecatan</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                    {{ $log->tanggal_aktivitas?->format('Y-m-d') }}
                                </td>
                                <td class="py-2.5 px-4 text-center font-semibold text-[#0F172A] whitespace-nowrap tabular-nums">
                                    {{ number_format($log->jumlah_diproses, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 text-center font-semibold text-rose-600 whitespace-nowrap tabular-nums">
                                    {{ number_format($log->jumlah_afkir, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 text-center font-semibold text-[#10B981] whitespace-nowrap tabular-nums">
                                    {{ number_format($lolos, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap">
                                    {{ $log->pelapor_gudang ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-xs text-[#64748B]">
                                    Tidak ada catatan proses pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($processLogs && $processLogs->hasPages())
                <div class="px-4 py-3 border-t border-[#E2E8F0] bg-white">
                    {{ $processLogs->links() }}
                </div>
            @endif

        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                        <tr>
                            <th class="py-3 px-4 whitespace-nowrap">No. Kunjungan</th>
                            <th class="py-3 px-4 whitespace-nowrap">No. Polisi</th>
                            <th class="py-3 px-4 whitespace-nowrap">Pengemudi</th>
                            <th class="py-3 px-4 whitespace-nowrap">Asal / Tujuan</th>
                            <th class="py-3 px-4 whitespace-nowrap">Waktu Masuk</th>
                            <th class="py-3 px-4 whitespace-nowrap">Waktu Keluar</th>
                            <th class="py-3 px-4 whitespace-nowrap">Surat Jalan B3</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($visits as $v)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                    {{ $v->nomor_kunjungan }}
                                </td>
                                <td class="py-2.5 px-4 font-mono font-semibold text-[#2563EB] whitespace-nowrap">
                                    {{ $v->nomor_polisi }}
                                </td>
                                <td class="py-2.5 px-4 text-[#0F172A] whitespace-nowrap">
                                    {{ $v->nama_pengemudi }}
                                </td>
                                <td class="py-2.5 px-4 text-[#64748B] max-w-[180px] truncate">
                                    {{ $v->asal_tujuan ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                    {{ $v->waktu_masuk?->format('Y-m-d H:i') }}
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap tabular-nums">
                                    @if ($v->waktu_keluar)
                                        <span class="text-[#166534] font-medium">{{ $v->waktu_keluar->format('Y-m-d H:i') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-[#FEF3C7] text-[#92400E] text-[11px] font-medium">Aktif</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    @php $repB3 = strtolower($v->status_pemeriksaan_surat_jalan_b3 ?? ''); @endphp
                                    @if ($repB3 === 'sesuai' || $repB3 === 'lolos')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">Sesuai B3</span>
                                    @elseif ($repB3 === 'tidak_sesuai' || $repB3 === 'tidak sesuai')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#FEE2E2] text-[#991B1B]">Tidak Sesuai</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">Non-B3</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-xs text-[#64748B]">
                                    Tidak ada data kunjungan kendaraan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($visits && $visits->hasPages())
                <div class="px-4 py-3 border-t border-[#E2E8F0] bg-white">
                    {{ $visits->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
