<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                    Dashboard
                    <span class="sr-only">Pos Gerbang Utama</span>
                </h1>
                <p class="text-xs text-[#64748B] mt-0.5">
                    Ringkasan arus keluar masuk kendaraan hari ini
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a 
                    href="{{ route('satpam.kendaraan-masuk') }}" 
                    wire:navigate
                    class="px-3.5 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Catat Truk Masuk</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $trukAktifCount = \App\Models\VehicleVisit::aktif()->count();
        $trukMasukHariIniCount = \App\Models\VehicleVisit::whereDate('waktu_masuk', today())->count();
        $dokumenB3SesuaiCount = \App\Models\VehicleVisit::where('status_pemeriksaan_surat_jalan_b3', 'sesuai')
            ->whereDate('waktu_masuk', today())
            ->count();
        $daftarTrukAktif = \App\Models\VehicleVisit::aktif()
            ->latest('waktu_masuk')
            ->take(5)
            ->get();
    @endphp

    <div class="space-y-4">
        <!-- 1. 3 KPI Cards (Horizontal Compact Style per Reference) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-full bg-amber-50 text-[#F59E0B] flex items-center justify-center shrink-0">
                        <x-icon name="clock" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs text-[#64748B] font-medium">Kendaraan Aktif</p>
                        <h3 class="text-xl font-bold text-[#0F172A] tabular-nums leading-tight mt-0.5">{{ $trukAktifCount }}</h3>
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-[#64748B]">
                    Sedang berada di area pabrik
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-[#2563EB] flex items-center justify-center shrink-0">
                        <x-icon name="truck" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs text-[#64748B] font-medium">Kunjungan Hari Ini</p>
                        <h3 class="text-xl font-bold text-[#0F172A] tabular-nums leading-tight mt-0.5">{{ $trukMasukHariIniCount }}</h3>
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-[#64748B]">
                    Total kendaraan masuk hari ini
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-[#10B981] flex items-center justify-center shrink-0">
                        <x-icon name="shield-check" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs text-[#64748B] font-medium">Surat Jalan B3 Sesuai</p>
                        <h3 class="text-xl font-bold text-[#0F172A] tabular-nums leading-tight mt-0.5">{{ $dokumenB3SesuaiCount }}</h3>
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-[#64748B]">
                    Terverifikasi hari ini
                </div>
            </div>
        </div>

        <!-- 2. Tabel Kendaraan Aktif -->
        <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
            <div class="p-4 sm:px-5 border-b border-[#E2E8F0] flex items-center justify-between">
                <h2 class="text-sm font-bold text-[#0F172A]">Kendaraan Aktif di Area Pabrik</h2>
                <a 
                    href="{{ route('satpam.kendaraan-aktif') }}" 
                    wire:navigate
                    class="text-xs font-medium text-[#2563EB] hover:text-[#1D4ED8] inline-flex items-center gap-1"
                >
                    <span>Lihat Semua</span>
                    <x-icon name="arrow-right" class="w-3 h-3" />
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                        <tr>
                            <th class="py-2.5 px-4 whitespace-nowrap">No. Kunjungan</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">No. Polisi</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">Pengemudi</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">Asal / Tujuan</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">Jam Masuk</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">Surat Jalan B3</th>
                            <th class="py-2.5 px-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($daftarTrukAktif as $visit)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                    {{ $visit->nomor_kunjungan }}
                                </td>
                                <td class="py-2.5 px-4 font-mono font-semibold text-[#2563EB] whitespace-nowrap">
                                    {{ $visit->nomor_polisi }}
                                </td>
                                <td class="py-2.5 px-4 text-[#0F172A] whitespace-nowrap">
                                    {{ $visit->nama_pengemudi }}
                                </td>
                                <td class="py-2.5 px-4 text-[#64748B] max-w-[180px] truncate">
                                    {{ $visit->asal_tujuan }}
                                </td>
                                <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                    {{ $visit->waktu_masuk?->format('H:i') }} WIB
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    @if ($visit->status_pemeriksaan_surat_jalan_b3 === 'sesuai')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                            Sesuai B3
                                        </span>
                                    @elseif ($visit->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#FEE2E2] text-[#991B1B]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span>
                                            Tidak Sesuai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                            Non-B3
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                    <a 
                                        href="{{ route('satpam.kendaraan-aktif') }}" 
                                        wire:navigate
                                        class="px-2.5 py-1 rounded-lg bg-[#F59E0B] hover:bg-amber-600 text-white font-medium text-xs inline-flex items-center gap-1 transition"
                                    >
                                        <span>Catat Keluar</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-xs text-[#64748B]">
                                    Tidak ada kendaraan aktif di area pabrik saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
