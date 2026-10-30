<?php

namespace App\Livewire\Keuangan;

use App\Models\WarehouseFinanceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceStockTransactions extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterJenis = '';

    public string $filterKategori = '';

    // Modal Create Transaksi
    public bool $showCreateModal = false;

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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterJenis(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
    }

    public function bukaCreateModal(): void
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
        $this->showCreateModal = true;
    }

    public function tutupCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function simpanTransaksi(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mencatat transaksi.');
        }

        $this->validate();

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

        session()->flash('success', "Transaksi {$kodeBaru} berhasil disimpan!");
        $this->tutupCreateModal();
    }

    public function render()
    {
        $query = WarehouseFinanceLog::with('user');

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('kode_transaksi', 'like', "%{$s}%")
                    ->orWhere('pihak_terkait', 'like', "%{$s}%")
                    ->orWhere('keterangan', 'like', "%{$s}%");
            });
        }

        if ($this->filterJenis) {
            $query->where('jenis_transaksi', $this->filterJenis);
        }

        if ($this->filterKategori) {
            $query->where('kategori', $this->filterKategori);
        }

        $summaryQuery = clone $query;
        $totalPemasukan = (float) (clone $summaryQuery)->where('jenis_transaksi', 'pemasukan')->sum('nominal');
        $totalPengeluaran = (float) (clone $summaryQuery)->where('jenis_transaksi', 'pengeluaran')->sum('nominal');

        $transactions = $query->orderBy('tanggal_transaksi', 'desc')->latest('id')->paginate(10);

        return view('livewire.keuangan.finance-stock-transactions', [
            'transactions' => $transactions,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
        ]);
    }
}
