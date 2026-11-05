<?php

namespace App\Livewire\Satpam;

use App\Models\VehicleVisit;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class RiwayatKendaraan extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = ''; // '' = Semua, 'aktif' = Di Pabrik, 'selesai' = Sudah Keluar

    public string $filterB3 = ''; // '' = Semua, 'sesuai', 'tidak_sesuai', 'tidak_ada_b3'

    public string $filterTanggal = '';

    // Modal Detail
    public bool $showDetailModal = false;

    public ?VehicleVisit $selectedVisit = null;

    // Modal Catat Keluar Langsung dari Riwayat
    public bool $showModalKeluar = false;

    public ?VehicleVisit $targetVisitKeluar = null;

    public string $waktu_keluar = '';

    public ?string $catatan_keluar = null;

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

    public function resetFilter(): void
    {
        $this->reset(['search', 'filterStatus', 'filterB3', 'filterTanggal']);
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

    public function bukaModalKeluar(int $id): void
    {
        $visit = VehicleVisit::aktif()->find($id);

        if (! $visit) {
            session()->flash('error', 'Kendaraan tidak ditemukan atau sudah selesai keluar.');

            return;
        }

        $this->targetVisitKeluar = $visit;
        $this->waktu_keluar = now()->format('Y-m-d\TH:i');
        $this->catatan_keluar = $visit->catatan;
        $this->showModalKeluar = true;
    }

    public function tutupModalKeluar(): void
    {
        $this->showModalKeluar = false;
        $this->targetVisitKeluar = null;
        $this->reset(['waktu_keluar', 'catatan_keluar']);
        $this->resetValidation();
    }

    public function simpanKeluar(): void
    {
        $this->validate([
            'waktu_keluar' => 'required|date',
            'catatan_keluar' => 'nullable|string|max:255',
        ], [
            'waktu_keluar.required' => 'Waktu kendaraan keluar wajib diisi.',
            'waktu_keluar.date' => 'Format waktu keluar tidak valid.',
        ]);

        if (! $this->targetVisitKeluar) {
            $this->tutupModalKeluar();

            return;
        }

        $visit = VehicleVisit::aktif()->find($this->targetVisitKeluar->id);

        if (! $visit) {
            session()->flash('error', 'Kunjungan kendaraan tidak ditemukan atau sudah ditutup.');
            $this->tutupModalKeluar();

            return;
        }

        if ($this->waktu_keluar < $visit->waktu_masuk->format('Y-m-d\TH:i')) {
            $this->addError('waktu_keluar', 'Waktu keluar tidak boleh lebih awal dari waktu masuk ('.$visit->waktu_masuk->format('d/m/Y H:i').').');

            return;
        }

        $visit->update([
            'waktu_keluar' => $this->waktu_keluar,
            'catatan' => $this->catatan_keluar,
        ]);

        session()->flash('success', "Kendaraan {$visit->nomor_polisi} berhasil dicatat keluar! Catatan riwayat telah diperbarui.");
        $this->tutupModalKeluar();
    }

    /**
     * Fitur Pemulihan: Mengembalikan status kendaraan ke aktif jika satpam salah mengklik 'Catat Keluar'
     */
    public function batalkanKeluar(int $id): void
    {
        $visit = VehicleVisit::find($id);

        if (! $visit || is_null($visit->waktu_keluar)) {
            session()->flash('error', 'Kendaraan tidak ditemukan atau masih berstatus aktif.');

            return;
        }

        $nopol = $visit->nomor_polisi;
        $visit->update([
            'waktu_keluar' => null,
        ]);

        session()->flash('success', "Status keluar kendaraan {$nopol} berhasil dibatalkan. Kendaraan kini kembali tercatat aktif di area pabrik.");
    }

    public function render()
    {
        $query = VehicleVisit::with('user');

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('nomor_polisi', 'like', "%{$s}%")
                    ->orWhere('nomor_kunjungan', 'like', "%{$s}%")
                    ->orWhere('nama_pengemudi', 'like', "%{$s}%")
                    ->orWhere('asal_tujuan', 'like', "%{$s}%")
                    ->orWhere('informasi_surat_jalan', 'like', "%{$s}%");
            });
        }

        if ($this->filterStatus === 'aktif') {
            $query->whereNull('waktu_keluar');
        } elseif ($this->filterStatus === 'selesai') {
            $query->whereNotNull('waktu_keluar');
        }

        if ($this->filterB3) {
            $query->where('status_pemeriksaan_surat_jalan_b3', $this->filterB3);
        }

        if ($this->filterTanggal) {
            $query->whereDate('waktu_masuk', $this->filterTanggal);
        }

        $totalSemua = VehicleVisit::count();
        $totalAktif = VehicleVisit::whereNull('waktu_keluar')->count();
        $totalSelesai = VehicleVisit::whereNotNull('waktu_keluar')->count();
        $totalHariIni = VehicleVisit::whereDate('waktu_masuk', Carbon::today())->count();

        $kunjungan = $query->orderBy('waktu_masuk', 'desc')->paginate(10);

        return view('livewire.satpam.riwayat-kendaraan', [
            'kunjungan' => $kunjungan,
            'totalSemua' => $totalSemua,
            'totalAktif' => $totalAktif,
            'totalSelesai' => $totalSelesai,
            'totalHariIni' => $totalHariIni,
        ]);
    }
}
