<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div x-data="{ sidebarOpen: false }">
    <!-- ============================================================== -->
    <!-- 1. MOBILE BACKDROP (SLIDE-OVER OVERLAY)                          -->
    <!-- ============================================================== -->
    <div 
        x-show="sidebarOpen" 
        x-cloak
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
        @click="sidebarOpen = false"
    ></div>

    <!-- ============================================================== -->
    <!-- 2. MOBILE DRAWER SIDEBAR                                        -->
    <!-- ============================================================== -->
    <aside 
        x-show="sidebarOpen" 
        x-cloak
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0F172A] text-slate-300 flex flex-col lg:hidden shadow-2xl border-r border-slate-800"
    >
        <!-- Brand Header (Mobile) -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800/80 bg-[#0B1120]">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-lg bg-blue-600/20 border border-blue-500/30 flex items-center justify-center p-1.5 shrink-0 group-hover:border-blue-400 transition-colors">
                    <x-application-logo class="w-full h-full" />
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-bold text-white tracking-tight leading-none group-hover:text-blue-300 transition-colors">Pulo Kencana</span>
                    <span class="text-[10px] font-semibold text-sky-400 tracking-wider uppercase leading-tight mt-0.5">Drum Industri</span>
                </div>
            </a>
            <button 
                type="button" 
                @click="sidebarOpen = false"
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none"
                aria-label="Tutup Navigasi"
            >
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <!-- Navigation Links (Mobile) -->
        <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
            @if (auth()->user()->isAdminKantor())
                <div class="px-3 pb-2 pt-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Operasional Gudang
                </div>

                <a 
                    href="{{ route('admin.dashboard') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.dashboard') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layout-dashboard" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Dashboard</span>
                </a>

                <a 
                    href="{{ route('admin.drum-batches') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.drum-batches') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="package" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.drum-batches') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Batch & Transit Drum</span>
                </a>

                <a 
                    href="{{ route('admin.drum-process') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.drum-process') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layers" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.drum-process') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">4 Tahap Proses Gudang</span>
                </a>

                <a 
                    href="{{ route('admin.vehicles') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.vehicles') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="truck" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.vehicles') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Catatan Kendaraan</span>
                </a>

                <div class="px-3 pb-2 pt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Laporan & Analitik
                </div>

                <a 
                    href="{{ route('admin.reports') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.reports*') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="bar-chart-3" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.reports*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Rekapitulasi & Ekspor</span>
                </a>

            @elseif (auth()->user()->isSatpam())
                <div class="px-3 pb-2 pt-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Pos Gerbang Utama
                </div>

                <a 
                    href="{{ route('satpam.dashboard') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('satpam.dashboard') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="shield-check" class="w-5 h-5 shrink-0 {{ request()->routeIs('satpam.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Dashboard Gerbang</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-masuk') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('satpam.kendaraan-masuk') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="log-in" class="w-5 h-5 shrink-0 {{ request()->routeIs('satpam.kendaraan-masuk') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Catat Truk Masuk</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-aktif') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('satpam.kendaraan-aktif') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="truck" class="w-5 h-5 shrink-0 {{ request()->routeIs('satpam.kendaraan-aktif') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Kendaraan Aktif (Di Dalam)</span>
                </a>
            @endif
        </nav>

        <!-- User Profile Footer (Mobile) -->
        <div class="p-3 border-t border-slate-800/80 bg-[#0B1120]">
            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/90 border border-slate-800">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg {{ auth()->user()->isAdminKantor() ? 'bg-blue-900/60 text-blue-300 border border-blue-700/50' : 'bg-emerald-900/60 text-emerald-300 border border-emerald-700/50' }} flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs font-semibold text-white truncate leading-tight">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider leading-tight">
                            {{ auth()->user()->isAdminKantor() ? 'Admin Kantor' : 'Petugas Satpam' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <a 
                        href="{{ route('profile') }}" 
                        wire:navigate 
                        title="Profil Pengguna"
                        class="p-1.5 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                    >
                        <x-icon name="user" class="w-4 h-4" />
                    </a>
                    <button 
                        wire:click="logout" 
                        type="button"
                        title="Keluar"
                        class="p-1.5 rounded-md text-slate-400 hover:text-rose-400 hover:bg-rose-950/40 transition-colors cursor-pointer"
                    >
                        <x-icon name="log-out" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- ============================================================== -->
    <!-- 3. DESKTOP FIXED SIDEBAR                                        -->
    <!-- ============================================================== -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 lg:left-0 z-40 bg-[#0F172A] text-slate-300 border-r border-slate-800/80 shadow-xl">
        <!-- Brand Header (Desktop) -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800/80 bg-[#0B1120]">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-lg bg-blue-600/20 border border-blue-500/30 flex items-center justify-center p-1.5 shrink-0 group-hover:border-blue-400 transition-colors">
                    <x-application-logo class="w-full h-full" />
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-bold text-white tracking-tight leading-none group-hover:text-blue-300 transition-colors">Pulo Kencana</span>
                    <span class="text-[10px] font-semibold text-sky-400 tracking-wider uppercase leading-tight mt-0.5">Drum Industri</span>
                </div>
            </a>
        </div>

        <!-- Navigation Links (Desktop) -->
        <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
            @if (auth()->user()->isAdminKantor())
                <div class="px-3 pb-2 pt-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Operasional Gudang
                </div>

                <a 
                    href="{{ route('admin.dashboard') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.dashboard') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layout-dashboard" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Dashboard</span>
                </a>

                <a 
                    href="{{ route('admin.drum-batches') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.drum-batches') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="package" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.drum-batches') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Batch & Transit Drum</span>
                </a>

                <a 
                    href="{{ route('admin.drum-process') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.drum-process') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layers" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.drum-process') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">4 Tahap Proses Gudang</span>
                </a>

                <a 
                    href="{{ route('admin.vehicles') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.vehicles') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="truck" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.vehicles') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Catatan Kendaraan</span>
                </a>

                <div class="px-3 pb-2 pt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Laporan & Analitik
                </div>

                <a 
                    href="{{ route('admin.reports') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('admin.reports*') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="bar-chart-3" class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.reports*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Rekapitulasi & Ekspor</span>
                </a>

            @elseif (auth()->user()->isSatpam())
                <div class="px-3 pb-2 pt-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Pos Gerbang Utama
                </div>

                <a 
                    href="{{ route('satpam.dashboard') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('satpam.dashboard') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="shield-check" class="w-5 h-5 shrink-0 {{ request()->routeIs('satpam.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Dashboard Gerbang</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-masuk') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('satpam.kendaraan-masuk') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="log-in" class="w-5 h-5 shrink-0 {{ request()->routeIs('satpam.kendaraan-masuk') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Catat Truk Masuk</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-aktif') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group {{ request()->routeIs('satpam.kendaraan-aktif') ? 'bg-[#2563EB] text-white shadow-sm shadow-blue-500/25 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="truck" class="w-5 h-5 shrink-0 {{ request()->routeIs('satpam.kendaraan-aktif') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                    <span class="truncate">Kendaraan Aktif (Di Dalam)</span>
                </a>
            @endif
        </nav>

        <!-- User Profile Footer (Desktop) -->
        <div class="p-3 border-t border-slate-800/80 bg-[#0B1120]">
            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/90 border border-slate-800">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg {{ auth()->user()->isAdminKantor() ? 'bg-blue-900/60 text-blue-300 border border-blue-700/50' : 'bg-emerald-900/60 text-emerald-300 border border-emerald-700/50' }} flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs font-semibold text-white truncate leading-tight">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider leading-tight">
                            {{ auth()->user()->isAdminKantor() ? 'Admin Kantor' : 'Petugas Satpam' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <a 
                        href="{{ route('profile') }}" 
                        wire:navigate 
                        title="Profil Pengguna"
                        class="p-1.5 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                    >
                        <x-icon name="user" class="w-4 h-4" />
                    </a>
                    <button 
                        wire:click="logout" 
                        type="button"
                        title="Keluar"
                        class="p-1.5 rounded-md text-slate-400 hover:text-rose-400 hover:bg-rose-950/40 transition-colors cursor-pointer"
                    >
                        <x-icon name="log-out" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- ============================================================== -->
    <!-- 4. TOP APP BAR                                                 -->
    <!-- ============================================================== -->
    <header class="sticky top-0 z-30 lg:pl-64 bg-white border-b border-[#E2E8F0] shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
        <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <!-- Left: Mobile Hamburger & Global Quick Search -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button 
                    type="button" 
                    @click="sidebarOpen = true" 
                    class="p-2 -ml-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 lg:hidden focus:outline-none"
                    aria-label="Buka Navigasi"
                >
                    <x-icon name="menu" class="w-5 h-5" />
                </button>

                <!-- Kolom Pencarian Cepat -->
                <div class="relative max-w-xs sm:max-w-sm w-full hidden sm:block">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                    <input 
                        type="text" 
                        placeholder="Pencarian cepat (batch, nopol, surat jalan)..." 
                        class="w-full pl-9 pr-3.5 py-1.5 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-slate-800 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] transition-colors"
                    />
                </div>
            </div>

            <!-- Right: Indikator Tanggal & User Profile Chip -->
            <div class="flex items-center gap-3 shrink-0">
                <!-- Indikator Hari & Tanggal Lokal -->
                <div class="hidden md:flex items-center gap-2 text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-lg">
                    <x-icon name="calendar" class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                    <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </div>

                <!-- User Profile Pill & Dropdown Menu -->
                <div x-data="{ userMenuOpen: false }" class="relative">
                    <button 
                        @click="userMenuOpen = !userMenuOpen" 
                        type="button"
                        class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-lg border border-[#E2E8F0] bg-white hover:bg-slate-50 text-slate-700 transition focus:outline-none cursor-pointer"
                    >
                        <div class="w-7 h-7 rounded-md {{ auth()->user()->isAdminKantor() ? 'bg-blue-100 text-[#2563EB]' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <div class="text-xs font-semibold text-[#0F172A] leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-slate-500 font-medium leading-none mt-0.5">
                                {{ auth()->user()->isAdminKantor() ? 'Admin Kantor' : 'Petugas Satpam' }}
                            </div>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wider {{ auth()->user()->isAdminKantor() ? 'bg-blue-50 text-blue-700 border border-blue-200/80' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' }}">
                            {{ auth()->user()->isAdminKantor() ? 'ADMIN' : 'SATPAM' }}
                        </span>
                        <x-icon name="chevron-down" class="w-3.5 h-3.5 text-slate-400" />
                    </button>

                    <!-- Dropdown Content -->
                    <div 
                        x-show="userMenuOpen" 
                        @click.outside="userMenuOpen = false"
                        x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 divide-y divide-slate-100"
                    >
                        <div class="px-3.5 py-2">
                            <p class="text-xs font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? auth()->user()->username }}</p>
                        </div>
                        <div class="py-1">
                            <a 
                                href="{{ route('profile') }}" 
                                wire:navigate
                                class="flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors"
                            >
                                <x-icon name="user" class="w-4 h-4 text-slate-400" />
                                <span>Profil Pengguna</span>
                            </a>
                        </div>
                        <div class="py-1">
                            <button 
                                wire:click="logout" 
                                type="button"
                                class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors text-left cursor-pointer"
                            >
                                <x-icon name="log-out" class="w-4 h-4 text-rose-500" />
                                <span>Keluar (Log Out)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>
