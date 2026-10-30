<div class="space-y-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Kendaraan Aktif di Area Pabrik
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Daftar kendaraan yang belum memiliki catatan waktu keluar
            </p>
        </div>
        <a
            href="{{ route('satpam.kendaraan-masuk') }}"
            wire:navigate
            class="px-3.5 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Catat Truk Masuk</span>
        </a>
    </div>

    @if (session('success'))
        <div class="p-3.5 rounded-xl bg-[#DCFCE7] border border-green-200 text-[#166534] flex items-center gap-2 text-xs font-medium">
            <x-icon name="circle-check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] flex flex-col sm:flex-row justify-between items-center gap-3">
        <div class="relative w-full sm:w-72">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[#64748B]">
                <x-icon name="search" class="w-3.5 h-3.5" />
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Cari nopol, pengemudi, atau asal..."
                class="w-full pl-9 pr-3 py-1.5 rounded-lg border border-[#E2E8F0] bg-[#F7F9FC] text-xs text-[#0F172A] placeholder:text-[#64748B] focus:bg-white focus:border-[#2563EB] focus:outline-none"
            />
        </div>
        <div class="text-xs text-[#64748B]">
            Total Aktif: <span class="font-semibold text-[#0F172A] tabular-nums">{{ $kendaraanAktif->total() }} Truk</span>
        </div>
    </div>

    <!-- Tabel Daftar Kendaraan Aktif -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] font-semibold">
                    <tr>
                        <th class="py-3 px-4 whitespace-nowrap">No. Kunjungan</th>
                        <th class="py-3 px-4 whitespace-nowrap">No. Polisi</th>
                        <th class="py-3 px-4 whitespace-nowrap">Pengemudi</th>
                        <th class="py-3 px-4 whitespace-nowrap">Asal / Keperluan</th>
                        <th class="py-3 px-4 whitespace-nowrap">Waktu Masuk</th>
                        <th class="py-3 px-4 whitespace-nowrap">Dokumen B3</th>
                        <th class="py-3 px-4 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kendaraanAktif as $visit)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-4 font-mono font-semibold text-[#0F172A] whitespace-nowrap">
                                {{ $visit->nomor_kunjungan }}
                            </td>
                            <td class="py-2.5 px-4 font-mono font-semibold text-[#2563EB] whitespace-nowrap">
                                {{ $visit->nomor_polisi }}
                            </td>
                            <td class="py-2.5 px-4 text-[#0F172A] font-medium whitespace-nowrap">
                                {{ $visit->nama_pengemudi }}
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] max-w-[200px] truncate">
                                {{ $visit->asal_tujuan }} ({{ $visit->keperluan }})
                            </td>
                            <td class="py-2.5 px-4 text-[#64748B] whitespace-nowrap tabular-nums">
                                {{ $visit->waktu_masuk->format('d M Y H:i') }}
                            </td>
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                @if ($visit->status_pemeriksaan_surat_jalan_b3 === 'sesuai')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#166534]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                        Sesuai B3
                                    </span>
                                @elseif ($visit->status_pemeriksaan_surat_jalan_b3 === 'tidak_sesuai')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#FEE2E2] text-[#991B1B]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span>
                                        Tidak Sesuai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-[#475569]">
                                        Non-B3
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                <button
                                    wire:click="bukaModalKeluar({{ $visit->id }})"
                                    type="button"
                                    class="px-2.5 py-1 rounded-lg bg-[#F59E0B] hover:bg-amber-600 text-white font-medium text-xs transition cursor-pointer"
                                >
                                    Catat Keluar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-xs text-[#64748B]">
                                Tidak ada kendaraan aktif saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kendaraanAktif->hasPages())
            <div class="px-4 py-3 border-t border-[#E2E8F0] bg-white">
                {{ $kendaraanAktif->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Catat Keluar (Clean White Style per Reference) -->
    @if ($showModal && $selectedVisit)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden border border-[#E2E8F0]">
                <div class="px-5 py-4 border-b border-[#E2E8F0] flex items-center justify-between">
                    <h3 class="text-base font-bold text-[#0F172A]">Catat Kendaraan Keluar</h3>
                    <button
                        wire:click="tutupModal"
                        type="button"
                        class="text-[#64748B] hover:text-[#0F172A] p-1 rounded-lg"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit="simpanKeluar" class="p-5 space-y-4">
                    <div class="p-3 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">No. Kunjungan</span>
                            <span class="font-mono font-semibold text-[#0F172A]">{{ $selectedVisit->nomor_kunjungan }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">No. Polisi</span>
                            <span class="font-mono font-semibold text-[#2563EB]">{{ $selectedVisit->nomor_polisi }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Pengemudi</span>
                            <span class="font-medium text-[#0F172A]">{{ $selectedVisit->nama_pengemudi }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Waktu Masuk</span>
                            <span class="font-mono text-[#0F172A]">{{ $selectedVisit->waktu_masuk->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">
                            Waktu Keluar <span class="text-rose-500">*</span>
                        </label>
                        <input
                            wire:model="waktu_keluar"
                            type="datetime-local"
                            class="w-full py-2 px-3 rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] text-xs focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                            required
                        />
                        @error('waktu_keluar') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">
                            Catatan Keluar (Opsional)
                        </label>
                        <textarea
                            wire:model="catatan_keluar"
                            rows="2"
                            placeholder="Masukkan keterangan..."
                            class="w-full py-2 px-3 rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] text-xs focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        ></textarea>
                        @error('catatan_keluar') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-[#E2E8F0] flex justify-end gap-2">
                        <button
                            wire:click="tutupModal"
                            type="button"
                            class="px-4 py-2 rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] text-xs font-medium hover:bg-slate-50 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold transition cursor-pointer"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
