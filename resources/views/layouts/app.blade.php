<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PT PKDI') }} — Sistem Administrasi Operasional Gudang</title>

        <!-- Google Fonts: Plus Jakarta Sans (Industrial Enterprise Typography) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#0F172A] bg-[#F7F9FC] antialiased min-h-full selection:bg-[#2563EB] selection:text-white">
        <div class="min-h-screen bg-[#F7F9FC] flex flex-col">
            <!-- Enterprise Shell: Sidebar & Top App Bar -->
            <livewire:layout.navigation />

            <!-- Main Content Viewport with offset for Desktop Sidebar -->
            <div class="lg:pl-64 flex flex-col flex-1 min-w-0">
                @if (isset($header))
                    <header class="bg-white border-b border-[#E2E8F0] shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Main Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>

                <!-- Footer Perusahaan Resmi -->
                <footer class="py-4 px-4 sm:px-6 lg:px-8 border-t border-[#E2E8F0] bg-white text-xs text-[#64748B]">
                    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
                        <span>&copy; {{ date('Y') }} PT Pulo Kencana Drum Industri — Sistem Administrasi Operasional Gudang</span>
                        <div class="flex items-center gap-3 text-[11px] text-slate-500 font-medium">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Server Lokal Terhubung
                            </span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
