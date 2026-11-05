<?php

namespace App\Livewire\Keuangan;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceSuppliers extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    // Modal Create / Edit
    public bool $modalTerbuka = false;

    public bool $isEdit = false;

    public ?int $selectedSupplierId = null;

    // Modal Hapus
    public bool $modalHapusTerbuka = false;

    public ?int $hapusSupplierId = null;

    public ?Supplier $targetHapus = null;

    public string $kode_supplier = '';

    #[Validate('required|string|max:150')]
    public string $nama_supplier = '';

    #[Validate('required|string|max:100')]
    public string $kategori_pasokan = 'Bahan Baku Drum Logam';

    #[Validate('nullable|string|max:100')]
    public ?string $nama_pic = '';

    #[Validate('required|string|min:8|max:25')]
    public string $nomor_telepon = '';

    #[Validate('nullable|string|max:255')]
    public ?string $alamat = '';

    #[Validate('required|in:aktif,nonaktif')]
    public string $status_kerjasama = 'aktif';

    #[Validate('nullable|string|max:500')]
    public ?string $catatan = null;

    protected function messages(): array
    {
        return [
            'nama_supplier.required' => 'Nama supplier / badan usaha pemasok wajib diisi.',
            'kategori_pasokan.required' => 'Kategori pasokan barang wajib dipilih.',
            'nomor_telepon.required' => 'Nomor telepon / kontak supplier wajib diisi.',
            'nomor_telepon.min' => 'Nomor telepon minimal 8 digit.',
            'status_kerjasama.required' => 'Status kerjasama wajib dipilih.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function bukaModalTambah(): void
    {
        $this->resetValidation();
        $this->isEdit = false;
        $this->selectedSupplierId = null;
        $this->kode_supplier = Supplier::generateKodeSupplier();
        $this->nama_supplier = '';
        $this->kategori_pasokan = 'Bahan Baku Drum Logam';
        $this->nama_pic = '';
        $this->nomor_telepon = '';
        $this->alamat = '';
        $this->status_kerjasama = 'aktif';
        $this->catatan = null;
        $this->modalTerbuka = true;
    }

    public function bukaModalEdit(int $id): void
    {
        $this->resetValidation();
        $supplier = Supplier::findOrFail($id);
        $this->isEdit = true;
        $this->selectedSupplierId = $supplier->id;
        $this->kode_supplier = $supplier->kode_supplier;
        $this->nama_supplier = $supplier->nama_supplier;
        $this->kategori_pasokan = $supplier->kategori_pasokan;
        $this->nama_pic = $supplier->nama_pic ?? '';
        $this->nomor_telepon = $supplier->nomor_telepon ?? '';
        $this->alamat = $supplier->alamat ?? '';
        $this->status_kerjasama = $supplier->status_kerjasama;
        $this->catatan = $supplier->catatan;
        $this->modalTerbuka = true;
    }

    public function tutupModal(): void
    {
        $this->modalTerbuka = false;
        $this->resetValidation();
    }

    public function simpanSupplier(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mengelola data supplier.');
        }

        $this->validate();

        if ($this->isEdit && $this->selectedSupplierId) {
            $supplier = Supplier::findOrFail($this->selectedSupplierId);
            $supplier->update([
                'nama_supplier' => $this->nama_supplier,
                'kategori_pasokan' => $this->kategori_pasokan,
                'nama_pic' => $this->nama_pic ?: null,
                'nomor_telepon' => $this->nomor_telepon,
                'alamat' => $this->alamat ?: null,
                'status_kerjasama' => $this->status_kerjasama,
                'catatan' => $this->catatan ?: null,
            ]);

            session()->flash('success', "Data supplier {$supplier->nama_supplier} berhasil diperbarui.");
        } else {
            $kode = Supplier::generateKodeSupplier();
            Supplier::create([
                'kode_supplier' => $kode,
                'nama_supplier' => $this->nama_supplier,
                'kategori_pasokan' => $this->kategori_pasokan,
                'nama_pic' => $this->nama_pic ?: null,
                'nomor_telepon' => $this->nomor_telepon,
                'alamat' => $this->alamat ?: null,
                'status_kerjasama' => $this->status_kerjasama,
                'catatan' => $this->catatan ?: null,
            ]);

            session()->flash('success', "Supplier baru {$this->nama_supplier} berhasil ditambahkan ke sistem.");
        }

        $this->tutupModal();
    }

    public function konfirmasiHapus(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $this->hapusSupplierId = $supplier->id;
        $this->targetHapus = $supplier;
        $this->modalHapusTerbuka = true;
    }

    public function tutupModalHapus(): void
    {
        $this->modalHapusTerbuka = false;
        $this->hapusSupplierId = null;
        $this->targetHapus = null;
    }

    public function hapusSupplier(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Akses ditolak.');
        }

        if ($this->targetHapus) {
            $nama = $this->targetHapus->nama_supplier;
            $this->targetHapus->delete();
            $this->tutupModalHapus();
            session()->flash('success', "Data supplier {$nama} berhasil dihapus.");
        }
    }

    public function render()
    {
        $query = Supplier::withCount('warehouseFinanceLogs');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_supplier', 'like', "%{$this->search}%")
                    ->orWhere('kode_supplier', 'like', "%{$this->search}%")
                    ->orWhere('kategori_pasokan', 'like', "%{$this->search}%")
                    ->orWhere('nama_pic', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status_kerjasama', $this->filterStatus);
        }

        $suppliers = $query->orderBy('nama_supplier')->paginate(10);

        // KPI
        $totalSupplier = Supplier::count();
        $totalAktif = Supplier::where('status_kerjasama', 'aktif')->count();
        $totalNonaktif = Supplier::where('status_kerjasama', 'nonaktif')->count();

        return view('livewire.keuangan.finance-suppliers', [
            'suppliers' => $suppliers,
            'totalSupplier' => $totalSupplier,
            'totalAktif' => $totalAktif,
            'totalNonaktif' => $totalNonaktif,
        ]);
    }
}
