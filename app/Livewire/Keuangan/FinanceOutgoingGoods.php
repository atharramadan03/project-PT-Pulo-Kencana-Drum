<?php

namespace App\Livewire\Keuangan;

use App\Models\DrumBatch;
use App\Models\WarehouseFinanceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceOutgoingGoods extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterMetode = '';

    // Modal Create Barang Keluar
    public bool $showCreateModal = false;

    // State Modal Hapus
    public bool $modalHapusTerbuka = false;

    public ?int $hapusLogId = null;

    public ?WarehouseFinanceLog $targetHapus = null;

    public string $kode_transaksi = '';

    #[Validate('required|string|max:150')]
    public string $pihak_terkait = '';

    public string $kategori = 'penjualan_drum';

    #[Validate('required|date')]
    public string $tanggal_transaksi = '';

    #[Validate('required|integer|min:1')]
    public int $jumlah_unit = 100;

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
            'pihak_terkait.required' => 'Nama pelanggan / pabrik pembeli wajib diisi.',
            'tanggal_transaksi.required' => 'Tanggal pengiriman barang keluar wajib diisi.',
            'jumlah_unit.required' => 'Jumlah unit drum yang dikirim wajib diisi.',
            'jumlah_unit.min' => 'Jumlah drum keluar minimal 1 unit.',
            'nominal.required' => 'Nilai penjualan drum wajib diisi.',
            'nominal.min' => 'Nilai penjualan minimal Rp 1.000.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
        ];
    }

    public function updatingSearch(): void
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
        $this->pihak_terkait = '';
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

    public function simpanBarangKeluar(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mencatat barang keluar.');
        }

        $this->nominal = (string) str_replace(['.', ','], ['', '.'], $this->nominal);

        $this->validate();

        $kode = WarehouseFinanceLog::generateKodeTransaksi();

        WarehouseFinanceLog::create([
            'user_id' => Auth::id(),
            'drum_batch_id' => $this->drum_batch_id,
            'kode_transaksi' => $kode,
            'jenis_transaksi' => 'pemasukan', // Penjualan drum menghasilkan arus kas masuk
            'kategori' => 'penjualan_drum',
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'jumlah_unit' => $this->jumlah_unit,
            'nominal' => (float) $this->nominal,
            'metode_pembayaran' => $this->metode_pembayaran,
            'pihak_terkait' => $this->pihak_terkait,
            'keterangan' => $this->keterangan,
        ]);

        $this->tutupCreateModal();
        session()->flash('success', "Data barang keluar {$kode} ({$this->pihak_terkait}) berhasil dicatat.");
    }

    public function konfirmasiHapus(int $id): void
    {
        $log = WarehouseFinanceLog::where('jenis_transaksi', 'pemasukan')->findOrFail($id);
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

    public function hapusBarangKeluar(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Akses ditolak.');
        }

        if ($this->targetHapus) {
            $kode = $this->targetHapus->kode_transaksi;
            $this->targetHapus->delete();
            $this->tutupModalHapus();
            session()->flash('success', "Catatan barang keluar {$kode} berhasil dihapus.");
        }
    }

    public function render()
    {
        $query = WarehouseFinanceLog::with(['user', 'drumBatch'])
            ->where('jenis_transaksi', 'pemasukan');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_transaksi', 'like', "%{$this->search}%")
                    ->orWhere('pihak_terkait', 'like', "%{$this->search}%")
                    ->orWhere('keterangan', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterMetode) {
            $query->where('metode_pembayaran', $this->filterMetode);
        }

        $barangKeluarList = $query->orderBy('tanggal_transaksi', 'desc')->paginate(10);

        // Ringkasan KPI
        $totalPenjualan = WarehouseFinanceLog::where('jenis_transaksi', 'pemasukan')->sum('nominal');
        $totalUnitTerjual = WarehouseFinanceLog::where('jenis_transaksi', 'pemasukan')->sum('jumlah_unit');
        $totalPengirimanBulanIni = WarehouseFinanceLog::where('jenis_transaksi', 'pemasukan')
            ->whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->count();
        $rataRataPenjualan = $barangKeluarList->total() > 0 ? $totalPenjualan / $barangKeluarList->total() : 0;

        $drumBatches = DrumBatch::latest('id')->take(30)->get();

        return view('livewire.keuangan.finance-outgoing-goods', [
            'barangKeluarList' => $barangKeluarList,
            'totalPenjualan' => $totalPenjualan,
            'totalUnitTerjual' => $totalUnitTerjual,
            'totalPengirimanBulanIni' => $totalPengirimanBulanIni,
            'rataRataPenjualan' => $rataRataPenjualan,
            'drumBatches' => $drumBatches,
        ]);
    }
}
