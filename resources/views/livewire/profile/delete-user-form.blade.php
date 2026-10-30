<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-sm font-bold text-[#0F172A]">
            Hapus Akun Pengguna
        </h2>
        <p class="text-xs text-[#64748B] mt-0.5">
            Penghapusan akun bersifat permanen dan hanya dilakukan apabila akun sudah tidak digunakan.
        </p>
    </div>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-3.5 py-2 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition shrink-0 cursor-pointer"
    >
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-5 space-y-4">
            <div>
                <h2 class="text-base font-bold text-[#0F172A]">
                    Konfirmasi Hapus Akun
                </h2>
                <p class="mt-1 text-xs text-[#64748B]">
                    Masukkan password akun Anda untuk mengonfirmasi penghapusan akun secara permanen.
                </p>
            </div>

            <div>
                <label for="password" class="block text-xs font-medium text-[#0F172A] mb-1">Password</label>
                <input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                    placeholder="Masukkan password"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="pt-3 border-t border-[#E2E8F0] flex justify-end gap-2">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2 rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] text-xs font-medium hover:bg-slate-50 transition cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 rounded-lg bg-[#EF4444] hover:bg-red-600 text-white text-xs font-semibold transition cursor-pointer"
                >
                    Hapus Permanen
                </button>
            </div>
        </form>
    </x-modal>
</section>
