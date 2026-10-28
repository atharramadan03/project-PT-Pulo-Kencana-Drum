<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Rekapitulasi Laporan & Ekspor Data
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    PT Pulo Kencana Drum Industri — Ringkasan Administrasi Operasional, Audit Proses Gudang & Rekap Ekspor
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tombol Ekspor PDF Resmi -->
                <a 
                    href="{{ route('admin.reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                    target="_blank"
                    class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-2"
                >
                    <span>📄 Unduh Laporan PDF</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Panel Filter Periode & Ekspor Spreadsheet -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase">Periode:</span>
                        <input 
                            wire:model.live="startDate" 
                            type="date" 
                            class="rounded-xl border-slate-200 text-xs font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500 py-1.5"
                        />
                        <span class="text-xs text-slate-400">s/d</span>
                        <input 
                            wire:model.live="endDate" 
                            type="date" 
                            class="rounded-xl border-slate-200 text-xs font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500 py-1.5"
                        />
                    </div>
                </div>

                <!-- Tombol Ekspor Excel per Kategori -->
                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <span class="text-xs font-bold text-slate-500 uppercase mr-1">Ekspor Excel:</span>
                    <a 
                        href="{{ route('admin.reports.export-batch', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                        class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-xs font-semibold transition inline-flex items-center gap-1.5"
                    >
                        <span>📊 Batch Drum</span>
                    </a>
                    <a 
                        href="{{ route('admin.reports.export-process', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                        class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-xs font-semibold transition inline-flex items-center gap-1.5"
                    >
                        <span>📊 4 Log Proses</span>
                    </a>
                    <a 
                        href="{{ route('admin.reports.export-vehicle', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                        class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-xs font-semibold transition inline-flex items-center gap-1.5"
                    >
                        <span>📊 Kunjungan Armada</span>
                    </a>
                </div>
            </div>

            <!-- Ringkasan KPI Eksekutif Periode Terpilih -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Kartu 1: Batch & Drum Masuk -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Penerimaan Drum</p>
                            <h4 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalDrumDiterima) }} <span class="text-sm font-normal text-slate-500">Drum</span></h4>
                            <p class="text-xs text-blue-600 font-semibold mt-0.5">{{ $totalBatches }} Batch Penerimaan</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">📦</div>
                    </div>
                </div>

                <!-- Kartu 2: Masa Pengendapan -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Masa Pengendapan</p>
                            <h4 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($drumTransit) }} <span class="text-sm font-normal text-slate-500">Drum</span></h4>
                            <p class="text-xs text-amber-600 font-semibold mt-0.5">Transit & Mengendap</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">⏳</div>
                    </div>
                </div>

                <!-- Kartu 3: 4 Tahap Proses Gudang -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">4 Tahap Proses</p>
                            <h4 class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($drumSortir + $drumTiup + $drumCuci + $drumCat) }} <span class="text-sm font-normal text-slate-500">Pcs</span></h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Sortir: {{ $drumSortir }} | Cuci: {{ $drumCuci }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">⚙️</div>
                    </div>
                </div>

                <!-- Kartu 4: Afkir & Kunjungan Truk -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Afkir & Arus Truk</p>
                            <h4 class="text-2xl font-black text-red-600 mt-1">{{ number_format($totalAfkir) }} <span class="text-sm font-normal text-slate-500">Afkir</span></h4>
                            <p class="text-xs text-purple-600 font-semibold mt-0.5">{{ $totalKunjungan }} Truk ({{ $kunjunganB3 }} Limbah B3)</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">🚛</div>
                    </div>
                </div>
            </div>

            <!-- TAB NAVIGASI RINCIAN LAPORAN -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Header Tab Nav -->
                <div class="border-b border-slate-200 px-6 pt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex space-x-6">
                        <button 
                            wire:click="setTab('batch')" 
                            class="pb-3 text-sm font-bold border-b-2 transition flex items-center gap-2 {{ $activeTab === 'batch' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}"
                        >
                            <span>📦 1. Batch & Transit Drum</span>
                        </button>
                        <button 
                            wire:click="setTab('proses')" 
                            class="pb-3 text-sm font-bold border-b-2 transition flex items-center gap-2 {{ $activeTab === 'proses' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}"
                        >
                            <span>⚙️ 2. Log 4 Proses Gudang</span>
                        </button>
                        <button 
                            wire:click="setTab('kendaraan')" 
                            class="pb-3 text-sm font-bold border-b-2 transition flex items-center gap-2 {{ $activeTab === 'kendaraan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}"
                        >
                            <span>🚛 3. Kunjungan Armada Pos Gerbang</span>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="pb-3 w-full sm:w-64">
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="🔍 Cari di tabel ini..." 
                            class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-xs text-slate-800 py-1.5"
                        />
                    </div>
                </div>

                <!-- KONTEN TAB 1: BATCH DRUM -->
                @if ($activeTab === 'batch')
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                                    <th class="p-4">Kode Batch</th>
                                    <th class="p-4">Tgl Terima</th>
                                    <th class="p-4">Kategori Jenis</th>
                                    <th class="p-4 text-center">Jumlah Diterima</th>
                                    <th class="p-4">Status Pengendapan</th>
                                    <th class="p-4">Status Proses</th>
                                    <th class="p-4">Sumber Laporan</th>
                                    <th class="p-4">Diinput Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse ($batches as $b)
                                    <tr class="hover:bg-slate-50/75 transition">
                                        <td class="p-4 font-mono font-bold text-blue-700">{{ $b->kode_batch }}</td>
                                        <td class="p-4 text-slate-600">{{ $b->tanggal_penerimaan?->format('d/m/Y') }}</td>
                                        <td class="p-4 font-medium text-slate-800">{{ $b->kategori_jenis }}</td>
                                        <td class="p-4 text-center font-bold text-slate-900">{{ number_format($b->jumlah_diterima) }} pcs</td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $b->status_pengendapan === 'Transit' ? 'bg-amber-100 text-amber-800' : ($b->status_pengendapan === 'Mengendap' ? 'bg-orange-100 text-orange-800' : 'bg-emerald-100 text-emerald-800') }}">
                                                {{ $b->status_pengendapan }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $b->status_proses === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : ($b->status_proses === 'Dalam Proses' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700') }}">
                                                {{ $b->status_proses }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-slate-500 text-xs">{{ $b->sumber_laporan ?? '-' }}</td>
                                        <td class="p-4 text-slate-500 text-xs">{{ $b->user?->name ?? 'Admin' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-8 text-center text-slate-400">
                                            Tidak ada batch drum dalam periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($batches && $batches->hasPages())
                        <div class="p-4 border-t border-slate-200">
                            {{ $batches->links() }}
                        </div>
                    @endif

                <!-- KONTEN TAB 2: LOG 4 PROSES GUDANG -->
                @elseif ($activeTab === 'proses')
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                                    <th class="p-4">Batch</th>
                                    <th class="p-4">Tahap Proses</th>
                                    <th class="p-4">Tgl Lapangan (Gudang)</th>
                                    <th class="p-4">Tgl Input (Sistem)</th>
                                    <th class="p-4 text-center">Diproses</th>
                                    <th class="p-4 text-center">Afkir</th>
                                    <th class="p-4 text-center">Lolos</th>
                                    <th class="p-4">Pelapor & Catatan</th>
                                    <th class="p-4">Admin Pencatat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse ($processLogs as $log)
                                    @php $lolos = max(0, $log->jumlah_diproses - $log->jumlah_afkir); @endphp
                                    <tr class="hover:bg-slate-50/75 transition">
                                        <td class="p-4 font-mono font-bold text-blue-700">{{ $log->drumBatch?->kode_batch ?? '-' }}</td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                {{ $log->label_proses }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-slate-800 font-medium">{{ $log->tanggal_aktivitas?->format('d/m/Y') }}</td>
                                        <td class="p-4 text-slate-500 text-xs">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                                        <td class="p-4 text-center font-bold text-slate-800">{{ number_format($log->jumlah_diproses) }}</td>
                                        <td class="p-4 text-center font-bold text-red-600">{{ number_format($log->jumlah_afkir) }}</td>
                                        <td class="p-4 text-center font-bold text-emerald-600">{{ number_format($lolos) }}</td>
                                        <td class="p-4">
                                            <div class="font-medium text-slate-800 text-xs">{{ $log->pelapor_gudang ?? 'Mandor Gudang' }}</div>
                                            @if($log->hasil_catatan)
                                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $log->hasil_catatan }}</div>
                                            @endif
                                        </td>
                                        <td class="p-4 text-slate-500 text-xs">{{ $log->user?->name ?? 'Admin' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-8 text-center text-slate-400">
                                            Tidak ada catatan proses gudang dalam periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($processLogs && $processLogs->hasPages())
                        <div class="p-4 border-t border-slate-200">
                            {{ $processLogs->links() }}
                        </div>
                    @endif

                <!-- KONTEN TAB 3: KUNJUNGAN ARMADA GERBANG -->
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                                    <th class="p-4">No Kunjungan</th>
                                    <th class="p-4">No Polisi</th>
                                    <th class="p-4">Pengemudi</th>
                                    <th class="p-4">Perusahaan / Keperluan</th>
                                    <th class="p-4">Waktu Masuk</th>
                                    <th class="p-4">Waktu Keluar</th>
                                    <th class="p-4">Surat Jalan B3</th>
                                    <th class="p-4">Petugas Satpam</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse ($visits as $v)
                                    <tr class="hover:bg-slate-50/75 transition">
                                        <td class="p-4 font-mono font-bold text-blue-700">{{ $v->nomor_kunjungan }}</td>
                                        <td class="p-4 font-semibold text-slate-900">{{ $v->nomor_polisi }}</td>
                                        <td class="p-4 text-slate-800">{{ $v->nama_pengemudi }}</td>
                                        <td class="p-4">
                                            <div class="font-medium text-slate-800 text-xs">{{ $v->asal_tujuan ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $v->keperluan }}</div>
                                        </td>
                                        <td class="p-4 text-slate-600 text-xs">{{ $v->waktu_masuk?->format('d/m/Y H:i') }}</td>
                                        <td class="p-4 text-xs">
                                            @if($v->waktu_keluar)
                                                <span class="text-emerald-700 font-medium">{{ $v->waktu_keluar->format('d/m/Y H:i') }}</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Di Area Pabrik</span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $v->status_pemeriksaan_surat_jalan_b3 === 'sesuai' ? 'bg-emerald-100 text-emerald-800' : ($v->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700') }}">
                                                {{ $v->status_pemeriksaan_surat_jalan_b3 === 'sesuai' ? 'B3: Sesuai' : ($v->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai' ? 'B3: Tidak Sesuai' : 'Bukan B3') }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-slate-500 text-xs">{{ $v->user?->name ?? 'Pos Satpam' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-8 text-center text-slate-400">
                                            Tidak ada catatan kunjungan kendaraan dalam periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($visits && $visits->hasPages())
                        <div class="p-4 border-t border-slate-200">
                            {{ $visits->links() }}
                        </div>
                    @endif
                @endif

            </div>

        </div>
    </div>
</div>
