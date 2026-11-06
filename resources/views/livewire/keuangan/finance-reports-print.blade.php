<div class="space-y-4">
    <!-- Screen-only Control Header -->
    <div class="print:hidden space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex-1 min-w-0">
                <h1 class="font-bold text-lg sm:text-xl text-[#0F172A] tracking-tight leading-snug">
                    Laporan Keuangan & Biaya Operasional untuk Dicetak
                </h1>
                <p class="text-xs text-[#64748B] mt-0.5 leading-relaxed">
                    Pusat cetak dokumen dan ekspor rekapitulasi pengeluaran, pemasukan, logistik armada, dan operasional pabrik
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <a 
                    href="{{ route('keuangan.export-pdf') }}" 
                    target="_blank"
                    class="px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
                >
                    <x-icon name="file-text" class="w-4 h-4 shrink-0" />
                    <span>Unduh PDF Resmi</span>
                </a>
                <a 
                    href="{{ route('keuangan.export-excel') }}" 
                    class="px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
                >
                    <x-icon name="file-spreadsheet" class="w-4 h-4 shrink-0" />
                    <span>Export Excel</span>
                </a>
                <button 
                    onclick="window.print()" 
                    type="button" 
                    class="px-3 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
                    title="Cetak langsung ke printer fisik"
                >
                    <x-icon name="printer" class="w-4 h-4 shrink-0 text-slate-500" />
                    <span>Print</span>
                </button>
            </div>
        </div>

        <!-- Filter & Parameter Toolbar -->
        <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Laporan</label>
                    <select 
                        wire:model.live="periode"
                        class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                    >
                        <option value="hari_ini">Hari Ini</option>
                        <option value="minggu_ini">Minggu Ini</option>
                        <option value="bulan_ini">Bulan Ini (Default)</option>
                        <option value="30_hari">30 Hari Terakhir</option>
                        <option value="kuartal_ini">Kuartal Ini</option>
                        <option value="tahun_ini">Tahun Berjalan</option>
                        <option value="custom">Kustom Tanggal...</option>
                    </select>
                </div>

                @if ($periode === 'custom')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                        <input 
                            wire:model.live="startDate" 
                            type="date" 
                            class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                        <input 
                            wire:model.live="endDate" 
                            type="date" 
                            class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                        >
                    </div>
                @endif

                <div class="{{ $periode === 'custom' ? '' : 'sm:col-span-3' }}">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Bagian Laporan</label>
                    <select 
                        wire:model.live="kategori"
                        class="w-full py-1.5 px-2.5 rounded-lg border border-[#E2E8F0] text-xs focus:ring-1 focus:ring-[#2563EB] focus:border-[#2563EB]"
                    >
                        <option value="semua">Semua Modul (Komprehensif)</option>
                        <option value="barang_masuk">Khusus Pengadaan Barang Masuk (Bahan Baku / Cat)</option>
                        <option value="barang_keluar">Khusus Penjualan Barang Keluar (Hasil Produksi)</option>
                        <option value="kendaraan">Khusus Biaya Perbaikan & Armada Mobil</option>
                        <option value="operasional">Khusus Biaya Operasional Pabrik & Kantor</option>
                        <option value="tagihan">Khusus Piutang & Tagihan Faktur</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards on Screen -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
                <div class="flex items-center justify-between text-xs text-[#64748B]">
                    <span>Pemasukan Penjualan</span>
                    <x-icon name="arrow-up-right" class="w-4 h-4 text-emerald-600" />
                </div>
                <p class="text-base sm:text-lg font-bold text-emerald-700 mt-1 tabular-nums">
                    <x-animated-number :value="$totalPemasukanPenjualan" prefix="Rp " />
                </p>
                <p class="text-[11px] text-[#64748B] mt-1">Barang keluar / drum siap kirim</p>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
                <div class="flex items-center justify-between text-xs text-[#64748B]">
                    <span>Total Pengeluaran</span>
                    <x-icon name="arrow-down-left" class="w-4 h-4 text-rose-500" />
                </div>
                <p class="text-base sm:text-lg font-bold text-rose-600 mt-1 tabular-nums">
                    <x-animated-number :value="$totalPengeluaran" prefix="Rp " />
                </p>
                <p class="text-[11px] text-[#64748B] mt-1">Pengadaan, mobil, & operasional</p>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
                <div class="flex items-center justify-between text-xs text-[#64748B]">
                    <span>Saldo Bersih Periode</span>
                    <x-icon name="wallet" class="w-4 h-4 text-[#2563EB]" />
                </div>
                <p class="text-base sm:text-lg font-bold {{ $saldoBersih >= 0 ? 'text-[#2563EB]' : 'text-rose-600' }} mt-1 tabular-nums">
                    <x-animated-number :value="$saldoBersih" prefix="Rp " />
                </p>
                <p class="text-[11px] text-[#64748B] mt-1">{{ $saldoBersih >= 0 ? 'Surplus Operasional' : 'Defisit Operasional' }}</p>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E2E8F0] shadow-2xs">
                <div class="flex items-center justify-between text-xs text-[#64748B]">
                    <span>Faktur Piutang Lunas</span>
                    <x-icon name="circle-check" class="w-4 h-4 text-emerald-600" />
                </div>
                <p class="text-base sm:text-lg font-bold text-slate-800 mt-1 tabular-nums">
                    <x-animated-number :value="$totalTagihanLunas" prefix="Rp " />
                </p>
                <p class="text-[11px] text-amber-600 mt-1 font-medium">Tertunggak: <x-animated-number :value="$totalTagihanTertunggak" prefix="Rp " /></p>
            </div>
        </div>
    </div>

    <!-- Printable Official Document Paper Container -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] p-6 sm:p-8 shadow-sm print:border-none print:shadow-none print:p-0">
        <!-- Letterhead (KOP SURAT) -->
        <div class="border-b-2 border-slate-900 pb-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-slate-900 uppercase">
                        PT Pulo Kencana Drum Industri
                    </h2>
                    <p class="text-xs text-slate-600 font-medium">
                        Kawasan Industri Pulo Gadung, Jakarta Timur • Telepon: (021) 460-1234 • Email: finance@pulokencana.co.id
                    </p>
                    <p class="text-[11px] text-slate-500">
                        Spesialis Rekondisi Drum Logam, Epoxy Coating, dan Logistik Industri
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300 uppercase">
                        Dokumen Resmi
                    </span>
                    <p class="text-[11px] text-slate-500 mt-1 font-mono">Dicetak: {{ $tanggalCetak }}</p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center text-xs">
                <div>
                    <span class="font-bold text-slate-800 uppercase">Laporan:</span>
                    <span class="text-slate-700">Rekapitulasi Arus Kas & Biaya Operasional</span>
                </div>
                <div>
                    <span class="font-bold text-slate-800 uppercase">Periode:</span>
                    <span class="font-semibold text-blue-700">{{ $formattedStart }} s/d {{ $formattedEnd }}</span>
                </div>
            </div>
        </div>

        <!-- Ringkasan Eksekutif Keuangan -->
        <div class="mb-6 bg-slate-50 p-4 rounded-lg border border-slate-200">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                Ringkasan Eksekutif Keuangan Periode Ini
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="text-slate-500 block text-[11px]">Penjualan Barang Keluar</span>
                    <span class="font-bold text-emerald-700">Rp {{ number_format($totalPemasukanPenjualan, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[11px]">Pengadaan Barang Masuk</span>
                    <span class="font-bold text-rose-600">Rp {{ number_format($totalPengadaanBarang, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[11px]">Biaya Mobil & Armada</span>
                    <span class="font-bold text-rose-600">Rp {{ number_format($totalBiayaMobil, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[11px]">Operasional Pabrik</span>
                    <span class="font-bold text-rose-600">Rp {{ number_format($totalOperasionalPabrik, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-200 flex justify-between items-center text-xs font-bold">
                <span class="text-slate-800">Saldo Bersih Operasional (Net Surplus / Defisit):</span>
                <span class="{{ $saldoBersih >= 0 ? 'text-blue-700' : 'text-rose-600' }} text-sm font-mono">
                    Rp {{ number_format($saldoBersih, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Section 1: Barang Masuk & Pengadaan (Jika dicakup) -->
        @if (($kategori === 'semua' || $kategori === 'barang_masuk') && $stockLogs->where('jenis_transaksi', 'pengeluaran')->count() > 0)
            <div class="mb-6">
                <h4 class="text-xs font-bold text-slate-900 uppercase border-b border-slate-200 pb-1.5 mb-2 flex items-center justify-between">
                    <span>1. Rincian Pengadaan Barang Masuk (Bahan Baku & Cat)</span>
                    <span class="text-[11px] font-normal text-slate-500">{{ $stockLogs->where('jenis_transaksi', 'pengeluaran')->count() }} Transaksi</span>
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[11px]">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                                <th class="py-1.5 px-2">Tanggal</th>
                                <th class="py-1.5 px-2">Kode Referensi</th>
                                <th class="py-1.5 px-2">Supplier</th>
                                <th class="py-1.5 px-2">Keterangan Item</th>
                                <th class="py-1.5 px-2 text-center">Jumlah</th>
                                <th class="py-1.5 px-2 text-right">Biaya Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($stockLogs->where('jenis_transaksi', 'pengeluaran') as $log)
                                <tr>
                                    <td class="py-1.5 px-2 whitespace-nowrap">{{ $log->tanggal_transaksi->format('d/m/Y') }}</td>
                                    <td class="py-1.5 px-2 font-mono whitespace-nowrap">{{ $log->kode_referensi ?: '-' }}</td>
                                    <td class="py-1.5 px-2">{{ $log->supplier?->nama_supplier ?: '-' }}</td>
                                    <td class="py-1.5 px-2">{{ $log->keterangan }}</td>
                                    <td class="py-1.5 px-2 text-center">{{ $log->jumlah_unit ? number_format($log->jumlah_unit).' unit' : '-' }}</td>
                                    <td class="py-1.5 px-2 text-right font-medium text-rose-600">Rp {{ number_format($log->nominal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 font-bold border-t border-slate-300">
                                <td colspan="5" class="py-1.5 px-2 text-right">Subtotal Pengadaan Barang Masuk:</td>
                                <td class="py-1.5 px-2 text-right text-rose-700">Rp {{ number_format($totalPengadaanBarang, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- Section 2: Barang Keluar & Penjualan (Jika dicakup) -->
        @if (($kategori === 'semua' || $kategori === 'barang_keluar') && $stockLogs->where('jenis_transaksi', 'pemasukan')->count() > 0)
            <div class="mb-6">
                <h4 class="text-xs font-bold text-slate-900 uppercase border-b border-slate-200 pb-1.5 mb-2 flex items-center justify-between">
                    <span>2. Rincian Penjualan Barang Keluar (Drum Siap Pakai)</span>
                    <span class="text-[11px] font-normal text-slate-500">{{ $stockLogs->where('jenis_transaksi', 'pemasukan')->count() }} Transaksi</span>
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[11px]">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                                <th class="py-1.5 px-2">Tanggal</th>
                                <th class="py-1.5 px-2">Kode Referensi</th>
                                <th class="py-1.5 px-2">Keterangan Pengiriman</th>
                                <th class="py-1.5 px-2 text-center">Jumlah</th>
                                <th class="py-1.5 px-2 text-right">Nilai Penjualan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($stockLogs->where('jenis_transaksi', 'pemasukan') as $log)
                                <tr>
                                    <td class="py-1.5 px-2 whitespace-nowrap">{{ $log->tanggal_transaksi->format('d/m/Y') }}</td>
                                    <td class="py-1.5 px-2 font-mono whitespace-nowrap">{{ $log->kode_referensi ?: '-' }}</td>
                                    <td class="py-1.5 px-2">{{ $log->keterangan }}</td>
                                    <td class="py-1.5 px-2 text-center">{{ $log->jumlah_unit ? number_format($log->jumlah_unit).' unit' : '-' }}</td>
                                    <td class="py-1.5 px-2 text-right font-medium text-emerald-700">Rp {{ number_format($log->nominal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 font-bold border-t border-slate-300">
                                <td colspan="4" class="py-1.5 px-2 text-right">Subtotal Pemasukan Barang Keluar:</td>
                                <td class="py-1.5 px-2 text-right text-emerald-800">Rp {{ number_format($totalPemasukanPenjualan, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- Section 3: Biaya Mobil & Armada (Jika dicakup) -->
        @if (($kategori === 'semua' || $kategori === 'kendaraan') && $repairs->count() > 0)
            <div class="mb-6">
                <h4 class="text-xs font-bold text-slate-900 uppercase border-b border-slate-200 pb-1.5 mb-2 flex items-center justify-between">
                    <span>3. Rincian Biaya Mobil & Pemeliharaan Armada Truk</span>
                    <span class="text-[11px] font-normal text-slate-500">{{ $repairs->count() }} Tindakan</span>
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[11px]">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                                <th class="py-1.5 px-2">Tanggal</th>
                                <th class="py-1.5 px-2">Nomor Polisi</th>
                                <th class="py-1.5 px-2">Jenis Kendaraan</th>
                                <th class="py-1.5 px-2">Jenis Perbaikan / Servis</th>
                                <th class="py-1.5 px-2">Bengkel Rekanan</th>
                                <th class="py-1.5 px-2 text-right">Biaya Servis</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($repairs as $repair)
                                <tr>
                                    <td class="py-1.5 px-2 whitespace-nowrap">{{ $repair->tanggal_perbaikan->format('d/m/Y') }}</td>
                                    <td class="py-1.5 px-2 font-mono font-semibold whitespace-nowrap">{{ $repair->nomor_polisi }}</td>
                                    <td class="py-1.5 px-2">{{ $repair->jenis_kendaraan ?: 'Truk Operasional' }}</td>
                                    <td class="py-1.5 px-2">{{ $repair->deskripsi_perbaikan }}</td>
                                    <td class="py-1.5 px-2">{{ $repair->nama_bengkel ?: '-' }}</td>
                                    <td class="py-1.5 px-2 text-right font-medium text-rose-600">Rp {{ number_format($repair->biaya, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 font-bold border-t border-slate-300">
                                <td colspan="5" class="py-1.5 px-2 text-right">Subtotal Biaya Mobil & Armada:</td>
                                <td class="py-1.5 px-2 text-right text-rose-700">Rp {{ number_format($totalBiayaMobil, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- Section 4: Biaya Operasional Pabrik (Jika dicakup) -->
        @if (($kategori === 'semua' || $kategori === 'operasional') && $expenses->count() > 0)
            <div class="mb-6">
                <h4 class="text-xs font-bold text-slate-900 uppercase border-b border-slate-200 pb-1.5 mb-2 flex items-center justify-between">
                    <span>4. Rincian Catatan Biaya Operasional Pabrik & Kantor</span>
                    <span class="text-[11px] font-normal text-slate-500">{{ $expenses->count() }} Pengeluaran</span>
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[11px]">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                                <th class="py-1.5 px-2">Tanggal</th>
                                <th class="py-1.5 px-2">Kategori</th>
                                <th class="py-1.5 px-2">Uraian Kebutuhan</th>
                                <th class="py-1.5 px-2">Metode / PIC</th>
                                <th class="py-1.5 px-2 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($expenses as $exp)
                                <tr>
                                    <td class="py-1.5 px-2 whitespace-nowrap">{{ $exp->tanggal_pengeluaran->format('d/m/Y') }}</td>
                                    <td class="py-1.5 px-2 whitespace-nowrap font-medium">{{ $exp->kategori }}</td>
                                    <td class="py-1.5 px-2">{{ $exp->keterangan }}</td>
                                    <td class="py-1.5 px-2">
                                        {{ $exp->metode_pembayaran }}
                                        @if($exp->employee)
                                            <span class="text-slate-500">({{ $exp->employee->nama_karyawan }})</span>
                                        @endif
                                    </td>
                                    <td class="py-1.5 px-2 text-right font-medium text-rose-600">Rp {{ number_format($exp->nominal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 font-bold border-t border-slate-300">
                                <td colspan="4" class="py-1.5 px-2 text-right">Subtotal Biaya Operasional Pabrik:</td>
                                <td class="py-1.5 px-2 text-right text-rose-700">Rp {{ number_format($totalOperasionalPabrik, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- Section 5: Riwayat Tagihan Faktur (Jika dicakup) -->
        @if (($kategori === 'semua' || $kategori === 'tagihan') && $invoices->count() > 0)
            <div class="mb-6">
                <h4 class="text-xs font-bold text-slate-900 uppercase border-b border-slate-200 pb-1.5 mb-2 flex items-center justify-between">
                    <span>5. Rincian Tagihan Faktur & Piutang Pelanggan / Vendor</span>
                    <span class="text-[11px] font-normal text-slate-500">{{ $invoices->count() }} Faktur</span>
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[11px]">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                                <th class="py-1.5 px-2">Tanggal Terbit</th>
                                <th class="py-1.5 px-2">Kode Faktur</th>
                                <th class="py-1.5 px-2">Pihak Terkait / Rekanan</th>
                                <th class="py-1.5 px-2 text-center">Status</th>
                                <th class="py-1.5 px-2 text-right">Nilai Tagihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($invoices as $inv)
                                <tr>
                                    <td class="py-1.5 px-2 whitespace-nowrap">{{ $inv->tanggal_terbit->format('d/m/Y') }}</td>
                                    <td class="py-1.5 px-2 font-mono whitespace-nowrap">{{ $inv->kode_tagihan }}</td>
                                    <td class="py-1.5 px-2">{{ $inv->pihak_terkait }}</td>
                                    <td class="py-1.5 px-2 text-center whitespace-nowrap">
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $inv->status_pembayaran === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ strtoupper(str_replace('_', ' ', $inv->status_pembayaran)) }}
                                        </span>
                                    </td>
                                    <td class="py-1.5 px-2 text-right font-medium {{ $inv->status_pembayaran === 'lunas' ? 'text-emerald-700' : 'text-slate-700' }}">
                                        Rp {{ number_format($inv->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 font-bold border-t border-slate-300">
                                <td colspan="4" class="py-1.5 px-2 text-right">Total Tagihan Lunas:</td>
                                <td class="py-1.5 px-2 text-right text-emerald-700">
                                    Rp {{ number_format($totalTagihanLunas, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- Signatures Block for Official Printing -->
        <div class="mt-12 pt-6 border-t border-slate-300 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <p class="text-slate-500 mb-16">Dipersiapkan & Disusun Oleh:</p>
                <p class="font-bold text-slate-900 uppercase underline">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-slate-500">Bagian Administrasi Keuangan PKDI</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Mengetahui & Menyetujui:</p>
                <p class="font-bold text-slate-900 uppercase underline">Direktur Operasional / Keuangan</p>
                <p class="text-[11px] text-slate-500">PT Pulo Kencana Drum Industri</p>
            </div>
        </div>
    </div>
</div>

