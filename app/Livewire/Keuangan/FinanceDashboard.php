<?php

namespace App\Livewire\Keuangan;

use App\Models\VehicleMaintenanceLog;
use App\Models\WarehouseFinanceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class FinanceDashboard extends Component
{
    // Properti Modal Tambah Transaksi Keuangan Stok
    public bool $showModalTransaksi = false;

    public string $kode_transaksi = '';

    #[Validate('required|in:pemasukan,pengeluaran')]
    public string $jenis_transaksi = 'pemasukan';

    #[Validate('required|string|max:50')]
    public string $kategori = 'penjualan_drum';

    #[Validate('required|date')]
    public string $tanggal_transaksi = '';

    #[Validate('nullable|integer|min:0')]
    public int $jumlah_unit = 0;

    #[Validate('required|numeric|min:1000')]
    public string $nominal = '';

    #[Validate('required|in:transfer,tunai,tempo')]
    public string $metode_pembayaran = 'transfer';

    #[Validate('required|string|max:150')]
    public string $pihak_terkait = '';

    #[Validate('nullable|string')]
    public ?string $keterangan = null;

    // Properti Modal Tambah Biaya Perbaikan Mobil
    public bool $showModalPerbaikan = false;

    #[Validate('required|string|max:20')]
    public string $nomor_polisi = '';

    #[Validate('required|string|max:100')]
    public string $jenis_kendaraan = 'Truk Tronton Box';

    #[Validate('required|date')]
    public string $tanggal_perbaikan = '';

    #[Validate('required|string|max:150')]
    public string $jenis_perbaikan = '';

    #[Validate('required|string|max:150')]
    public string $nama_bengkel = '';

    #[Validate('required|numeric|min:1000')]
    public string $biaya = '';

    #[Validate('required|in:selesai,dalam_pengerjaan,dijadwalkan')]
    public string $status_perbaikan = 'selesai';

    #[Validate('nullable|string')]
    public ?string $keterangan_perbaikan = null;

    public function mount(): void
    {
        $this->tanggal_transaksi = now()->format('Y-m-d');
        $this->tanggal_perbaikan = now()->format('Y-m-d');
    }

    public function bukaModalTransaksi(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            return;
        }

        $this->resetValidation();
        $this->kode_transaksi = WarehouseFinanceLog::generateKodeTransaksi();
        $this->jenis_transaksi = 'pemasukan';
        $this->kategori = 'penjualan_drum';
        $this->tanggal_transaksi = now()->format('Y-m-d');
        $this->jumlah_unit = 100;
        $this->nominal = '';
        $this->metode_pembayaran = 'transfer';
        $this->pihak_terkait = '';
        $this->keterangan = null;
        $this->showModalTransaksi = true;
    }

    public function tutupModalTransaksi(): void
    {
        $this->showModalTransaksi = false;
        $this->resetValidation();
    }

    public function simpanTransaksi(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mencatat transaksi keuangan.');
        }

        $this->validate([
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string|max:50',
            'tanggal_transaksi' => 'required|date',
            'jumlah_unit' => 'nullable|integer|min:0',
            'nominal' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|in:transfer,tunai,tempo',
            'pihak_terkait' => 'required|string|max:150',
            'keterangan' => 'nullable|string',
        ]);

        $kodeBaru = WarehouseFinanceLog::generateKodeTransaksi();

        WarehouseFinanceLog::create([
            'user_id' => Auth::id(),
            'kode_transaksi' => $kodeBaru,
            'jenis_transaksi' => $this->jenis_transaksi,
            'kategori' => $this->kategori,
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'jumlah_unit' => $this->jumlah_unit,
            'nominal' => (float) $this->nominal,
            'metode_pembayaran' => $this->metode_pembayaran,
            'pihak_terkait' => $this->pihak_terkait,
            'keterangan' => $this->keterangan,
        ]);

        session()->flash('success', "Transaksi {$kodeBaru} berhasil dicatat ke sistem keuangan!");
        $this->tutupModalTransaksi();
    }

    public function bukaModalPerbaikan(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            return;
        }

        $this->resetValidation();
        $this->nomor_polisi = '';
        $this->jenis_kendaraan = 'Truk Tronton Box';
        $this->tanggal_perbaikan = now()->format('Y-m-d');
        $this->jenis_perbaikan = '';
        $this->nama_bengkel = '';
        $this->biaya = '';
        $this->status_perbaikan = 'selesai';
        $this->keterangan_perbaikan = null;
        $this->showModalPerbaikan = true;
    }

    public function tutupModalPerbaikan(): void
    {
        $this->showModalPerbaikan = false;
        $this->resetValidation();
    }

    public function simpanPerbaikan(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mencatat biaya perbaikan.');
        }

        $this->validate([
            'nomor_polisi' => 'required|string|max:20',
            'jenis_kendaraan' => 'required|string|max:100',
            'tanggal_perbaikan' => 'required|date',
            'jenis_perbaikan' => 'required|string|max:150',
            'nama_bengkel' => 'required|string|max:150',
            'biaya' => 'required|numeric|min:1000',
            'status_perbaikan' => 'required|in:selesai,dalam_pengerjaan,dijadwalkan',
            'keterangan_perbaikan' => 'nullable|string',
        ]);

        VehicleMaintenanceLog::create([
            'user_id' => Auth::id(),
            'nomor_polisi' => strtoupper(trim($this->nomor_polisi)),
            'jenis_kendaraan' => $this->jenis_kendaraan,
            'tanggal_perbaikan' => $this->tanggal_perbaikan,
            'jenis_perbaikan' => $this->jenis_perbaikan,
            'nama_bengkel' => $this->nama_bengkel,
            'biaya' => (float) $this->biaya,
            'status_perbaikan' => $this->status_perbaikan,
            'keterangan' => $this->keterangan_perbaikan,
        ]);

        session()->flash('success', "Biaya perbaikan armada {$this->nomor_polisi} berhasil disimpan!");
        $this->tutupModalPerbaikan();
    }

    public function render()
    {
        // 1. KPI Aggregates
        $totalPemasukan = (float) WarehouseFinanceLog::where('jenis_transaksi', 'pemasukan')->sum('nominal');
        $totalPengeluaranStok = (float) WarehouseFinanceLog::where('jenis_transaksi', 'pengeluaran')->sum('nominal');
        $totalBiayaPerbaikan = (float) VehicleMaintenanceLog::sum('biaya');
        $netOperasional = $totalPemasukan - $totalPengeluaranStok - $totalBiayaPerbaikan;

        $totalUnitTerjual = (int) WarehouseFinanceLog::where('jenis_transaksi', 'pemasukan')->sum('jumlah_unit');
        $totalUnitBahanMasuk = (int) WarehouseFinanceLog::where('jenis_transaksi', 'pengeluaran')->sum('jumlah_unit');
        $totalPerbaikanSelesai = VehicleMaintenanceLog::where('status_perbaikan', 'selesai')->count();
        $perbaikanProsesCount = VehicleMaintenanceLog::where('status_perbaikan', 'dalam_pengerjaan')->count();

        $totalBeban = $totalPengeluaranStok + $totalBiayaPerbaikan;
        $totalArusUang = $totalPemasukan + $totalBeban;

        $pctPemasukan = $totalArusUang > 0 ? round(($totalPemasukan / $totalArusUang) * 100) : 0;
        $pctPengeluaranStok = $totalArusUang > 0 ? round(($totalPengeluaranStok / $totalArusUang) * 100) : 0;
        $pctBiayaPerbaikan = $totalArusUang > 0 ? max(0, 100 - $pctPemasukan - $pctPengeluaranStok) : 0;

        // Margin Operasional
        $marginPct = $totalPemasukan > 0 ? round(($netOperasional / $totalPemasukan) * 100, 1) : 0;

        // 2. Data 8 Hari Terakhir untuk Grafik Tracker Keuangan
        $chartDates = collect(range(7, 0))->map(fn ($i) => now()->subDays($i));
        $startDateStr = now()->subDays(7)->toDateString();

        $dailyStock = WarehouseFinanceLog::whereDate('tanggal_transaksi', '>=', $startDateStr)
            ->selectRaw('DATE(tanggal_transaksi) as tgl, jenis_transaksi, SUM(nominal) as total')
            ->groupBy('tgl', 'jenis_transaksi')
            ->get();

        $dailyRepairs = VehicleMaintenanceLog::whereDate('tanggal_perbaikan', '>=', $startDateStr)
            ->selectRaw('DATE(tanggal_perbaikan) as tgl, SUM(biaya) as total')
            ->groupBy('tgl')
            ->get();

        $seriesPemasukan = [];
        $seriesPengeluaranStok = [];
        $seriesPerbaikan = [];
        $xLabels = [];
        $fullDateLabels = [];

        foreach ($chartDates as $dateObj) {
            $dStr = $dateObj->toDateString();
            $xLabels[] = $dateObj->locale('id')->isoFormat('D MMM');
            $fullDateLabels[] = $dateObj->locale('id')->isoFormat('dddd, D MMMM Y');

            $in = (float) $dailyStock->where('tgl', $dStr)->where('jenis_transaksi', 'pemasukan')->sum('total');
            $out = (float) $dailyStock->where('tgl', $dStr)->where('jenis_transaksi', 'pengeluaran')->sum('total');
            $rep = (float) $dailyRepairs->where('tgl', $dStr)->sum('total');

            $seriesPemasukan[] = $in;
            $seriesPengeluaranStok[] = $out;
            $seriesPerbaikan[] = $rep;
        }

        $peakVal = max(1000000, max($seriesPemasukan), max($seriesPengeluaranStok), max($seriesPerbaikan));
        $yMax = (float) (ceil(($peakVal * 1.15) / 10000000) * 10000000);
        if ($yMax < 10000000) {
            $yMax = 10000000;
        }

        // Helper Bezier Curve
        $buildSmoothChart = function (array $values, float $maxVal, float $visualOffsetPx = 0.0) {
            $leftX = 52;
            $rightX = 620;
            $topY = 18;
            $bottomY = 176;
            $count = count($values);
            $stepX = ($rightX - $leftX) / max(1, $count - 1);

            $pts = [];
            foreach ($values as $i => $val) {
                $x = round($leftX + ($i * $stepX), 1);
                $ratio = $maxVal > 0 ? min(1, max(0, $val / $maxVal)) : 0;
                $rawY = $bottomY - ($ratio * ($bottomY - $topY));
                $y = round($val > 0 ? $rawY + $visualOffsetPx : $bottomY, 1);
                $pts[] = ['x' => $x, 'y' => $y, 'val' => $val];
            }

            $path = 'M '.$pts[0]['x'].','.$pts[0]['y'];
            for ($i = 0; $i < $count - 1; $i++) {
                $p0 = $pts[$i];
                $p1 = $pts[$i + 1];
                $midX = round(($p0['x'] + $p1['x']) / 2, 1);
                $path .= ' C '.$midX.','.$p0['y'].' '.$midX.','.$p1['y'].' '.$p1['x'].','.$p1['y'];
            }

            $area = $path.' L '.$pts[$count - 1]['x'].','.$bottomY.' L '.$pts[0]['x'].','.$bottomY.' Z';

            return ['line' => $path, 'area' => $area, 'points' => $pts];
        };

        $chartPemasukan = $buildSmoothChart($seriesPemasukan, $yMax, -2.0);
        $chartPengeluaranStok = $buildSmoothChart($seriesPengeluaranStok, $yMax, 0.0);
        $chartPerbaikan = $buildSmoothChart($seriesPerbaikan, $yMax, 2.0);

        $trackerData = [];
        foreach ($xLabels as $idx => $shortLabel) {
            $inVal = $seriesPemasukan[$idx];
            $outVal = $seriesPengeluaranStok[$idx];
            $repVal = $seriesPerbaikan[$idx];
            $trackerData[] = [
                'shortDate' => $shortLabel,
                'fullDate' => $fullDateLabels[$idx],
                'x' => $chartPemasukan['points'][$idx]['x'],
                'pctX' => round((($chartPemasukan['points'][$idx]['x'] - 52) / (620 - 52)) * 100, 1),
                'pemasukan' => 'Rp '.number_format($inVal, 0, ',', '.'),
                'pengeluaran' => 'Rp '.number_format($outVal, 0, ',', '.'),
                'perbaikan' => 'Rp '.number_format($repVal, 0, ',', '.'),
                'net' => 'Rp '.number_format($inVal - $outVal - $repVal, 0, ',', '.'),
            ];
        }

        // 3. Aktivitas Keuangan Terbaru (Gabungan Transaksi Stok & Servis Mobil)
        $recentStock = WarehouseFinanceLog::with('user')->latest('id')->take(3)->get();
        $recentRepairs = VehicleMaintenanceLog::with('user')->latest('id')->take(2)->get();

        // 4. Tabel Transaksi Terkini
        $latestTransactions = WarehouseFinanceLog::latest('tanggal_transaksi')->latest('id')->take(5)->get();

        return view('livewire.keuangan.finance-dashboard', [
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaranStok' => $totalPengeluaranStok,
            'totalBiayaPerbaikan' => $totalBiayaPerbaikan,
            'netOperasional' => $netOperasional,
            'totalUnitTerjual' => $totalUnitTerjual,
            'totalUnitBahanMasuk' => $totalUnitBahanMasuk,
            'totalPerbaikanSelesai' => $totalPerbaikanSelesai,
            'perbaikanProsesCount' => $perbaikanProsesCount,
            'pctPemasukan' => $pctPemasukan,
            'pctPengeluaranStok' => $pctPengeluaranStok,
            'pctBiayaPerbaikan' => $pctBiayaPerbaikan,
            'marginPct' => $marginPct,
            'yMax' => $yMax,
            'xLabels' => $xLabels,
            'trackerData' => $trackerData,
            'chartPemasukan' => $chartPemasukan,
            'chartPengeluaranStok' => $chartPengeluaranStok,
            'chartPerbaikan' => $chartPerbaikan,
            'recentStock' => $recentStock,
            'recentRepairs' => $recentRepairs,
            'latestTransactions' => $latestTransactions,
        ]);
    }
}
