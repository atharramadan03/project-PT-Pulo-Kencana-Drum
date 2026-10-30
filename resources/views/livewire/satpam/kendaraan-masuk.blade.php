<div class="max-w-3xl mx-auto space-y-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Catat Kendaraan Masuk
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Formulir pencatatan kunjungan kendaraan masuk
            </p>
        </div>
        <a
            href="{{ route('satpam.kendaraan-aktif') }}"
            wire:navigate
            class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-50 text-[#0F172A] border border-[#E2E8F0] font-medium text-xs transition inline-flex items-center gap-1.5"
        >
            <x-icon name="truck" class="w-3.5 h-3.5 text-[#64748B]" />
            <span>Kendaraan Aktif</span>
        </a>
    </div>

    <!-- Card Form (Matches Form Data Drum Reference) -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
        <div class="px-5 py-4 border-b border-[#E2E8F0] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="text-base font-bold text-[#0F172A]">Tambah Kunjungan Kendaraan</h2>
            </div>
            <span class="font-mono text-xs font-semibold text-[#2563EB] bg-blue-50 px-2.5 py-1 rounded-lg">
                {{ $nomor_kunjungan }}
            </span>
        </div>

        <form wire:submit="simpan" class="p-5 space-y-4">
            <!-- Baris 1 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                        Nomor Polisi <span class="text-rose-500">*</span>
                    </label>
                    <input
                        wire:model="nomor_polisi"
                        type="text"
                        placeholder="Contoh: B 9123 KDA"
                        class="w-full py-2 px-3 uppercase font-mono text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        required
                    />
                    @error('nomor_polisi') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                        Nama Pengemudi <span class="text-rose-500">*</span>
                    </label>
                    <input
                        wire:model="nama_pengemudi"
                        type="text"
                        placeholder="Masukkan nama pengemudi"
                        class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        required
                    />
                    @error('nama_pengemudi') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 2 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                        Asal / Tujuan Perusahaan <span class="text-rose-500">*</span>
                    </label>
                    <input
                        wire:model="asal_tujuan"
                        type="text"
                        placeholder="Contoh: PT Mulia Chem"
                        class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        required
                    />
                    @error('asal_tujuan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                        Keperluan Kunjungan <span class="text-rose-500">*</span>
                    </label>
                    <input
                        wire:model="keperluan"
                        type="text"
                        placeholder="Contoh: Pengiriman Drum B3"
                        class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        required
                    />
                    @error('keperluan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 3 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                        Waktu Masuk <span class="text-rose-500">*</span>
                    </label>
                    <input
                        wire:model="waktu_masuk"
                        type="datetime-local"
                        class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        required
                    />
                    @error('waktu_masuk') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                        Nomor Surat Jalan
                    </label>
                    <input
                        wire:model="informasi_surat_jalan"
                        type="text"
                        placeholder="Nomor dokumen surat jalan"
                        class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                    />
                    @error('informasi_surat_jalan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 4 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                        Pemeriksaan Surat Jalan B3 <span class="text-rose-500">*</span>
                    </label>
                    <select
                        wire:model="status_pemeriksaan_surat_jalan_b3"
                        class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                        required
                    >
                        <option value="tidak_ada_b3">Non-B3 (Umum / Kosong)</option>
                        <option value="sesuai">Sesuai Dokumen B3</option>
                        <option value="tidak_sesuai">Tidak Sesuai Dokumen B3</option>
                    </select>
                    @error('status_pemeriksaan_surat_jalan_b3') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                        Keterangan Muatan
                    </label>
                    <input
                        wire:model="keterangan_muatan"
                        type="text"
                        placeholder="Contoh: 150 Drum Besi"
                        class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                    />
                    @error('keterangan_muatan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1">
                    Catatan (Opsional)
                </label>
                <textarea
                    wire:model="catatan"
                    rows="2"
                    placeholder="Masukkan keterangan tambahan..."
                    class="w-full py-2 px-3 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                ></textarea>
                @error('catatan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-3 border-t border-[#E2E8F0] flex justify-end gap-2">
                <a
                    href="{{ route('satpam.dashboard') }}"
                    wire:navigate
                    class="px-4 py-2 rounded-lg border border-[#E2E8F0] bg-white hover:bg-slate-50 text-[#0F172A] font-medium text-xs transition"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-5 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition cursor-pointer"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
