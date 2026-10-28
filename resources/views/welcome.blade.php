<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'PT PKDI') }} — Sistem Administrasi Operasional Gudang</title>

        <!-- Auto Redirect ke Login jika dibuka -->
        <meta http-equiv="refresh" content="0; url={{ route('login') }}">

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#F7F9FC] text-[#0F172A] min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white rounded-2xl p-8 border border-[#E2E8F0] shadow-lg text-center">
            <div class="w-16 h-16 rounded-2xl bg-[#0F172A] p-3 mx-auto mb-4 flex items-center justify-center shadow-md">
                <x-application-logo class="w-10 h-10" />
            </div>
            <h1 class="text-xl font-bold text-[#0F172A]">PT Pulo Kencana Drum Industri</h1>
            <p class="text-xs text-[#64748B] mt-1 mb-6">Sistem Administrasi Operasional & Pos Gerbang</p>

            <a 
                href="{{ route('login') }}" 
                class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-[#0284C7] hover:bg-[#0369A1] text-white font-semibold text-sm transition"
            >
                <span>Masuk ke Halaman Login</span>
            </a>
        </div>

        <script>
            window.location.href = "{{ route('login') }}";
        </script>
    </body>
</html>
