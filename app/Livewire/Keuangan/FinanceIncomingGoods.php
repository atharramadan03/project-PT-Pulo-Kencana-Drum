<?php

namespace App\Livewire\Keuangan;

use App\Models\DrumBatch;
use App\Models\Supplier;
use App\Models\WarehouseFinanceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceIncomingGoods extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterKategori = '';

    public string $filterSupplier = '';

    public string $filterMetode = '';

    // Modal Create Barang Masuk
    public bool $showCreateModal = false;

    // State Modal Hapus
    public bool $modalHapusTerbuka = false;

    public ?int $hapusLogId = null;

    public ?WarehouseFinanceLog $targetHapus = null;

    public string $kode_transaksi = '';

    #[Validate('nullable|exists:suppliers,id')]
    public ?int $supplier_id = null;

    #[Validate('required|string|max:150')]
    public string $pihak_terkait = '';

    #[Validate('required|string|max:50')]
    public string $kategori = 'pembelian_batch';

    #[Validate('required|date')]
    public string $tanggal_transaksi = '';

    #[Validate('nullable|integer|min:0')]
    public int $jumlah_unit = 0;

    #[Validate('required|numeric|min:1000')]
    public string $nominal = '';

    #[Validate('required|in:transfer,tunai,tempo')]
    public string $metode_pembayaran = 'transfer';

    #[Validate('nullable|exists:drum_batches,id')]
    public ?int $drum_batch_id = null;

    #[Validate('nullable|string|max:500')]
    public ?string $keterangan = null;

    protected function messages(): array
    {
        return [
            'pihak_terkait.required' => 'Nama supplier / pemasok barang wajib diisi.',
            'kategori.required' => 'Kategori barang masuk wajib dipilih.',
            'tanggal_transaksi.required' => 'Tanggal barang masuk wajib diisi.',
            'nominal.required' => 'Nominal pembelian wajib diisi.',
            'nominal.min' => 'Nominal pembelian minimal Rp 1.000.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'supplier_id.exists' => 'Data supplier yang dipilih tidak valid.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSupplier(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMetode(): void
    {
        $this->resetPage();
    }

    public function bukaCreateModal(): void
    {
        $this->resetValidation();
        $this->kode_transaksi = WarehouseFinanceLog::generateKodeTransaksi();
        $this->supplier_id = null;
        $this->pihak_terkait = '';
        $this->kategori = 'pembelian_batch';
        $this->tanggal_transaksi = now()->format('Y-m-d');
        $this->jumlah_unit = 100;
        $this->nominal = '';
        $this->metode_pembayaran = 'transfer';
        $this->drum_batch_id = null;
        $this->keterangan = null;
        $this->showCreateModal = true;
    }

    public function tutupCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function updatedSupplierId(?int $value): void
    {
        if ($value) {
            $sup = Supplier::find($value);
            if ($sup) {
                $this->pihak_terkait = $sup->nama_supplier;
            }
        }
    }

    public function simpanBarangMasuk(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mencatat barang masuk.');
        }

        $this->nominal = (string) str_replace(['.', ','], ['', '.'], $this->nominal);

        $this->validate();

        $kode = WarehouseFinanceLog::generateKodeTransaksi();

        WarehouseFinanceLog::create([
            'user_id' => Auth::id(),
            'supplier_id' => $this->supplier_id,
            'drum_batch_id' => $this->drum_batch_id,
            'kode_transaksi' => $kode,
            'jenis_transaksi' => 'pengeluaran', // Barang masuk adalah arus barang masuk dari pembelian/pengeluaran modal
            'kategori' => $this->kategori,
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'jumlah_unit' => $this->jumlah_unit,
            'nominal' => (float) $this->nominal,
            'metode_pembayaran' => $this->metode_pembayaran,
            'pihak_terkait' => $this->pihak_terkait,
            'keterangan' => $this->keterangan,
        ]);

        $this->tutupCreateModal();
        session()->flash('success', "Data barang masuk {$kode} ({$this->pihak_terkait}) berhasil dicatat.");
    }

    public function konfirmasiHapus(int $id): void
    {
        $log = WarehouseFinanceLog::where('jenis_transaksi', 'pengeluaran')->findOrFail($id);
        $this->hapusLogId = $log->id;
        $this->targetHapus = $log;
        $this->modalHapusTerbuka = true;
    }

    public function tutupModalHapus(): void
    {
        $this->modalHapusTerbuka = false;
        $this->hapusLogId = null;
        $this->targetHapus = null;
    }

    public function hapusBarangMasuk(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Akses ditolak.');
        }

        if ($this->targetHapus) {
            $kode = $this->targetHapus->kode_transaksi;
            $this->targetHapus->delete();
            $this->tutupModalHapus();
            session()->flash('success', "Catatan barang masuk {$kode} berhasil dihapus.");
        }
    }

    public function render()
    {
        $query = WarehouseFinanceLog::with(['user', 'supplier', 'drumBatch'])
            ->where('jenis_transaksi', 'pengeluaran');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_transaksi', 'like', "%{$this->search}%")
                    ->orWhere('pihak_terkait', 'like', "%{$this->search}%")
                    ->orWhere('keterangan', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterKategori) {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->filterSupplier) {
            $query->where('supplier_id', $this->filterSupplier);
        }

        if ($this->filterMetode) {
            $query->where('metode_pembayaran', $this->filterMetode);
        }

        $barangMasukList = $query->orderBy('tanggal_transaksi', 'desc')->paginate(10);

        // Ringkasan KPI
        $totalPengeluaranMasuk = WarehouseFinanceLog::where('jenis_transaksi', 'pengeluaran')->sum('nominal');
        $totalUnitMasuk = WarehouseFinanceLog::where('jenis_transaksi', 'pengeluaran')->sum('jumlah_unit');
        $totalTransaksiBulanIni = WarehouseFinanceLog::where('jenis_transaksi', 'pengeluaran')
            ->whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->count();
        $totalSupplierAktif = Supplier::where('status_kerjasama', 'aktif')->count();

        $suppliers = Supplier::where('status_kerjasama', 'aktif')->orderBy('nama_supplier')->get();
        $drumBatches = DrumBatch::latest('id')->take(30)->get();

        return view('livewire.keuangan.finance-incoming-goods', [
            'barangMasukList' => $barangMasukList,
            'totalPengeluaranMasuk' => $totalPengeluaranMasuk,
            'totalUnitMasuk' => $totalUnitMasuk,
            'totalTransaksiBulanIni' => $totalTransaksiBulanIni,
            'totalSupplierAktif' => $totalSupplierAktif,
            'suppliers' => $suppliers,
            'drumBatches' => $drumBatches,
        ]);
    }
}
