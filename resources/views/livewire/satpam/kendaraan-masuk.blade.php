<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Catat Kendaraan Masuk (Pos Gerbang)
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Input pencatatan truk armada / pengiriman yang melintasi gerbang utama PT Pulo Kencana
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                Shift: {{ auth()->user()->shift_jaga ?? 'Pagi' }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                
                <!-- Header Kartu -->
                <div class="bg-slate-900 text-white p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-wider text-emerald-400 font-semibold">Transaksi Gerbang Baru</span>
                        <h3 class="text-xl font-bold mt-1">Formulir Kunjungan Truk</h3>
                    </div>
                    <div class="bg-slate-800 border border-slate-700 px-4 py-2 rounded-xl text-right">
                        <span class="text-[10px] text-slate-400 block uppercase">Nomor Kunjungan Otomatis</span>
                        <span class="text-base font-mono font-bold text-emerald-400">{{ $nomor_kunjungan }}</span>
                    </div>
                </div>

                <!-- Form Input -->
                <form wire:submit="simpan" class="p-6 sm:p-8 space-y-6">
                    
                    <!-- Baris 1: Plat Nomor & Nama Supir -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Nomor Polisi (Plat Nomor) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                wire:model="nomor_polisi" 
                                type="text" 
                                placeholder="Contoh: B 9123 KDA" 
                                class="w-full uppercase font-mono rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800"
                                required
                            />
                            @error('nomor_polisi') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Nama Pengemudi / Supir <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                wire:model="nama_pengemudi" 
                                type="text" 
                                placeholder="Nama lengkap supir" 
                                class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800"
                                required
                            />
                            @error('nama_pengemudi') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Baris 2: Asal/Tujuan & Keperluan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Asal / Tujuan Perusahaan <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                wire:model="asal_tujuan" 
                                type="text" 
                                placeholder="Contoh: PT Mulia Chem / Cikarang" 
                                class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800"
                                required
                            />
                            @error('asal_tujuan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Keperluan Kunjungan <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                wire:model="keperluan" 
                                type="text" 
                                placeholder="Contoh: Pengiriman Kemasan Drum B3" 
                                class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800"
                                required
                            />
                            @error('keperluan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Baris 3: Waktu Masuk & Pos Gerbang -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Waktu Masuk <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                wire:model="waktu_masuk" 
                                type="datetime-local" 
                                class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800"
                                required
                            />
                            @error('waktu_masuk') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Pos Gerbang Pencatatan
                            </label>
                            <input 
                                wire:model="pos_gerbang" 
                                type="text" 
                                class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Seksi Verifikasi Dokumen B3 (Mandat Khusus PRD v1.2) -->
                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">⚠️</span>
                            <div>
                                <h4 class="text-sm font-bold text-amber-900">Verifikasi Dokumen Surat Jalan Kemasan Limbah B3</h4>
                                <p class="text-xs text-amber-700">Wajib diperiksa secara fisik saat pengiriman/penerimaan kemasan limbah B3</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-amber-900 mb-1">Nomor Fisik Surat Jalan / Manifest</label>
                                <input 
                                    wire:model="informasi_surat_jalan" 
                                    type="text" 
                                    placeholder="Nomor dokumen surat jalan supir" 
                                    class="w-full rounded-xl border-amber-300 focus:border-amber-500 focus:ring-amber-500 text-slate-800 text-sm bg-white"
                                />
                                @error('informasi_surat_jalan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-amber-900 mb-1">Status Pemeriksaan Fisik Dokumen B3 <span class="text-rose-500">*</span></label>
                                <select 
                                    wire:model="status_pemeriksaan_surat_jalan_b3" 
                                    class="w-full rounded-xl border-amber-300 focus:border-amber-500 focus:ring-amber-500 text-slate-800 text-sm bg-white"
                                    required
                                >
                                    <option value="tidak_ada_b3">Bukan Muatan B3 (Umum / Truk Kosong)</option>
                                    <option value="sesuai">✅ Sesuai (Fisik Muatan Cocok dengan Dokumen B3)</option>
                                    <option value="tidak_sesuai">❌ Tidak Sesuai (Ada Selisih / Dokumen Kurang Lengkap)</option>
                                </select>
                                @error('status_pemeriksaan_surat_jalan_b3') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Baris 4: Keterangan Muatan & Catatan Tambahan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Keterangan Ringkas Muatan
                            </label>
                            <input 
                                wire:model="keterangan_muatan" 
                                type="text" 
                                placeholder="Contoh: 150 Drum Besi Bekas Pelarut" 
                                class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800"
                            />
                            @error('keterangan_muatan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Catatan Tambahan Gerbang (Opsional)
                            </label>
                            <input 
                                wire:model="catatan" 
                                type="text" 
                                placeholder="Catatan kondisi supir / fisik truk" 
                                class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-slate-800"
                            />
                            @error('catatan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <button 
                            type="submit" 
                            class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm transition inline-flex items-center gap-2"
                        >
                            <span>💾 Simpan & Catat Kendaraan Masuk</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
