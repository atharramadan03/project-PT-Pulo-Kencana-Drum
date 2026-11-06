<div class="space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex-1 min-w-0">
            <h1 class="font-bold text-lg sm:text-xl text-[#0F172A] tracking-tight leading-snug">
                Biaya Operasional Pabrik & Gudang
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5 leading-relaxed">
                Pencatatan pengeluaran rutin utilitas listrik, air cuci drum, BBM solar armada, APD limbah B3, dan konsumsi
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <a 
                href="{{ route('keuangan.export-excel') }}?jenis=operasional" 
                class="px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
            >
                <x-icon name="file-spreadsheet" class="w-4 h-4 shrink-0" />
                <span>Export Excel</span>
            </a>
            <a 
                href="{{ route('keuangan.export-pdf') }}?jenis=operasional" 
                target="_blank"
                class="px-3.5 py-2 rounded-lg bg-white border border-[#E2E8F0] hover:bg-slate-50 text-[#0F172A] font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
            >
                <x-icon name="printer" class="w-4 h-4 text-rose-600 shrink-0" />
                <span>Cetak PDF</span>
            </a>
            @if (auth()->user()->isAdminKeuangan())
                <button 
                    wire:click="bukaModal"
                    type="button" 
                    class="px-4 py-2 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
                >
                    <x-icon name="plus" class="w-4 h-4 shrink-0" />
                    <span>Catat Biaya Operasional</span>
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

    <!-- 4 KPI Cards (Clean Icons without Colored Background Circles) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Bulan Ini -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Beban Bulan Ini</p>
                <x-icon name="banknote" class="w-4 h-4 text-[#EF4444] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                <x-animated-number :value="$totalBiayaBulanIni" prefix="Rp " />
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-rose-600 font-semibold">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}</span>
                <span class="text-[#64748B]">Beban berjalan</span>
            </div>
        </div>

        <!-- Card 2: Kas Kecil / Tunai -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Beban Kas Kecil</p>
                <x-icon name="wallet" class="w-4 h-4 text-[#F59E0B] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                <x-animated-number :value="$totalKasKecil" prefix="Rp " />
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#F59E0B] font-semibold">Petty Cash</span>
                <span class="text-[#64748B]">Nota & bon fisik</span>
            </div>
        </div>

        <!-- Card 3: Transfer Bank -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Transfer Bank</p>
                <x-icon name="trending-up" class="w-4 h-4 text-[#2563EB] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                <x-animated-number :value="$totalTransferBank" prefix="Rp " />
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#2563EB] font-semibold">Rekening PT</span>
                <span class="text-[#64748B]">PLN, solar & supplier</span>
            </div>
        </div>

        <!-- Card 4: Akumulasi Seluruh Beban -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Total Akumulasi</p>
                <x-icon name="package" class="w-4 h-4 text-[#64748B] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                <x-animated-number :value="$totalKeseluruhan" prefix="Rp " />
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#64748B] font-semibold"><x-animated-number :value="$expenses->total()" suffix=" Transaksi" /></span>
                <span class="text-[#64748B]">Semua catatan</span>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
        <!-- Filter Toolbar -->
        <div class="p-4 border-b border-[#E2E8F0] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-sm">
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    placeholder="Cari kode, nama biaya, toko/penerima, nota..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                />
                <x-icon name="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" />
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select 
                    wire:model.live="filterKategori"
                    class="px-2.5 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                >
                    <option value="">Semua Kategori</option>
                    <option value="listrik_air">Listrik & Air Pabrik</option>
                    <option value="bbm_solar">BBM Solar Armada</option>
                    <option value="perlengkapan_apd">Perlengkapan & APD B3</option>
                    <option value="konsumsi_karyawan">Konsumsi Karyawan</option>
                    <option value="atk_kantor">ATK & Kantor</option>
                    <option value="pemeliharaan_fasilitas">Pemeliharaan Fasilitas</option>
                    <option value="lainnya">Lain-lain</option>
                </select>

                <select 
                    wire:model.live="filterMetode"
                    class="px-2.5 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                >
                    <option value="">Semua Metode</option>
                    <option value="tunai_kas_kecil">Kas Kecil / Tunai</option>
                    <option value="transfer_bank">Transfer Bank</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-[#E2E8F0] text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">
                        <th class="px-3 py-2.5">Kode & Tanggal</th>
                        <th class="px-3 py-2.5">Kategori</th>
                        <th class="px-3 py-2.5">Deskripsi Pengeluaran</th>
                        <th class="px-3 py-2.5">Penerima Dana & Bukti</th>
                        <th class="px-3 py-2.5 text-center">Metode</th>
                        <th class="px-3 py-2.5 text-right">Nominal</th>
                        <th class="px-3 py-2.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($expenses as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-3 py-2.5 font-medium text-[#0F172A] whitespace-nowrap">
                                <div class="font-bold text-[#0F172A]">{{ $item->kode_biaya }}</div>
                                <div class="text-[11px] text-[#64748B] tabular-nums">{{ $item->tanggal_pengeluaran?->format('d M Y') }}</div>
                            </td>
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                @php
                                    $kategoriClasses = match ($item->kategori_biaya) {
                                        'listrik_air' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'bbm_solar' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'perlengkapan_apd' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'konsumsi_karyawan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'atk_kantor' => 'bg-slate-50 text-slate-700 border-slate-200',
                                        'pemeliharaan_fasilitas' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        default => 'bg-slate-50 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $kategoriClasses }}">
                                    {{ $item->kategori_label }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="font-medium text-[#0F172A] leading-tight">{{ $item->nama_biaya }}</div>
                                @if ($item->keterangan)
                                    <div class="text-[11px] text-[#64748B] truncate max-w-xs mt-0.5">{{ $item->keterangan }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <div class="font-medium text-[#0F172A]">{{ $item->penerima_dana }}</div>
                                <div class="text-[11px] text-[#64748B]">
                                    {{ $item->nomor_bukti ? 'Bukti: '.$item->nomor_bukti : 'Tanpa Nota' }}
                                    @if ($item->employee)
                                        • <span class="text-purple-700 font-medium">Driver: {{ $item->employee->nama_karyawan }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                <span class="text-[11px] text-[#64748B] font-medium">
                                    {{ $item->metode_pembayaran === 'tunai_kas_kecil' ? 'Kas Kecil' : 'Transfer' }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5 text-right font-bold text-rose-600 tabular-nums whitespace-nowrap">
                                {{ $item->formatted_nominal }}
                            </td>
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                @if (auth()->user()->isAdminKeuangan())
                                    <button 
                                        wire:click="konfirmasiHapus({{ $item->id }})" 
                                        wire:loading.attr="disabled"
                                        type="button" 
                                        class="p-1 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 hover:border-rose-300 text-slate-400 hover:text-rose-600 transition cursor-pointer shadow-2xs disabled:opacity-50"
                                        title="Hapus data biaya operasional"
                                    >
                                        <x-icon name="trash" class="w-3.5 h-3.5" wire:loading.remove wire:target="konfirmasiHapus({{ $item->id }})" />
                                        <svg wire:loading wire:target="konfirmasiHapus({{ $item->id }})" class="animate-spin w-3.5 h-3.5 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-[#64748B]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-xs text-[#64748B]">
                                Belum ada catatan biaya operasional yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($expenses->hasPages())
            <div class="p-3 border-t border-[#E2E8F0]">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Catat Biaya Operasional -->
    @if ($modalTerbuka)
        <div 
            x-data 
            x-init="document.body.classList.add('overflow-hidden'); return () => document.body.classList.remove('overflow-hidden')" 
            class="fixed inset-0 z-100 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-3 sm:p-6 flex justify-center items-center"
        >
            <div class="relative bg-white rounded-xl max-w-lg w-full border border-[#E2E8F0] shadow-2xl overflow-hidden my-auto flex flex-col max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3.5rem)] animate-in fade-in zoom-in-95 duration-100">
                <!-- Modal Header (Fixed at top) -->
                <div class="px-5 py-3.5 border-b border-[#E2E8F0] flex items-center justify-between bg-slate-50/75 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                            <x-icon name="banknote" class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-[#0F172A] leading-tight">Catat Biaya Operasional Pabrik</h3>
                            <div class="flex items-center gap-1.5 mt-0.5 text-xs text-[#64748B]">
                                <span>No. Sistem:</span>
                                <span class="font-mono font-semibold text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-100 text-[11px]">{{ $kode_biaya }}</span>
                            </div>
                        </div>
                    </div>
                    <button wire:click="tutupModal" type="button" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <!-- Form & Scrollable Body -->
                <form wire:submit="simpanBiaya" class="flex flex-col flex-1 min-h-0 overflow-hidden" novalidate>
                    <div class="p-5 space-y-3.5 overflow-y-auto flex-1 min-h-0">
                        <!-- 1. Kategori Biaya & Metode Pembayaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">Kategori Beban <span class="text-rose-500">*</span></label>
                                <select wire:model="kategori_biaya" class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs">
                                    <option value="listrik_air">Listrik & Air Pabrik</option>
                                    <option value="bbm_solar">BBM Solar Armada / Genset</option>
                                    <option value="perlengkapan_apd">Perlengkapan & APD B3</option>
                                    <option value="konsumsi_karyawan">Konsumsi Lembur Karyawan</option>
                                    <option value="atk_kantor">ATK & Keperluan Kantor</option>
                                    <option value="pemeliharaan_fasilitas">Pemeliharaan Fasilitas</option>
                                    <option value="lainnya">Lain-lain</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">Metode Pengeluaran</label>
                                <select wire:model="metode_pembayaran" class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs">
                                    <option value="tunai_kas_kecil">Kas Kecil (Tunai / Petty Cash)</option>
                                    <option value="transfer_bank">Transfer Rekening Perusahaan</option>
                                </select>
                            </div>
                        </div>

                        <!-- 2. Deskripsi Pengeluaran -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1">Deskripsi Biaya Pengeluaran <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="nama_biaya" 
                                type="text" 
                                placeholder="Misal: Tagihan Listrik PLN Pabrik & Pompa Cuci Drum" 
                                class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" 
                            />
                            <x-input-error :messages="$errors->get('nama_biaya')" class="mt-1" />
                        </div>

                        <!-- 3. Nominal Biaya & Tanggal -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">Nominal Biaya <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 font-bold text-xs">
                                        Rp
                                    </div>
                                    <input 
                                        wire:model="nominal" 
                                        type="number" 
                                        placeholder="0" 
                                        class="w-full pl-9 pr-3 py-1.5 text-xs font-semibold text-[#0F172A] rounded-lg border border-[#E2E8F0] bg-white placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" 
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('nominal')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">Tanggal Pengeluaran <span class="text-rose-500">*</span></label>
                                <input wire:model="tanggal_pengeluaran" type="date" class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" />
                                <x-input-error :messages="$errors->get('tanggal_pengeluaran')" class="mt-1" />
                            </div>
                        </div>

                        <!-- 4. Penerima Dana & No. Bukti -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">Penerima Dana / Toko <span class="text-rose-500">*</span></label>
                                <input 
                                    wire:model="penerima_dana" 
                                    type="text" 
                                    placeholder="Misal: PT PLN / SPBU Pertamina" 
                                    class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" 
                                />
                                <x-input-error :messages="$errors->get('penerima_dana')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1">No. Nota / Kwitansi (Opsional)</label>
                                <input 
                                    wire:model="nomor_bukti" 
                                    type="text" 
                                    placeholder="Misal: NOTA-1234 / PLN-9988" 
                                    class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" 
                                />
                            </div>
                        </div>

                        <!-- 4b. Driver Truk Pengelola Biaya -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1">Driver Truk Penanggung Jawab Biaya (Opsional)</label>
                            <select 
                                wire:model="employee_id"
                                class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs"
                            >
                                <option value="">-- Pilih Driver Truk (Opsional) --</option>
                                @foreach ($employeesList as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->nama_karyawan }} ({{ $emp->nik_karyawan }} - {{ $emp->departemen }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('employee_id')" class="mt-1" />
                        </div>

                        <!-- 5. Keterangan Tambahan -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1">Keterangan Tambahan</label>
                            <textarea wire:model="keterangan" rows="2" placeholder="Catatan peruntukan atau rincian spesifik pengeluaran..." class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs"></textarea>
                        </div>
                    </div>

                    <!-- Modal Actions (Fixed at bottom) -->
                    <div class="px-5 py-3 border-t border-[#E2E8F0] bg-slate-50/75 flex items-center justify-end gap-2.5 shrink-0">
                        <button wire:click="tutupModal" type="button" class="px-3.5 py-1.5 rounded-lg border border-[#E2E8F0] text-xs font-semibold text-[#0F172A] bg-white hover:bg-slate-50 transition cursor-pointer shadow-2xs">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-1.5 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 shadow-xs disabled:opacity-75">
                            <svg wire:loading wire:target="simpanBiaya" class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Simpan Biaya</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Biaya Operasional -->
    @if ($modalHapusTerbuka && $hapusExpense)
        <div 
            x-data 
            x-init="document.body.classList.add('overflow-hidden'); return () => document.body.classList.remove('overflow-hidden')" 
            class="fixed inset-0 z-100 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-3 sm:p-6 flex justify-center items-center"
        >
            <div class="relative bg-white rounded-xl max-w-md w-full border border-[#E2E8F0] shadow-2xl overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-100">
                <div class="p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 mx-auto mb-4">
                        <x-icon name="trash" class="w-6 h-6" />
                    </div>
                    <h3 class="text-base font-bold text-[#0F172A] mb-1">Konfirmasi Hapus Biaya Operasional</h3>
                    <p class="text-xs text-[#64748B] mb-5 leading-relaxed">
                        Apakah Anda yakin ingin menghapus catatan biaya <strong class="text-[#0F172A]">{{ $hapusExpense->kode_biaya }}</strong> ({{ $hapusExpense->nama_biaya }}) senilai <strong class="text-rose-600">{{ $hapusExpense->formatted_nominal }}</strong>? Tindakan ini akan menghapus data dari pembukuan secara permanen.
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <button 
                            wire:click="tutupModalHapus" 
                            type="button" 
                            class="px-4 py-2 rounded-lg border border-[#E2E8F0] text-xs font-semibold text-[#0F172A] bg-white hover:bg-slate-50 transition cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            wire:click="hapusBiaya" 
                            type="button" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 shadow-xs disabled:opacity-75"
                        >
                            <span wire:loading.remove wire:target="hapusBiaya">Ya, Hapus Biaya</span>
                            <span wire:loading wire:target="hapusBiaya" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Menghapus...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

