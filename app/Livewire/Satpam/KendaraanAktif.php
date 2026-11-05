<?php

namespace App\Livewire\Satpam;

use App\Models\VehicleVisit;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class KendaraanAktif extends Component
{
    use WithPagination;

    public string $search = '';

    // Properti untuk Modal Form Catat Keluar
    public ?int $selectedVisitId = null;

    public ?VehicleVisit $selectedVisit = null;

    public string $waktu_keluar = '';

    public ?string $catatan_keluar = null;

    public bool $showModal = false;

    // Properti untuk Modal Hapus Kunjungan
    public bool $modalHapusTerbuka = false;

    public ?int $hapusVisitId = null;

    public ?VehicleVisit $targetVisitHapus = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Buka modal untuk memproses kendaraan keluar
     */
    public function bukaModalKeluar(int $visitId): void
    {
        $visit = VehicleVisit::aktif()->find($visitId);

        if (! $visit) {
            session()->flash('error', 'Kunjungan kendaraan tidak ditemukan atau sudah selesai ditutup.');

            return;
        }

        $this->selectedVisitId = $visit->id;
        $this->selectedVisit = $visit;
        $this->waktu_keluar = now()->format('Y-m-d\TH:i');
        $this->catatan_keluar = $visit->catatan;
        $this->showModal = true;
    }

    /**
     * Tutup modal
     */
    public function tutupModal(): void
    {
        $this->showModal = false;
        $this->reset(['selectedVisitId', 'selectedVisit', 'waktu_keluar', 'catatan_keluar']);
        $this->resetValidation();
    }

    /**
     * Simpan proses kendaraan keluar dan tutup kunjungan
     */
    public function simpanKeluar(): void
    {
        $this->validate([
            'waktu_keluar' => 'required|date',
            'catatan_keluar' => 'nullable|string|max:255',
        ], [
            'waktu_keluar.required' => 'Waktu kendaraan keluar wajib diisi.',
            'waktu_keluar.date' => 'Format waktu keluar tidak valid.',
        ]);

        $visit = VehicleVisit::aktif()->find($this->selectedVisitId);

        if (! $visit) {
            session()->flash('error', 'Kunjungan kendaraan tidak ditemukan atau sudah selesai ditutup.');
            $this->tutupModal();

            return;
        }

        // Aturan Validasi PRD v1.2: Waktu keluar tidak boleh lebih awal dari waktu masuk
        if ($this->waktu_keluar < $visit->waktu_masuk->format('Y-m-d\TH:i')) {
            $this->addError('waktu_keluar', 'Waktu keluar tidak boleh lebih awal dari waktu masuk ('.$visit->waktu_masuk->format('d/m/Y H:i').').');

            return;
        }

        $visit->update([
            'waktu_keluar' => $this->waktu_keluar,
            'catatan' => $this->catatan_keluar,
        ]);

        session()->flash('success', "Kendaraan {$visit->nomor_polisi} berhasil dicatat keluar pabrik! Catatan tersimpan di menu Riwayat Kendaraan.");

        $this->tutupModal();
    }

    public function konfirmasiHapus(int $id): void
    {
        $visit = VehicleVisit::findOrFail($id);
        $this->hapusVisitId = $visit->id;
        $this->targetVisitHapus = $visit;
        $this->modalHapusTerbuka = true;
    }

    public function tutupModalHapus(): void
    {
        $this->modalHapusTerbuka = false;
        $this->hapusVisitId = null;
        $this->targetVisitHapus = null;
    }

    public function hapusVisit(): void
    {
        $visit = VehicleVisit::findOrFail($this->hapusVisitId);
        $nopol = $visit->nomor_polisi;
        $noKunjungan = $visit->nomor_kunjungan;

        $visit->drumBatches()->detach();
        $visit->delete();

        $this->tutupModalHapus();
        session()->flash('success', "Data kunjungan kendaraan {$nopol} ({$noKunjungan}) berhasil dihapus.");
    }

    public function render()
    {
        $query = VehicleVisit::aktif()->with('user');

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('nomor_polisi', 'like', "%{$s}%")
                    ->orWhere('nomor_kunjungan', 'like', "%{$s}%")
                    ->orWhere('nama_pengemudi', 'like', "%{$s}%")
                    ->orWhere('asal_tujuan', 'like', "%{$s}%");
            });
        }

        $kendaraanAktif = $query->orderBy('waktu_masuk', 'asc')->paginate(10);

        return view('livewire.satpam.kendaraan-aktif', [
            'kendaraanAktif' => $kendaraanAktif,
        ]);
    }
}
