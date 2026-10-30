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
    <!-- Mobile Backdrop -->
    <div 
        x-show="sidebarOpen" 
        x-cloak
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs lg:hidden"
        @click="sidebarOpen = false"
    ></div>

    <!-- Mobile Sidebar -->
    <aside 
        x-show="sidebarOpen" 
        x-cloak
        x-transition:enter="transition ease-in-out duration-200 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-60 bg-[#0F172A] text-slate-300 flex flex-col lg:hidden shadow-xl"
    >
        <!-- Brand -->
        <div class="h-16 px-5 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#2563EB] flex items-center justify-center p-1.5 shrink-0">
                    <x-application-logo class="w-full h-full text-white" />
                </div>
                <div class=" leading-tight">
                    <span class="text-sm font-bold text-white block">Pulo Kencana</span>
                    <span class="text-[11px] text-slate-400 block">Drum Industri</span>
                </div>
            </a>
            <button 
                type="button" 
                @click="sidebarOpen = false"
                class="p-1 rounded-lg text-slate-400 hover:text-white"
            >
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>

        <!-- Menu -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
            @if (auth()->user()->isAdminKantor())
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0" />
                    <span>Dashboard</span>
                </a>

                <a 
                    href="{{ route('admin.drum-batches') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.drum-batches') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="package" class="w-4 h-4 shrink-0" />
                    <span>Data Drum</span>
                </a>

                <a 
                    href="{{ route('admin.drum-process') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.drum-process') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layers" class="w-4 h-4 shrink-0" />
                    <span>Proses Gudang</span>
                </a>

                <a 
                    href="{{ route('admin.vehicles') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.vehicles') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="truck" class="w-4 h-4 shrink-0" />
                    <span>Kunjungan Truk</span>
                </a>

                <a 
                    href="{{ route('admin.reports') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.reports*') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="file-text" class="w-4 h-4 shrink-0" />
                    <span>Laporan</span>
                </a>

                <a 
                    href="{{ route('keuangan.dashboard') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.*') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="wallet" class="w-4 h-4 shrink-0" />
                    <span>Rekap Keuangan</span>
                </a>
            @elseif (auth()->user()->isSatpam())
                <a 
                    href="{{ route('satpam.dashboard') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('satpam.dashboard') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0" />
                    <span>Dashboard</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-masuk') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('satpam.kendaraan-masuk') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="log-in" class="w-4 h-4 shrink-0" />
                    <span>Catat Truk Masuk</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-aktif') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('satpam.kendaraan-aktif') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="truck" class="w-4 h-4 shrink-0" />
                    <span>Kendaraan Aktif</span>
                </a>

                <a 
                    href="{{ route('keuangan.dashboard') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.*') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="wallet" class="w-4 h-4 shrink-0" />
                    <span>Rekap Keuangan</span>
                </a>
            @elseif (auth()->user()->isAdminKeuangan())
                <a 
                    href="{{ route('keuangan.dashboard') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.dashboard') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0" />
                    <span>Dashboard Keuangan</span>
                </a>

                <a 
                    href="{{ route('keuangan.stok-transaksi') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.stok-transaksi') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="wallet" class="w-4 h-4 shrink-0" />
                    <span>Keuangan Stok</span>
                </a>

                <a 
                    href="{{ route('keuangan.biaya-kendaraan') }}" 
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.biaya-kendaraan') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="wrench" class="w-4 h-4 shrink-0" />
                    <span>Biaya Mobil</span>
                </a>
            @endif
        </nav>

        <!-- Bottom Profile & Logout -->
        <div class="p-3 border-t border-slate-800/80 space-y-1">
            <a 
                href="{{ route('profile') }}" 
                wire:navigate
                @click="sidebarOpen = false"
                class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('profile') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
            >
                <x-icon name="user" class="w-4 h-4 shrink-0" />
                <span>Pengguna</span>
            </a>
            <button 
                wire:click="logout" 
                type="button"
                class="w-full flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-800/70 transition-colors cursor-pointer"
            >
                <x-icon name="log-out" class="w-4 h-4" />
                <span>Keluar</span>
            </button>
        </div>
    </aside>

    <!-- Desktop Fixed Sidebar (Matches Industrial Pro Reference) -->
    <aside class="hidden lg:flex lg:flex-col lg:w-60 lg:fixed lg:inset-y-0 lg:left-0 z-40 bg-[#0F172A] text-slate-300">
        <!-- Brand Header -->
        <div class="h-16 px-5 flex items-center">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#2563EB] flex items-center justify-center p-1.5 shrink-0">
                    <x-application-logo class="w-full h-full text-white" />
                </div>
                <div class="leading-tight">
                    <span class="text-sm font-bold text-white block">Pulo Kencana</span>
                    <span class="text-[11px] text-slate-400 block">Drum Industri</span>
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
            @if (auth()->user()->isAdminKantor())
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0" />
                    <span>Dashboard</span>
                </a>

                <a 
                    href="{{ route('admin.drum-batches') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.drum-batches') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="package" class="w-4 h-4 shrink-0" />
                    <span>Data Drum</span>
                </a>

                <a 
                    href="{{ route('admin.drum-process') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.drum-process') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layers" class="w-4 h-4 shrink-0" />
                    <span>Proses Gudang</span>
                </a>

                <a 
                    href="{{ route('admin.vehicles') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.vehicles') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="truck" class="w-4 h-4 shrink-0" />
                    <span>Kunjungan Truk</span>
                </a>

                <a 
                    href="{{ route('admin.reports') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.reports*') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="file-text" class="w-4 h-4 shrink-0" />
                    <span>Laporan</span>
                </a>

                <a 
                    href="{{ route('keuangan.dashboard') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.*') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="wallet" class="w-4 h-4 shrink-0" />
                    <span>Rekap Keuangan</span>
                </a>
            @elseif (auth()->user()->isSatpam())
                <a 
                    href="{{ route('satpam.dashboard') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('satpam.dashboard') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0" />
                    <span>Dashboard</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-masuk') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('satpam.kendaraan-masuk') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="log-in" class="w-4 h-4 shrink-0" />
                    <span>Catat Truk Masuk</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-aktif') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('satpam.kendaraan-aktif') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="truck" class="w-4 h-4 shrink-0" />
                    <span>Kendaraan Aktif</span>
                </a>

                <a 
                    href="{{ route('keuangan.dashboard') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.*') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="wallet" class="w-4 h-4 shrink-0" />
                    <span>Rekap Keuangan</span>
                </a>
            @elseif (auth()->user()->isAdminKeuangan())
                <a 
                    href="{{ route('keuangan.dashboard') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.dashboard') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0" />
                    <span>Dashboard Keuangan</span>
                </a>

                <a 
                    href="{{ route('keuangan.stok-transaksi') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.stok-transaksi') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="wallet" class="w-4 h-4 shrink-0" />
                    <span>Keuangan Stok</span>
                </a>

                <a 
                    href="{{ route('keuangan.biaya-kendaraan') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('keuangan.biaya-kendaraan') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <x-icon name="wrench" class="w-4 h-4 shrink-0" />
                    <span>Biaya Mobil</span>
                </a>
            @endif
        </nav>

        <!-- Bottom Profile & Logout -->
        <div class="p-3 border-t border-slate-800/80 space-y-1">
            <a 
                href="{{ route('profile') }}" 
                wire:navigate
                class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('profile') ? 'bg-[#2563EB] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}"
            >
                <x-icon name="user" class="w-4 h-4 shrink-0" />
                <span>Pengguna</span>
            </a>
            <button 
                wire:click="logout" 
                type="button"
                class="w-full flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-medium text-slate-400 hover:text-rose-400 hover:bg-slate-800/70 transition-colors cursor-pointer"
            >
                <x-icon name="log-out" class="w-4 h-4 shrink-0" />
                <span>Keluar</span>
            </button>
        </div>
    </aside>

    <!-- Top App Bar (Matches Industrial Pro Reference) -->
    <header class="sticky top-0 z-30 lg:pl-60 bg-white border-b border-[#E2E8F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between gap-4">
            <!-- Left: Hamburger & Search -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button 
                    type="button" 
                    @click="sidebarOpen = true" 
                    class="p-1.5 -ml-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 lg:hidden"
                >
                    <x-icon name="menu" class="w-5 h-5" />
                </button>

                <div class="relative max-w-sm w-full hidden sm:block">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#64748B]">
                        <x-icon name="search" class="w-3.5 h-3.5" />
                    </div>
                    <input 
                        type="text" 
                        placeholder="Cari data, nomor drum, atau dokumen..." 
                        class="w-full pl-9 pr-3 py-1.5 text-xs bg-[#F7F9FC] border border-[#E2E8F0] rounded-lg text-[#0F172A] placeholder:text-[#64748B] focus:outline-none focus:bg-white focus:border-[#2563EB]"
                    />
                </div>
            </div>

            <!-- Right: Bell + User Dropdown -->
            <div class="flex items-center gap-3 shrink-0">
                <div class="relative p-1.5 text-[#64748B] hover:text-[#0F172A] transition-colors">
                    <x-icon name="bell" class="w-4 h-4" />
                    <span class="absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span>
                </div>

                <div x-data="{ userMenuOpen: false }" class="relative">
                    <button 
                        @click="userMenuOpen = !userMenuOpen" 
                        type="button"
                        class="flex items-center gap-2.5 py-1 px-2 rounded-lg hover:bg-slate-50 transition focus:outline-none cursor-pointer"
                    >
                        <div class="w-8 h-8 rounded-full bg-[#0F172A] text-white flex items-center justify-center font-semibold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <div class="text-xs font-semibold text-[#0F172A] leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-[11px] text-[#64748B] leading-tight">
                                @if (auth()->user()->isAdminKantor())
                                    Administrator
                                @elseif (auth()->user()->isAdminKeuangan())
                                    Admin Keuangan
                                @else
                                    Satpam
                                @endif
                            </div>
                        </div>
                        <x-icon name="chevron-down" class="w-3.5 h-3.5 text-[#64748B]" />
                    </button>

                    <!-- Dropdown -->
                    <div 
                        x-show="userMenuOpen" 
                        @click.outside="userMenuOpen = false"
                        x-cloak
                        class="absolute right-0 mt-1.5 w-48 bg-white rounded-xl shadow-lg border border-[#E2E8F0] py-1 z-50 divide-y divide-slate-100"
                    >
                        <div class="px-3.5 py-2">
                            <p class="text-xs font-semibold text-[#0F172A] truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-[#64748B] truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <a 
                                href="{{ route('profile') }}" 
                                wire:navigate
                                class="flex items-center gap-2 px-3.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50"
                            >
                                <x-icon name="user" class="w-3.5 h-3.5 text-slate-400" />
                                <span>Profil Pengguna</span>
                            </a>
                            <button 
                                wire:click="logout" 
                                type="button"
                                class="w-full flex items-center gap-2 px-3.5 py-1.5 text-xs text-rose-600 hover:bg-rose-50 text-left cursor-pointer"
                            >
                                <x-icon name="log-out" class="w-3.5 h-3.5 text-rose-500" />
                                <span>Keluar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>
