<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
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

    <!-- Kolom Kanan: Panel Form Buat Kata Sandi Baru -->
    <div class="w-full lg:w-[44%] xl:w-[42%] min-h-screen flex flex-col justify-center px-6 sm:px-12 md:px-16 lg:px-10 xl:px-16 py-10 sm:py-12 bg-white">
        <div class="max-w-md w-full mx-auto">
            <!-- Brand Icon Box -->
            <div class="mb-5 flex items-center">
                <div class="w-12 h-12 rounded-xl bg-[#0F172A] p-2 flex items-center justify-center shadow-md">
                    <x-application-logo class="w-8 h-8" />
                </div>
            </div>

            <!-- Judul & Keterangan -->
            <h1 class="text-2xl sm:text-[28px] font-bold text-[#0284C7] tracking-tight leading-tight">
                Atur Kata Sandi Baru
            </h1>
            <p class="text-[#0F172A] font-bold text-base sm:text-lg mt-1 mb-2">
                Sistem Informasi Operasional Gudang
            </p>
            <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed mb-6">
                Silakan masukkan kata sandi baru yang aman untuk akun operasional Anda.
            </p>

            <form wire:submit="resetPassword" class="space-y-4">
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
                        autocomplete="username"
                        class="w-full px-4 py-2.5 sm:py-3 rounded-lg border border-[#E2E8F0] text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#0284C7] focus:ring-1 focus:ring-[#0284C7] transition outline-none"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-600" />
                </div>

                <!-- Field Password Baru -->
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Kata Sandi Baru
                    </label>
                    <div class="relative flex items-center">
                        <input 
                            wire:model="password" 
                            id="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
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
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-600" />
                </div>

                <!-- Field Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-[#0F172A] mb-1.5">
                        Konfirmasi Kata Sandi Baru
                    </label>
                    <input 
                        wire:model="password_confirmation" 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        placeholder="Ulangi kata sandi baru"
                        class="w-full px-4 py-2.5 sm:py-3 rounded-lg border border-[#E2E8F0] text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#0284C7] focus:ring-1 focus:ring-[#0284C7] transition outline-none"
                    />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-red-600" />
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 rounded-lg bg-[#0284C7] hover:bg-[#0369A1] active:bg-[#075985] text-white font-semibold text-sm sm:text-base shadow-sm transition flex items-center justify-center gap-2"
                    >
                        <span>Simpan Kata Sandi Baru</span>
                        <span wire:loading wire:target="resetPassword" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>

            <!-- Footer Hak Cipta -->
            <div class="mt-8 pt-4 border-t border-[#E2E8F0] text-center">
                <p class="text-xs text-[#64748B]">© 2026 PT Pulo Kencana Drum Industri. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>
