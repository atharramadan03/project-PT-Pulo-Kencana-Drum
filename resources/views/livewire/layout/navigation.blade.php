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

<div>
    <!-- Mobile Backdrop -->
    <div 
        x-show="mobileSidebarOpen" 
        x-cloak
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs lg:hidden"
        @click="mobileSidebarOpen = false"
    ></div>

    <!-- Mobile Sidebar (White & Dark Theme) -->
    <aside 
        x-show="mobileSidebarOpen" 
        x-cloak
        x-transition:enter="transition ease-in-out duration-200 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-60 bg-white dark:bg-[#1E293B] border-r border-[#E2E8F0] dark:border-[#334155] text-slate-700 dark:text-slate-200 flex flex-col lg:hidden shadow-xl"
    >
        <!-- Brand Header -->
        <div class="h-16 px-5 border-b border-[#E2E8F0] dark:border-[#334155] flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#2563EB] flex items-center justify-center p-1.5 shrink-0 shadow-xs">
                    <x-application-logo class="w-full h-full text-white" />
                </div>
                <div class="leading-tight">
                    <span class="text-sm font-bold text-[#0F172A] dark:text-[#F8FAFC] block">Pulo Kencana</span>
                    <span class="text-[11px] text-[#64748B] dark:text-[#94A3B8] block">Drum Industri</span>
                </div>
            </a>
            <button 
                type="button" 
                @click="mobileSidebarOpen = false"
                class="p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            >
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>

        <!-- Menu Links -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
            @if (auth()->user()->isAdminKantor())
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('admin.dashboard') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Dashboard</span>
                </a>

                <a 
                    href="{{ route('admin.drum-batches') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('admin.drum-batches') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="package" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.drum-batches') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Data Drum</span>
                </a>

                <a 
                    href="{{ route('admin.drum-process') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('admin.drum-process') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="layers" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.drum-process') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Proses Gudang</span>
                </a>

                <a 
                    href="{{ route('admin.reports') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('admin.reports*') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="file-text" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.reports*') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Laporan</span>
                </a>
            @elseif (auth()->user()->isSatpam())
                <a 
                    href="{{ route('satpam.dashboard') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('satpam.dashboard') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0 {{ request()->routeIs('satpam.dashboard') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Dashboard</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-masuk') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('satpam.kendaraan-masuk') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="log-in" class="w-4 h-4 shrink-0 {{ request()->routeIs('satpam.kendaraan-masuk') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Catat Truk Masuk</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-aktif') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('satpam.kendaraan-aktif') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="truck" class="w-4 h-4 shrink-0 {{ request()->routeIs('satpam.kendaraan-aktif') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Kendaraan Aktif</span>
                </a>

                <a 
                    href="{{ route('satpam.riwayat') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('satpam.riwayat') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="history" class="w-4 h-4 shrink-0 {{ request()->routeIs('satpam.riwayat') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Riwayat Kendaraan</span>
                </a>
            @elseif (auth()->user()->isAdminKeuangan())
                <div class="pt-2.5 pb-1 px-3">
                    <span class="text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase block select-none">Ringkasan</span>
                </div>

                <a 
                    href="{{ route('keuangan.dashboard') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('keuangan.dashboard') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0 {{ request()->routeIs('keuangan.dashboard') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Dashboard Keuangan</span>
                </a>

                <div class="pt-3 pb-1 px-3">
                    <span class="text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase block select-none">Arus Barang & Mitra</span>
                </div>

                <a 
                    href="{{ route('keuangan.barang-masuk') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('keuangan.barang-masuk') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="arrow-down-left" class="w-4 h-4 shrink-0 text-emerald-500" />
                    <span>Data Barang Masuk</span>
                </a>

                <a 
                    href="{{ route('keuangan.barang-keluar') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('keuangan.barang-keluar') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="arrow-up-right" class="w-4 h-4 shrink-0 text-blue-500" />
                    <span>Data Barang Keluar</span>
                </a>

                <a 
                    href="{{ route('keuangan.suppliers') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('keuangan.suppliers') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="truck" class="w-4 h-4 shrink-0 {{ request()->routeIs('keuangan.suppliers') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Data Supplier</span>
                </a>

                <div class="pt-3 pb-1 px-3">
                    <span class="text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase block select-none">Biaya & Transaksi</span>
                </div>

                <!-- Biaya Operasional (Dropdown Collapsible) -->
                <div x-data="{ openBiaya: {{ request()->routeIs('keuangan.operasional*', 'keuangan.biaya-kendaraan*', 'keuangan.laporan-cetak*', 'keuangan.riwayat-keuangan*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button 
                        type="button" 
                        @click="openBiaya = !openBiaya"
                        class="w-full flex items-center justify-between px-3 py-2.5 text-xs transition-colors rounded-r-lg cursor-pointer {{ request()->routeIs('keuangan.operasional*', 'keuangan.biaya-kendaraan*', 'keuangan.laporan-cetak*', 'keuangan.riwayat-keuangan*') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        <div class="flex items-center gap-3">
                            <x-icon name="banknote" class="w-4 h-4 shrink-0 text-amber-500" />
                            <span>Biaya Operasional</span>
                        </div>
                        <svg 
                            class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400 dark:text-slate-500" 
                            :class="openBiaya ? 'rotate-180 text-slate-700 dark:text-slate-200' : ''" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openBiaya" x-cloak class="pl-4 space-y-1 pt-1">
                        <a 
                            href="{{ route('keuangan.operasional') }}" 
                            wire:navigate
                            @click="mobileSidebarOpen = false"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-r-lg text-[11px] transition-colors {{ request()->routeIs('keuangan.operasional') ? 'border-l-2 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-2 border-transparent text-[#64748B] dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' }}"
                        >
                            <x-icon name="wallet" class="w-3.5 h-3.5 shrink-0" />
                            <span>Catatan Keuangan</span>
                        </a>

                        <a 
                            href="{{ route('keuangan.biaya-kendaraan') }}" 
                            wire:navigate
                            @click="mobileSidebarOpen = false"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-r-lg text-[11px] transition-colors {{ request()->routeIs('keuangan.biaya-kendaraan') ? 'border-l-2 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-2 border-transparent text-[#64748B] dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' }}"
                        >
                            <x-icon name="wrench" class="w-3.5 h-3.5 shrink-0" />
                            <span>Biaya Mobil</span>
                        </a>

                        <a 
                            href="{{ route('keuangan.laporan-cetak') }}" 
                            wire:navigate
                            @click="mobileSidebarOpen = false"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-r-lg text-[11px] transition-colors {{ request()->routeIs('keuangan.laporan-cetak') ? 'border-l-2 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-2 border-transparent text-[#64748B] dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' }}"
                        >
                            <x-icon name="printer" class="w-3.5 h-3.5 shrink-0" />
                            <span>Laporan untuk di Cetak</span>
                        </a>

                        <a 
                            href="{{ route('keuangan.riwayat-keuangan') }}" 
                            wire:navigate
                            @click="mobileSidebarOpen = false"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-r-lg text-[11px] transition-colors {{ request()->routeIs('keuangan.riwayat-keuangan') ? 'border-l-2 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-2 border-transparent text-[#64748B] dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' }}"
                        >
                            <x-icon name="receipt" class="w-3.5 h-3.5 shrink-0" />
                            <span>Riwayat Keuangan</span>
                        </a>
                    </div>
                </div>

                <a 
                    href="{{ route('keuangan.tagihan') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('keuangan.tagihan') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="file-text" class="w-4 h-4 shrink-0 {{ request()->routeIs('keuangan.tagihan') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Riwayat Tagihan</span>
                </a>

                <div class="pt-3 pb-1 px-3">
                    <span class="text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase block select-none">Operasional Armada</span>
                </div>

                <a 
                    href="{{ route('keuangan.driver-truk') }}" 
                    wire:navigate
                    @click="mobileSidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 text-xs transition-colors {{ request()->routeIs('keuangan.driver-truk*', 'keuangan.karyawan*') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <x-icon name="truck" class="w-4 h-4 shrink-0 {{ request()->routeIs('keuangan.driver-truk*', 'keuangan.karyawan*') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                    <span>Data Driver Truk</span>
                </a>
            @endif
        </nav>

        <!-- Bottom Profile & Logout -->
        <div class="p-3 border-t border-[#E2E8F0] dark:border-[#334155] space-y-1">
            <a 
                href="{{ route('profile') }}" 
                wire:navigate
                @click="mobileSidebarOpen = false"
                class="flex items-center gap-3 px-3 py-2 text-xs transition-colors {{ request()->routeIs('profile') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
            >
                <x-icon name="user" class="w-4 h-4 shrink-0 {{ request()->routeIs('profile') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}" />
                <span>Pengguna</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full" x-data="{ loggingOut: false }" @submit="loggingOut = true">
                @csrf
                <button 
                    type="submit" 
                    :disabled="loggingOut"
                    class="w-full flex items-center gap-3 px-3 py-2 text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 border-l-4 border-transparent rounded-r-lg transition-colors cursor-pointer disabled:opacity-60"
                >
                    <template x-if="!loggingOut">
                        <x-icon name="log-out" class="w-4 h-4" />
                    </template>
                    <template x-if="loggingOut">
                        <svg class="animate-spin w-4 h-4 text-rose-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </template>
                    <span x-text="loggingOut ? 'Mengeluarkan...' : 'Keluar'">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Desktop Fixed Sidebar (Full 240px <-> Mini Icon-Only 68px Rail, Zero-Jump Fixed Icon Column) -->
    <aside 
        class="pkdi-sidebar hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 lg:left-0 z-30 bg-white dark:bg-[#1E293B] border-r border-[#E2E8F0] dark:border-[#334155] text-slate-700 dark:text-slate-200 shadow-[1px_0_3px_rgba(0,0,0,0.02)] overflow-x-hidden"
    >
        <!-- Brand Header -->
        <div class="h-16 px-2.5 border-b border-[#E2E8F0] dark:border-[#334155] flex items-center">
            <a href="{{ route('dashboard') }}" wire:navigate title="Pulo Kencana Drum Industri" class="flex items-center min-w-0">
                <div class="w-11 h-10 flex items-center justify-center shrink-0">
                    <div class="w-8 h-8 rounded-lg bg-[#2563EB] flex items-center justify-center p-1.5 shrink-0 shadow-xs">
                        <x-application-logo class="w-full h-full text-white" />
                    </div>
                </div>
                <div class="pkdi-sidebar-text leading-tight whitespace-nowrap overflow-hidden pl-1">
                    <span class="text-sm font-bold text-[#0F172A] dark:text-[#F8FAFC] block">Pulo Kencana</span>
                    <span class="text-[11px] text-[#64748B] dark:text-[#94A3B8] block">Drum Industri</span>
                </div>
            </a>
        </div>

        <!-- Navigation Links (with Sliding Tab Pill Indicator) -->
        <nav 
            x-ref="desktopNav"
            x-data="{
                pillTop: 0,
                pillHeight: 36,
                pillVisible: false,
                pillReady: false,
                initSlidingTabs() {
                    const nav = this.$refs.desktopNav;
                    if (!nav) return;
                    const items = Array.from(nav.querySelectorAll('[data-tab-item]'));
                    items.forEach((el, idx) => el.dataset.tabIndex = idx);
                    const activeEl = nav.querySelector('[data-tab-active=true]');
                    if (!activeEl) {
                        this.pillVisible = false;
                        return;
                    }
                    const targetTop = activeEl.offsetTop;
                    const targetHeight = activeEl.offsetHeight;
                    const activeIdx = parseInt(activeEl.dataset.tabIndex || '0', 10);

                    if (typeof window.__pkdiLastTabTop === 'number' && window.__pkdiLastTabTop !== targetTop) {
                        this.pillReady = false;
                        this.pillTop = window.__pkdiLastTabTop;
                        this.pillHeight = window.__pkdiLastTabHeight || targetHeight;
                        this.pillVisible = true;
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                this.pillReady = true;
                                this.pillTop = targetTop;
                                this.pillHeight = targetHeight;
                            });
                        });
                    } else {
                        this.pillTop = targetTop;
                        this.pillHeight = targetHeight;
                        this.pillVisible = true;
                        requestAnimationFrame(() => { this.pillReady = true; });
                    }

                    window.__pkdiLastTabTop = targetTop;
                    window.__pkdiLastTabHeight = targetHeight;
                    window.__pkdiLastTabIndex = activeIdx;
                },
                onTabClick(event) {
                    const el = event.currentTarget;
                    if (!el) return;
                    const newIdx = parseInt(el.dataset.tabIndex || '0', 10);

                    this.pillReady = true;
                    this.pillVisible = true;
                    this.pillTop = el.offsetTop;
                    this.pillHeight = el.offsetHeight;
                    window.__pkdiLastTabTop = el.offsetTop;
                    window.__pkdiLastTabHeight = el.offsetHeight;
                    window.__pkdiLastTabIndex = newIdx;
                }
            }"
            x-init="initSlidingTabs()"
            class="relative flex-1 px-2 py-3 space-y-1 overflow-y-auto overflow-x-hidden"
        >
            <!-- Floating Sliding Tab Indicator Pill -->
            <div 
                x-show="pillVisible"
                :class="pillReady ? 'pkdi-sliding-tab-pill' : 'pkdi-sliding-tab-pill !transition-none'"
                :style="`transform: translate3d(0, ${pillTop}px, 0); height: ${pillHeight}px;`"
            ></div>

            @if (auth()->user()->isAdminKantor())
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Dashboard"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('admin.dashboard') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Dashboard</span>
                </a>

                <a 
                    href="{{ route('admin.drum-batches') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('admin.drum-batches') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Data Drum"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('admin.drum-batches') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="package" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.drum-batches') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Data Drum</span>
                </a>

                <a 
                    href="{{ route('admin.drum-process') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('admin.drum-process') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Proses Gudang"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('admin.drum-process') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="layers" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.drum-process') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Proses Gudang</span>
                </a>

                <a 
                    href="{{ route('admin.reports') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('admin.reports*') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Laporan"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('admin.reports*') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="file-text" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.reports*') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Laporan</span>
                </a>
            @elseif (auth()->user()->isSatpam())
                <a 
                    href="{{ route('satpam.dashboard') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('satpam.dashboard') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Dashboard"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('satpam.dashboard') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0 {{ request()->routeIs('satpam.dashboard') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Dashboard</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-masuk') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('satpam.kendaraan-masuk') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Catat Truk Masuk"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('satpam.kendaraan-masuk') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="log-in" class="w-4 h-4 shrink-0 {{ request()->routeIs('satpam.kendaraan-masuk') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Catat Truk Masuk</span>
                </a>

                <a 
                    href="{{ route('satpam.kendaraan-aktif') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('satpam.kendaraan-aktif') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Kendaraan Aktif"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('satpam.kendaraan-aktif') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="truck" class="w-4 h-4 shrink-0 {{ request()->routeIs('satpam.kendaraan-aktif') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Kendaraan Aktif</span>
                </a>

                <a 
                    href="{{ route('satpam.riwayat') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('satpam.riwayat') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Riwayat Kendaraan"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('satpam.riwayat') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="history" class="w-4 h-4 shrink-0 {{ request()->routeIs('satpam.riwayat') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Riwayat Kendaraan</span>
                </a>
            @elseif (auth()->user()->isAdminKeuangan())
                <div class="pt-2 pb-1 px-3">
                    <span class="pkdi-sidebar-section-title pkdi-sidebar-text text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase block select-none">Ringkasan</span>
                    <div class="pkdi-sidebar-section-divider border-t border-slate-200 dark:border-slate-700/60 my-1"></div>
                </div>

                <a 
                    href="{{ route('keuangan.dashboard') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('keuangan.dashboard') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Dashboard Keuangan"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('keuangan.dashboard') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="layout-dashboard" class="w-4 h-4 shrink-0 {{ request()->routeIs('keuangan.dashboard') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Dashboard Keuangan</span>
                </a>

                <div class="pt-3 pb-1 px-3">
                    <span class="pkdi-sidebar-section-title pkdi-sidebar-text text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase block select-none">Arus Barang & Mitra</span>
                    <div class="pkdi-sidebar-section-divider border-t border-slate-200 dark:border-slate-700/60 my-1"></div>
                </div>

                <a 
                    href="{{ route('keuangan.barang-masuk') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('keuangan.barang-masuk') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Data Barang Masuk"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('keuangan.barang-masuk') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="arrow-down-left" class="w-4 h-4 shrink-0 text-emerald-500" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Data Barang Masuk</span>
                </a>

                <a 
                    href="{{ route('keuangan.barang-keluar') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('keuangan.barang-keluar') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Data Barang Keluar"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('keuangan.barang-keluar') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="arrow-up-right" class="w-4 h-4 shrink-0 text-blue-500" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Data Barang Keluar</span>
                </a>

                <a 
                    href="{{ route('keuangan.suppliers') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('keuangan.suppliers') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Data Supplier"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('keuangan.suppliers') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="truck" class="w-4 h-4 shrink-0 {{ request()->routeIs('keuangan.suppliers') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Data Supplier</span>
                </a>

                <div class="pt-3 pb-1 px-3">
                    <span class="pkdi-sidebar-section-title pkdi-sidebar-text text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase block select-none">Biaya & Transaksi</span>
                    <div class="pkdi-sidebar-section-divider border-t border-slate-200 dark:border-slate-700/60 my-1"></div>
                </div>

                <!-- Biaya Operasional (Dropdown Collapsible) -->
                <div x-data="{ openBiaya: {{ request()->routeIs('keuangan.operasional*', 'keuangan.biaya-kendaraan*', 'keuangan.laporan-cetak*', 'keuangan.riwayat-keuangan*') ? 'true' : 'false' }} }" class="relative z-10 space-y-1">
                    <button 
                        type="button" 
                        data-tab-item
                        data-tab-active="{{ request()->routeIs('keuangan.operasional*', 'keuangan.biaya-kendaraan*', 'keuangan.laporan-cetak*', 'keuangan.riwayat-keuangan*') ? 'true' : 'false' }}"
                        title="Biaya Operasional"
                        @click="if (!desktopSidebarOpen) { expandDesktopSidebar(); openBiaya = true; } else { openBiaya = !openBiaya; }"
                        class="w-full flex items-center justify-between py-2 pr-2 text-xs transition-colors duration-150 rounded-r-lg cursor-pointer {{ request()->routeIs('keuangan.operasional*', 'keuangan.biaya-kendaraan*', 'keuangan.laporan-cetak*', 'keuangan.riwayat-keuangan*') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        <div class="flex items-center min-w-0">
                            <span class="w-11 flex items-center justify-center shrink-0">
                                <x-icon name="banknote" class="w-4 h-4 shrink-0 text-amber-500" />
                            </span>
                            <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Biaya Operasional</span>
                        </div>
                        <svg 
                            class="pkdi-sidebar-text w-3.5 h-3.5 transition-transform duration-200 text-slate-400 dark:text-slate-500 shrink-0" 
                            :class="openBiaya ? 'rotate-180 text-slate-700 dark:text-slate-200' : ''" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openBiaya && desktopSidebarOpen" x-cloak class="pl-5 space-y-1 pt-1">
                        <a 
                            href="{{ route('keuangan.operasional') }}" 
                            wire:navigate
                            data-tab-item
                            @click="onTabClick($event)"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-r-lg text-[11px] transition-colors {{ request()->routeIs('keuangan.operasional') ? 'border-l-2 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-2 border-transparent text-[#64748B] dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' }}"
                        >
                            <x-icon name="wallet" class="w-3.5 h-3.5 shrink-0" />
                            <span class="truncate">Catatan Keuangan</span>
                        </a>

                        <a 
                            href="{{ route('keuangan.biaya-kendaraan') }}" 
                            wire:navigate
                            data-tab-item
                            @click="onTabClick($event)"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-r-lg text-[11px] transition-colors {{ request()->routeIs('keuangan.biaya-kendaraan') ? 'border-l-2 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-2 border-transparent text-[#64748B] dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' }}"
                        >
                            <x-icon name="wrench" class="w-3.5 h-3.5 shrink-0" />
                            <span class="truncate">Biaya Mobil</span>
                        </a>

                        <a 
                            href="{{ route('keuangan.laporan-cetak') }}" 
                            wire:navigate
                            data-tab-item
                            @click="onTabClick($event)"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-r-lg text-[11px] transition-colors {{ request()->routeIs('keuangan.laporan-cetak') ? 'border-l-2 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-2 border-transparent text-[#64748B] dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' }}"
                        >
                            <x-icon name="printer" class="w-3.5 h-3.5 shrink-0" />
                            <span class="truncate">Laporan untuk di Cetak</span>
                        </a>

                        <a 
                            href="{{ route('keuangan.riwayat-keuangan') }}" 
                            wire:navigate
                            data-tab-item
                            @click="onTabClick($event)"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-r-lg text-[11px] transition-colors {{ request()->routeIs('keuangan.riwayat-keuangan') ? 'border-l-2 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold' : 'border-l-2 border-transparent text-[#64748B] dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' }}"
                        >
                            <x-icon name="receipt" class="w-3.5 h-3.5 shrink-0" />
                            <span class="truncate">Riwayat Keuangan</span>
                        </a>
                    </div>
                </div>

                <a 
                    href="{{ route('keuangan.tagihan') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('keuangan.tagihan') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Riwayat Tagihan"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('keuangan.tagihan') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="file-text" class="w-4 h-4 shrink-0 {{ request()->routeIs('keuangan.tagihan') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Riwayat Tagihan</span>
                </a>

                <div class="pt-3 pb-1 px-3">
                    <span class="pkdi-sidebar-section-title pkdi-sidebar-text text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase block select-none">Operasional Armada</span>
                    <div class="pkdi-sidebar-section-divider border-t border-slate-200 dark:border-slate-700/60 my-1"></div>
                </div>

                <a 
                    href="{{ route('keuangan.driver-truk') }}" 
                    wire:navigate
                    data-tab-item
                    data-tab-active="{{ request()->routeIs('keuangan.driver-truk*', 'keuangan.karyawan*') ? 'true' : 'false' }}"
                    @click="onTabClick($event)"
                    title="Data Driver Truk"
                    class="relative z-10 flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('keuangan.driver-truk*', 'keuangan.karyawan*') ? 'border-l-4 border-transparent text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100/80 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <x-icon name="truck" class="w-4 h-4 shrink-0 {{ request()->routeIs('keuangan.driver-truk*', 'keuangan.karyawan*') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Data Driver Truk</span>
                </a>
            @endif
        </nav>

        <!-- Bottom Profile & Logout -->
        <div class="p-2 border-t border-[#E2E8F0] dark:border-[#334155] space-y-1">
            <a 
                href="{{ route('profile') }}" 
                wire:navigate
                title="Pengguna"
                class="flex items-center py-2 text-xs transition-colors duration-150 {{ request()->routeIs('profile') ? 'border-l-4 border-[#2563EB] bg-blue-50/80 dark:bg-blue-900/40 text-[#2563EB] dark:text-blue-400 font-semibold rounded-r-lg' : 'border-l-4 border-transparent text-[#475569] dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white rounded-r-lg' }}"
            >
                <span class="w-11 flex items-center justify-center shrink-0">
                    <x-icon name="user" class="w-4 h-4 shrink-0 {{ request()->routeIs('profile') ? 'text-[#2563EB] dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" />
                </span>
                <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate">Pengguna</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full" x-data="{ loggingOut: false }" @submit="loggingOut = true">
                @csrf
                <button 
                    type="submit" 
                    title="Keluar"
                    :disabled="loggingOut"
                    class="w-full flex items-center py-2 text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 border-l-4 border-transparent rounded-r-lg transition-colors duration-150 cursor-pointer disabled:opacity-60"
                >
                    <span class="w-11 flex items-center justify-center shrink-0">
                        <template x-if="!loggingOut">
                            <x-icon name="log-out" class="w-4 h-4 shrink-0" />
                        </template>
                        <template x-if="loggingOut">
                            <svg class="animate-spin w-4 h-4 text-rose-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </template>
                    </span>
                    <span class="pkdi-sidebar-text whitespace-nowrap overflow-hidden truncate" x-text="loggingOut ? 'Mengeluarkan...' : 'Keluar'">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Top App Bar (Fixed Header at Top of Viewport — Never Scrolls Away) -->
    <header 
        class="pkdi-content-offset fixed top-0 inset-x-0 z-20 bg-white dark:bg-[#1E293B] border-b border-[#E2E8F0] dark:border-[#334155] transition-colors duration-200"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between gap-4">
            <!-- Left: Hamburger Toggle (Desktop & Mobile) -->
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    @click="window.innerWidth >= 1024 ? toggleDesktopSidebar() : mobileSidebarOpen = true" 
                    class="p-2 -ml-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    title="Buka / Kecilkan Menu Sidebar"
                >
                    <x-icon name="menu" class="w-5 h-5" />
                </button>
            </div>

            <!-- Right: Dark Mode Toggle & User Dropdown -->
            <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
                <!-- Dark Mode Toggle Button -->
                <div
                    x-data="{
                        isDark: document.documentElement.classList.contains('dark'),
                        toggleTheme() {
                            this.isDark = !this.isDark;
                            if (this.isDark) {
                                document.documentElement.classList.add('dark');
                                localStorage.setItem('pkdi_theme', 'dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                                localStorage.setItem('pkdi_theme', 'light');
                            }
                            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: this.isDark } }));
                        }
                    }"
                    @theme-changed.window="isDark = $event.detail.dark"
                >
                    <button 
                        type="button" 
                        @click="toggleTheme()" 
                        class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center"
                        :title="isDark ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
                        aria-label="Toggle Theme"
                    >
                        <template x-if="isDark">
                            <x-icon name="sun" class="w-4 h-4 text-amber-400" />
                        </template>
                        <template x-if="!isDark">
                            <x-icon name="moon" class="w-4 h-4 text-slate-600" />
                        </template>
                    </button>
                </div>

                <!-- User Dropdown -->
                <div
                    x-data="{
                        userMenuOpen: false,
                        navName: @js(auth()->user()->name),
                        navEmail: @js(auth()->user()->email),
                        navInitials: @js(strtoupper(substr(auth()->user()->name, 0, 2)))
                    }"
                    @profile-updated.window="
                        if ($event.detail.name) navName = $event.detail.name;
                        if ($event.detail.email) navEmail = $event.detail.email;
                        if ($event.detail.initials) navInitials = $event.detail.initials;
                    "
                    class="relative"
                >
                    <button 
                        @click="userMenuOpen = !userMenuOpen" 
                        type="button"
                        class="flex items-center gap-2.5 py-1 px-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/80 transition focus:outline-none cursor-pointer"
                    >
                        <div class="w-8 h-8 rounded-full bg-[#0F172A] dark:bg-blue-600 text-white flex items-center justify-center font-semibold text-xs shrink-0" x-text="navInitials">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <div class="text-xs font-semibold text-[#0F172A] dark:text-[#F8FAFC] leading-tight" x-text="navName">{{ auth()->user()->name }}</div>
                            <div class="text-[11px] text-[#64748B] dark:text-[#94A3B8] leading-tight">
                                @if (auth()->user()->isAdminKantor())
                                    Admin Gudang
                                @elseif (auth()->user()->isAdminKeuangan())
                                    Admin Keuangan
                                @else
                                    Satpam
                                @endif
                            </div>
                        </div>
                        <x-icon name="chevron-down" class="w-3.5 h-3.5 text-[#64748B] dark:text-[#94A3B8]" />
                    </button>

                    <!-- Dropdown -->
                    <div 
                        x-show="userMenuOpen" 
                        @click.outside="userMenuOpen = false"
                        x-cloak
                        class="absolute right-0 mt-1.5 w-48 bg-white dark:bg-[#1E293B] rounded-xl shadow-lg border border-[#E2E8F0] dark:border-[#334155] py-1 z-50 divide-y divide-slate-100 dark:divide-slate-700/60"
                    >
                        <div class="px-3.5 py-2">
                            <p class="text-xs font-semibold text-[#0F172A] dark:text-[#F8FAFC] truncate" x-text="navName">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-[#64748B] dark:text-[#94A3B8] truncate" x-text="navEmail">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <a 
                                href="{{ route('profile') }}" 
                                wire:navigate
                                class="flex items-center gap-2 px-3.5 py-1.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800"
                            >
                                <x-icon name="user" class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" />
                                <span>Profil Pengguna</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="w-full" x-data="{ loggingOut: false }" @submit="loggingOut = true">
                                @csrf
                                <button 
                                    type="submit" 
                                    :disabled="loggingOut"
                                    class="w-full flex items-center gap-2 px-3.5 py-1.5 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-left cursor-pointer disabled:opacity-60"
                                >
                                    <template x-if="!loggingOut">
                                        <x-icon name="log-out" class="w-3.5 h-3.5 text-rose-500 shrink-0" />
                                    </template>
                                    <template x-if="loggingOut">
                                        <svg class="animate-spin w-3.5 h-3.5 text-rose-600 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </template>
                                    <span x-text="loggingOut ? 'Mengeluarkan...' : 'Keluar'">Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>
