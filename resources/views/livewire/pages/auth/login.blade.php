<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // Redirect cerdas berdasarkan role pengguna
        $user = auth()->user();
        if ($user && $user->role === 'satpam') {
            $this->redirectIntended(default: route('satpam.dashboard', absolute: false), navigate: true);
        } else {
            $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
        }
    }

}; ?>

<div class="min-h-screen w-full flex flex-col lg:flex-row bg-white">
    <!-- Kolom Kiri: Foto Pabrik & Kantor PT PKDI (fotoPT.png) sesuai reference_login.png -->
    <div class="relative w-full lg:w-[56%] xl:w-[58%] min-h-[340px] lg:min-h-screen bg-slate-900 overflow-hidden">
        <img 
            src="{{ asset('images/fotoPT.png') }}" 
            alt="Pabrik & Kantor PT Pulo Kencana Drum Industri" 
            class="w-full h-full object-cover object-center"
        />
    </div>

    <!-- Kolom Kanan: Panel Form Login Bersih sesuai reference_login.png -->
    <div class="w-full lg:w-[44%] xl:w-[42%] min-h-screen flex flex-col justify-center px-6 sm:px-12 md:px-16 lg:px-10 xl:px-16 py-10 sm:py-12 bg-white">
        <div class="max-w-md w-full mx-auto">
            <!-- Brand Icon Box (Sesuai Logo Box di reference_login.png) -->
            <div class="mb-5 flex items-center">
                <div class="w-12 h-12 rounded-xl bg-[#0F172A] p-2 flex items-center justify-center shadow-md">
                    <x-application-logo class="w-8 h-8" />
                </div>
            </div>

            <!-- Judul & Sub-judul (Tipografi persis reference_login.png) -->
            <h1 class="text-2xl sm:text-[28px] font-bold text-[#0284C7] tracking-tight leading-tight">
                Fasilitas Layanan Administrasi
            </h1>
            <p class="text-[#0F172A] font-bold text-base sm:text-lg mt-1 mb-6">
                Sistem Informasi Operasional Gudang
            </p>

            <!-- Session Status Alert -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Form Login -->
            <form wire:submit="login" class="space-y-4">
                <!-- Field 1: Username / Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Username
                    </label>
                    <input 
                        wire:model="form.email" 
                        id="email" 
                        type="email" 
                        name="email" 
                        required 
                        autofocus 
                        autocomplete="username" 
                        placeholder="Masukkan Email / NIP Anda"
                        class="w-full px-4 py-2.5 sm:py-3 rounded-lg border border-[#E2E8F0] text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#0284C7] focus:ring-1 focus:ring-[#0284C7] transition outline-none"
                    />
                    <x-input-error :messages="$errors->get('form.email')" class="mt-1.5 text-xs text-red-600" />
                </div>

                <!-- Field 2: Password dengan Toggle Visibility (Eye Icon di sisi kanan seperti reference_login.png) -->
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Password
                    </label>
                    <div class="relative flex items-center">
                        <input 
                            wire:model="form.password" 
                            id="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            required 
                            autocomplete="current-password" 
                            placeholder="Masukkan password Anda"
                            class="w-full pl-4 pr-12 py-2.5 sm:py-3 rounded-lg border border-[#E2E8F0] text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#0284C7] focus:ring-1 focus:ring-[#0284C7] transition outline-none"
                        />
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute right-0 inset-y-0 px-3.5 flex items-center justify-center text-[#64748B] hover:text-[#0F172A] border-l border-[#E2E8F0] rounded-r-lg bg-slate-50/50 hover:bg-slate-100 transition"
                            title="Tampilkan / Sembunyikan Password"
                        >
                            <span x-show="!showPassword">
                                <x-icon name="eye" class="w-4 h-4" />
                            </span>
                            <span x-show="showPassword" style="display: none;">
                                <x-icon name="eye-off" class="w-4 h-4" />
                            </span>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-1.5 text-xs text-red-600" />
                </div>

                <!-- Checkbox Remember Me -->
                <div class="flex items-center pt-1">
                    <label for="remember" class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input 
                            wire:model="form.remember" 
                            id="remember" 
                            type="checkbox" 
                            class="w-4 h-4 rounded border-[#E2E8F0] text-[#0284C7] focus:ring-[#0284C7]/20 focus:ring-offset-0" 
                            name="remember"
                        >
                        <span class="text-sm text-[#0F172A]">Remember me</span>
                    </label>
                </div>

                <!-- Tombol Sign In (Besar, Biru Terang sesuai reference_login.png) -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 rounded-lg bg-[#0284C7] hover:bg-[#0369A1] active:bg-[#075985] text-white font-semibold text-sm sm:text-base shadow-sm transition flex items-center justify-center gap-2"
                    >
                        <span>Sign In</span>
                        <span wire:loading wire:target="login" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    </button>
                </div>

                <!-- Tombol Lupa Password -->
                <div class="pt-1">
                    @if (Route::has('password.request'))
                        <a 
                            href="{{ route('password.request') }}" 
                            wire:navigate
                            class="w-full py-2.5 px-3 rounded-lg bg-[#334E84] hover:bg-[#283E6B] text-white font-medium text-xs sm:text-sm text-center transition flex items-center justify-center"
                        >
                            Lupa Password?
                        </a>
                    @else
                        <button 
                            type="button" 
                            @click="alert('Silakan hubungi Administrator IT PKDI untuk bantuan reset password kredensial.')"
                            class="w-full py-2.5 px-3 rounded-lg bg-[#334E84] hover:bg-[#283E6B] text-white font-medium text-xs sm:text-sm text-center transition flex items-center justify-center"
                        >
                            Lupa Password?
                        </button>
                    @endif
                </div>
            </form>

            <!-- Footer Hak Cipta -->
            <div class="mt-8 pt-4 border-t border-[#E2E8F0] text-center">
                <p class="text-xs text-[#64748B]">© 2026 PT Pulo Kencana Drum Industri. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>
