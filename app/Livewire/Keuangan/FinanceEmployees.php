<?php

namespace App\Livewire\Keuangan;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceEmployees extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $filterDepartemen = '';

    // Modal Create / Edit
    public bool $modalTerbuka = false;

    public bool $isEdit = false;

    public ?int $selectedEmployeeId = null;

    // Modal Hapus
    public bool $modalHapusTerbuka = false;

    public ?int $hapusEmployeeId = null;

    public ?Employee $targetHapus = null;

    public string $nik_karyawan = '';

    #[Validate('required|string|max:150')]
    public string $nama_karyawan = '';

    #[Validate('required|string|max:100')]
    public string $departemen = 'Armada Logistik';

    #[Validate('required|string|max:100')]
    public string $jabatan = 'Supir Truk Armada';

    #[Validate('required|string|min:8|max:25')]
    public string $nomor_telepon = '';

    #[Validate('required|in:aktif,nonaktif')]
    public string $status_kerja = 'aktif';

    #[Validate('required|date')]
    public ?string $tanggal_masuk = null;

    #[Validate('nullable|string|max:500')]
    public ?string $catatan = null;

    protected function messages(): array
    {
        return [
            'nama_karyawan.required' => 'Nama lengkap driver truk wajib diisi.',
            'departemen.required' => 'Wilayah operasi / unit armada wajib diisi.',
            'jabatan.required' => 'Posisi / tipe supir armada wajib diisi.',
            'nomor_telepon.required' => 'Nomor telepon / kontak WA driver wajib diisi.',
            'nomor_telepon.min' => 'Nomor telepon minimal 8 digit.',
            'status_kerja.required' => 'Status driver truk wajib dipilih.',
            'tanggal_masuk.required' => 'Tanggal mulai bertugas wajib diisi.',
            'tanggal_masuk.date' => 'Format tanggal mulai bertugas tidak valid.',
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

    public function updatingFilterDepartemen(): void
    {
        $this->resetPage();
    }

    public function bukaModalTambah(): void
    {
        $this->resetValidation();
        $this->isEdit = false;
        $this->selectedEmployeeId = null;
        $this->nik_karyawan = Employee::generateNikKaryawan();
        $this->nama_karyawan = '';
        $this->departemen = 'Armada Logistik';
        $this->jabatan = 'Supir Truk Armada';
        $this->nomor_telepon = '';
        $this->status_kerja = 'aktif';
        $this->tanggal_masuk = now()->format('Y-m-d');
        $this->catatan = null;
        $this->modalTerbuka = true;
    }

    public function bukaModalEdit(int $id): void
    {
        $this->resetValidation();
        $emp = Employee::findOrFail($id);
        $this->isEdit = true;
        $this->selectedEmployeeId = $emp->id;
        $this->nik_karyawan = $emp->nik_karyawan;
        $this->nama_karyawan = $emp->nama_karyawan;
        $this->departemen = $emp->departemen;
        $this->jabatan = $emp->jabatan;
        $this->nomor_telepon = $emp->nomor_telepon ?? '';
        $this->status_kerja = $emp->status_kerja;
        $this->tanggal_masuk = $emp->tanggal_masuk ? $emp->tanggal_masuk->format('Y-m-d') : now()->format('Y-m-d');
        $this->catatan = $emp->catatan;
        $this->modalTerbuka = true;
    }

    public function tutupModal(): void
    {
        $this->modalTerbuka = false;
        $this->resetValidation();
    }

    public function simpanKaryawan(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mengelola data driver truk.');
        }

        $this->validate();

        if ($this->isEdit && $this->selectedEmployeeId) {
            $emp = Employee::findOrFail($this->selectedEmployeeId);
            $emp->update([
                'nama_karyawan' => $this->nama_karyawan,
                'departemen' => $this->departemen,
                'jabatan' => $this->jabatan,
                'nomor_telepon' => $this->nomor_telepon,
                'status_kerja' => $this->status_kerja,
                'tanggal_masuk' => $this->tanggal_masuk,
                'catatan' => $this->catatan ?: null,
            ]);

            session()->flash('success', "Data driver truk {$emp->nama_karyawan} ({$emp->nik_karyawan}) berhasil diperbarui.");
        } else {
            $nik = Employee::generateNikKaryawan();
            Employee::create([
                'nik_karyawan' => $nik,
                'nama_karyawan' => $this->nama_karyawan,
                'departemen' => $this->departemen,
                'jabatan' => $this->jabatan,
                'nomor_telepon' => $this->nomor_telepon,
                'status_kerja' => $this->status_kerja,
                'tanggal_masuk' => $this->tanggal_masuk,
                'catatan' => $this->catatan ?: null,
            ]);

            session()->flash('success', "Driver truk baru {$this->nama_karyawan} berhasil didaftarkan ke sistem armada.");
        }

        $this->tutupModal();
    }

    public function konfirmasiHapus(int $id): void
    {
        $emp = Employee::findOrFail($id);
        $this->hapusEmployeeId = $emp->id;
        $this->targetHapus = $emp;
        $this->modalHapusTerbuka = true;
    }

    public function tutupModalHapus(): void
    {
        $this->modalHapusTerbuka = false;
        $this->hapusEmployeeId = null;
        $this->targetHapus = null;
    }

    public function hapusKaryawan(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Akses ditolak.');
        }

        if ($this->targetHapus) {
            $nama = $this->targetHapus->nama_karyawan;
            $this->targetHapus->delete();
            $this->tutupModalHapus();
            session()->flash('success', "Data driver truk {$nama} berhasil dihapus dari sistem.");
        }
    }

    public function render()
    {
        $query = Employee::withCount('operationalExpenseLogs');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_karyawan', 'like', "%{$this->search}%")
                    ->orWhere('nik_karyawan', 'like', "%{$this->search}%")
                    ->orWhere('departemen', 'like', "%{$this->search}%")
                    ->orWhere('jabatan', 'like', "%{$this->search}%")
                    ->orWhere('nomor_telepon', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status_kerja', $this->filterStatus);
        }

        if ($this->filterDepartemen) {
            $query->where('departemen', $this->filterDepartemen);
        }

        $employees = $query->orderBy('nama_karyawan')->paginate(10);

        // KPI
        $totalKaryawan = Employee::count();
        $totalAktif = Employee::where('status_kerja', 'aktif')->count();
        $totalPengelolaBiaya = Employee::has('operationalExpenseLogs')->count();
        $departemenList = Employee::select('departemen')->distinct()->pluck('departemen');

        return view('livewire.keuangan.finance-employees', [
            'employees' => $employees,
            'totalKaryawan' => $totalKaryawan,
            'totalAktif' => $totalAktif,
            'totalPengelolaBiaya' => $totalPengelolaBiaya,
            'departemenList' => $departemenList,
        ]);
    }
}
