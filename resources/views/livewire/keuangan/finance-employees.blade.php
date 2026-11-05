<div class="space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex-1 min-w-0">
            <h1 class="font-bold text-lg sm:text-xl text-[#0F172A] tracking-tight leading-snug">
                Data Driver Truk & Armada Operasional
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5 leading-relaxed">
                Pengelolaan profil supir / driver armada pengangkut drum rekondisi, penanggung jawab kas operasional BBM solar, biaya tol, dan perawatan truk
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            @if (auth()->user()->isAdminKeuangan())
                <button 
                    wire:click="bukaModalTambah"
                    type="button" 
                    class="px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
                >
                    <x-icon name="plus" class="w-4 h-4 shrink-0" />
                    <span>Tambah Driver Truk</span>
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="p-3.5 rounded-xl bg-[#DCFCE7] border border-green-200 text-[#166534] flex items-center gap-2 text-xs font-medium">
            <x-icon name="circle-check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs font-medium text-[#64748B]">
                <span>Total Driver Terdaftar</span>
                <x-icon name="truck" class="w-4 h-4 text-[#2563EB]" />
            </div>
            <p class="text-lg sm:text-xl font-bold text-[#0F172A] mt-1.5 tabular-nums tracking-tight">
                {{ $totalKaryawan }} Supir
            </p>
            <p class="text-[11px] text-[#94A3B8] mt-0.5">Personil armada terdata</p>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs font-medium text-[#64748B]">
                <span>Driver Aktif Bertugas</span>
                <x-icon name="circle-check" class="w-4 h-4 text-emerald-600" />
            </div>
            <p class="text-lg sm:text-xl font-bold text-emerald-700 mt-1.5 tabular-nums tracking-tight">
                {{ $totalAktif }} Driver
            </p>
            <p class="text-[11px] text-[#94A3B8] mt-0.5">Siap penugasan logistik</p>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="flex items-center justify-between text-xs font-medium text-[#64748B]">
                <span>Driver Pengelola Biaya</span>
                <x-icon name="wallet" class="w-4 h-4 text-purple-600" />
            </div>
            <p class="text-lg sm:text-xl font-bold text-purple-700 mt-1.5 tabular-nums tracking-tight">
                {{ $totalPengelolaBiaya }} Driver
            </p>
            <p class="text-[11px] text-[#94A3B8] mt-0.5">Tercatat di kas BBM & armada</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-3 rounded-xl border border-[#E2E8F0] shadow-2xs">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <div class="relative">
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    placeholder="Cari NIK/SIM, nama driver, tipe armada..." 
                    class="w-full pl-9 pr-3 py-1.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB] placeholder:text-[#94A3B8]"
                >
                <div class="absolute left-2.5 top-2 text-[#94A3B8]">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
            </div>

            <div>
                <select 
                    wire:model.live="filterDepartemen"
                    class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                >
                    <option value="">Semua Unit Armada</option>
                    @foreach($departemenList as $dep)
                        <option value="{{ $dep }}">{{ $dep }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select 
                    wire:model.live="filterStatus"
                    class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                >
                    <option value="">Semua Status Driver</option>
                    <option value="aktif">Status: Aktif</option>
                    <option value="nonaktif">Status: Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#475569] font-semibold">
                        <th class="py-2.5 px-3">NIK / No. SIM</th>
                        <th class="py-2.5 px-3">Nama Driver Truk</th>
                        <th class="py-2.5 px-3">Unit Armada & Tipe Truk</th>
                        <th class="py-2.5 px-3">Kontak HP / WA</th>
                        <th class="py-2.5 px-3 text-center">Kelola Kas Biaya</th>
                        <th class="py-2.5 px-3 text-center">Status</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-2.5 px-3 font-mono font-medium text-slate-700 whitespace-nowrap">
                                {{ $emp->nik_karyawan }}
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="font-semibold text-[#0F172A]">{{ $emp->nama_karyawan }}</div>
                                <div class="text-[11px] text-[#64748B]">Mulai Tugas: {{ $emp->tanggal_masuk ? $emp->tanggal_masuk->format('d/m/Y') : '-' }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $emp->departemen }}
                                </span>
                                <div class="text-[11px] text-slate-600 mt-0.5">{{ $emp->jabatan }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="flex items-center gap-1 text-slate-700">
                                    <x-icon name="phone" class="w-3 h-3 text-slate-400" />
                                    <span>{{ $emp->nomor_telepon }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                                    <x-icon name="receipt" class="w-3.5 h-3.5" />
                                    <span>{{ $emp->operational_expense_logs_count }} Biaya</span>
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                @if($emp->status_kerja === 'aktif')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                @if (auth()->user()->isAdminKeuangan())
                                    <div class="flex items-center justify-end gap-1">
                                        <button 
                                            wire:click="bukaModalEdit({{ $emp->id }})"
                                            type="button" 
                                            class="p-1.5 rounded-lg border border-blue-200 hover:bg-blue-50 text-blue-600 transition cursor-pointer"
                                            title="Edit Data Driver Truk"
                                        >
                                            <x-icon name="edit" class="w-3.5 h-3.5" />
                                        </button>
                                        <button 
                                            wire:click="konfirmasiHapus({{ $emp->id }})"
                                            type="button" 
                                            class="p-1.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 transition cursor-pointer"
                                            title="Hapus Driver Truk"
                                        >
                                            <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400">Lihat Saja</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-[#64748B]">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <x-icon name="inbox" class="w-8 h-8 text-slate-300" />
                                    <p class="font-medium text-xs">Belum ada data driver truk yang cocok</p>
                                    <p class="text-[11px] text-slate-400">Silakan tambahkan data supir armada atau periksa filter</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-3 border-t border-[#E2E8F0]">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Tambah / Edit Driver Truk -->
    @if ($modalTerbuka)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" wire:click="tutupModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                    <div class="bg-white px-5 pt-5 pb-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">
                                    {{ $isEdit ? 'Ubah Data Driver Truk' : 'Registrasi Driver Truk Baru' }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Data personil supir armada penanggung jawab pengeluaran BBM & operasional truk
                                </p>
                            </div>
                            <button wire:click="tutupModal" type="button" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                                <x-icon name="x" class="w-4 h-4" />
                            </button>
                        </div>

                        <form wire:submit="simpanKaryawan" class="space-y-3.5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">NIK / No. SIM Driver</label>
                                    <input 
                                        type="text" 
                                        wire:model="nik_karyawan" 
                                        readonly 
                                        class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-600 focus:outline-none"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Driver <span class="text-rose-500">*</span></label>
                                    <select 
                                        wire:model="status_kerja"
                                        class="w-full px-3 py-2 border rounded-lg text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600 {{ $errors->has('status_kerja') ? 'border-rose-300 ring-rose-300' : 'border-slate-300' }}"
                                    >
                                        <option value="aktif">Aktif Bertugas</option>
                                        <option value="nonaktif">Nonaktif</option>
                                    </select>
                                    @error('status_kerja') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Driver Truk <span class="text-rose-500">*</span></label>
                                <input 
                                    type="text" 
                                    wire:model="nama_karyawan" 
                                    placeholder="Contoh: Dedi Suryana / Agus Prasetyo"
                                    class="w-full px-3 py-2 border rounded-lg text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600 {{ $errors->has('nama_karyawan') ? 'border-rose-300 ring-rose-300' : 'border-slate-300' }}"
                                >
                                @error('nama_karyawan') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Unit Armada / Wilayah <span class="text-rose-500">*</span></label>
                                    <input 
                                        type="text" 
                                        wire:model="departemen" 
                                        placeholder="Contoh: Armada Logistik Jakarta"
                                        class="w-full px-3 py-2 border rounded-lg text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600 {{ $errors->has('departemen') ? 'border-rose-300 ring-rose-300' : 'border-slate-300' }}"
                                    >
                                    @error('departemen') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Driver / Posisi <span class="text-rose-500">*</span></label>
                                    <input 
                                        type="text" 
                                        wire:model="jabatan" 
                                        placeholder="Contoh: Supir Truk Tronton Box"
                                        class="w-full px-3 py-2 border rounded-lg text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600 {{ $errors->has('jabatan') ? 'border-rose-300 ring-rose-300' : 'border-slate-300' }}"
                                    >
                                    @error('jabatan') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WA Driver <span class="text-rose-500">*</span></label>
                                    <input 
                                        type="text" 
                                        wire:model="nomor_telepon" 
                                        placeholder="Contoh: 0812-3456-7890"
                                        class="w-full px-3 py-2 border rounded-lg text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600 {{ $errors->has('nomor_telepon') ? 'border-rose-300 ring-rose-300' : 'border-slate-300' }}"
                                    >
                                    @error('nomor_telepon') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Bertugas <span class="text-rose-500">*</span></label>
                                    <input 
                                        type="date" 
                                        wire:model="tanggal_masuk" 
                                        class="w-full px-3 py-2 border rounded-lg text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600 {{ $errors->has('tanggal_masuk') ? 'border-rose-300 ring-rose-300' : 'border-slate-300' }}"
                                    >
                                    @error('tanggal_masuk') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tugas & No. Plat Truk Bawaan</label>
                                <textarea 
                                    wire:model="catatan" 
                                    rows="2"
                                    placeholder="Contoh: Supir armada truk B 9481 KDA, PIC klaim kas kecil BBM solar dan uang jalan tol..."
                                    class="w-full px-3 py-2 border rounded-lg text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600 border-slate-300"
                                ></textarea>
                                @error('catatan') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                <button 
                                    type="button" 
                                    wire:click="tutupModal" 
                                    class="px-3.5 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition"
                                >
                                    Batal
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition"
                                >
                                    {{ $isEdit ? 'Simpan Perubahan' : 'Daftarkan Driver Truk' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Driver Truk -->
    @if ($modalHapusTerbuka && $targetHapus)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" wire:click="tutupModalHapus"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <x-icon name="trash-2" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Konfirmasi Hapus Data Driver Truk</h3>
                            <p class="text-xs text-slate-500">Tindakan ini tidak dapat dibatalkan</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        Apakah Anda yakin ingin menghapus data driver truk <strong>{{ $targetHapus->nama_karyawan }}</strong> (NIK: <span class="font-mono">{{ $targetHapus->nik_karyawan }}</span>)?
                    </p>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="tutupModalHapus" 
                            class="px-3.5 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="button" 
                            wire:click="hapusKaryawan" 
                            class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs transition"
                        >
                            Ya, Hapus Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
