<?php

namespace App\Livewire\Admin;

use App\Models\DrumBatch;
use App\Models\DrumProcessLog;
use App\Models\VehicleVisit;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ReportIndex extends Component
{
    use WithPagination;

    // Filter Periode Tanggal
    public string $startDate = '';
    public string $endDate = '';

    // Tab Aktif: 'batch', 'proses', 'kendaraan'
    public string $activeTab = 'batch';

    // Filter Tambahan
    public string $search = '';

    public function mount(): void
    {
        // Default periode: awal bulan berjalan s/d hari ini
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        // 1. Hitung Ringkasan KPI Eksekutif dalam rentang periode
        $totalBatches = DrumBatch::whereDate('tanggal_penerimaan', '>=', $this->startDate)
            ->whereDate('tanggal_penerimaan', '<=', $this->endDate)
            ->count();
        $totalDrumDiterima = DrumBatch::whereDate('tanggal_penerimaan', '>=', $this->startDate)
            ->whereDate('tanggal_penerimaan', '<=', $this->endDate)
            ->sum('jumlah_diterima');
        $drumTransit = DrumBatch::whereDate('tanggal_penerimaan', '>=', $this->startDate)
            ->whereDate('tanggal_penerimaan', '<=', $this->endDate)
            ->whereIn('status_pengendapan', ['Transit', 'Mengendap'])
            ->sum('jumlah_diterima');

        $processQuery = DrumProcessLog::whereDate('tanggal_aktivitas', '>=', $this->startDate)
            ->whereDate('tanggal_aktivitas', '<=', $this->endDate);
        $drumSortir = (clone $processQuery)->where('jenis_proses', 'penyortiran')->sum('jumlah_diproses');
        $drumTiup = (clone $processQuery)->where('jenis_proses', 'peniupan')->sum('jumlah_diproses');
        $drumCuci = (clone $processQuery)->where('jenis_proses', 'pencucian')->sum('jumlah_diproses');
        $drumCat = (clone $processQuery)->where('jenis_proses', 'pengecatan')->sum('jumlah_diproses');
        $totalAfkir = (clone $processQuery)->sum('jumlah_afkir');

        $totalKunjungan = VehicleVisit::whereDate('waktu_masuk', '>=', $this->startDate)
            ->whereDate('waktu_masuk', '<=', $this->endDate)
            ->count();
        $kunjunganB3 = VehicleVisit::whereDate('waktu_masuk', '>=', $this->startDate)
            ->whereDate('waktu_masuk', '<=', $this->endDate)
            ->where('status_pemeriksaan_surat_jalan_b3', 'sesuai')
            ->count();

        // 2. Query Tab Aktif
        $batches = null;
        $processLogs = null;
        $visits = null;

        if ($this->activeTab === 'batch') {
            $batches = DrumBatch::with('user')
                ->whereDate('tanggal_penerimaan', '>=', $this->startDate)
                ->whereDate('tanggal_penerimaan', '<=', $this->endDate)
                ->when($this->search, function ($q) {
                    $q->where(function ($query) {
                        $query->where('kode_batch', 'like', "%{$this->search}%")
                            ->orWhere('kategori_jenis', 'like', "%{$this->search}%")
                            ->orWhere('sumber_laporan', 'like', "%{$this->search}%");
                    });
                })
                ->orderBy('tanggal_penerimaan', 'desc')
                ->paginate(15);
        } elseif ($this->activeTab === 'proses') {
            $processLogs = DrumProcessLog::with(['drumBatch', 'user'])
                ->whereDate('tanggal_aktivitas', '>=', $this->startDate)
                ->whereDate('tanggal_aktivitas', '<=', $this->endDate)
                ->when($this->search, function ($q) {
                    $q->where(function ($query) {
                        $query->where('pelapor_gudang', 'like', "%{$this->search}%")
                            ->orWhere('hasil_catatan', 'like', "%{$this->search}%")
                            ->orWhereHas('drumBatch', function ($batchQ) {
                                $batchQ->where('kode_batch', 'like', "%{$this->search}%");
                            });
                    });
                })
                ->orderBy('tanggal_aktivitas', 'desc')
                ->paginate(15);
        } else {
            $visits = VehicleVisit::with('user')
                ->whereDate('waktu_masuk', '>=', $this->startDate)
                ->whereDate('waktu_masuk', '<=', $this->endDate)
                ->when($this->search, function ($q) {
                    $q->where(function ($query) {
                        $query->where('nomor_kunjungan', 'like', "%{$this->search}%")
                            ->orWhere('nomor_polisi', 'like', "%{$this->search}%")
                            ->orWhere('nama_pengemudi', 'like', "%{$this->search}%")
                            ->orWhere('asal_tujuan', 'like', "%{$this->search}%");
                    });
                })
                ->orderBy('waktu_masuk', 'desc')
                ->paginate(15);
        }

        return view('livewire.admin.report-index', [
            'totalBatches' => $totalBatches,
            'totalDrumDiterima' => $totalDrumDiterima,
            'drumTransit' => $drumTransit,
            'drumSortir' => $drumSortir,
            'drumTiup' => $drumTiup,
            'drumCuci' => $drumCuci,
            'drumCat' => $drumCat,
            'totalAfkir' => $totalAfkir,
            'totalKunjungan' => $totalKunjungan,
            'kunjunganB3' => $kunjunganB3,
            'batches' => $batches,
            'processLogs' => $processLogs,
            'visits' => $visits,
        ]);
    }
}
