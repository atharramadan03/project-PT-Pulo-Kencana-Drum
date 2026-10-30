<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                    Profil Pengguna
                </h1>
                <p class="text-xs text-[#64748B] mt-0.5">
                    Kelola informasi identitas akun dan ganti password baru
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-[#E2E8F0] text-xs font-medium text-[#0F172A]">
                <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                <span>{{ auth()->user()->isAdminKantor() ? 'Admin Kantor' : (auth()->user()->isAdminKeuangan() ? 'Admin Keuangan' : 'Petugas Satpam') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Ringkasan Identitas Pengguna -->
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-full bg-[#2563EB] text-white flex items-center justify-center font-bold text-sm shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-sm font-bold text-[#0F172A]">{{ auth()->user()->name }}</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-[#2563EB]">
                            {{ auth()->user()->isAdminKantor() ? 'Admin Kantor' : (auth()->user()->isAdminKeuangan() ? 'Admin Keuangan' : 'Satpam') }}
                        </span>
                    </div>
                    <p class="text-xs text-[#64748B] mt-0.5">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 text-xs">
                <div class="px-3 py-1.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0]">
                    <span class="text-[#64748B]">Status Akun:</span>
                    <span class="font-semibold text-[#166534] ml-1">{{ ucfirst(auth()->user()->status_akun ?? 'Aktif') }}</span>
                </div>
            </div>
        </div>

        <!-- Grid 2 Kolom: Informasi Profil & Ganti Password Baru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
            <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
                <livewire:profile.update-profile-information-form />
            </div>

            <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <!-- Hapus Akun -->
        <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
            <livewire:profile.delete-user-form />
        </div>
    </div>
</x-app-layout>
