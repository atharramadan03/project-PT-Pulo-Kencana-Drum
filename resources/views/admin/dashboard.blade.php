<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Dashboard Administrasi Operasional
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    PT Pulo Kencana Drum Industri — Portal Monitoring & Rekapitulasi Laporan Gudang
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Peran: Admin Kantor
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Banner Pengguna & Tombol Pintas -->
            <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                    <p class="text-blue-200 text-sm mt-1 max-w-xl">
                        Merekapitulasi laporan fisik gudang, memantau batch drum, 4 tahapan proses rekondisi, serta memonitor arus armada masuk.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a 
                        href="{{ route('admin.drum-batches') }}" 
                        wire:navigate
                        class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow transition inline-flex items-center gap-1.5"
                    >
                        <span>📦 Kelola Batch Drum</span>
                    </a>
                    <a 
                        href="{{ route('admin.drum-process') }}" 
                        wire:navigate
                        class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition inline-flex items-center gap-1.5"
                    >
                        <span>⚙️ Catat 4 Proses</span>
                    </a>
                    <a 
                        href="{{ route('admin.vehicles') }}" 
                        wire:navigate
                        class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition inline-flex items-center gap-1.5"
                    >
                        <span>🚛 Catatan Kendaraan</span>
                    </a>
                    <a 
                        href="{{ route('admin.reports') }}" 
                        wire:navigate
                        class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow transition inline-flex items-center gap-1.5"
                    >
                        <span>📊 Rekapitulasi & Ekspor</span>
                    </a>
                </div>
            </div>

            <!-- Perhitungan Real-Time Database -->
            @php
                $totalBatchCount = \App\Models\DrumBatch::count();
                $drumTransitCount = \App\Models\DrumBatch::whereIn('status_pengendapan', ['Transit', 'Mengendap'])->sum('jumlah_diterima');
                $prosesHariIniCount = \App\Models\DrumProcessLog::whereDate('tanggal_aktivitas', today())->count();
                $trukAktifCount = \App\Models\VehicleVisit::aktif()->count();
            @endphp

            <!-- Kartu Statistik Cepat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Batch Drum -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Batch</p>
                        <h4 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalBatchCount }} Batch</h4>
                        <span class="text-xs text-blue-600 font-medium">Tercatat di sistem</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        📦
                    </div>
                </div>

                <!-- Status Transit -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Masa Pengendapan</p>
                        <h4 class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($drumTransitCount) }} Drum</h4>
                        <span class="text-xs text-amber-500 font-medium">Transit & Mengendap</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                        ⏳
                    </div>
                </div>

                <!-- Log 4 Proses -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Proses Hari Ini</p>
                        <h4 class="text-2xl font-bold text-emerald-600 mt-1">{{ $prosesHariIniCount }} Aktivitas</h4>
                        <span class="text-xs text-emerald-500 font-medium">Sortir, Tiup, Cuci, Cat</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        ⚙️
                    </div>
                </div>

                <!-- Truk di Pabrik -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Truk di Area Pabrik</p>
                        <h4 class="text-2xl font-bold text-purple-600 mt-1">{{ $trukAktifCount }} Truk</h4>
                        <span class="text-xs text-purple-500 font-medium">Laporan Pos Gerbang</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                        🚛
                    </div>
                </div>
            </div>

            <!-- Catatan Audit PRD -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-600 flex items-start gap-3">
                <span class="text-base">📌</span>
                <p>
                    <strong>Pedoman Audit MPPL:</strong> Penginputan data proses dilakukan oleh Admin Kantor berdasarkan laporan nota fisik orang gudang dengan pemisahan tegas antara <em>Tanggal Aktivitas Lapangan</em> dan <em>Tanggal Input Sistem</em>.
                </p>
            </div>

        </div>
    </div>

</x-app-layout>
