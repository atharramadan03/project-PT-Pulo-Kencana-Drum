<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Operasional Gudang - PT Pulo Kencana Drum Industri</title>
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
        .metric-title {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }
        .metric-value {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #94a3b8;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
        .badge-transit { background-color: #fef3c7; color: #92400e; }
        .badge-selesai { background-color: #dcfce7; color: #166534; }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 50%;
        }
        .signature-space {
            height: 50px;
        }
        .footer-note {
            margin-top: 15px;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- KOP PERUSAHAAN -->
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="company-title">PT Pulo Kencana Drum Industri</div>
                <div class="company-subtitle">
                    Kawasan Industri Pulogadung, Jakarta Timur • Telp: (021) 460-XXXX<br>
                    Spesialis Rekondisi Drum Baja, Seng & Kemasan Industri Berizin KLHK
                </div>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: bottom;">
                <span style="font-size: 8pt; color: #64748b;">
                    Dokumen Administrasi Internal<br>
                    Cetak: {{ $generatedAt }}
                </span>
            </td>
        </tr>
    </table>

    <div class="report-title">Rekapitulasi Laporan Operasional & Gudang</div>
    <div class="report-period">Periode: <strong>{{ $startDate }}</strong> s/d <strong>{{ $endDate }}</strong></div>

    <!-- METRIK EKSEKUTIF -->
    <table class="metrics-table">
        <tr>
            <td class="metric-box">
                <div class="metric-title">Total Batch Diterima</div>
                <div class="metric-value">{{ $metrics['total_batch'] }} <span style="font-size: 8pt; font-weight: normal;">Batch</span></div>
                <div style="font-size: 7.5pt; color: #64748b;">{{ number_format($metrics['total_drum_diterima']) }} Drum Fisik</div>
            </td>
            <td class="metric-box">
                <div class="metric-title">Masa Pengendapan</div>
                <div class="metric-value" style="color: #b45309;">{{ number_format($metrics['drum_transit']) }} <span style="font-size: 8pt; font-weight: normal;">Drum</span></div>
                <div style="font-size: 7.5pt; color: #64748b;">Status Transit & Mengendap</div>
            </td>
            <td class="metric-box">
                <div class="metric-title">Drum Diproses (4 Tahap)</div>
                <div class="metric-value" style="color: #047857;">{{ number_format($metrics['drum_sortir'] + $metrics['drum_tiup'] + $metrics['drum_cuci'] + $metrics['drum_cat']) }} <span style="font-size: 8pt; font-weight: normal;">Pcs</span></div>
                <div style="font-size: 7.5pt; color: #64748b;">Sortir / Tiup / Cuci / Cat</div>
            </td>
            <td class="metric-box">
                <div class="metric-title">Total Reject</div>
                <div class="metric-value" style="color: #b91c1c;">{{ number_format($metrics['total_afkir']) }} <span style="font-size: 8pt; font-weight: normal;">Pcs</span></div>
                <div style="font-size: 7.5pt; color: #64748b;">Kerusakan Tidak Lolos Rekondisi</div>
            </td>
        </tr>
    </table>

    <!-- 1. TABEL PENERIMAAN & TRANSIT BATCH DRUM -->
    <div class="section-title">1. Data Penerimaan & Status Masa Pengendapan (Transit)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 18%;">Kode Batch</th>
                <th style="width: 12%;">Tgl Terima</th>
                <th style="width: 20%;">Kategori Jenis</th>
                <th style="width: 10%;" class="text-center">Jumlah</th>
                <th style="width: 14%;">Pengendapan</th>
                <th style="width: 12%;">Proses</th>
                <th style="width: 10%;">Pelapor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($batches as $index => $b)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $b->kode_batch }}</strong></td>
                    <td>{{ $b->tanggal_penerimaan?->format('d/m/Y') }}</td>
                    <td>{{ $b->kategori_jenis }}</td>
                    <td class="text-center font-bold">{{ number_format($b->jumlah_diterima) }}</td>
                    <td>
                        <span class="badge {{ in_array($b->status_pengendapan, ['Transit', 'Mengendap']) ? 'badge-transit' : 'badge-selesai' }}">
                            {{ $b->status_pengendapan }}
                        </span>
                    </td>
                    <td>{{ $b->status_proses }}</td>
                    <td>{{ $b->sumber_laporan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="color: #94a3b8; padding: 12px;">Tidak ada data batch penerimaan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. TABEL 4 TAHAP PROSES GUDANG -->
    <div class="section-title">2. Catatan 4 Tahap Proses Gudang (Sortir, Tiup, Cuci, Cat)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 16%;">Kode Batch</th>
                <th style="width: 14%;">Tahap Proses</th>
                <th style="width: 13%;">Tgl Lapangan</th>
                <th style="width: 13%;">Tgl Input Sistem</th>
                <th style="width: 9%;" class="text-center">Diproses</th>
                <th style="width: 8%;" class="text-center">Reject</th>
                <th style="width: 8%;" class="text-center">Lolos</th>
                <th style="width: 15%;">Pelapor & Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($processLogs as $index => $log)
                @php $lolos = max(0, $log->jumlah_diproses - $log->jumlah_afkir); @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $log->drumBatch?->kode_batch ?? '-' }}</strong></td>
                    <td><strong>{{ $log->label_proses }}</strong></td>
                    <td>{{ $log->tanggal_aktivitas?->format('d/m/Y') }}</td>
                    <td style="color: #64748b;">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="text-center">{{ number_format($log->jumlah_diproses) }}</td>
                    <td class="text-center" style="color: #dc2626; font-weight: bold;">{{ number_format($log->jumlah_afkir) }}</td>
                    <td class="text-center" style="color: #16a34a; font-weight: bold;">{{ number_format($lolos) }}</td>
                    <td>
                        <strong>{{ $log->pelapor_gudang ?? 'Mandor' }}</strong>
                        @if($log->hasil_catatan)<br><span style="color: #64748b;">{{ $log->hasil_catatan }}</span>@endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="color: #94a3b8; padding: 12px;">Tidak ada catatan proses gudang pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 3. TABEL ARUS KENDARAAN & SURAT JALAN B3 -->
    <div class="section-title">3. Ringkasan Kunjungan Kendaraan & Pemeriksaan Limbah B3</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 18%;">No Kunjungan</th>
                <th style="width: 14%;">No Polisi</th>
                <th style="width: 16%;">Pengemudi</th>
                <th style="width: 20%;">Perusahaan / Keperluan</th>
                <th style="width: 14%;">Waktu Masuk</th>
                <th style="width: 14%;">Status Surat Jalan B3</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visits->take(15) as $index => $v)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $v->nomor_kunjungan }}</strong></td>
                    <td>{{ $v->nomor_polisi }}</td>
                    <td>{{ $v->nama_pengemudi }}</td>
                    <td>{{ $v->asal_tujuan ?? '-' }} ({{ $v->keperluan }})</td>
                    <td>{{ $v->waktu_masuk?->format('d/m/Y H:i') }}</td>
                    <td>{{ $v->status_pemeriksaan_surat_jalan_b3 ?? 'Bukan B3' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="color: #94a3b8; padding: 12px;">Tidak ada catatan kunjungan kendaraan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- PENGESAHAN LAPORAN -->
    <table class="signature-table">
        <tr>
            <td class="signature-box">
                Dibuat Oleh,<br>
                <strong>Admin Kantor</strong>
                <div class="signature-space"></div>
                <u>( {{ $adminName }} )</u><br>
                <span style="font-size: 7.5pt; color: #64748b;">Departemen Administrasi Operasional</span>
            </td>
            <td class="signature-box">
                Mengetahui & Menyetujui,<br>
                <strong>Kepala Pabrik / Manajemen</strong>
                <div class="signature-space"></div>
                <u>( .................................................. )</u><br>
                <span style="font-size: 7.5pt; color: #64748b;">PT Pulo Kencana Drum Industri</span>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        * Catatan Sistem: Laporan ini dihasilkan secara otomatis oleh Sistem Administrasi Operasional PT Pulo Kencana Drum Industri. Status operasional drum dalam laporan ini merepresentasikan data rekonsiliasi yang diterima dari bagian gudang dan gerbang.
    </div>

</body>
</html>
