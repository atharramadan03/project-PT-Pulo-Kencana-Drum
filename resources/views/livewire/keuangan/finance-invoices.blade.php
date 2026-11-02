<div class="space-y-4" x-data @buka-wa-langsung.window="window.open($event.detail.url, '_blank')">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">
        <div class="min-w-0">
            <h1 class="font-bold text-xl text-[#0F172A] leading-tight">
                Riwayat Tagihan & Faktur
            </h1>
            <p class="text-xs text-[#64748B] mt-0.5">
                Kelola pencatatan faktur piutang pelanggan drum dan tagihan vendor supplier
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
            <a 
                href="{{ route('keuangan.export-excel') }}?jenis=tagihan" 
                class="px-3 py-1.5 rounded-lg border border-[#E2E8F0] bg-white hover:bg-slate-50 text-xs font-semibold text-[#0F172A] shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer shrink-0"
            >
                <x-icon name="file-spreadsheet" class="w-3.5 h-3.5 text-emerald-600" />
                <span>Unduh Excel</span>
            </a>
            <a 
                href="{{ route('keuangan.export-pdf') }}?jenis=tagihan" 
                target="_blank"
                class="px-3 py-1.5 rounded-lg border border-[#E2E8F0] bg-white hover:bg-slate-50 text-xs font-semibold text-[#0F172A] shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer shrink-0"
            >
                <x-icon name="printer" class="w-3.5 h-3.5 text-rose-600" />
                <span>Cetak PDF</span>
            </a>
            @if (auth()->user()->isAdminKeuangan())
                <button 
                    wire:click="bukaModal"
                    type="button" 
                    class="px-3 py-1.5 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer shrink-0 whitespace-nowrap"
                >
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Buat Tagihan Baru</span>
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
        <!-- Card 1: Total Nilai Tagihan -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Total Nilai Tagihan</p>
                <x-icon name="receipt" class="w-4 h-4 text-[#2563EB] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                Rp {{ number_format($totalSemuaTagihan, 0, ',', '.') }}
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#2563EB] font-semibold">{{ $invoices->total() }} Faktur</span>
                <span class="text-[#64748B]">Semua berkas</span>
            </div>
        </div>

        <!-- Card 2: Tagihan Belum Lunas (Sisa) -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Sisa Belum Lunas</p>
                <x-icon name="wallet" class="w-4 h-4 text-[#F59E0B] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#D97706] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#F59E0B] font-semibold">Outstanding</span>
                <span class="text-[#64748B]">Menunggu pembayaran</span>
            </div>
        </div>

        <!-- Card 3: Tagihan Sudah Lunas -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Sudah Lunas</p>
                <x-icon name="circle-check" class="w-4 h-4 text-[#10B981] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                Rp {{ number_format($totalSudahLunas, 0, ',', '.') }}
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-[#10B981] font-semibold">Tuntas</span>
                <span class="text-[#64748B]">Arus kas terealisasi</span>
            </div>
        </div>

        <!-- Card 4: Jatuh Tempo -->
        <div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs text-[#64748B] font-medium truncate">Lewat Jatuh Tempo</p>
                <x-icon name="calendar" class="w-4 h-4 text-[#EF4444] shrink-0" />
            </div>
            <h3 class="text-base sm:text-lg font-bold text-rose-600 tabular-nums leading-tight mt-1.5 whitespace-nowrap">
                {{ $tagihanJatuhTempoCount }} Faktur
            </h3>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-rose-600 font-semibold">Perlu Tindak Lanjut</span>
                <span class="text-[#64748B]">Overdue</span>
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
                    placeholder="Cari kode, no invoice, klien/vendor..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                />
                <x-icon name="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" />
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select 
                    wire:model.live="filterJenis"
                    class="px-2.5 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                >
                    <option value="">Semua Jenis</option>
                    <option value="tagihan_pelanggan">Tagihan Pelanggan (Piutang)</option>
                    <option value="tagihan_vendor">Tagihan Vendor (Hutang)</option>
                </select>

                <select 
                    wire:model.live="filterStatus"
                    class="px-2.5 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]"
                >
                    <option value="">Semua Status</option>
                    <option value="belum_dibayar">Belum Dibayar</option>
                    <option value="sebagian">Sebagian</option>
                    <option value="lunas">Lunas</option>
                    <option value="jatuh_tempo">Jatuh Tempo</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-[#E2E8F0] text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">
                        <th class="px-3 py-2.5">Kode & Faktur</th>
                        <th class="px-3 py-2.5">Pihak Terkait</th>
                        <th class="px-3 py-2.5">Jatuh Tempo</th>
                        <th class="px-3 py-2.5 text-right">Nilai Tagihan</th>
                        <th class="px-3 py-2.5 text-right">Dibayar / Sisa</th>
                        <th class="px-3 py-2.5 text-center">Status</th>
                        <th class="px-3 py-2.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invoices as $inv)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-3 py-2.5 font-medium text-[#0F172A] whitespace-nowrap">
                                <div class="font-bold text-[#0F172A]">{{ $inv->kode_tagihan }}</div>
                                <div class="text-[11px] text-[#64748B]">{{ $inv->nomor_invoice }}</div>
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="font-semibold text-[#0F172A] leading-tight">{{ $inv->pihak_terkait }}</div>
                                <div class="text-[11px] text-[#64748B] flex items-center gap-1.5 mt-0.5 whitespace-nowrap">
                                    <span>{{ $inv->jenis_tagihan === 'tagihan_pelanggan' ? 'Pelanggan (Piutang)' : 'Vendor (Hutang)' }}</span>
                                    @if ($inv->nomor_wa)
                                        <span class="text-slate-300">•</span>
                                        <span class="text-emerald-700 font-mono text-[10px]">{{ $inv->nomor_wa }}</span>
                                    @endif
                                </div>
                                @if ($inv->terakhir_ditagih_at)
                                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5 inline-flex items-center gap-1 whitespace-nowrap" title="Terakhir ditagih {{ $inv->terakhir_ditagih_at->format('d/m/Y H:i') }}">
                                        <x-icon name="message-circle" class="w-2.5 h-2.5" />
                                        <span>Ditagih {{ $inv->terakhir_ditagih_human }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <div class="tabular-nums font-medium text-[#0F172A]">{{ $inv->tanggal_jatuh_tempo?->format('d M Y') }}</div>
                                <div class="text-[11px] text-[#64748B]">Terbit: {{ $inv->tanggal_terbit?->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-3 py-2.5 text-right font-bold text-[#0F172A] tabular-nums whitespace-nowrap">
                                {{ $inv->formatted_nominal }}
                            </td>
                            <td class="px-3 py-2.5 text-right tabular-nums whitespace-nowrap">
                                <div class="text-emerald-600 font-semibold">Rp {{ number_format((float) $inv->nominal_dibayar, 0, ',', '.') }}</div>
                                @if ($inv->status_pembayaran !== 'lunas')
                                    <div class="text-[11px] text-amber-600">Sisa: {{ $inv->formatted_sisa_tagihan }}</div>
                                @else
                                    <div class="text-[11px] text-slate-400">Lunas {{ $inv->tanggal_lunas?->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                @if ($inv->status_pembayaran === 'lunas')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Lunas
                                    </span>
                                @elseif ($inv->status_pembayaran === 'sebagian')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        Sebagian
                                    </span>
                                @elseif ($inv->tanggal_jatuh_tempo < now()->toDateString())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        Jatuh Tempo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    @if ($inv->jenis_tagihan === 'tagihan_pelanggan' && $inv->status_pembayaran !== 'lunas')
                                        @if ($inv->formatted_nomor_wa)
                                            <a 
                                                href="{{ $inv->whatsapp_direct_url }}" 
                                                target="_blank"
                                                class="px-2 py-1 rounded-lg border border-emerald-300 bg-emerald-50 hover:bg-[#25D366] hover:text-white hover:border-[#25D366] text-emerald-700 text-[11px] font-semibold transition cursor-pointer inline-flex items-center gap-1 shadow-2xs group"
                                                title="Langsung Kirim Pesan Penagihan ke WhatsApp Client"
                                            >
                                                <x-icon name="message-circle" class="w-3.5 h-3.5 text-emerald-600 group-hover:text-white" />
                                                <span>WA</span>
                                            </a>
                                        @else
                                            <button 
                                                wire:click="bukaModalWa({{ $inv->id }})"
                                                type="button"
                                                class="px-2 py-1 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-semibold transition cursor-pointer inline-flex items-center gap-1 shadow-2xs"
                                                title="Input nomor WhatsApp pelanggan"
                                            >
                                                <x-icon name="message-circle" class="w-3.5 h-3.5 text-emerald-600" />
                                                <span>+ WA</span>
                                            </button>
                                        @endif
                                    @endif

                                    @if (auth()->user()->isAdminKeuangan() && $inv->status_pembayaran !== 'lunas')
                                        <button 
                                            wire:click="bukaModalLunas({{ $inv->id }})"
                                            type="button"
                                            class="px-2 py-1 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-[11px] font-semibold transition cursor-pointer inline-flex items-center gap-1 shadow-2xs"
                                            title="Bayar cicilan / pelunasan"
                                        >
                                            <x-icon name="circle-check" class="w-3 h-3" />
                                            <span>Bayar</span>
                                        </button>
                                    @endif

                                    @if ($inv->status_pembayaran === 'lunas')
                                        <span class="text-[11px] font-medium text-emerald-600 px-1.5 py-0.5 rounded bg-emerald-50 border border-emerald-100">Lunas</span>
                                    @endif

                                    @if (auth()->user()->isAdminKeuangan())
                                        <button 
                                            wire:click="konfirmasiHapus({{ $inv->id }})"
                                            type="button"
                                            class="p-1 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 hover:border-rose-300 text-slate-400 hover:text-rose-600 transition cursor-pointer shadow-2xs"
                                            title="Hapus data tagihan"
                                        >
                                            <x-icon name="trash" class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-xs text-[#64748B]">
                                Belum ada riwayat tagihan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($invoices->hasPages())
            <div class="p-3 border-t border-[#E2E8F0]">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Tagihan Baru -->
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
                        <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-[#2563EB] shrink-0">
                            <x-icon name="receipt" class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-[#0F172A] leading-tight whitespace-nowrap">Catat Faktur & Tagihan Baru</h3>
                            <div class="flex items-center gap-1.5 mt-0.5 text-xs text-[#64748B]">
                                <span>No. Sistem:</span>
                                <span class="font-mono font-semibold text-[#2563EB] bg-blue-50 px-1.5 py-0.2 rounded border border-blue-100 text-[11px]">{{ $kode_tagihan }}</span>
                            </div>
                        </div>
                    </div>
                    <button wire:click="tutupModal" type="button" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <!-- Form & Scrollable Body -->
                <form wire:submit="simpanInvoice" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-5 space-y-3.5 overflow-y-auto flex-1 min-h-0">
                        <!-- 1. Pilihan Jenis Tagihan (Interactive Visual Cards) -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1.5 whitespace-nowrap">Klasifikasi Tagihan <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2.5">
                                <button 
                                    type="button" 
                                    wire:click="$set('jenis_tagihan', 'tagihan_pelanggan')"
                                    class="p-2.5 rounded-lg border text-left transition flex items-start gap-2 cursor-pointer {{ $jenis_tagihan === 'tagihan_pelanggan' ? 'border-[#2563EB] bg-blue-50/50 ring-1 ring-[#2563EB]' : 'border-[#E2E8F0] bg-white hover:bg-slate-50' }}"
                                >
                                    <span class="w-2 h-2 mt-1 rounded-full {{ $jenis_tagihan === 'tagihan_pelanggan' ? 'bg-[#2563EB]' : 'bg-slate-300' }}"></span>
                                    <div>
                                        <div class="font-semibold text-xs text-[#0F172A] leading-tight whitespace-nowrap">Piutang Pelanggan</div>
                                        <div class="text-[10px] text-[#64748B] mt-0.5 whitespace-nowrap">Penjualan drum & olahan</div>
                                    </div>
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="$set('jenis_tagihan', 'tagihan_vendor')"
                                    class="p-2.5 rounded-lg border text-left transition flex items-start gap-2 cursor-pointer {{ $jenis_tagihan === 'tagihan_vendor' ? 'border-[#2563EB] bg-blue-50/50 ring-1 ring-[#2563EB]' : 'border-[#E2E8F0] bg-white hover:bg-slate-50' }}"
                                >
                                    <span class="w-2 h-2 mt-1 rounded-full {{ $jenis_tagihan === 'tagihan_vendor' ? 'bg-[#2563EB]' : 'bg-slate-300' }}"></span>
                                    <div>
                                        <div class="font-semibold text-xs text-[#0F172A] leading-tight whitespace-nowrap">Hutang Vendor</div>
                                        <div class="text-[10px] text-[#64748B] mt-0.5 whitespace-nowrap">Pengadaan drum / supplier</div>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- 2. Nomor Invoice Fisik & Nama Klien/Vendor -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">No. Faktur / Invoice Cetak <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                        <x-icon name="file-text" class="w-3.5 h-3.5" />
                                    </div>
                                    <input 
                                        wire:model="nomor_invoice" 
                                        type="text" 
                                        placeholder="Misal: INV/PKDI/XI/2026/08" 
                                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" 
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('nomor_invoice')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">Pihak Terkait (Klien / Vendor) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                        <x-icon name="user" class="w-3.5 h-3.5" />
                                    </div>
                                    <input 
                                        wire:model="pihak_terkait" 
                                        type="text" 
                                        placeholder="Misal: PT Agro Pelita Nusantara" 
                                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" 
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('pihak_terkait')" class="mt-1" />
                            </div>
                        </div>

                        <!-- 2b. Kontak PIC & Nomor Telepon / WhatsApp Client (Khusus Penagihan Cepat) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 rounded-lg bg-emerald-50/50 border border-emerald-100">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">Nama PIC Client <span class="text-slate-400 font-normal text-[11px]">(Opsional)</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                        <x-icon name="user" class="w-3.5 h-3.5" />
                                    </div>
                                    <input 
                                        wire:model="kontak_pic" 
                                        type="text" 
                                        placeholder="Misal: Bpk. Hendra (Finance)" 
                                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" 
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('kontak_pic')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">No. WhatsApp Client</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-emerald-600">
                                        <x-icon name="message-circle" class="w-3.5 h-3.5" />
                                    </div>
                                    <input 
                                        wire:model="nomor_wa" 
                                        type="text" 
                                        placeholder="Contoh: 081234567890" 
                                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs font-mono" 
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('nomor_wa')" class="mt-1" />
                            </div>
                        </div>

                        <!-- 3. Nilai Tagihan (Rp) & Metode Pembayaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">Total Nilai Tagihan <span class="text-rose-500">*</span></label>
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
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">Rencana Metode Bayar</label>
                                <select wire:model="metode_pembayaran" class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs">
                                    <option value="transfer_bank">Transfer Bank (BCA / Mandiri)</option>
                                    <option value="giro">Bilyet Giro / Cek Perusahaan</option>
                                    <option value="tunai">Tunai Kas Gudang</option>
                                </select>
                            </div>
                        </div>

                        <!-- 4. Tanggal Terbit & Tanggal Jatuh Tempo -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">Tanggal Terbit Faktur <span class="text-rose-500">*</span></label>
                                <input wire:model="tanggal_terbit" type="date" class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">Batas Jatuh Tempo <span class="text-rose-500">*</span></label>
                                <input wire:model="tanggal_jatuh_tempo" type="date" class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" />
                                <x-input-error :messages="$errors->get('tanggal_jatuh_tempo')" class="mt-1" />
                            </div>
                        </div>

                        <!-- 5. Status Awal -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">Status Pembayaran Awal</label>
                            <select wire:model="status_pembayaran" class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs">
                                <option value="belum_dibayar">Belum Dibayar (Menunggu Termin)</option>
                                <option value="lunas">Langsung Lunas (Sudah Diterima/Dibayar Penuh)</option>
                            </select>
                        </div>

                        <!-- 6. Keterangan / Rincian Muatan -->
                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1 whitespace-nowrap">Keterangan / Rincian Muatan</label>
                            <textarea wire:model="keterangan" rows="2" placeholder="Catatan termin pembayaran, nomor surat jalan, atau rincian item drum..." class="w-full text-xs rounded-lg border border-[#E2E8F0] py-1.5 px-3 bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs"></textarea>
                        </div>
                    </div>

                    <!-- Modal Actions (Fixed at bottom) -->
                    <div class="px-5 py-3 border-t border-[#E2E8F0] bg-slate-50/75 flex items-center justify-end gap-2.5 shrink-0">
                        <button wire:click="tutupModal" type="button" class="px-3.5 py-1.5 rounded-lg border border-[#E2E8F0] text-xs font-semibold text-[#0F172A] bg-white hover:bg-slate-50 transition cursor-pointer shadow-2xs">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-1.5 rounded-lg bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 shadow-xs disabled:opacity-75">
                            <svg wire:loading wire:target="simpanInvoice" class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Simpan Tagihan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Pelunasan Cepat -->
    @if ($modalLunasTerbuka && $selectedInvoice)
        <div 
            x-data 
            x-init="document.body.classList.add('overflow-hidden'); return () => document.body.classList.remove('overflow-hidden')"
            class="fixed inset-0 z-100 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-3 sm:p-6 flex justify-center items-center"
        >
            <div class="relative bg-white rounded-xl max-w-md w-full border border-[#E2E8F0] shadow-2xl overflow-hidden my-auto flex flex-col max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3.5rem)] animate-in fade-in zoom-in-95 duration-100">
                <!-- Modal Header (Fixed at top) -->
                <div class="px-5 py-3.5 border-b border-[#E2E8F0] flex items-center justify-between bg-slate-50/75 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <x-icon name="wallet" class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-[#0F172A] whitespace-nowrap">Pembayaran & Cicilan Tagihan</h3>
                            <p class="text-xs text-[#64748B] mt-0.5 truncate">{{ $selectedInvoice->kode_tagihan }} - {{ $selectedInvoice->pihak_terkait }}</p>
                        </div>
                    </div>
                    <button wire:click="tutupModalLunas" type="button" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <!-- Form & Body -->
                <form wire:submit="konfirmasiPelunasan" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-5 space-y-3.5 overflow-y-auto flex-1 min-h-0">
                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200 text-xs space-y-1.5">
                            <div class="flex justify-between text-[#64748B]">
                                <span class="whitespace-nowrap">Total Nilai Tagihan:</span>
                                <span class="font-bold text-[#0F172A]">{{ $selectedInvoice->formatted_nominal }}</span>
                            </div>
                            <div class="flex justify-between text-[#64748B]">
                                <span class="whitespace-nowrap">Total Sudah Dicicil:</span>
                                <span class="text-emerald-600 font-semibold">Rp {{ number_format((float) $selectedInvoice->nominal_dibayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[#64748B] pt-1.5 border-t border-slate-200">
                                <span class="font-semibold text-[#0F172A] whitespace-nowrap">Sisa Tagihan Saat Ini:</span>
                                <span class="text-amber-600 font-bold tabular-nums">{{ $selectedInvoice->formatted_sisa_tagihan }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-[#0F172A] whitespace-nowrap">Nominal Bayar / Cicilan Kali Ini <span class="text-rose-500">*</span></label>
                                <span class="text-[11px] text-[#2563EB] cursor-pointer hover:underline font-medium whitespace-nowrap" wire:click="$set('lunas_nominal', '{{ (int) $selectedInvoice->sisa_tagihan }}')">Set Lunas Sisa</span>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 font-bold text-xs">
                                    Rp
                                </div>
                                <input 
                                    wire:model="lunas_nominal" 
                                    type="number" 
                                    class="w-full pl-9 pr-3 py-2 text-xs font-semibold text-[#0F172A] rounded-lg border border-[#E2E8F0] bg-white focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" 
                                    required 
                                />
                            </div>
                            <p class="text-[11px] text-[#64748B] mt-1">
                                Nominal ini akan otomatis diakumulasikan ke total cicilan sebelumnya.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1.5 whitespace-nowrap">Tanggal Bayar <span class="text-rose-500">*</span></label>
                                <input wire:model="lunas_tanggal" type="date" class="w-full text-xs rounded-lg border border-[#E2E8F0] py-2 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" required />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#0F172A] mb-1.5 whitespace-nowrap">Metode Bayar</label>
                                <select wire:model="lunas_metode" class="w-full text-xs rounded-lg border border-[#E2E8F0] py-2 px-3 bg-white text-[#0F172A] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs">
                                    <option value="transfer_bank">Transfer Bank</option>
                                    <option value="giro">Bilyet Giro</option>
                                    <option value="tunai">Tunai Kas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Actions (Fixed at bottom) -->
                    <div class="px-5 py-3 border-t border-[#E2E8F0] bg-slate-50/75 flex items-center justify-end gap-2.5 shrink-0">
                        <button wire:click="tutupModalLunas" type="button" class="px-3.5 py-1.5 rounded-lg border border-[#E2E8F0] text-xs font-semibold text-[#0F172A] bg-white hover:bg-slate-50 transition cursor-pointer shadow-2xs">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 shadow-xs disabled:opacity-75">
                            <svg wire:loading wire:target="konfirmasiPelunasan" class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Simpan Cicilan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Lengkapi WhatsApp Cepat & Buka WA Langsung -->
    @if ($modalWaTerbuka && $quickWaInvoice)
        <div 
            x-data 
            x-init="document.body.classList.add('overflow-hidden'); return () => document.body.classList.remove('overflow-hidden')"
            class="fixed inset-0 z-100 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-3 sm:p-6 flex justify-center items-center"
        >
            <div class="relative bg-white rounded-xl max-w-md w-full border border-[#E2E8F0] shadow-2xl overflow-hidden my-auto flex flex-col max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3.5rem)] animate-in fade-in zoom-in-95 duration-100">
                <!-- Modal Header -->
                <div class="px-5 py-3.5 border-b border-[#E2E8F0] flex items-center justify-between bg-slate-50/75 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <x-icon name="message-circle" class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-[#0F172A] whitespace-nowrap">Kirim Tagihan via WhatsApp</h3>
                            <p class="text-xs text-[#64748B] mt-0.5 truncate">{{ $quickWaInvoice->kode_tagihan }} &bull; {{ $quickWaInvoice->pihak_terkait }}</p>
                        </div>
                    </div>
                    <button wire:click="tutupModalWa" type="button" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>

                <!-- Form & Body -->
                <form wire:submit="simpanNomorWa" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-5 space-y-3.5 overflow-y-auto flex-1 min-h-0">
                        <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-200 text-xs space-y-1.5">
                            <div class="flex justify-between text-[#64748B]">
                                <span class="whitespace-nowrap">No. Faktur Fisik:</span>
                                <span class="font-semibold text-[#0F172A]">{{ $quickWaInvoice->nomor_invoice }}</span>
                            </div>
                            <div class="flex justify-between text-[#64748B]">
                                <span class="whitespace-nowrap">Total Tagihan:</span>
                                <span class="font-bold text-[#0F172A]">{{ $quickWaInvoice->formatted_nominal }}</span>
                            </div>
                            <div class="flex justify-between text-[#64748B]">
                                <span class="whitespace-nowrap">Sisa Tagihan:</span>
                                <span class="text-amber-600 font-bold tabular-nums">{{ $quickWaInvoice->formatted_sisa_tagihan }}</span>
                            </div>
                            <div class="flex justify-between text-[#64748B] pt-1.5 border-t border-slate-200">
                                <span class="whitespace-nowrap">Batas Jatuh Tempo:</span>
                                <span class="font-semibold text-[#0F172A]">{{ $quickWaInvoice->tanggal_jatuh_tempo?->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1.5 whitespace-nowrap">Nama PIC Pelanggan <span class="text-slate-400 font-normal text-[11px]">(Opsional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                    <x-icon name="user" class="w-3.5 h-3.5" />
                                </div>
                                <input 
                                    wire:model="quick_wa_pic" 
                                    type="text" 
                                    placeholder="Misal: Bpk. Hendra (Finance)" 
                                    class="w-full pl-8 pr-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs" 
                                />
                            </div>
                            <x-input-error :messages="$errors->get('quick_wa_pic')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#0F172A] mb-1.5 whitespace-nowrap">Nomor WhatsApp Pelanggan <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-emerald-600">
                                    <x-icon name="message-circle" class="w-3.5 h-3.5" />
                                </div>
                                <input 
                                    wire:model="quick_wa_nomor" 
                                    type="text" 
                                    placeholder="Contoh: 081234567890" 
                                    class="w-full pl-8 pr-3 py-2 text-xs rounded-lg border border-[#E2E8F0] bg-white text-[#0F172A] placeholder:text-slate-400 focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] shadow-2xs font-mono" 
                                    required
                                    autofocus
                                />
                            </div>
                            <x-input-error :messages="$errors->get('quick_wa_nomor')" class="mt-1" />
                            <p class="text-[11px] text-[#64748B] mt-1">
                                Klik simpan untuk langsung menuju ke WhatsApp dengan format pesan resmi tagihan.
                            </p>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="px-5 py-3 border-t border-[#E2E8F0] bg-slate-50/75 flex items-center justify-end gap-2.5 shrink-0">
                        <button wire:click="tutupModalWa" type="button" class="px-3.5 py-1.5 rounded-lg border border-[#E2E8F0] text-xs font-semibold text-[#0F172A] bg-white hover:bg-slate-50 transition cursor-pointer shadow-2xs">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-1.5 rounded-lg bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 shadow-xs disabled:opacity-75">
                            <x-icon name="message-circle" class="w-3.5 h-3.5 text-white" />
                            <span>Simpan & Buka WA</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Tagihan -->
    @if ($modalHapusTerbuka && $hapusInvoice)
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
                    <h3 class="text-base font-bold text-[#0F172A] mb-1">Konfirmasi Hapus Data Tagihan</h3>
                    <p class="text-xs text-[#64748B] mb-5 leading-relaxed">
                        Apakah Anda yakin ingin menghapus tagihan <strong class="text-[#0F172A]">{{ $hapusInvoice->kode_tagihan }}</strong> ({{ $hapusInvoice->pihak_terkait }}) senilai <strong class="text-[#0F172A]">{{ $hapusInvoice->formatted_nominal }}</strong>? Tindakan ini akan menghapus data tagihan secara permanen.
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
                            wire:click="hapusTagihan" 
                            type="button" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5 shadow-xs disabled:opacity-75"
                        >
                            <svg wire:loading wire:target="hapusTagihan" class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Ya, Hapus Tagihan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

