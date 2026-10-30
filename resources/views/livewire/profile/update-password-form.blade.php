<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user without requiring the old password.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section x-data="{ showNewPassword: false, showConfirmPassword: false }">
    <div class="px-5 py-4 border-b border-[#E2E8F0] flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-amber-50 text-[#F59E0B] flex items-center justify-center shrink-0">
            <x-icon name="lock" class="w-4 h-4" />
        </div>
        <div>
            <h2 class="text-sm font-bold text-[#0F172A]">
                Ganti Password Baru
            </h2>
            <p class="text-xs text-[#64748B]">
                Buat password baru dan konfirmasi password secara langsung
            </p>
        </div>
    </div>

    <form wire:submit="updatePassword" class="p-5 space-y-4">
        <div>
            <label for="update_password_password" class="block text-xs font-medium text-[#0F172A] mb-1">
                Password Baru <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input
                    wire:model="password"
                    id="update_password_password"
                    name="password"
                    :type="showNewPassword ? 'text' : 'password'"
                    placeholder="Masukkan password baru"
                    class="w-full py-2 pl-3 pr-9 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                    autocomplete="new-password"
                    required
                />
                <button
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#64748B] hover:text-[#0F172A]"
                    tabindex="-1"
                >
                    <x-icon name="eye" class="w-3.5 h-3.5" x-show="!showNewPassword" />
                    <x-icon name="eye-off" class="w-3.5 h-3.5" x-show="showNewPassword" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-medium text-[#0F172A] mb-1">
                Konfirmasi Password Baru <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input
                    wire:model="password_confirmation"
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    placeholder="Ulangi password baru"
                    class="w-full py-2 pl-3 pr-9 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                    autocomplete="new-password"
                    required
                />
                <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#64748B] hover:text-[#0F172A]"
                    tabindex="-1"
                >
                    <x-icon name="eye" class="w-3.5 h-3.5" x-show="!showConfirmPassword" />
                    <x-icon name="eye-off" class="w-3.5 h-3.5" x-show="showConfirmPassword" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-3 border-t border-[#E2E8F0] flex items-center justify-between gap-3">
            <x-action-message class="text-xs font-medium text-[#166534]" on="password-updated">
                Password baru berhasil disimpan.
            </x-action-message>

            <button
                type="submit"
                class="ml-auto px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold transition cursor-pointer"
            >
                Simpan Password Baru
            </button>
        </div>
    </form>
</section>
