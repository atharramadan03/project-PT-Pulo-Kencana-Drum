<?php

namespace App\Livewire\Keuangan;

use App\Models\VehicleMaintenanceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceVehicleRepairs extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    // Modal Create Perbaikan
    public bool $showCreateModal = false;

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
    public ?string $keterangan = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function bukaCreateModal(): void
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
        $this->keterangan = null;
        $this->showCreateModal = true;
    }

    public function tutupCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function simpanPerbaikan(): void
    {
        if (! Auth::user()?->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mencatat data perbaikan.');
        }

        $this->validate();

        VehicleMaintenanceLog::create([
            'user_id' => Auth::id(),
            'nomor_polisi' => strtoupper(trim($this->nomor_polisi)),
            'jenis_kendaraan' => $this->jenis_kendaraan,
            'tanggal_perbaikan' => $this->tanggal_perbaikan,
            'jenis_perbaikan' => $this->jenis_perbaikan,
            'nama_bengkel' => $this->nama_bengkel,
            'biaya' => (float) $this->biaya,
            'status_perbaikan' => $this->status_perbaikan,
            'keterangan' => $this->keterangan,
        ]);

        session()->flash('success', "Data servis armada {$this->nomor_polisi} berhasil disimpan!");
        $this->tutupCreateModal();
    }

    public function render()
    {
        $query = VehicleMaintenanceLog::with('user');

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('nomor_polisi', 'like', "%{$s}%")
                    ->orWhere('jenis_kendaraan', 'like', "%{$s}%")
                    ->orWhere('jenis_perbaikan', 'like', "%{$s}%")
                    ->orWhere('nama_bengkel', 'like', "%{$s}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status_perbaikan', $this->filterStatus);
        }

        $totalBiaya = (float) (clone $query)->sum('biaya');
        $countSelesai = (clone $query)->where('status_perbaikan', 'selesai')->count();
        $countProses = (clone $query)->where('status_perbaikan', 'dalam_pengerjaan')->count();

        $repairs = $query->orderBy('tanggal_perbaikan', 'desc')->latest('id')->paginate(10);

        return view('livewire.keuangan.finance-vehicle-repairs', [
            'repairs' => $repairs,
            'totalBiaya' => $totalBiaya,
            'countSelesai' => $countSelesai,
            'countProses' => $countProses,
        ]);
    }
}
