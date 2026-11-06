<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                    Dashboard
                    <span class="sr-only">Dashboard Administrasi Operasional</span>
                </h1>
                <p class="text-xs text-[#64748B] mt-0.5">
                    Ringkasan kegiatan operasional hari ini
                </p>
            </div>
            <div class="inline-flex items-center gap-1.5 text-xs text-[#64748B] bg-white border border-[#E2E8F0] px-3 py-1.5 rounded-lg">
                <x-icon name="calendar" class="w-3.5 h-3.5 text-[#64748B]" />
                <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </x-slot>

    @php
        $totalBatchCount = \App\Models\DrumBatch::count();
        $totalDrumDiterima = (int) \App\Models\DrumBatch::sum('jumlah_diterima');
        $drumDiprosesCount = (int) \App\Models\DrumBatch::where('status_proses', 'Dalam Proses')->sum('jumlah_diterima');
        $drumSelesaiCount = (int) \App\Models\DrumBatch::where('status_proses', 'Selesai')
            ->where('status_pengendapan', 'Selesai')
            ->sum('jumlah_diterima');
        $drumTransitCount = (int) \App\Models\DrumBatch::whereIn('status_pengendapan', ['Transit', 'Mengendap'])->sum('jumlah_diterima');

        $sortirCount = (int) \App\Models\DrumProcessLog::where('jenis_proses', 'penyortiran')->sum('jumlah_diproses');
        $tiupCount = (int) \App\Models\DrumProcessLog::where('jenis_proses', 'peniupan')->sum('jumlah_diproses');
        $cuciCount = (int) \App\Models\DrumProcessLog::where('jenis_proses', 'pencucian')->sum('jumlah_diproses');
        $catCount = (int) \App\Models\DrumProcessLog::where('jenis_proses', 'pengecatan')->sum('jumlah_diproses');
        $totalAfkirCount = (int) \App\Models\DrumProcessLog::sum('jumlah_afkir');

        $latestBatches = \App\Models\DrumBatch::latest('updated_at')->latest('id')->take(5)->get();
        $latestBatchUpdates = \App\Models\DrumBatch::with('user')->latest('updated_at')->take(3)->get();
        $latestLogs = \App\Models\DrumProcessLog::with('drumBatch')->latest('id')->take(3)->get();

        $maxProcess = max(1, $sortirCount, $tiupCount, $cuciCount, $catCount);

        // Data 8 Hari Terakhir Real-Time dari Database untuk Grafik Operasional Gudang
        $chartDates = collect(range(7, 0))->map(fn ($i) => now()->subDays($i));
        $rawDailyLogs = \App\Models\DrumProcessLog::whereDate('tanggal_aktivitas', '>=', now()->subDays(7)->toDateString())
            ->selectRaw('DATE(tanggal_aktivitas) as tgl, jenis_proses, SUM(jumlah_diproses) as total')
            ->groupBy('tgl', 'jenis_proses')
            ->get();

        $seriesSortir = [];
        $seriesTiup = [];
        $seriesCuci = [];
        $seriesCat = [];
        $xLabels = [];
        $fullDateLabels = [];

        foreach ($chartDates as $dateObj) {
            $dateStr = $dateObj->toDateString();
            $xLabels[] = $dateObj->locale('id')->isoFormat('D MMM');
            $fullDateLabels[] = $dateObj->locale('id')->isoFormat('dddd, D MMMM Y');

            $seriesSortir[] = (int) $rawDailyLogs->where('tgl', $dateStr)->where('jenis_proses', 'penyortiran')->sum('total');
            $seriesTiup[]   = (int) $rawDailyLogs->where('tgl', $dateStr)->where('jenis_proses', 'peniupan')->sum('total');
            $seriesCuci[]   = (int) $rawDailyLogs->where('tgl', $dateStr)->where('jenis_proses', 'pencucian')->sum('total');
            $seriesCat[]    = (int) $rawDailyLogs->where('tgl', $dateStr)->where('jenis_proses', 'pengecatan')->sum('total');
        }

        $peakVal = max(100, max($seriesSortir), max($seriesTiup), max($seriesCuci), max($seriesCat));
        $yMax = (int) (ceil(($peakVal * 1.15) / 50) * 50);

        // Helper pembuat path SVG Smooth Cubic Bezier (dengan sedikit offset visual bila nilai sama persis agar garis tidak saling menutupi)
        $buildSmoothChart = function (array $values, int $maxVal, float $visualOffsetPx = 0.0) {
            $leftX = 42;
            $rightX = 620;
            $topY = 18;
            $bottomY = 176;
            $count = count($values);
            $stepX = ($rightX - $leftX) / max(1, $count - 1);

            $pts = [];
            foreach ($values as $i => $val) {
                $x = round($leftX + ($i * $stepX), 1);
                $ratio = $maxVal > 0 ? min(1, max(0, $val / $maxVal)) : 0;
                $rawY = $bottomY - ($ratio * ($bottomY - $topY));
                $y = round($val > 0 ? $rawY + $visualOffsetPx : $bottomY, 1);
                $pts[] = ['x' => $x, 'y' => $y, 'val' => $val];
            }

            $path = 'M '.$pts[0]['x'].','.$pts[0]['y'];
            for ($i = 0; $i < $count - 1; $i++) {
                $p0 = $pts[$i];
                $p1 = $pts[$i + 1];
                $midX = round(($p0['x'] + $p1['x']) / 2, 1);
                $path .= ' C '.$midX.','.$p0['y'].' '.$midX.','.$p1['y'].' '.$p1['x'].','.$p1['y'];
            }

            $area = $path.' L '.$pts[$count - 1]['x'].','.$bottomY.' L '.$pts[0]['x'].','.$bottomY.' Z';

            return ['line' => $path, 'area' => $area, 'points' => $pts];
        };

        $chartSortir = $buildSmoothChart($seriesSortir, $yMax, -3.0);
        $chartTiup   = $buildSmoothChart($seriesTiup, $yMax, -1.0);
        $chartCat    = $buildSmoothChart($seriesCat, $yMax, 1.0);
        $chartCuci   = $buildSmoothChart($seriesCuci, $yMax, 3.0);

        $trackerData = [];
        foreach ($xLabels as $idx => $shortLabel) {
            $trackerData[] = [
                'shortDate' => $shortLabel,
                'fullDate' => $fullDateLabels[$idx],
                'x' => $chartSortir['points'][$idx]['x'],
                'pctX' => round((($chartSortir['points'][$idx]['x'] - 42) / (620 - 42)) * 100, 1),
                'sortir' => $seriesSortir[$idx],
                'tiup' => $seriesTiup[$idx],
                'cuci' => $seriesCuci[$idx],
                'cat' => $seriesCat[$idx],
                'total' => $seriesSortir[$idx] + $seriesTiup[$idx] + $seriesCuci[$idx] + $seriesCat[$idx],
            ];
        }

        $pctSelesai = $totalDrumDiterima > 0 ? round(($drumSelesaiCount / $totalDrumDiterima) * 100) : 0;
        $pctDiproses = $totalDrumDiterima > 0 ? round(($drumDiprosesCount / $totalDrumDiterima) * 100) : 0;
        $pctTransit = $totalDrumDiterima > 0 ? max(0, 100 - $pctSelesai - $pctDiproses) : 0;

        // Analisis tren mingguan (4 hari terakhir vs 4 hari sebelumnya)
        $firstHalfTotal = array_sum(array_slice($seriesSortir, 0, 4)) + array_sum(array_slice($seriesTiup, 0, 4)) + array_sum(array_slice($seriesCuci, 0, 4)) + array_sum(array_slice($seriesCat, 0, 4));
        $secondHalfTotal = array_sum(array_slice($seriesSortir, 4)) + array_sum(array_slice($seriesTiup, 4)) + array_sum(array_slice($seriesCuci, 4)) + array_sum(array_slice($seriesCat, 4));
        $diffTotal = $secondHalfTotal - $firstHalfTotal;
        $trendPct = $firstHalfTotal > 0 ? round(abs($diffTotal / $firstHalfTotal) * 100, 1) : ($secondHalfTotal > 0 ? 100 : 0);
        $isTrendUp = $diffTotal >= 0;
    @endphp

    <div class="space-y-4">
        <!-- 1. 4 KPI Cards (Clean Icon Without Colored Circle + Animated Number) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Drum -->
            <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs text-[#64748B] font-medium truncate">Total Drum</p>
                    <x-icon name="package" class="w-4 h-4 text-[#2563EB] shrink-0" />
                </div>
                <h3 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5">
                    <x-animated-number :value="$totalDrumDiterima" />
                </h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="text-[#10B981] font-semibold">{{ $totalBatchCount }} Batch</span>
                    <span class="text-[#64748B]">Terdaftar di gudang</span>
                </div>
            </div>

            <!-- Drum Diproses -->
            <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs text-[#64748B] font-medium truncate">Drum Diproses</p>
                    <x-icon name="layers" class="w-4 h-4 text-[#3B82F6] shrink-0" />
                </div>
                <h3 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5">
                    <x-animated-number :value="$drumDiprosesCount" />
                </h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="text-[#2563EB] font-semibold">{{ number_format($drumTransitCount, 0, ',', '.') }} Transit</span>
                    <span class="text-[#64748B]">Masa pengendapan</span>
                </div>
            </div>

            <!-- Drum Selesai -->
            <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs text-[#64748B] font-medium truncate">Drum Selesai</p>
                    <x-icon name="circle-check" class="w-4 h-4 text-[#10B981] shrink-0" />
                </div>
                <h3 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5">
                    <x-animated-number :value="$drumSelesaiCount" />
                </h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="text-rose-600 font-semibold">{{ number_format($totalAfkirCount, 0, ',', '.') }} Reject</span>
                    <span class="text-[#64748B]">Siap distribusi</span>
                </div>
            </div>

            <!-- Total Drum Afkir / Reject -->
            <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs text-[#64748B] font-medium truncate">Total Drum Afkir</p>
                    <x-icon name="alert-triangle" class="w-4 h-4 text-rose-500 shrink-0" />
                </div>
                <h3 class="text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5">
                    <x-animated-number :value="$totalAfkirCount" />
                </h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="text-rose-600 font-semibold">{{ $totalDrumDiterima > 0 ? round(($totalAfkirCount / $totalDrumDiterima) * 100, 1) : 0 }}% Tingkat Afkir</span>
                    <span class="text-[#64748B]">Sortir & rekondisi</span>
                </div>
            </div>
        </div>

        <!-- 2. Middle Row: Grafik Operasional Gudang (Area Spline Chart) + Aktivitas Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Grafik Operasional Gudang (Interactive Gradient Area Spline Chart) -->
            <div 
                x-data="{
                    activeIdx: null,
                    showSortir: true,
                    showTiup: true,
                    showCuci: true,
                    showCat: true,
                    days: @js($trackerData)
                }"
                class="lg:col-span-2 bg-white p-4 sm:p-5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col justify-between"
            >
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <div>
                        <h2 class="text-sm font-bold text-[#0F172A]">Grafik Operasional Gudang</h2>
                        <p class="text-[11px] text-[#64748B]">Ringkasan pemrosesan drum 8 hari terakhir</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-[#64748B]">
                        <button type="button" @click="showSortir = !showSortir" :class="showSortir ? 'opacity-100 font-medium text-[#0F172A]' : 'opacity-40'" class="inline-flex items-center gap-1.5 transition cursor-pointer">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#2563EB]"></span>
                            <span>Penyortiran</span>
                        </button>
                        <button type="button" @click="showTiup = !showTiup" :class="showTiup ? 'opacity-100 font-medium text-[#0F172A]' : 'opacity-40'" class="inline-flex items-center gap-1.5 transition cursor-pointer">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span>
                            <span>Peniupan</span>
                        </button>
                        <button type="button" @click="showCuci = !showCuci" :class="showCuci ? 'opacity-100 font-medium text-[#0F172A]' : 'opacity-40'" class="inline-flex items-center gap-1.5 transition cursor-pointer">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#7C3AED]"></span>
                            <span>Pencucian</span>
                        </button>
                        <button type="button" @click="showCat = !showCat" :class="showCat ? 'opacity-100 font-medium text-[#0F172A]' : 'opacity-40'" class="inline-flex items-center gap-1.5 transition cursor-pointer">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span>
                            <span>Pengecatan</span>
                        </button>
                    </div>
                </div>

                <!-- Interactive SVG Area Spline Chart with Gradient Bloom & Path Draw -->
                <div class="relative w-full pt-1 select-none" @mouseleave="activeIdx = null">
                    <!-- Floating Tooltip Tracker Card -->
                    <div 
                        x-show="activeIdx !== null" 
                        x-cloak
                        x-transition.opacity.duration.100ms
                        :style="activeIdx !== null ? (activeIdx > 4 ? 'right: ' + (100 - days[activeIdx].pctX + 4) + '%; left: auto;' : 'left: ' + Math.max(8, days[activeIdx].pctX + 4) + '%;') : ''"
                        class="pointer-events-none absolute top-2 z-20 w-48 rounded-xl bg-[#0F172A]/95 text-white p-3 shadow-xl border border-slate-700 text-xs backdrop-blur-xs"
                    >
                        <template x-if="activeIdx !== null">
                            <div>
                                <div class="font-semibold text-[11px] text-slate-300 border-b border-slate-700/80 pb-1.5 mb-2" x-text="days[activeIdx].fullDate"></div>
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between" x-show="showSortir">
                                        <span class="inline-flex items-center gap-1.5 text-slate-300">
                                            <span class="w-2 h-2 rounded-full bg-[#3B82F6]"></span>
                                            Penyortiran
                                        </span>
                                        <span class="font-bold tabular-nums" x-text="days[activeIdx].sortir + ' Drum'"></span>
                                    </div>
                                    <div class="flex items-center justify-between" x-show="showTiup">
                                        <span class="inline-flex items-center gap-1.5 text-slate-300">
                                            <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                                            Peniupan
                                        </span>
                                        <span class="font-bold tabular-nums" x-text="days[activeIdx].tiup + ' Drum'"></span>
                                    </div>
                                    <div class="flex items-center justify-between" x-show="showCuci">
                                        <span class="inline-flex items-center gap-1.5 text-slate-300">
                                            <span class="w-2 h-2 rounded-full bg-[#A855F7]"></span>
                                            Pencucian
                                        </span>
                                        <span class="font-bold tabular-nums" x-text="days[activeIdx].cuci + ' Drum'"></span>
                                    </div>
                                    <div class="flex items-center justify-between" x-show="showCat">
                                        <span class="inline-flex items-center gap-1.5 text-slate-300">
                                            <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                                            Pengecatan
                                        </span>
                                        <span class="font-bold tabular-nums" x-text="days[activeIdx].cat + ' Drum'"></span>
                                    </div>
                                </div>
                                <div class="mt-2 pt-1.5 border-t border-slate-700/80 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">Total Aktivitas</span>
                                    <span class="font-bold text-sky-400 tabular-nums" x-text="days[activeIdx].total + ' Drum'"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <svg viewBox="0 0 640 205" class="w-full h-52 overflow-visible">
                        <defs>
                            <linearGradient id="gradSortir" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#2563EB" stop-opacity="0.32" />
                                <stop offset="100%" stop-color="#2563EB" stop-opacity="0.0" />
                            </linearGradient>
                            <linearGradient id="gradTiup" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#F59E0B" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#F59E0B" stop-opacity="0.0" />
                            </linearGradient>
                            <linearGradient id="gradCuci" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#7C3AED" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#7C3AED" stop-opacity="0.0" />
                            </linearGradient>
                            <linearGradient id="gradCat" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#10B981" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#10B981" stop-opacity="0.0" />
                            </linearGradient>
                        </defs>

                        <!-- Y-Axis Labels & Horizontal Grid Lines -->
                        @foreach ([1, 0.75, 0.5, 0.25, 0] as $ratio)
                            @php
                                $yPos = round(176 - ($ratio * 158), 1);
                                $yLabel = (int) round($yMax * $ratio);
                            @endphp
                            <line x1="42" y1="{{ $yPos }}" x2="620" y2="{{ $yPos }}" stroke="#F1F5F9" stroke-width="1" />
                            <text x="32" y="{{ $yPos + 4 }}" text-anchor="end" class="fill-[#64748B] text-[10px] font-medium">{{ $yLabel }}</text>
                        @endforeach

                        <!-- Vertical Grid Lines & X-Axis Date Labels -->
                        @foreach ($chartSortir['points'] as $idx => $pt)
                            <line x1="{{ $pt['x'] }}" y1="18" x2="{{ $pt['x'] }}" y2="176" stroke="#F8FAFC" stroke-width="1" />
                            <text 
                                x="{{ $pt['x'] }}" 
                                y="196" 
                                text-anchor="middle" 
                                :class="activeIdx === {{ $idx }} ? 'fill-[#2563EB] font-bold' : 'fill-[#64748B] font-medium'"
                                class="text-[10px] transition-colors"
                            >{{ $xLabels[$idx] }}</text>
                        @endforeach

                        <!-- Active Vertical Crosshair Tracker Line -->
                        @foreach ($chartSortir['points'] as $idx => $pt)
                            <line 
                                x-show="activeIdx === {{ $idx }}"
                                x-cloak
                                x1="{{ $pt['x'] }}" 
                                y1="14" 
                                x2="{{ $pt['x'] }}" 
                                y2="176" 
                                stroke="#64748B" 
                                stroke-width="1.5" 
                                stroke-dasharray="4 3" 
                            />
                        @endforeach

                        <!-- Multi-stop Gradient Area Fills with Bloom Animation -->
                        <path x-show="showSortir" d="{{ $chartSortir['area'] }}" fill="url(#gradSortir)" class="area-bloom" />
                        <path x-show="showTiup" d="{{ $chartTiup['area'] }}" fill="url(#gradTiup)" class="area-bloom" />
                        <path x-show="showCuci" d="{{ $chartCuci['area'] }}" fill="url(#gradCuci)" class="area-bloom" />
                        <path x-show="showCat" d="{{ $chartCat['area'] }}" fill="url(#gradCat)" class="area-bloom" />

                        <!-- Smooth Spline Curves with Entrance Draw Animation -->
                        <path x-show="showCuci" pathLength="100" d="{{ $chartCuci['line'] }}" fill="none" stroke="#7C3AED" stroke-width="2.2" stroke-linecap="round" class="chart-spline-draw" />
                        <path x-show="showCat" pathLength="100" d="{{ $chartCat['line'] }}" fill="none" stroke="#10B981" stroke-width="2.2" stroke-linecap="round" class="chart-spline-draw" />
                        <path x-show="showTiup" pathLength="100" d="{{ $chartTiup['line'] }}" fill="none" stroke="#F59E0B" stroke-width="2.2" stroke-linecap="round" class="chart-spline-draw" />
                        <path x-show="showSortir" pathLength="100" d="{{ $chartSortir['line'] }}" fill="none" stroke="#2563EB" stroke-width="2.4" stroke-linecap="round" class="chart-spline-draw" />

                        <!-- Data Nodes: Pencucian (Purple) -->
                        @foreach ($chartCuci['points'] as $idx => $pt)
                            <circle 
                                x-show="showCuci"
                                cx="{{ $pt['x'] }}" 
                                cy="{{ $pt['y'] }}" 
                                :r="activeIdx === {{ $idx }} ? 5.5 : 3.5" 
                                fill="#7C3AED" 
                                stroke="#FFFFFF" 
                                stroke-width="1.5"
                                class="transition-all duration-100"
                            />
                        @endforeach

                        <!-- Data Nodes: Pengecatan (Emerald) -->
                        @foreach ($chartCat['points'] as $idx => $pt)
                            <circle 
                                x-show="showCat"
                                cx="{{ $pt['x'] }}" 
                                cy="{{ $pt['y'] }}" 
                                :r="activeIdx === {{ $idx }} ? 5.5 : 3.5" 
                                fill="#10B981" 
                                stroke="#FFFFFF" 
                                stroke-width="1.5"
                                class="transition-all duration-100"
                            />
                        @endforeach

                        <!-- Data Nodes: Peniupan (Amber) -->
                        @foreach ($chartTiup['points'] as $idx => $pt)
                            <circle 
                                x-show="showTiup"
                                cx="{{ $pt['x'] }}" 
                                cy="{{ $pt['y'] }}" 
                                :r="activeIdx === {{ $idx }} ? 5.5 : 3.5" 
                                fill="#F59E0B" 
                                stroke="#FFFFFF" 
                                stroke-width="1.5"
                                class="transition-all duration-100"
                            />
                        @endforeach

                        <!-- Data Nodes: Penyortiran (Blue) -->
                        @foreach ($chartSortir['points'] as $idx => $pt)
                            <circle 
                                x-show="showSortir"
                                cx="{{ $pt['x'] }}" 
                                cy="{{ $pt['y'] }}" 
                                :r="activeIdx === {{ $idx }} ? 5.5 : 3.5" 
                                fill="#2563EB" 
                                stroke="#FFFFFF" 
                                stroke-width="1.5"
                                class="transition-all duration-100"
                            />
                        @endforeach

                        <!-- Invisible Interactive Hover Columns (Captures Mouse Across Full Height of Each Day) -->
                        @foreach ($chartSortir['points'] as $idx => $pt)
                            @php
                                $colWidth = 82;
                                $rectX = max(35, round($pt['x'] - ($colWidth / 2), 1));
                            @endphp
                            <rect 
                                x="{{ $rectX }}" 
                                y="10" 
                                width="{{ $colWidth }}" 
                                height="185" 
                                fill="transparent" 
                                class="cursor-crosshair"
                                @mouseenter="activeIdx = {{ $idx }}"
                                @click="activeIdx = {{ $idx }}"
                            />
                        @endforeach
                    </svg>
                </div>
            </div>

            <!-- Aktivitas Terbaru -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-[#0F172A]">Aktivitas Terbaru</h2>
                    <a href="{{ route('admin.reports') }}" wire:navigate.hover class="text-xs font-medium text-[#2563EB] hover:text-[#1D4ED8] inline-flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <x-icon name="arrow-right" class="w-3 h-3" />
                    </a>
                </div>

                <div class="divide-y divide-slate-100 flex-1">
                    @foreach ($latestBatchUpdates as $bUpdate)
                        <div class="py-2.5 flex items-center justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <x-icon name="package" class="w-4 h-4 mt-0.5 shrink-0 text-[#F59E0B]" />
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-[#0F172A] truncate">
                                        Batch {{ $bUpdate->kode_batch }} ({{ $bUpdate->status_pengendapan }})
                                    </p>
                                    <p class="text-[11px] text-[#64748B] truncate">
                                        Status Proses: {{ $bUpdate->status_proses }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[11px] text-[#64748B] tabular-nums shrink-0">
                                {{ $bUpdate->updated_at?->format('H:i') }}
                            </span>
                        </div>
                    @endforeach

                    @foreach ($latestLogs as $log)
                        <div class="py-2.5 flex items-center justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <x-icon name="layers" class="w-4 h-4 mt-0.5 shrink-0 text-[#10B981]" />
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-[#0F172A] truncate">
                                        Proses {{ ucfirst($log->jenis_proses) }} ({{ $log->jumlah_diproses }} drum)
                                    </p>
                                    <p class="text-[11px] text-[#64748B] truncate">
                                        Oleh {{ $log->pelapor_gudang }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[11px] text-[#64748B] tabular-nums shrink-0">
                                {{ $log->tanggal_input?->format('H:i') ?? $log->created_at?->format('H:i') }}
                            </span>
                        </div>
                    @endforeach

                    @if ($latestBatchUpdates->isEmpty() && $latestLogs->isEmpty())
                        <div class="py-8 text-center text-xs text-[#64748B]">
                            Belum ada aktivitas tercatat hari ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Bottom Row: Data Drum Terbaru (2 cols) + Status Drum (1 col) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Data Drum Terbaru -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
                <div class="p-4 sm:px-5 border-b border-[#E2E8F0] flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[#0F172A]">Data Drum Terbaru</h2>
                    <a href="{{ route('admin.drum-batches') }}" wire:navigate.hover class="text-xs font-medium text-[#2563EB] hover:text-[#1D4ED8] inline-flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <x-icon name="arrow-right" class="w-3 h-3" />
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                            <tr>
                                <th class="py-2.5 px-4 whitespace-nowrap">No. Batch</th>
                                <th class="py-2.5 px-4 whitespace-nowrap">Jenis</th>
                                <th class="py-2.5 px-4 whitespace-nowrap">Pengendapan</th>
                                <th class="py-2.5 px-4 whitespace-nowrap">Status Proses</th>
                                <th class="py-2.5 px-4 whitespace-nowrap">Tanggal Masuk</th>
                                <th class="py-2.5 px-4 whitespace-nowrap text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($latestBatches as $batch)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                        {{ $batch->kode_batch }}
                                    </td>
                                    <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap">
                                        {{ $batch->kategori_jenis }}
                                    </td>
                                    <td class="py-2.5 px-4 whitespace-nowrap">
                                        @if ($batch->status_pengendapan === 'Transit')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Transit
                                            </span>
                                        @elseif ($batch->status_pengendapan === 'Mengendap')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#FEF3C7] text-[#92400E]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                                                Mengendap
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                                Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 whitespace-nowrap">
                                        @if ($batch->status_proses === 'Selesai')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                                Selesai
                                            </span>
                                        @elseif ($batch->status_proses === 'Dalam Proses')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-[#2563EB]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                                                Diproses
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                                Diterima
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                        {{ $batch->tanggal_penerimaan?->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-semibold text-[#0F172A] whitespace-nowrap tabular-nums">
                                        {{ number_format($batch->jumlah_diterima, 0, ',', '.') }} Unit
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-xs text-[#64748B]">
                                        Belum ada data batch drum.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Status Drum (Animated Donut Ring Card) -->
            <div 
                x-data="{
                    loaded: false,
                    activeSegment: null,
                    pctSelesai: {{ $pctSelesai }},
                    pctDiproses: {{ $pctDiproses }},
                    pctTransit: {{ $pctTransit }}
                }"
                x-init="setTimeout(() => loaded = true, 60)"
                class="bg-white p-4 sm:p-5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col justify-between"
            >
                <h2 class="text-sm font-bold text-[#0F172A] mb-3">Status Drum</h2>

                <!-- Animated SVG Donut Ring -->
                <div class="relative w-36 h-36 mx-auto my-2 flex items-center justify-center">
                    <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90 overflow-visible">
                        <!-- Background Circle Track -->
                        <circle 
                            cx="50" 
                            cy="50" 
                            r="38" 
                            stroke="#F1F5F9" 
                            stroke-width="10" 
                            fill="none" 
                        />

                        @php
                            $circ = round(2 * pi() * 38, 2); // ~ 238.76
                            $dashSelesai = round(($pctSelesai / 100) * $circ, 2);
                            $offsetSelesai = round($circ - $dashSelesai, 2);
                            
                            $dashDiproses = round(($pctDiproses / 100) * $circ, 2);
                            $offsetDiproses = round($circ - $dashDiproses, 2);
                            $rotDiproses = round(($pctSelesai / 100) * 360, 1);

                            $dashTransit = round(($pctTransit / 100) * $circ, 2);
                            $offsetTransit = round($circ - $dashTransit, 2);
                            $rotTransit = round((($pctSelesai + $pctDiproses) / 100) * 360, 1);
                        @endphp

                        @if ($pctSelesai > 0)
                            <circle 
                                cx="50" 
                                cy="50" 
                                r="38" 
                                stroke="#10B981" 
                                stroke-width="10" 
                                stroke-dasharray="{{ $circ }}" 
                                :style="loaded ? 'stroke-dashoffset: {{ $offsetSelesai }}' : 'stroke-dashoffset: {{ $circ }}'"
                                style="stroke-dashoffset: {{ $circ }}"
                                stroke-linecap="round"
                                fill="none" 
                                class="transition-all duration-1000 ease-out cursor-pointer hover:stroke-[12]"
                                :class="activeSegment === 'selesai' ? 'stroke-[12]' : ''"
                                @mouseenter="activeSegment = 'selesai'"
                                @mouseleave="activeSegment = null"
                            />
                        @endif

                        @if ($pctDiproses > 0)
                            <circle 
                                cx="50" 
                                cy="50" 
                                r="38" 
                                stroke="#2563EB" 
                                stroke-width="10" 
                                stroke-dasharray="{{ $circ }}" 
                                :style="loaded ? 'stroke-dashoffset: {{ $offsetDiproses }}' : 'stroke-dashoffset: {{ $circ }}'"
                                style="stroke-dashoffset: {{ $circ }}"
                                stroke-linecap="round"
                                fill="none" 
                                transform="rotate({{ $rotDiproses }} 50 50)"
                                class="transition-all duration-1000 ease-out cursor-pointer hover:stroke-[12]"
                                :class="activeSegment === 'diproses' ? 'stroke-[12]' : ''"
                                @mouseenter="activeSegment = 'diproses'"
                                @mouseleave="activeSegment = null"
                            />
                        @endif

                        @if ($pctTransit > 0)
                            <circle 
                                cx="50" 
                                cy="50" 
                                r="38" 
                                stroke="#F59E0B" 
                                stroke-width="10" 
                                stroke-dasharray="{{ $circ }}" 
                                :style="loaded ? 'stroke-dashoffset: {{ $offsetTransit }}' : 'stroke-dashoffset: {{ $circ }}'"
                                style="stroke-dashoffset: {{ $circ }}"
                                stroke-linecap="round"
                                fill="none" 
                                transform="rotate({{ $rotTransit }} 50 50)"
                                class="transition-all duration-1000 ease-out cursor-pointer hover:stroke-[12]"
                                :class="activeSegment === 'transit' ? 'stroke-[12]' : ''"
                                @mouseenter="activeSegment = 'transit'"
                                @mouseleave="activeSegment = null"
                            />
                        @endif
                    </svg>

                    <!-- Center Count-Up Number & Label -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
                        <x-animated-number :value="$totalDrumDiterima" class="text-lg font-bold text-[#0F172A] leading-tight" />
                        <span class="text-[10px] text-[#64748B] mt-0.5 font-medium">Total Drum</span>
                    </div>
                </div>

                <div class="space-y-2 mt-2 text-xs">
                    <div 
                        @mouseenter="activeSegment = 'selesai'" 
                        @mouseleave="activeSegment = null"
                        class="flex items-center justify-between p-1.5 rounded-lg transition-colors cursor-pointer"
                        :class="activeSegment === 'selesai' ? 'bg-emerald-50/70 font-semibold' : 'hover:bg-slate-50'"
                    >
                        <span class="inline-flex items-center gap-2 text-[#64748B]">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span>
                            <span>Selesai</span>
                        </span>
                        <span class="font-bold text-[#0F172A] tabular-nums">{{ $pctSelesai }}%</span>
                    </div>

                    <div 
                        @mouseenter="activeSegment = 'diproses'" 
                        @mouseleave="activeSegment = null"
                        class="flex items-center justify-between p-1.5 rounded-lg transition-colors cursor-pointer"
                        :class="activeSegment === 'diproses' ? 'bg-blue-50/70 font-semibold' : 'hover:bg-slate-50'"
                    >
                        <span class="inline-flex items-center gap-2 text-[#64748B]">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#2563EB]"></span>
                            <span>Diproses</span>
                        </span>
                        <span class="font-bold text-[#0F172A] tabular-nums">{{ $pctDiproses }}%</span>
                    </div>

                    <div 
                        @mouseenter="activeSegment = 'transit'" 
                        @mouseleave="activeSegment = null"
                        class="flex items-center justify-between p-1.5 rounded-lg transition-colors cursor-pointer"
                        :class="activeSegment === 'transit' ? 'bg-amber-50/70 font-semibold' : 'hover:bg-slate-50'"
                    >
                        <span class="inline-flex items-center gap-2 text-[#64748B]">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span>
                            <span>Transit & Mengendap</span>
                        </span>
                        <span class="font-bold text-[#0F172A] tabular-nums">{{ $pctTransit }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
