<?php

namespace App\Livewire\Keuangan;

use App\Models\OperationalExpenseLog;
use App\Models\VehicleMaintenanceLog;
use App\Models\WarehouseFinanceLog;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceCashFlowHistory extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterJenis = ''; // '', 'masuk', 'keluar'

    public string $filterSumber = ''; // '', 'barang_masuk', 'barang_keluar', 'mobil', 'operasional'

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterJenis(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSumber(): void
    {
        $this->resetPage();
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->filterJenis = '';
        $this->filterSumber = '';
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : Carbon::now()->endOfMonth();

        $entries = collect();

        // 1. Dari Warehouse Finance Log (Barang Masuk & Keluar)
        $stockLogs = WarehouseFinanceLog::with(['supplier', 'user'])
            ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($stockLogs as $log) {
            $isMasuk = ($log->jenis_transaksi === 'pemasukan');
            $sumber = $isMasuk ? 'barang_keluar' : 'barang_masuk';
            $sumberLabel = $isMasuk ? 'Penjualan Barang Keluar' : 'Pengadaan Barang Masuk';
            $keteranganDetail = $log->keterangan.($log->supplier ? " (Supplier: {$log->supplier->nama_supplier})" : '');

            $entries->push((object) [
                'id' => 'wh-'.$log->id,
                'tanggal' => $log->tanggal_transaksi,
                'jenis' => $isMasuk ? 'masuk' : 'keluar',
                'sumber' => $sumber,
                'sumber_label' => $sumberLabel,
                'kode_ref' => $log->kode_referensi ?: '-',
                'keterangan' => $keteranganDetail,
                'nominal' => (float) $log->nominal,
                'petugas' => $log->user?->name ?? 'Sistem Gudang',
            ]);
        }

        // 2. Dari Biaya Mobil & Kendaraan
        $repairs = VehicleMaintenanceLog::with('user')
            ->whereBetween('tanggal_perbaikan', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($repairs as $repair) {
            $entries->push((object) [
                'id' => 'vm-'.$repair->id,
                'tanggal' => $repair->tanggal_perbaikan,
                'jenis' => 'keluar',
                'sumber' => 'mobil',
                'sumber_label' => 'Biaya Armada & Mobil',
                'kode_ref' => $repair->nomor_polisi,
                'keterangan' => "Perbaikan/Servis {$repair->nomor_polisi} - {$repair->deskripsi_perbaikan}".($repair->nama_bengkel ? " ({$repair->nama_bengkel})" : ''),
                'nominal' => (float) $repair->biaya,
                'petugas' => $repair->user?->name ?? 'Admin Keuangan',
            ]);
        }

        // 3. Dari Biaya Operasional Pabrik & Kantor
        $expenses = OperationalExpenseLog::with(['employee', 'user'])
            ->whereBetween('tanggal_pengeluaran', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($expenses as $exp) {
            $pic = $exp->employee ? " (PIC: {$exp->employee->nama_lengkap})" : '';
            $entries->push((object) [
                'id' => 'op-'.$exp->id,
                'tanggal' => $exp->tanggal_pengeluaran,
                'jenis' => 'keluar',
                'sumber' => 'operasional',
                'sumber_label' => 'Operasional Pabrik',
                'kode_ref' => $exp->kategori,
                'keterangan' => "{$exp->kategori}: {$exp->keterangan}{$pic} [{$exp->metode_pembayaran}]",
                'nominal' => (float) $exp->nominal,
                'petugas' => $exp->user?->name ?? 'Admin Keuangan',
            ]);
        }

        // Filter Pencarian
        if ($this->search) {
            $searchLower = strtolower($this->search);
            $entries = $entries->filter(function ($item) use ($searchLower) {
                return str_contains(strtolower($item->keterangan), $searchLower)
                    || str_contains(strtolower($item->kode_ref), $searchLower)
                    || str_contains(strtolower($item->sumber_label), $searchLower)
                    || str_contains(strtolower($item->petugas), $searchLower);
            });
        }

        // Filter Jenis (masuk / keluar)
        if ($this->filterJenis) {
            $entries = $entries->where('jenis', $this->filterJenis);
        }

        // Filter Sumber Modul
        if ($this->filterSumber) {
            $entries = $entries->where('sumber', $this->filterSumber);
        }

        // Urutkan berdasarkan tanggal descending
        $sortedEntries = $entries->sortByDesc(fn ($item) => $item->tanggal->timestamp)->values();

        // Hitung Summary
        $totalMutasiMasuk = $sortedEntries->where('jenis', 'masuk')->sum('nominal');
        $totalMutasiKeluar = $sortedEntries->where('jenis', 'keluar')->sum('nominal');
        $selisihBersih = $totalMutasiMasuk - $totalMutasiKeluar;
        $totalTransaksi = $sortedEntries->count();

        // Paginate manual
        $page = $this->getPage();
        $perPage = 15;
        $paginatedItems = $sortedEntries->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $paginatedItems,
            $sortedEntries->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.keuangan.finance-cash-flow-history', [
            'mutasiItems' => $paginator,
            'totalMutasiMasuk' => $totalMutasiMasuk,
            'totalMutasiKeluar' => $totalMutasiKeluar,
            'selisihBersih' => $selisihBersih,
            'totalTransaksi' => $totalTransaksi,
        ]);
    }
}
