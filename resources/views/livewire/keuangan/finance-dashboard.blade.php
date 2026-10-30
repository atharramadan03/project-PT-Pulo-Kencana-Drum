<div class="space-y-4">
    <!-- Header Section (Matches Dashboard Kantor Style) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <div>
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Dashboard Keuangan
                <span class="sr-only">Tracker Arus Kas Stok Gudang & Biaya Armada</span>
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Tracker arus kas stok gudang dan biaya pemeliharaan armada
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="inline-flex items-center gap-1.5 text-xs text-[#64748B] bg-white border border-[#E2E8F0] px-3 py-1.5 rounded-lg shadow-2xs">
                <x-icon name="calendar" class="w-3.5 h-3.5 text-[#64748B]" />
                <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            @if (auth()->user()->isAdminKeuangan())
                <button 
                    wire:click="bukaModalTransaksi"
                    type="button" 
                    class="px-3 py-1.5 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
                >
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Catat Transaksi</span>
                </button>
                <button 
                    wire:click="bukaModalPerbaikan"
                    type="button" 
                    class="px-3 py-1.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
                >
                    <x-icon name="wrench" class="w-3.5 h-3.5" />
                    <span>Biaya Mobil</span>
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="p-3.5 rounded-xl bg-[#DCFCE7] border border-green-200 text-[#166534] flex items-center gap-2 text-xs font-medium">
            <x-icon name="circle-check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 1. 4 KPI Cards (Clean Icon Without Colored Circle Background & Balanced Font Size) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Pemasukan Stok -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Total Pemasukan Stok</p>
                <x-icon name="wallet" class="w-4 h-4 text-[#10B981] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#10B981] font-semibold">{{ number_format($totalUnitTerjual, 0, ',', '.') }} Drum</span>
                <span class="text-[#64748B]">Hasil penjualan</span>
            </div>
        </div>

        <!-- Card 2: Pengeluaran Stok Gudang -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Pengeluaran Stok</p>
                <x-icon name="package" class="w-4 h-4 text-[#F59E0B] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                Rp {{ number_format($totalPengeluaranStok, 0, ',', '.') }}
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#F59E0B] font-semibold">{{ number_format($totalUnitBahanMasuk, 0, ',', '.') }} Unit</span>
                <span class="text-[#64748B]">Modal & operasional</span>
            </div>
        </div>

        <!-- Card 3: Biaya Perbaikan Mobil -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Biaya Perbaikan Mobil</p>
                <x-icon name="wrench" class="w-4 h-4 text-[#EF4444] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                Rp {{ number_format($totalBiayaPerbaikan, 0, ',', '.') }}
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-rose-600 font-semibold">{{ $totalPerbaikanSelesai }} Selesai</span>
                <span class="text-[#64748B]">{{ $perbaikanProsesCount }} pengerjaan</span>
            </div>
        </div>

        <!-- Card 4: Saldo Kas Operasional (Net) -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Saldo Kas Operasional</p>
                <x-icon name="trending-up" class="w-4 h-4 text-[#2563EB] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold {{ $netOperasional >= 0 ? 'text-[#0F172A]' : 'text-rose-600' }} tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                Rp {{ number_format($netOperasional, 0, ',', '.') }}
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#2563EB] font-semibold">Margin {{ $marginPct }}%</span>
                <span class="text-[#64748B]">Arus kas bersih</span>
            </div>
        </div>
    </div>

    <!-- 2. Middle Row: Grafik Tracker Keuangan (2 cols) + Aktivitas Keuangan Terbaru (1 col) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Grafik Tracker Keuangan (Interactive SVG Spline Curve & Area per Reference) -->
        <div 
            x-data="{
                activeIdx: null,
                showPemasukan: true,
                showPengeluaran: true,
                showPerbaikan: true,
                days: @js($trackerData)
            }"
            class="lg:col-span-2 bg-white p-4 sm:p-5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col justify-between"
        >
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                <div>
                    <h2 class="text-sm font-bold text-[#0F172A]">Grafik Tracker Keuangan Operasional</h2>
                    <p class="text-[11px] text-[#64748B]">Arahkan kursor pada grafik untuk melihat rincian arus kas harian</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-[#64748B]">
                    <button type="button" @click="showPemasukan = !showPemasukan" :class="showPemasukan ? 'opacity-100 font-medium text-[#0F172A]' : 'opacity-40'" class="inline-flex items-center gap-1.5 transition cursor-pointer">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span>
                        <span>Pemasukan</span>
                    </button>
                    <button type="button" @click="showPengeluaran = !showPengeluaran" :class="showPengeluaran ? 'opacity-100 font-medium text-[#0F172A]' : 'opacity-40'" class="inline-flex items-center gap-1.5 transition cursor-pointer">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span>
                        <span>Beban Stok</span>
                    </button>
                    <button type="button" @click="showPerbaikan = !showPerbaikan" :class="showPerbaikan ? 'opacity-100 font-medium text-[#0F172A]' : 'opacity-40'" class="inline-flex items-center gap-1.5 transition cursor-pointer">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#EF4444]"></span>
                        <span>Biaya Mobil</span>
                    </button>
                </div>
            </div>

            <!-- Interactive SVG Spline Curves -->
            <div class="relative w-full pt-1 select-none" @mouseleave="activeIdx = null">
                <!-- Floating Tooltip Card -->
                <div 
                    x-show="activeIdx !== null" 
                    x-cloak
                    x-transition.opacity.duration.100ms
                    :style="activeIdx !== null ? (activeIdx > 4 ? 'right: ' + (100 - days[activeIdx].pctX + 4) + '%; left: auto;' : 'left: ' + Math.max(8, days[activeIdx].pctX + 4) + '%;') : ''"
                    class="pointer-events-none absolute top-2 z-20 w-52 rounded-xl bg-[#0F172A]/95 text-white p-3 shadow-xl border border-slate-700 text-xs"
                >
                    <template x-if="activeIdx !== null">
                        <div>
                            <div class="font-semibold text-[11px] text-slate-300 border-b border-slate-700/80 pb-1.5 mb-2" x-text="days[activeIdx].fullDate"></div>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between" x-show="showPemasukan">
                                    <span class="inline-flex items-center gap-1.5 text-slate-300">
                                        <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                                        Pemasukan
                                    </span>
                                    <span class="font-bold tabular-nums text-emerald-400" x-text="days[activeIdx].pemasukan"></span>
                                </div>
                                <div class="flex items-center justify-between" x-show="showPengeluaran">
                                    <span class="inline-flex items-center gap-1.5 text-slate-300">
                                        <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                                        Beban Stok
                                    </span>
                                    <span class="font-bold tabular-nums text-amber-400" x-text="days[activeIdx].pengeluaran"></span>
                                </div>
                                <div class="flex items-center justify-between" x-show="showPerbaikan">
                                    <span class="inline-flex items-center gap-1.5 text-slate-300">
                                        <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                                        Biaya Mobil
                                    </span>
                                    <span class="font-bold tabular-nums text-rose-400" x-text="days[activeIdx].perbaikan"></span>
                                </div>
                            </div>
                            <div class="mt-2 pt-1.5 border-t border-slate-700/80 flex items-center justify-between text-[11px]">
                                <span class="text-slate-400">Saldo Net Harian</span>
                                <span class="font-bold text-sky-400 tabular-nums" x-text="days[activeIdx].net"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <svg viewBox="0 0 640 205" class="w-full h-52 overflow-visible">
                    <defs>
                        <linearGradient id="gradPemasukan" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10B981" stop-opacity="0.18" />
                            <stop offset="100%" stop-color="#10B981" stop-opacity="0.0" />
                        </linearGradient>
                        <linearGradient id="gradPengeluaran" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#F59E0B" stop-opacity="0.14" />
                            <stop offset="100%" stop-color="#F59E0B" stop-opacity="0.0" />
                        </linearGradient>
                        <linearGradient id="gradPerbaikan" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#EF4444" stop-opacity="0.14" />
                            <stop offset="100%" stop-color="#EF4444" stop-opacity="0.0" />
                        </linearGradient>
                    </defs>

                    <!-- Y-Axis Labels & Grid Lines -->
                    @foreach ([1, 0.75, 0.5, 0.25, 0] as $ratio)
                        @php
                            $yPos = round(176 - ($ratio * 158), 1);
                            $yVal = $yMax * $ratio;
                            $yLabel = $yVal >= 1000000 ? round($yVal / 1000000, 1).' Jt' : number_format($yVal, 0, ',', '.');
                        @endphp
                        <line x1="52" y1="{{ $yPos }}" x2="620" y2="{{ $yPos }}" stroke="#F1F5F9" stroke-width="1" />
                        <text x="44" y="{{ $yPos + 4 }}" text-anchor="end" class="fill-[#64748B] text-[10px] font-medium">{{ $yLabel }}</text>
                    @endforeach

                    <!-- Vertical Grid Lines & X-Axis Date Labels -->
                    @foreach ($chartPemasukan['points'] as $idx => $pt)
                        <line x1="{{ $pt['x'] }}" y1="18" x2="{{ $pt['x'] }}" y2="176" stroke="#F8FAFC" stroke-width="1" />
                        <text 
                            x="{{ $pt['x'] }}" 
                            y="196" 
                            text-anchor="middle" 
                            :class="activeIdx === {{ $idx }} ? 'fill-[#2563EB] font-bold' : 'fill-[#64748B] font-medium'"
                            class="text-[10px] transition-colors"
                        >{{ $xLabels[$idx] }}</text>
                    @endforeach

                    <!-- Vertical Crosshair Tracker Line -->
                    @foreach ($chartPemasukan['points'] as $idx => $pt)
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

                    <!-- Area Fills -->
                    <path x-show="showPemasukan" d="{{ $chartPemasukan['area'] }}" fill="url(#gradPemasukan)" />
                    <path x-show="showPengeluaran" d="{{ $chartPengeluaranStok['area'] }}" fill="url(#gradPengeluaran)" />
                    <path x-show="showPerbaikan" d="{{ $chartPerbaikan['area'] }}" fill="url(#gradPerbaikan)" />

                    <!-- Spline Curves -->
                    <path x-show="showPerbaikan" d="{{ $chartPerbaikan['line'] }}" fill="none" stroke="#EF4444" stroke-width="2.2" stroke-linecap="round" />
                    <path x-show="showPengeluaran" d="{{ $chartPengeluaranStok['line'] }}" fill="none" stroke="#F59E0B" stroke-width="2.2" stroke-linecap="round" />
                    <path x-show="showPemasukan" d="{{ $chartPemasukan['line'] }}" fill="none" stroke="#10B981" stroke-width="2.4" stroke-linecap="round" />

                    <!-- Data Nodes: Perbaikan Mobil -->
                    @foreach ($chartPerbaikan['points'] as $idx => $pt)
                        <circle 
                            x-show="showPerbaikan"
                            cx="{{ $pt['x'] }}" 
                            cy="{{ $pt['y'] }}" 
                            :r="activeIdx === {{ $idx }} ? 5.5 : 3.5" 
                            fill="#EF4444" 
                            stroke="#FFFFFF" 
                            stroke-width="1.5"
                            class="transition-all duration-100"
                        />
                    @endforeach

                    <!-- Data Nodes: Pengeluaran Stok -->
                    @foreach ($chartPengeluaranStok['points'] as $idx => $pt)
                        <circle 
                            x-show="showPengeluaran"
                            cx="{{ $pt['x'] }}" 
                            cy="{{ $pt['y'] }}" 
                            :r="activeIdx === {{ $idx }} ? 5.5 : 3.5" 
                            fill="#F59E0B" 
                            stroke="#FFFFFF" 
                            stroke-width="1.5"
                            class="transition-all duration-100"
                        />
                    @endforeach

                    <!-- Data Nodes: Pemasukan Stok -->
                    @foreach ($chartPemasukan['points'] as $idx => $pt)
                        <circle 
                            x-show="showPemasukan"
                            cx="{{ $pt['x'] }}" 
                            cy="{{ $pt['y'] }}" 
                            :r="activeIdx === {{ $idx }} ? 5.5 : 3.5" 
                            fill="#10B981" 
                            stroke="#FFFFFF" 
                            stroke-width="1.5"
                            class="transition-all duration-100"
                        />
                    @endforeach

                    <!-- Hover Columns -->
                    @foreach ($chartPemasukan['points'] as $idx => $pt)
                        @php
                            $colWidth = 72;
                            $rectX = max(45, round($pt['x'] - ($colWidth / 2), 1));
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

        <!-- Aktivitas Keuangan Terbaru (Identik Layout Dashboard Kantor) -->
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-[#0F172A]">Aktivitas Keuangan</h2>
                <a href="{{ route('keuangan.stok-transaksi') }}" wire:navigate class="text-xs font-medium text-[#2563EB] hover:text-[#1D4ED8] inline-flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <x-icon name="arrow-right" class="w-3 h-3" />
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @foreach ($recentStock as $stk)
                    <div class="py-2.5 flex items-center justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <x-icon name="{{ $stk->jenis_transaksi === 'pemasukan' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4 mt-0.5 shrink-0 {{ $stk->jenis_transaksi === 'pemasukan' ? 'text-[#10B981]' : 'text-[#F59E0B]' }}" />
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-[#0F172A] truncate">
                                    {{ ucfirst($stk->jenis_transaksi) }}: {{ $stk->pihak_terkait }}
                                </p>
                                <p class="text-[11px] text-[#64748B] truncate">
                                    {{ $stk->formatted_nominal }}
                                </p>
                            </div>
                        </div>
                        <span class="text-[11px] text-[#64748B] tabular-nums shrink-0">
                            {{ $stk->tanggal_transaksi?->format('d M') }}
                        </span>
                    </div>
                @endforeach

                @foreach ($recentRepairs as $rep)
                    <div class="py-2.5 flex items-center justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <x-icon name="wrench" class="w-4 h-4 mt-0.5 shrink-0 text-[#EF4444]" />
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-[#0F172A] truncate">
                                    Servis {{ $rep->nomor_polisi }} ({{ $rep->jenis_perbaikan }})
                                </p>
                                <p class="text-[11px] text-[#64748B] truncate">
                                    {{ $rep->formatted_biaya }} ({{ $rep->nama_bengkel }})
                                </p>
                            </div>
                        </div>
                        <span class="text-[11px] text-[#64748B] tabular-nums shrink-0">
                            {{ $rep->tanggal_perbaikan?->format('d M') }}
                        </span>
                    </div>
                @endforeach

                @if ($recentStock->isEmpty() && $recentRepairs->isEmpty())
                    <div class="py-8 text-center text-xs text-[#64748B]">
                        Belum ada riwayat aktivitas keuangan.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 3. Bottom Row: Data Transaksi Terbaru (2 cols) + Status Arus Kas (1 col) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Data Transaksi Terbaru -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
            <div class="p-4 sm:px-5 border-b border-[#E2E8F0] flex items-center justify-between">
                <h2 class="text-sm font-bold text-[#0F172A]">Data Transaksi Stok Terbaru</h2>
                <a href="{{ route('keuangan.stok-transaksi') }}" wire:navigate class="text-xs font-medium text-[#2563EB] hover:text-[#1D4ED8] inline-flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <x-icon name="arrow-right" class="w-3 h-3" />
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                        <tr>
                            <th class="py-2.5 px-4 whitespace-nowrap">No. Transaksi</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">Jenis</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">Pihak Terkait</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">Tanggal</th>
                            <th class="py-2.5 px-4 whitespace-nowrap text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestTransactions as $trx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                    {{ $trx->kode_transaksi }}
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    @if ($trx->jenis_transaksi === 'pemasukan')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                            Pemasukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#FEF3C7] text-[#92400E]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                                            Pengeluaran
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-[#0F172A] whitespace-nowrap">
                                    {{ $trx->pihak_terkait }}
                                </td>
                                <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                    {{ $trx->tanggal_transaksi?->format('d M Y') }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-semibold {{ $trx->jenis_transaksi === 'pemasukan' ? 'text-emerald-600' : 'text-[#0F172A]' }} whitespace-nowrap tabular-nums">
                                    {{ $trx->formatted_nominal }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-[#64748B]">
                                    Belum ada transaksi keuangan stok tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Status Arus Kas (Donut Ring Summary per Reference) -->
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col justify-between">
            <h2 class="text-sm font-bold text-[#0F172A] mb-3">Distribusi Arus Kas</h2>

            <div class="flex items-center justify-center my-2">
                <div class="relative w-32 h-32 flex items-center justify-center rounded-full"
                     style="background: conic-gradient(#10B981 0% {{ $pctPemasukan }}%, #F59E0B {{ $pctPemasukan }}% {{ $pctPemasukan + $pctPengeluaranStok }}%, #EF4444 {{ $pctPemasukan + $pctPengeluaranStok }}% 100%); border: none; padding: 12px;">
                    <div class="w-full h-full bg-white rounded-full flex flex-col items-center justify-center text-center px-1">
                        <span class="text-xs font-bold text-[#0F172A] tabular-nums leading-none">
                            Rp {{ number_format($totalPemasukan / 1000000, 1) }}M
                        </span>
                        <span class="text-[10px] text-[#64748B] mt-1">Pemasukan</span>
                    </div>
                </div>
            </div>

            <div class="space-y-2 mt-3 text-xs">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-2 text-[#64748B]">
                        <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                        Pemasukan Stok
                    </span>
                    <span class="font-semibold text-[#0F172A] tabular-nums">{{ $pctPemasukan }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-2 text-[#64748B]">
                        <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                        Pengeluaran Stok
                    </span>
                    <span class="font-semibold text-[#0F172A] tabular-nums">{{ $pctPengeluaranStok }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-2 text-[#64748B]">
                        <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                        Biaya Mobil
                    </span>
                    <span class="font-semibold text-[#0F172A] tabular-nums">{{ $pctBiayaPerbaikan }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 1: Catat Transaksi Keuangan Stok (Khusus Admin Keuangan) -->
    @if ($showModalTransaksi)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden border border-[#E2E8F0]">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-[#0F172A]">Catat Transaksi Keuangan Stok</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">Kode: <span class="font-mono font-semibold text-[#2563EB]">{{ $kode_transaksi }}</span></p>
                    </div>
                    <button 
                        wire:click="tutupModalTransaksi" 
                        type="button" 
                        class="p-1 rounded-lg text-[#64748B] hover:text-[#0F172A] hover:bg-slate-100 cursor-pointer"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanTransaksi" class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jenis Transaksi <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model.live="jenis_transaksi" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="pemasukan">Pemasukan (Hasil Penjualan)</option>
                                <option value="pengeluaran">Pengeluaran (Modal / Bahan)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Kategori <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="kategori" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                @if ($jenis_transaksi === 'pemasukan')
                                    <option value="penjualan_drum">Penjualan Drum Rekondisi</option>
                                    <option value="pendapatan_jasa">Jasa Cuci / Servis Drum</option>
                                    <option value="lainnya">Pemasukan Lainnya</option>
                                @else
                                    <option value="pembelian_batch">Pembelian Batch Drum Bahan Baku</option>
                                    <option value="bahan_operasional">Bahan Kimia & Cat Operasional</option>
                                    <option value="lainnya">Pengeluaran Gudang Lainnya</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="tanggal_transaksi" 
                                type="date" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('tanggal_transaksi') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="nominal" 
                                type="number" 
                                min="1000" 
                                placeholder="Contoh: 25000000" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('nominal') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jumlah Unit Drum</label>
                            <input 
                                wire:model="jumlah_unit" 
                                type="number" 
                                min="0" 
                                placeholder="Contoh: 100" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Metode Pembayaran</label>
                            <select 
                                wire:model="metode_pembayaran" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="transfer">Transfer Bank</option>
                                <option value="tunai">Tunai / Kas</option>
                                <option value="tempo">Tempo (Kredit)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Pihak Terkait (Customer / Supplier / Vendor) <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model="pihak_terkait" 
                            type="text" 
                            placeholder="Contoh: PT Sumber Kimia Abadi" 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                            required 
                        />
                        @error('pihak_terkait') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Keterangan</label>
                        <textarea 
                            wire:model="keterangan" 
                            rows="2" 
                            placeholder="Keterangan tambahan..." 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        ></textarea>
                    </div>

                    <div class="pt-3 border-t border-[#E2E8F0] flex justify-end gap-2">
                        <button 
                            wire:click="tutupModalTransaksi" 
                            type="button" 
                            class="px-4 py-2 rounded-lg border border-[#E2E8F0] text-[#0F172A] text-xs font-medium hover:bg-slate-50 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold transition cursor-pointer"
                        >
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal 2: Catat Biaya Perbaikan Mobil (Khusus Admin Keuangan) -->
    @if ($showModalPerbaikan)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden border border-[#E2E8F0]">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-[#0F172A]">Catat Biaya Perbaikan Mobil</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">Pemeliharaan dan perbaikan armada operasional</p>
                    </div>
                    <button 
                        wire:click="tutupModalPerbaikan" 
                        type="button" 
                        class="p-1 rounded-lg text-[#64748B] hover:text-[#0F172A] hover:bg-slate-100 cursor-pointer"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanPerbaikan" class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Nomor Polisi <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="nomor_polisi" 
                                type="text" 
                                placeholder="Contoh: B 9481 KDA" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 uppercase focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('nomor_polisi') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jenis Kendaraan <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="jenis_kendaraan" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="Truk Tronton Box">Truk Tronton Box</option>
                                <option value="Truk Engkel Bak">Truk Engkel Bak</option>
                                <option value="Truk Tangki B3">Truk Tangki B3</option>
                                <option value="Pickup Operasional">Pickup Operasional</option>
                                <option value="Mobil Operasional">Mobil Operasional</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Tanggal Perbaikan <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="tanggal_perbaikan" 
                                type="date" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('tanggal_perbaikan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Biaya Perbaikan (Rp) <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="biaya" 
                                type="number" 
                                min="1000" 
                                placeholder="Contoh: 3500000" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('biaya') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Jenis Perbaikan <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="jenis_perbaikan" 
                                type="text" 
                                placeholder="Contoh: Servis Rem & Ganti Oli" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                                required 
                            />
                            @error('jenis_perbaikan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#0F172A] mb-1">Status Perbaikan <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="status_perbaikan" 
                                class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            >
                                <option value="selesai">Selesai</option>
                                <option value="dalam_pengerjaan">Dalam Pengerjaan</option>
                                <option value="dijadwalkan">Dijadwalkan</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Nama Bengkel Rekanan <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model="nama_bengkel" 
                            type="text" 
                            placeholder="Contoh: Bengkel Sumber Diesel Cikarang" 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]" 
                            required 
                        />
                        @error('nama_bengkel') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">Keterangan</label>
                        <textarea 
                            wire:model="keterangan_perbaikan" 
                            rows="2" 
                            placeholder="Rincian suku cadang atau catatan perbaikan..." 
                            class="w-full rounded-lg border-[#E2E8F0] bg-white text-xs text-[#0F172A] py-2 px-3 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        ></textarea>
                    </div>

                    <div class="pt-3 border-t border-[#E2E8F0] flex justify-end gap-2">
                        <button 
                            wire:click="tutupModalPerbaikan" 
                            type="button" 
                            class="px-4 py-2 rounded-lg border border-[#E2E8F0] text-[#0F172A] text-xs font-medium hover:bg-slate-50 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold transition cursor-pointer"
                        >
                            Simpan Data Perbaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

