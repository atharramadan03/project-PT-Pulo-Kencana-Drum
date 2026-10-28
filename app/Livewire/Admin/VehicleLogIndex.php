<?php

namespace App\Livewire\Admin;

use App\Models\VehicleVisit;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class VehicleLogIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = ''; // '' (Semua), 'aktif', 'selesai'
    public string $filterB3 = ''; // '' (Semua), 'Lengkap', 'Tidak Lengkap', 'Bukan B3'
    public string $filterTanggal = '';

    // Modal Detail
    public bool $showDetailModal = false;
    public ?VehicleVisit $selectedVisit = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterB3(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTanggal(): void
    {
        $this->resetPage();
    }

    public function lihatDetail(int $id): void
    {
        $this->selectedVisit = VehicleVisit::with(['user', 'drumBatches'])->find($id);
        if ($this->selectedVisit) {
            $this->showDetailModal = true;
        }
    }

    public function tutupDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedVisit = null;
    }

    public function render()
    {
        $query = VehicleVisit::with(['user'])
            ->when($this->search, function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('nomor_kunjungan', 'like', "%{$this->search}%")
                        ->orWhere('nomor_polisi', 'like', "%{$this->search}%")
                        ->orWhere('nama_pengemudi', 'like', "%{$this->search}%")
                        ->orWhere('asal_tujuan', 'like', "%{$this->search}%")
                        ->orWhere('informasi_surat_jalan', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus === 'aktif', fn ($q) => $q->whereNull('waktu_keluar'))
            ->when($this->filterStatus === 'selesai', fn ($q) => $q->whereNotNull('waktu_keluar'))
            ->when($this->filterB3, fn ($q) => $q->where('status_pemeriksaan_surat_jalan_b3', $this->filterB3))
            ->when($this->filterTanggal, fn ($q) => $q->whereDate('waktu_masuk', $this->filterTanggal))
            ->orderBy('waktu_masuk', 'desc');

        $visits = $query->paginate(15);

        return view('livewire.admin.vehicle-log-index', [
            'visits' => $visits,
        ]);
    }
}
