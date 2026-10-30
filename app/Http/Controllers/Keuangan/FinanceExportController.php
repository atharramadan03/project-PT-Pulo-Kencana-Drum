<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\VehicleMaintenanceLog;
use App\Models\WarehouseFinanceLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class FinanceExportController extends Controller
{
    /**
     * Ekspor Rekapitulasi Keuangan ke Format Excel (.xlsx).
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $jenis = $request->query('jenis', 'all');
        $filename = 'Rekapitulasi_Keuangan_PKDI_'.now()->format('Ymd_His').'.xlsx';

        $sheets = [];

        if ($jenis === 'all' || $jenis === 'stok') {
            $sheets[] = [
                'name' => 'Keuangan Stok Gudang',
                'rows' => $this->getStockFinanceRows(),
            ];
        }

        if ($jenis === 'all' || $jenis === 'kendaraan') {
            $sheets[] = [
                'name' => 'Biaya Perbaikan Armada',
                'rows' => $this->getVehicleRepairRows(),
            ];
        }

        return $this->generateXlsxResponse($sheets, $filename);
    }

    /**
     * Ekspor Rekapitulasi Keuangan ke Format PDF Resmi Ber-Kop PT PKDI.
     */
    public function exportPdf(Request $request)
    {
        $totalPemasukan = (float) WarehouseFinanceLog::where('jenis_transaksi', 'pemasukan')->sum('nominal');
        $totalPengeluaranStok = (float) WarehouseFinanceLog::where('jenis_transaksi', 'pengeluaran')->sum('nominal');
        $totalBiayaPerbaikan = (float) VehicleMaintenanceLog::sum('biaya');
        $netOperasional = $totalPemasukan - $totalPengeluaranStok - $totalBiayaPerbaikan;

        $stockLogs = WarehouseFinanceLog::with('user')->orderBy('tanggal_transaksi', 'desc')->get();
        $repairs = VehicleMaintenanceLog::with('user')->orderBy('tanggal_perbaikan', 'desc')->get();

        $pdf = Pdf::loadView('keuangan.reports.pdf', [
            'tanggalCetak' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, H:i').' WIB',
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaranStok' => $totalPengeluaranStok,
            'totalBiayaPerbaikan' => $totalBiayaPerbaikan,
            'netOperasional' => $netOperasional,
            'stockLogs' => $stockLogs,
            'repairs' => $repairs,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Keuangan_Operasional_PKDI_'.now()->format('Ymd').'.pdf');
    }

    /**
     * Susun baris data Keuangan Stok Gudang untuk Sheet Excel.
     *
     * @return array<int, array<int, string|int|float>>
     */
    private function getStockFinanceRows(): array
    {
        $logs = WarehouseFinanceLog::with('user')->orderBy('tanggal_transaksi', 'desc')->get();

        $rows = [[
            'No',
            'Kode Transaksi',
            'Jenis Transaksi',
            'Kategori',
            'Pihak Terkait',
            'Jumlah Unit (Drum)',
            'Tanggal Transaksi',
            'Metode Pembayaran',
            'Nominal (Rp)',
            'Keterangan',
            'Pencatat',
        ]];

        $no = 1;
        foreach ($logs as $log) {
            $rows[] = [
                $no++,
                (string) $log->kode_transaksi,
                (string) ucfirst($log->jenis_transaksi),
                (string) str_replace('_', ' ', $log->kategori),
                (string) $log->pihak_terkait,
                (int) ($log->jumlah_unit ?? 0),
                $log->tanggal_transaksi?->format('Y-m-d') ?? '-',
                (string) strtoupper($log->metode_pembayaran),
                (float) $log->nominal,
                (string) ($log->keterangan ?? '-'),
                (string) ($log->user?->name ?? 'Admin'),
            ];
        }

        return $rows;
    }

    /**
     * Susun baris data Biaya Perbaikan Armada untuk Sheet Excel.
     *
     * @return array<int, array<int, string|int|float>>
     */
    private function getVehicleRepairRows(): array
    {
        $repairs = VehicleMaintenanceLog::with('user')->orderBy('tanggal_perbaikan', 'desc')->get();

        $rows = [[
            'No',
            'Nomor Polisi',
            'Jenis Kendaraan',
            'Tanggal Perbaikan',
            'Jenis Perbaikan / Servis',
            'Nama Bengkel',
            'Status Perbaikan',
            'Biaya (Rp)',
            'Keterangan',
            'Pencatat',
        ]];

        $no = 1;
        foreach ($repairs as $rep) {
            $rows[] = [
                $no++,
                (string) $rep->nomor_polisi,
                (string) $rep->jenis_kendaraan,
                $rep->tanggal_perbaikan?->format('Y-m-d') ?? '-',
                (string) $rep->jenis_perbaikan,
                (string) $rep->nama_bengkel,
                (string) ucfirst(str_replace('_', ' ', $rep->status_perbaikan)),
                (float) $rep->biaya,
                (string) ($rep->keterangan ?? '-'),
                (string) ($rep->user?->name ?? 'Admin'),
            ];
        }

        return $rows;
    }

    /**
     * Bangun file workbook .xlsx OpenXML murni menggunakan ZipArchive.
     *
     * @param  array<int, array{name: string, rows: array<int, array<int, string|int|float>>}>  $sheets
     */
    private function generateXlsxResponse(array $sheets, string $filename): BinaryFileResponse
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pkdi_fin_');
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

        // 3. xl/workbook.xml & rels
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

        // 4. xl/styles.xml
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
     * @param  array<int, array<int, string|int|float>>  $rows
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
     * Konversi indeks kolom ke huruf Excel.
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
