<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DrumBatch;
use App\Models\DrumProcessLog;
use App\Models\VehicleVisit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    /**
     * Ekspor Rekapitulasi Laporan Resmi ke format PDF (Kop PT Pulo Kencana Drum Industri)
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // 1. Ambil data Batch Drum dalam periode
        $batches = DrumBatch::with('user')
            ->whereDate('tanggal_penerimaan', '>=', $startDate)
            ->whereDate('tanggal_penerimaan', '<=', $endDate)
            ->orderBy('tanggal_penerimaan', 'desc')
            ->get();

        // 2. Ambil data 4 Log Proses Gudang dalam periode (Berdasarkan tanggal_aktivitas lapangan)
        $processLogs = DrumProcessLog::with(['drumBatch', 'user'])
            ->whereDate('tanggal_aktivitas', '>=', $startDate)
            ->whereDate('tanggal_aktivitas', '<=', $endDate)
            ->orderBy('tanggal_aktivitas', 'desc')
            ->get();

        // 3. Ambil data Kunjungan Kendaraan dalam periode
        $visits = VehicleVisit::with('user')
            ->whereDate('waktu_masuk', '>=', $startDate)
            ->whereDate('waktu_masuk', '<=', $endDate)
            ->orderBy('waktu_masuk', 'desc')
            ->get();

        // 4. Ringkasan Metrik Eksekutif
        $metrics = [
            'total_batch' => $batches->count(),
            'total_drum_diterima' => $batches->sum('jumlah_diterima'),
            'drum_transit' => $batches->whereIn('status_pengendapan', ['Transit', 'Mengendap'])->sum('jumlah_diterima'),
            'drum_sortir' => $processLogs->where('jenis_proses', 'penyortiran')->sum('jumlah_diproses'),
            'drum_tiup' => $processLogs->where('jenis_proses', 'peniupan')->sum('jumlah_diproses'),
            'drum_cuci' => $processLogs->where('jenis_proses', 'pencucian')->sum('jumlah_diproses'),
            'drum_cat' => $processLogs->where('jenis_proses', 'pengecatan')->sum('jumlah_diproses'),
            'total_afkir' => $processLogs->sum('jumlah_afkir'),
            'total_kunjungan' => $visits->count(),
            'kunjungan_b3' => $visits->where('status_pemeriksaan_surat_jalan_b3', 'sesuai')->count(),
        ];

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'startDate' => $start->format('d/m/Y'),
            'endDate' => $end->format('d/m/Y'),
            'batches' => $batches,
            'processLogs' => $processLogs,
            'visits' => $visits,
            'metrics' => $metrics,
            'generatedAt' => now()->translatedFormat('d F Y, H:i'),
            'adminName' => auth()->user()->name,
        ])->setPaper('a4', 'portrait');

        $filename = 'Laporan-Rekap-PKDI-' . $start->format('Ymd') . '-' . $end->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Ekspor Data Batch Drum ke format Excel (.csv dengan UTF-8 BOM)
     */
    public function exportBatchCsv(Request $request): StreamedResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $batches = DrumBatch::with('user')
            ->whereDate('tanggal_penerimaan', '>=', $startDate)
            ->whereDate('tanggal_penerimaan', '<=', $endDate)
            ->orderBy('tanggal_penerimaan', 'desc')
            ->get();

        $filename = 'rekap-batch-drum-' . $startDate . '-sd-' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($batches) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'Kode Batch',
                'Tanggal Penerimaan',
                'Kategori / Jenis Drum',
                'Jumlah Diterima (Pcs)',
                'Status Pengendapan',
                'Status Proses',
                'Sumber Laporan',
                'Catatan Gudang',
                'Diinput Oleh',
                'Waktu Input Sistem',
            ]);

            $no = 1;
            foreach ($batches as $batch) {
                fputcsv($handle, [
                    $no++,
                    $batch->kode_batch,
                    $batch->tanggal_penerimaan?->format('Y-m-d') ?? '-',
                    $batch->kategori_jenis,
                    $batch->jumlah_diterima,
                    $batch->status_pengendapan,
                    $batch->status_proses,
                    $batch->sumber_laporan ?? '-',
                    $batch->catatan ?? '-',
                    $batch->user?->name ?? 'Admin',
                    $batch->created_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Ekspor Catatan 4 Tahap Proses Gudang ke format Excel (.csv)
     */
    public function exportProcessCsv(Request $request): StreamedResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $logs = DrumProcessLog::with(['drumBatch', 'user'])
            ->whereDate('tanggal_aktivitas', '>=', $startDate)
            ->whereDate('tanggal_aktivitas', '<=', $endDate)
            ->orderBy('tanggal_aktivitas', 'desc')
            ->get();

        $filename = 'rekap-proses-gudang-' . $startDate . '-sd-' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'Kode Batch',
                'Jenis Proses',
                'Tgl Aktivitas Fisik (Gudang)',
                'Tgl Input Sistem (Admin)',
                'Jumlah Diproses (Pcs)',
                'Jumlah Afkir / Reject (Pcs)',
                'Jumlah Lolos Baik (Pcs)',
                'Pelapor / Mandor Gudang',
                'Hasil & Catatan Rekondisi',
                'Admin Pencatat',
            ]);

            $no = 1;
            foreach ($logs as $log) {
                $lolos = max(0, $log->jumlah_diproses - $log->jumlah_afkir);
                fputcsv($handle, [
                    $no++,
                    $log->drumBatch?->kode_batch ?? '-',
                    $log->label_proses,
                    $log->tanggal_aktivitas?->format('Y-m-d') ?? '-',
                    $log->created_at?->format('Y-m-d H:i') ?? '-',
                    $log->jumlah_diproses,
                    $log->jumlah_afkir,
                    $lolos,
                    $log->pelapor_gudang ?? '-',
                    $log->hasil_catatan ?? '-',
                    $log->user?->name ?? 'Admin',
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Ekspor Data Kunjungan Kendaraan ke format Excel (.csv)
     */
    public function exportVehicleCsv(Request $request): StreamedResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $visits = VehicleVisit::with('user')
            ->whereDate('waktu_masuk', '>=', $startDate)
            ->whereDate('waktu_masuk', '<=', $endDate)
            ->orderBy('waktu_masuk', 'desc')
            ->get();

        $filename = 'rekap-kunjungan-kendaraan-' . $startDate . '-sd-' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($visits) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'Nomor Kunjungan',
                'Nomor Polisi',
                'Nama Pengemudi',
                'Perusahaan / Asal-Tujuan',
                'Keperluan',
                'Waktu Masuk',
                'Waktu Keluar',
                'Status Kunjungan',
                'Status Dokumen Surat Jalan B3',
                'Nomor / Keterangan Surat Jalan',
                'Keterangan Muatan',
                'Petugas Satpam Pos Gerbang',
            ]);

            $no = 1;
            foreach ($visits as $visit) {
                fputcsv($handle, [
                    $no++,
                    $visit->nomor_kunjungan,
                    $visit->nomor_polisi,
                    $visit->nama_pengemudi,
                    $visit->asal_tujuan ?? '-',
                    $visit->keperluan ?? '-',
                    $visit->waktu_masuk?->format('Y-m-d H:i') ?? '-',
                    $visit->waktu_keluar?->format('Y-m-d H:i') ?? 'Masih di Area Pabrik',
                    $visit->waktu_keluar ? 'Selesai' : 'Aktif',
                    $visit->status_pemeriksaan_surat_jalan_b3 ?? 'Bukan B3 / Tanpa Surat Jalan',
                    $visit->informasi_surat_jalan ?? '-',
                    $visit->keterangan_muatan ?? '-',
                    $visit->user?->name ?? 'Petugas Satpam',
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
