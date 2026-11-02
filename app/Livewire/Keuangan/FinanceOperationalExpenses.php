<?php

namespace App\Livewire\Keuangan;

use App\Models\OperationalExpenseLog;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceOperationalExpenses extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterKategori = '';

    public string $filterMetode = '';

    public ?string $tanggalMulai = null;

    public ?string $tanggalSelesai = null;

    // State Modal Tambah
    public bool $modalTerbuka = false;

    // State Modal Hapus
    public bool $modalHapusTerbuka = false;

    public ?int $hapusExpenseId = null;

    public ?OperationalExpenseLog $hapusExpense = null;

    public string $kode_biaya = '';

    public string $kategori_biaya = 'listrik_air';

    public string $nama_biaya = '';

    public string $nominal = '';

    public string $tanggal_pengeluaran = '';

    public string $metode_pembayaran = 'tunai_kas_kecil';

    public string $nomor_bukti = '';

    public string $penerima_dana = '';

    public string $keterangan = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKategori' => ['except' => ''],
        'filterMetode' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->tanggal_pengeluaran = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMetode(): void
    {
        $this->resetPage();
    }

    public function bukaModal(): void
    {
        $this->resetForm();
        $this->kode_biaya = OperationalExpenseLog::generateKodeBiaya();
        $this->modalTerbuka = true;
    }

    public function tutupModal(): void
    {
        $this->modalTerbuka = false;
        $this->resetValidation();
    }

    public function resetForm(): void
    {
        $this->kategori_biaya = 'listrik_air';
        $this->nama_biaya = '';
        $this->nominal = '';
        $this->tanggal_pengeluaran = now()->toDateString();
        $this->metode_pembayaran = 'tunai_kas_kecil';
        $this->nomor_bukti = '';
        $this->penerima_dana = '';
        $this->keterangan = '';
    }

    public function simpanBiaya(): void
    {
        if (! auth()->user()->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mencatat biaya operasional.');
        }

        $nominalClean = (float) str_replace(['.', ','], ['', '.'], $this->nominal);

        $this->validate([
            'kategori_biaya' => ['required', 'in:listrik_air,bbm_solar,perlengkapan_apd,konsumsi_karyawan,atk_kantor,pemeliharaan_fasilitas,lainnya'],
            'nama_biaya' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:1000'],
            'tanggal_pengeluaran' => ['required', 'date'],
            'metode_pembayaran' => ['required', 'in:tunai_kas_kecil,transfer_bank'],
            'nomor_bukti' => ['nullable', 'string', 'max:100'],
            'penerima_dana' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_biaya.required' => 'Deskripsi biaya pengeluaran wajib diisi.',
            'penerima_dana.required' => 'Nama penerima dana/toko penyedia wajib diisi.',
            'nominal.min' => 'Nominal pengeluaran minimal Rp 1.000.',
        ]);

        $kodeBaru = OperationalExpenseLog::generateKodeBiaya();

        OperationalExpenseLog::create([
            'user_id' => auth()->id(),
            'kode_biaya' => $kodeBaru,
            'kategori_biaya' => $this->kategori_biaya,
            'nama_biaya' => $this->nama_biaya,
            'nominal' => $nominalClean,
            'tanggal_pengeluaran' => $this->tanggal_pengeluaran,
            'metode_pembayaran' => $this->metode_pembayaran,
            'nomor_bukti' => $this->nomor_bukti ?: null,
            'penerima_dana' => $this->penerima_dana,
            'keterangan' => $this->keterangan ?: null,
        ]);

        $this->tutupModal();
        session()->flash('success', "Biaya operasional {$kodeBaru} ({$this->nama_biaya}) berhasil dicatat.");
    }

    public function konfirmasiHapus(int $id): void
    {
        $expense = OperationalExpenseLog::findOrFail($id);
        $this->hapusExpenseId = $expense->id;
        $this->hapusExpense = $expense;
        $this->modalHapusTerbuka = true;
    }

    public function tutupModalHapus(): void
    {
        $this->modalHapusTerbuka = false;
        $this->hapusExpenseId = null;
        $this->hapusExpense = null;
    }

    public function hapusBiaya(): void
    {
        if (! auth()->user()->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak menghapus biaya operasional.');
        }

        $expense = OperationalExpenseLog::findOrFail($this->hapusExpenseId);
        $kode = $expense->kode_biaya;
        $nama = $expense->nama_biaya;

        $expense->delete();

        $this->tutupModalHapus();
        session()->flash('success', "Biaya operasional {$kode} ({$nama}) berhasil dihapus dari sistem.");
    }

    public function render()
    {
        $query = OperationalExpenseLog::with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_biaya', 'like', "%{$this->search}%")
                    ->orWhere('nama_biaya', 'like', "%{$this->search}%")
                    ->orWhere('penerima_dana', 'like', "%{$this->search}%")
                    ->orWhere('nomor_bukti', 'like', "%{$this->search}%")
                    ->orWhere('keterangan', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterKategori) {
            $query->where('kategori_biaya', $this->filterKategori);
        }

        if ($this->filterMetode) {
            $query->where('metode_pembayaran', $this->filterMetode);
        }

        if ($this->tanggalMulai) {
            $query->whereDate('tanggal_pengeluaran', '>=', $this->tanggalMulai);
        }

        if ($this->tanggalSelesai) {
            $query->whereDate('tanggal_pengeluaran', '<=', $this->tanggalSelesai);
        }

        $expenses = $query->orderBy('tanggal_pengeluaran', 'desc')->paginate(10);

        // Perhitungan Statistik KPI
        $totalBiayaBulanIni = (float) OperationalExpenseLog::whereYear('tanggal_pengeluaran', now()->year)
            ->whereMonth('tanggal_pengeluaran', now()->month)
            ->sum('nominal');

        $totalKasKecil = (float) OperationalExpenseLog::where('metode_pembayaran', 'tunai_kas_kecil')->sum('nominal');
        $totalTransferBank = (float) OperationalExpenseLog::where('metode_pembayaran', 'transfer_bank')->sum('nominal');
        $totalKeseluruhan = (float) OperationalExpenseLog::sum('nominal');

        return view('livewire.keuangan.finance-operational-expenses', [
            'expenses' => $expenses,
            'totalBiayaBulanIni' => $totalBiayaBulanIni,
            'totalKasKecil' => $totalKasKecil,
            'totalTransferBank' => $totalTransferBank,
            'totalKeseluruhan' => $totalKeseluruhan,
        ]);
    }
}
