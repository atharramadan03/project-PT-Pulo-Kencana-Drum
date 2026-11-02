<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <div class="px-5 py-4 border-b border-[#E2E8F0] flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#2563EB] flex items-center justify-center shrink-0">
            <x-icon name="user" class="w-4 h-4" />
        </div>
        <div>
            <h2 class="text-sm font-bold text-[#0F172A]">
                Informasi Profil
            </h2>
            <p class="text-xs text-[#64748B]">
                Perbarui nama pengguna dan alamat email akun Anda
            </p>
        </div>
    </div>

    <form wire:submit="updateProfileInformation" class="p-5 space-y-4">
        <div>
            <label for="name" class="block text-xs font-medium text-[#0F172A] mb-1">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <input
                wire:model="name"
                id="name"
                name="name"
                type="text"
                class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-xs font-medium text-[#0F172A] mb-1">
                Alamat Email <span class="text-rose-500">*</span>
            </label>
            <input
                wire:model="email"
                id="email"
                name="email"
                type="email"
                class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-xs text-[#64748B]">
                        Alamat email belum diverifikasi.
                        <button wire:click.prevent="sendVerification" class="underline text-[#2563EB] hover:text-[#1D4ED8] font-medium">
                            Kirim ulang tautan verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1.5 font-medium text-xs text-[#166534]">
                            Tautan verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="pt-3 border-t border-[#E2E8F0] flex items-center justify-between gap-3">
            <x-action-message class="text-xs font-medium text-[#166534] inline-flex items-center gap-1.5 bg-green-50 px-2.5 py-1 rounded-md border border-green-200" on="profile-updated">
                <x-icon name="circle-check" class="w-3.5 h-3.5 text-[#166534]" />
                <span>Perubahan profil berhasil disimpan!</span>
            </x-action-message>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="updateProfileInformation"
                class="ml-auto px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 disabled:opacity-75 disabled:cursor-not-allowed"
            >
                <svg wire:loading wire:target="updateProfileInformation" class="animate-spin -ml-0.5 w-3.5 h-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="updateProfileInformation">Simpan Profil</span>
                <span wire:loading wire:target="updateProfileInformation">Menyimpan...</span>
            </button>
        </div>
    </form>
</section>
