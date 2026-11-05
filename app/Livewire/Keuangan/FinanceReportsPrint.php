<?php

namespace App\Livewire\Keuangan;

use App\Models\FinanceInvoice;
use App\Models\OperationalExpenseLog;
use App\Models\VehicleMaintenanceLog;
use App\Models\WarehouseFinanceLog;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FinanceReportsPrint extends Component
{
    public string $periode = 'bulan_ini';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public string $kategori = 'semua';

    public function mount(): void
    {
        $this->applyPeriodeDates();
    }

    public function updatedPeriode(): void
    {
        $this->applyPeriodeDates();
    }

    private function applyPeriodeDates(): void
    {
        $now = Carbon::now();

        switch ($this->periode) {
            case 'hari_ini':
                $this->startDate = $now->copy()->format('Y-m-d');
                $this->endDate = $now->copy()->format('Y-m-d');
                break;
            case 'minggu_ini':
                $this->startDate = $now->copy()->startOfWeek()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'bulan_ini':
                $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
                break;
            case '30_hari':
                $this->startDate = $now->copy()->subDays(30)->format('Y-m-d');
                $this->endDate = $now->copy()->format('Y-m-d');
                break;
            case 'kuartal_ini':
                $this->startDate = $now->copy()->firstOfQuarter()->format('Y-m-d');
                $this->endDate = $now->copy()->lastOfQuarter()->format('Y-m-d');
                break;
            case 'tahun_ini':
                $this->startDate = $now->copy()->startOfYear()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // biarkan nilai startDate dan endDate saat ini
                if (! $this->startDate) {
                    $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                }
                if (! $this->endDate) {
                    $this->endDate = $now->copy()->format('Y-m-d');
                }
                break;
        }
    }

    public function render()
    {
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : Carbon::now()->endOfMonth();

        // 1. Data Barang Masuk & Keluar (Stok)
        $stockLogsQuery = WarehouseFinanceLog::with(['user', 'supplier'])
            ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()]);

        if ($this->kategori === 'barang_masuk') {
            $stockLogsQuery->where('jenis_transaksi', 'pengeluaran');
        } elseif ($this->kategori === 'barang_keluar') {
            $stockLogsQuery->where('jenis_transaksi', 'pemasukan');
        }

        $stockLogs = ($this->kategori === 'semua' || str_starts_with($this->kategori, 'barang_'))
            ? $stockLogsQuery->orderBy('tanggal_transaksi', 'desc')->get()
            : collect();

        // 2. Data Biaya Perbaikan Kendaraan
        $repairs = ($this->kategori === 'semua' || $this->kategori === 'kendaraan')
            ? VehicleMaintenanceLog::with('user')
                ->whereBetween('tanggal_perbaikan', [$start->toDateString(), $end->toDateString()])
                ->orderBy('tanggal_perbaikan', 'desc')
                ->get()
            : collect();

        // 3. Data Biaya Operasional Pabrik
        $expenses = ($this->kategori === 'semua' || $this->kategori === 'operasional')
            ? OperationalExpenseLog::with(['user', 'employee'])
                ->whereBetween('tanggal_pengeluaran', [$start->toDateString(), $end->toDateString()])
                ->orderBy('tanggal_pengeluaran', 'desc')
                ->get()
            : collect();

        // 4. Data Tagihan / Faktur
        $invoices = ($this->kategori === 'semua' || $this->kategori === 'tagihan')
            ? FinanceInvoice::with('user')
                ->whereBetween('tanggal_terbit', [$start->toDateString(), $end->toDateString()])
                ->orderBy('tanggal_terbit', 'desc')
                ->get()
            : collect();

        // Total Perhitungan Keuangan
        $totalPemasukanPenjualan = WarehouseFinanceLog::where('jenis_transaksi', 'pemasukan')
            ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
            ->sum('nominal');

        $totalPengadaanBarang = WarehouseFinanceLog::where('jenis_transaksi', 'pengeluaran')
            ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
            ->sum('nominal');

        $totalBiayaMobil = VehicleMaintenanceLog::whereBetween('tanggal_perbaikan', [$start->toDateString(), $end->toDateString()])
            ->sum('biaya');

        $totalOperasionalPabrik = OperationalExpenseLog::whereBetween('tanggal_pengeluaran', [$start->toDateString(), $end->toDateString()])
            ->sum('nominal');

        $totalPengeluaran = $totalPengadaanBarang + $totalBiayaMobil + $totalOperasionalPabrik;
        $saldoBersih = $totalPemasukanPenjualan - $totalPengeluaran;

        $totalTagihanLunas = FinanceInvoice::where('status_pembayaran', 'lunas')
            ->whereBetween('tanggal_terbit', [$start->toDateString(), $end->toDateString()])
            ->sum('nominal');

        $totalTagihanTertunggak = FinanceInvoice::whereIn('status_pembayaran', ['belum_dibayar', 'jatuh_tempo'])
            ->whereBetween('tanggal_terbit', [$start->toDateString(), $end->toDateString()])
            ->sum('nominal');

        return view('livewire.keuangan.finance-reports-print', [
            'stockLogs' => $stockLogs,
            'repairs' => $repairs,
            'expenses' => $expenses,
            'invoices' => $invoices,
            'totalPemasukanPenjualan' => $totalPemasukanPenjualan,
            'totalPengadaanBarang' => $totalPengadaanBarang,
            'totalBiayaMobil' => $totalBiayaMobil,
            'totalOperasionalPabrik' => $totalOperasionalPabrik,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoBersih' => $saldoBersih,
            'totalTagihanLunas' => $totalTagihanLunas,
            'totalTagihanTertunggak' => $totalTagihanTertunggak,
            'formattedStart' => Carbon::parse($this->startDate)->translatedFormat('d F Y'),
            'formattedEnd' => Carbon::parse($this->endDate)->translatedFormat('d F Y'),
            'tanggalCetak' => Carbon::now()->translatedFormat('d F Y, H:i').' WIB',
        ]);
    }
}
