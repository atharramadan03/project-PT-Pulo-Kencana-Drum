<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="min-h-screen w-full flex flex-col lg:flex-row bg-white">
    <!-- Kolom Kiri: Foto Pabrik & Kantor PT PKDI (fotoPT.png) -->
    <div class="relative w-full lg:w-[56%] xl:w-[58%] min-h-[340px] lg:min-h-screen bg-slate-900 overflow-hidden">
        <img 
            src="{{ asset('images/fotoPT.png') }}" 
            alt="Pabrik & Kantor PT Pulo Kencana Drum Industri" 
            class="w-full h-full object-cover object-center"
        />
    </div>

    <!-- Kolom Kanan: Panel Form Pemulihan Kata Sandi -->
    <div class="w-full lg:w-[44%] xl:w-[42%] min-h-screen flex flex-col justify-center px-6 sm:px-12 md:px-16 lg:px-10 xl:px-16 py-10 sm:py-12 bg-white">
        <div class="max-w-md w-full mx-auto">
            <!-- Tautan Kembali -->
            <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-semibold text-[#64748B] hover:text-[#0284C7] transition mb-6">
                <x-icon name="arrow-right" class="w-3.5 h-3.5 rotate-180" />
                <span>Kembali ke Halaman Login</span>
            </a>

            <!-- Brand Icon Box -->
            <div class="mb-5 flex items-center">
                <div class="w-12 h-12 rounded-xl bg-[#0F172A] p-2 flex items-center justify-center shadow-md">
                    <x-application-logo class="w-8 h-8" />
                </div>
            </div>

            <!-- Judul & Keterangan -->
            <h1 class="text-2xl sm:text-[28px] font-bold text-[#0284C7] tracking-tight leading-tight">
                Pemulihan Kata Sandi
            </h1>
            <p class="text-[#0F172A] font-bold text-base sm:text-lg mt-1 mb-2">
                Sistem Informasi Operasional Gudang
            </p>
            <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed mb-6">
                Lupa kata sandi Anda? Masukkan alamat email akun Anda. Kami akan mengirimkan tautan untuk membuat kata sandi baru.
            </p>

            <!-- Status Sesi / Notifikasi Berhasil -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Form Permintaan Link Reset -->
            <form wire:submit="sendPasswordResetLink" class="space-y-4">
                <!-- Field Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Alamat Email
                    </label>
                    <input 
                        wire:model="email" 
                        id="email" 
                        type="email" 
                        name="email" 
                        required 
                        autofocus 
                        placeholder="Masukkan email akun terdaftar Anda"
                        class="w-full px-4 py-2.5 sm:py-3 rounded-lg border border-[#E2E8F0] text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#0284C7] focus:ring-1 focus:ring-[#0284C7] transition outline-none"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-600" />
                </div>

                <!-- Tombol Kirim Tautan -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 rounded-lg bg-[#0284C7] hover:bg-[#0369A1] active:bg-[#075985] text-white font-semibold text-sm sm:text-base shadow-sm transition flex items-center justify-center gap-2"
                    >
                        <span>Kirim Tautan Reset Password</span>
                        <span wire:loading wire:target="sendPasswordResetLink" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    </button>
                </div>

                <!-- Tombol Batal / Kembali ke Login -->
                <div class="pt-1">
                    <a 
                        href="{{ route('login') }}" 
                        wire:navigate
                        class="w-full py-2.5 px-3 rounded-lg bg-[#334E84] hover:bg-[#283E6B] text-white font-medium text-xs sm:text-sm text-center transition flex items-center justify-center"
                    >
                        Batal & Kembali ke Login
                    </a>
                </div>
            </form>

            <!-- Footer Hak Cipta -->
            <div class="mt-8 pt-4 border-t border-[#E2E8F0] text-center">
                <p class="text-xs text-[#64748B]">© 2026 PT Pulo Kencana Drum Industri. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>
