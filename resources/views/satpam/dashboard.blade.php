<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Pos Gerbang Utama — Catatan Keluar Masuk Kendaraan
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    PT Pulo Kencana Drum Industri — Shift Jaga: <span class="font-semibold text-emerald-700">{{ auth()->user()->shift_jaga ?? 'Pagi' }}</span>
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                Peran: Satpam Gerbang
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Banner Petunjuk Pos Satpam & Tombol Pintas -->
            <div class="bg-gradient-to-r from-emerald-900 to-teal-900 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold">Halo, {{ auth()->user()->name }} (Pos Gerbang)</h3>
                    <p class="text-emerald-200 text-sm mt-1 max-w-xl">
                        Catat kendaraan masuk dan lengkapi jam keluar saat armada meninggalkan pabrik. Pastikan pemeriksaan fisik dokumen surat jalan kemasan limbah B3 diperiksa dan diverifikasi.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a 
                        href="{{ route('satpam.kendaraan-masuk') }}" 
                        wire:navigate
                        class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-xs shadow transition inline-flex items-center gap-1.5"
                    >
                        <span>➕ Catat Truk Masuk</span>
                    </a>
                    <a 
                        href="{{ route('satpam.kendaraan-aktif') }}" 
                        wire:navigate
                        class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition inline-flex items-center gap-1.5"
                    >
                        <span>📋 Daftar Truk Aktif</span>
                    </a>
                </div>
            </div>

            <!-- Kartu Pantau Gerbang Real-Time -->
            @php
                $trukAktifCount = \App\Models\VehicleVisit::aktif()->count();
                $trukMasukHariIniCount = \App\Models\VehicleVisit::whereDate('waktu_masuk', today())->count();
                $dokumenB3SesuaiCount = \App\Models\VehicleVisit::where('status_pemeriksaan_surat_jalan_b3', 'sesuai')->whereDate('waktu_masuk', today())->count();
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Truk Aktif Belum Keluar -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kendaraan Aktif (Di Dalam)</p>
                        <h4 class="text-2xl font-bold text-amber-600 mt-1">{{ $trukAktifCount }} Truk</h4>
                        <span class="text-xs text-slate-500">Belum memiliki jam keluar</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                        ⏳
                    </div>
                </div>

                <!-- Total Truk Masuk Hari Ini -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kunjungan Hari Ini</p>
                        <h4 class="text-2xl font-bold text-blue-600 mt-1">{{ $trukMasukHariIniCount }} Kunjungan</h4>
                        <span class="text-xs text-slate-500">Total armada tercatat</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        🚛
                    </div>
                </div>

                <!-- Verifikasi Dokumen B3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dokumen B3 Sesuai Hari Ini</p>
                        <h4 class="text-2xl font-bold text-emerald-600 mt-1">{{ $dokumenB3SesuaiCount }} Dokumen</h4>
                        <span class="text-xs text-slate-500">Surat jalan terverifikasi</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        📋
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
