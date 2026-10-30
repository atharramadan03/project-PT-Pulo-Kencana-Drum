<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DrumBatch;
use App\Models\DrumProcessLog;
use App\Models\VehicleVisit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ReportExportController extends Controller
{
    /**
     * Ekspor Rekapitulasi Laporan Resmi ke format PDF (Kop PT Pulo Kencana Drum Industri).
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $batches = DrumBatch::with('user')
            ->whereDate('tanggal_penerimaan', '>=', $startDate)
            ->whereDate('tanggal_penerimaan', '<=', $endDate)
            ->orderBy('tanggal_penerimaan', 'desc')
            ->get();

        $processLogs = DrumProcessLog::with(['drumBatch', 'user'])
            ->whereDate('tanggal_aktivitas', '>=', $startDate)
            ->whereDate('tanggal_aktivitas', '<=', $endDate)
            ->orderBy('tanggal_aktivitas', 'desc')
            ->get();

        $visits = VehicleVisit::with('user')
            ->whereDate('waktu_masuk', '>=', $startDate)
            ->whereDate('waktu_masuk', '<=', $endDate)
            ->orderBy('waktu_masuk', 'desc')
            ->get();

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

        $filename = 'Laporan-Rekap-PKDI-'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Ekspor Semua Data (Batch, Proses Gudang, dan Kunjungan Kendaraan) ke 1 file Excel (.xlsx) multi-sheet.
     */
    public function exportAllExcel(Request $request): BinaryFileResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $sheets = [
            [
                'name' => 'Data Batch Drum',
                'rows' => $this->getBatchRows($startDate, $endDate),
            ],
            [
                'name' => 'Proses Gudang',
                'rows' => $this->getProcessRows($startDate, $endDate),
            ],
            [
                'name' => 'Kunjungan Kendaraan',
                'rows' => $this->getVehicleRows($startDate, $endDate),
            ],
        ];

        $filename = 'Rekap-Semua-Data-PKDI-'.$startDate.'-sd-'.$endDate.'.xlsx';

        return $this->downloadXlsx($sheets, $filename);
    }

    /**
     * Ekspor Data Batch Drum ke format Excel (.xlsx).
     */
    public function exportBatchExcel(Request $request): BinaryFileResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $sheets = [
            [
                'name' => 'Data Batch Drum',
                'rows' => $this->getBatchRows($startDate, $endDate),
            ],
        ];

        $filename = 'Rekap-Batch-Drum-'.$startDate.'-sd-'.$endDate.'.xlsx';

        return $this->downloadXlsx($sheets, $filename);
    }

    /**
     * Ekspor Catatan 4 Tahap Proses Gudang ke format Excel (.xlsx).
     */
    public function exportProcessExcel(Request $request): BinaryFileResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $sheets = [
            [
                'name' => 'Proses Gudang',
                'rows' => $this->getProcessRows($startDate, $endDate),
            ],
        ];

        $filename = 'Rekap-Proses-Gudang-'.$startDate.'-sd-'.$endDate.'.xlsx';

        return $this->downloadXlsx($sheets, $filename);
    }

    /**
     * Ekspor Data Kunjungan Kendaraan ke format Excel (.xlsx).
     */
    public function exportVehicleExcel(Request $request): BinaryFileResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $sheets = [
            [
                'name' => 'Kunjungan Kendaraan',
                'rows' => $this->getVehicleRows($startDate, $endDate),
            ],
        ];

        $filename = 'Rekap-Kunjungan-Kendaraan-'.$startDate.'-sd-'.$endDate.'.xlsx';

        return $this->downloadXlsx($sheets, $filename);
    }

    /**
     * Susun baris data Batch Drum untuk sheet Excel.
     *
     * @return array<int, array<int, string|int>>
     */
    private function getBatchRows(string $startDate, string $endDate): array
    {
        $batches = DrumBatch::with('user')
            ->whereDate('tanggal_penerimaan', '>=', $startDate)
            ->whereDate('tanggal_penerimaan', '<=', $endDate)
            ->orderBy('tanggal_penerimaan', 'desc')
            ->get();

        $rows = [[
            'No',
            'Kode Batch',
            'Tanggal Penerimaan',
            'Jenis Drum',
            'Jumlah Diterima (Unit)',
            'Status Pengendapan',
            'Status Proses',
            'Sumber Laporan',
            'Catatan Gudang',
            'Diinput Oleh',
            'Waktu Input Sistem',
        ]];

        $no = 1;
        foreach ($batches as $batch) {
            $rows[] = [
                $no++,
                (string) $batch->kode_batch,
                $batch->tanggal_penerimaan?->format('Y-m-d') ?? '-',
                (string) $batch->kategori_jenis,
                (int) $batch->jumlah_diterima,
                (string) $batch->status_pengendapan,
                (string) $batch->status_proses,
                (string) ($batch->sumber_laporan ?? '-'),
                (string) ($batch->catatan ?? '-'),
                (string) ($batch->user?->name ?? 'Admin'),
                $batch->created_at?->format('Y-m-d H:i:s') ?? '-',
            ];
        }

        return $rows;
    }

    /**
     * Susun baris data 4 Tahap Proses Gudang untuk sheet Excel.
     *
     * @return array<int, array<int, string|int>>
     */
    private function getProcessRows(string $startDate, string $endDate): array
    {
        $logs = DrumProcessLog::with(['drumBatch', 'user'])
            ->whereDate('tanggal_aktivitas', '>=', $startDate)
            ->whereDate('tanggal_aktivitas', '<=', $endDate)
            ->orderBy('tanggal_aktivitas', 'desc')
            ->get();

        $rows = [[
            'No',
            'Kode Batch',
            'Tahap Proses',
            'Tanggal Aktivitas',
            'Tanggal Input Sistem',
            'Jumlah Diproses (Unit)',
            'Jumlah Reject (Unit)',
            'Hasil Baik (Unit)',
            'Pelapor Gudang',
            'Keterangan',
            'Admin Pencatat',
        ]];

        $no = 1;
        foreach ($logs as $log) {
            $lolos = max(0, $log->jumlah_diproses - $log->jumlah_afkir);
            $rows[] = [
                $no++,
                (string) ($log->drumBatch?->kode_batch ?? '-'),
                (string) $log->label_proses,
                $log->tanggal_aktivitas?->format('Y-m-d') ?? '-',
                $log->created_at?->format('Y-m-d H:i') ?? '-',
                (int) $log->jumlah_diproses,
                (int) $log->jumlah_afkir,
                (int) $lolos,
                (string) ($log->pelapor_gudang ?? '-'),
                (string) ($log->hasil_catatan ?? '-'),
                (string) ($log->user?->name ?? 'Admin'),
            ];
        }

        return $rows;
    }

    /**
     * Susun baris data Kunjungan Kendaraan untuk sheet Excel.
     *
     * @return array<int, array<int, string|int>>
     */
    private function getVehicleRows(string $startDate, string $endDate): array
    {
        $visits = VehicleVisit::with('user')
            ->whereDate('waktu_masuk', '>=', $startDate)
            ->whereDate('waktu_masuk', '<=', $endDate)
            ->orderBy('waktu_masuk', 'desc')
            ->get();

        $rows = [[
            'No',
            'Nomor Kunjungan',
            'Nomor Polisi',
            'Nama Pengemudi',
            'Asal / Tujuan',
            'Keperluan',
            'Waktu Masuk',
            'Waktu Keluar',
            'Status Kunjungan',
            'Pemeriksaan Surat Jalan B3',
            'Nomor Surat Jalan',
            'Keterangan Muatan',
            'Petugas Satpam',
        ]];

        $no = 1;
        foreach ($visits as $visit) {
            $rows[] = [
                $no++,
                (string) $visit->nomor_kunjungan,
                (string) $visit->nomor_polisi,
                (string) $visit->nama_pengemudi,
                (string) ($visit->asal_tujuan ?? '-'),
                (string) ($visit->keperluan ?? '-'),
                $visit->waktu_masuk?->format('Y-m-d H:i') ?? '-',
                $visit->waktu_keluar?->format('Y-m-d H:i') ?? 'Aktif di Pabrik',
                $visit->waktu_keluar ? 'Selesai' : 'Aktif',
                (string) ($visit->status_pemeriksaan_surat_jalan_b3 ?? 'Non-B3'),
                (string) ($visit->informasi_surat_jalan ?? '-'),
                (string) ($visit->keterangan_muatan ?? '-'),
                (string) ($visit->user?->name ?? 'Satpam'),
            ];
        }

        return $rows;
    }

    /**
     * Bangun file native OpenXML Excel (.xlsx) menggunakan ZipArchive dan kirim sebagai unduhan.
     *
     * @param  array<int, array{name: string, rows: array<int, array<int, string|int>>}>  $sheets
     */
    private function downloadXlsx(array $sheets, string $filename): BinaryFileResponse
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pkdi_xlsx_');

        $zip = new ZipArchive;
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // 1. [Content_Types].xml
        $sheetOverrides = '';
        foreach ($sheets as $index => $sheet) {
            $sheetId = $index + 1;
            $sheetOverrides .= '<Override PartName="/xl/worksheets/sheet'.$sheetId.'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .$sheetOverrides
            .'</Types>');

        // 2. _rels/.rels
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>');

        // 3. xl/_rels/workbook.xml.rels & xl/workbook.xml
        $workbookSheets = '';
        $workbookRels = '';
        foreach ($sheets as $index => $sheet) {
            $sheetId = $index + 1;
            $safeName = htmlspecialchars(mb_substr($sheet['name'], 0, 31), ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $workbookSheets .= '<sheet name="'.$safeName.'" sheetId="'.$sheetId.'" r:id="rId'.$sheetId.'"/>';
            $workbookRels .= '<Relationship Id="rId'.$sheetId.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.$sheetId.'.xml"/>';
        }
        $styleRelId = count($sheets) + 1;
        $workbookRels .= '<Relationship Id="rId'.$styleRelId.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'.$workbookSheets.'</sheets>'
            .'</workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .$workbookRels
            .'</Relationships>');

        // 4. xl/styles.xml (Style 0: Default, Style 1: Header Navy #0F172A Bold White, Style 2: Data Cell Bordered)
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2">'
            .'<font><sz val="10"/><color rgb="FF0F172A"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="3">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="2">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border>'
            .'<left style="thin"><color rgb="FFE2E8F0"/></left>'
            .'<right style="thin"><color rgb="FFE2E8F0"/></right>'
            .'<top style="thin"><color rgb="FFE2E8F0"/></top>'
            .'<bottom style="thin"><color rgb="FFE2E8F0"/></bottom>'
            .'<diagonal/>'
            .'</border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="3">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>'
            .'</cellXfs>'
            .'</styleSheet>');

        // 5. Worksheets
        foreach ($sheets as $index => $sheet) {
            $sheetId = $index + 1;
            $zip->addFromString('xl/worksheets/sheet'.$sheetId.'.xml', $this->buildWorksheetXml($sheet['rows']));
        }

        $zip->close();

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Bangun XML untuk satu worksheet Excel.
     *
     * @param  array<int, array<int, string|int>>  $rows
     */
    private function buildWorksheetXml(array $rows): string
    {
        $colCount = isset($rows[0]) ? count($rows[0]) : 1;
        $colsXml = '<cols>';
        for ($c = 1; $c <= $colCount; $c++) {
            $width = $c === 1 ? 6 : 22;
            $colsXml .= '<col min="'.$c.'" max="'.$c.'" width="'.$width.'" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        $sheetDataXml = '<sheetData>';
        foreach ($rows as $rowIndex => $row) {
            $r = $rowIndex + 1;
            $style = $rowIndex === 0 ? 1 : 2;
            $sheetDataXml .= '<row r="'.$r.'">';
            foreach ($row as $colIndex => $cellValue) {
                $colLetter = $this->columnLetter($colIndex + 1);
                $cellRef = $colLetter.$r;

                if (is_int($cellValue) || is_float($cellValue)) {
                    $sheetDataXml .= '<c r="'.$cellRef.'" s="'.$style.'" t="n"><v>'.$cellValue.'</v></c>';
                } else {
                    $escaped = htmlspecialchars((string) $cellValue, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    $sheetDataXml .= '<c r="'.$cellRef.'" s="'.$style.'" t="inlineStr"><is><t>'.$escaped.'</t></is></c>';
                }
            }
            $sheetDataXml .= '</row>';
        }
        $sheetDataXml .= '</sheetData>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .$colsXml
            .$sheetDataXml
            .'</worksheet>';
    }

    /**
     * Konversi indeks kolom berbasis 1 ke huruf kolom Excel (A, B, C, ...).
     */
    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $index = (int) (($index - $mod) / 26);
        }

        return $letter;
    }
}
