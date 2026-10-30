<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Keuangan Operasional - PT Pulo Kencana Drum Industri</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #333333;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #1e293b;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .company-subtitle {
            font-size: 8pt;
            color: #64748b;
        }
        .report-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 4px;
            color: #0f172a;
        }
        .report-period {
            text-align: center;
            font-size: 9pt;
            color: #475569;
            margin-bottom: 15px;
        }
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .metric-box {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: center;
            background-color: #f8fafc;
            width: 25%;
        }
        .metric-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .metric-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }
        .section-header {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-top: 16px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #0f172a;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>

    <!-- KOP RESMI PERUSAHAAN -->
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="company-title">PT Pulo Kencana Drum Industri</div>
                <div class="company-subtitle">Spesialis Rekondisi, Pencucian & Pengadaan Drum Industri (Besi & Plastik)</div>
                <div class="company-subtitle">Kawasan Industri Pulo Gadung / Cikarang - Jawa Barat</div>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: bottom;">
                <div style="font-size: 8pt; color: #64748b;">Dokumen Internal Perusahaan</div>
                <div style="font-size: 8pt; font-weight: bold; color: #0f172a;">Dicetak: {{ $tanggalCetak }}</div>
            </td>
        </tr>
    </table>

    <!-- JUDUL LAPORAN -->
    <div class="report-title">Laporan Rekapitulasi Keuangan Operasional</div>
    <div class="report-period">Arus Kas Mutasi Stok Gudang & Biaya Pemeliharaan Armada Kendaraan</div>

    <!-- METRIK RINGKASAN (4 KOTAK) -->
    <table class="metrics-table">
        <tr>
            <td class="metric-box">
                <div class="metric-label">Pemasukan Penjualan Stok</div>
                <div class="metric-value" style="color: #10b981;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </td>
            <td class="metric-box">
                <div class="metric-label">Pengeluaran Stok Gudang</div>
                <div class="metric-value" style="color: #f59e0b;">Rp {{ number_format($totalPengeluaranStok, 0, ',', '.') }}</div>
            </td>
            <td class="metric-box">
                <div class="metric-label">Biaya Perbaikan Armada</div>
                <div class="metric-value" style="color: #ef4444;">Rp {{ number_format($totalBiayaPerbaikan, 0, ',', '.') }}</div>
            </td>
            <td class="metric-box">
                <div class="metric-label">Saldo Bersih Operasional</div>
                <div class="metric-value" style="color: #2563eb;">Rp {{ number_format($netOperasional, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <!-- 1. DATA MUTASI KEUANGAN STOK GUDANG -->
    <div class="section-header">1. Riwayat Transaksi Keuangan Stok Gudang</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 18%;">No. Transaksi</th>
                <th style="width: 12%;">Jenis</th>
                <th style="width: 25%;">Pihak Terkait</th>
                <th class="text-center" style="width: 10%;">Unit</th>
                <th class="text-center" style="width: 12%;">Tanggal</th>
                <th class="text-right" style="width: 18%;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stockLogs as $index => $trx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $trx->kode_transaksi }}</td>
                    <td>{{ ucfirst($trx->jenis_transaksi) }}</td>
                    <td>{{ $trx->pihak_terkait }}</td>
                    <td class="text-center">{{ $trx->jumlah_unit ? $trx->jumlah_unit.' Unit' : '-' }}</td>
                    <td class="text-center">{{ $trx->tanggal_transaksi?->format('d/m/Y') }}</td>
                    <td class="text-right" style="font-weight: bold; color: {{ $trx->jenis_transaksi === 'pemasukan' ? '#10b981' : '#0f172a' }};">
                        {{ $trx->formatted_nominal }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 12px; color: #64748b;">Tidak ada data transaksi keuangan stok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. DATA BIAYA PERBAIKAN ARMADA KENDARAAN -->
    <div class="section-header">2. Riwayat Biaya Perbaikan & Servis Armada Mobil</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 16%;">No. Polisi</th>
                <th style="width: 20%;">Jenis Kendaraan</th>
                <th style="width: 23%;">Jenis Perbaikan</th>
                <th style="width: 18%;">Nama Bengkel</th>
                <th class="text-center" style="width: 8%;">Status</th>
                <th class="text-right" style="width: 15%;">Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($repairs as $index => $rep)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $rep->nomor_polisi }}</td>
                    <td>{{ $rep->jenis_kendaraan }}</td>
                    <td>{{ $rep->jenis_perbaikan }}</td>
                    <td>{{ $rep->nama_bengkel }}</td>
                    <td class="text-center" style="text-transform: capitalize;">{{ str_replace('_', ' ', $rep->status_perbaikan) }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ $rep->formatted_biaya }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 12px; color: #64748b;">Tidak ada data perbaikan armada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN & PENGESAHAN -->
    <table class="footer-table">
        <tr>
            <td class="signature-box" style="float: left;">
                <p>Mengetahui,</p>
                <p style="font-weight: bold; margin-top: -5px;">Kepala Bagian Operasional</p>
                <div style="height: 45px;"></div>
                <p style="text-decoration: underline; font-weight: bold;">( .................................................. )</p>
                <p style="font-size: 7.5pt; color: #64748b; margin-top: -8px;">NIP. 2026-OPR-001</p>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box" style="float: right;">
                <p>Bekasi / Cikarang, {{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                <p style="font-weight: bold; margin-top: -5px;">Petugas Admin Keuangan</p>
                <div style="height: 45px;"></div>
                <p style="text-decoration: underline; font-weight: bold;">( {{ auth()->user()?->name ?? 'Admin Keuangan' }} )</p>
                <p style="font-size: 7.5pt; color: #64748b; margin-top: -8px;">PT Pulo Kencana Drum Industri</p>
            </td>
        </tr>
    </table>

</body>
</html>

