<?php

namespace App\Livewire\Admin;

use App\Models\DrumBatch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DrumBatchIndex extends Component
{
    use WithPagination;

    // Filter & Pencarian
    public string $search = '';
    public string $filterPengendapan = '';
    public string $filterProses = '';

    // Properti Modal Input Batch Baru
    public bool $showCreateModal = false;
    public string $kode_batch = '';

    #[Validate('required|date')]
    public string $tanggal_penerimaan = '';

    #[Validate('required|integer|min:1')]
    public int $jumlah_diterima = 100;

    #[Validate('required|string|max:100')]
    public string $kategori_jenis = 'Besi Tight Head';

    #[Validate('required|in:Transit,Mengendap,Selesai')]
    public string $status_pengendapan = 'Transit';

    #[Validate('required|in:Diterima,Dalam Proses,Selesai')]
    public string $status_proses = 'Diterima';

    #[Validate('nullable|string|max:150')]
    public ?string $sumber_laporan = null;

    #[Validate('nullable|string')]
    public ?string $catatan = null;

    // Properti Modal Update Status Cepat
    public bool $showStatusModal = false;
    public ?int $selectedBatchId = null;
    public string $edit_status_pengendapan = '';
    public string $edit_status_proses = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPengendapan(): void
    {
        $this->resetPage();
    }

    public function updatingFilterProses(): void
    {
        $this->resetPage();
    }

    public function bukaCreateModal(): void
    {
        $this->resetValidation();
        $this->kode_batch = DrumBatch::generateKodeBatch();
        $this->tanggal_penerimaan = now()->format('Y-m-d');
        $this->jumlah_diterima = 100;
        $this->kategori_jenis = 'Besi Tight Head';
        $this->status_pengendapan = 'Transit';
        $this->status_proses = 'Diterima';
        $this->sumber_laporan = 'Nota Fisik Gudang';
        $this->catatan = null;
        $this->showCreateModal = true;
    }

    public function tutupCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function simpanBatch(): void
    {
        $this->validate();

        $kodeBaru = DrumBatch::generateKodeBatch();

        DrumBatch::create([
            'user_id' => auth()->id(),
            'kode_batch' => $kodeBaru,
            'tanggal_penerimaan' => $this->tanggal_penerimaan,
            'jumlah_diterima' => $this->jumlah_diterima,
            'kategori_jenis' => $this->kategori_jenis,
            'status_pengendapan' => $this->status_pengendapan,
            'status_proses' => $this->status_proses,
            'sumber_laporan' => $this->sumber_laporan,
            'catatan' => $this->catatan,
        ]);

        session()->flash('success', "Batch Drum {$kodeBaru} ({$this->jumlah_diterima} drum) berhasil dicatat ke sistem!");

        $this->tutupCreateModal();
    }

    public function bukaStatusModal(int $id): void
    {
        $batch = DrumBatch::find($id);
        if ($batch) {
            $this->selectedBatchId = $batch->id;
            $this->edit_status_pengendapan = $batch->status_pengendapan;
            $this->edit_status_proses = $batch->status_proses;
            $this->showStatusModal = true;
        }
    }

    public function tutupStatusModal(): void
    {
        $this->showStatusModal = false;
        $this->reset(['selectedBatchId', 'edit_status_pengendapan', 'edit_status_proses']);
    }

    public function simpanStatus(): void
    {
        $batch = DrumBatch::find($this->selectedBatchId);
        if ($batch) {
            $batch->update([
                'status_pengendapan' => $this->edit_status_pengendapan,
                'status_proses' => $this->edit_status_proses,
            ]);

            session()->flash('success', "Status Batch {$batch->kode_batch} berhasil diperbarui!");
        }

        $this->tutupStatusModal();
    }

    public function render()
    {
        $query = DrumBatch::with(['user', 'drumProcessLogs']);

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('kode_batch', 'like', "%{$s}%")
                  ->orWhere('kategori_jenis', 'like', "%{$s}%")
                  ->orWhere('sumber_laporan', 'like', "%{$s}%");
            });
        }

        if ($this->filterPengendapan) {
            $query->where('status_pengendapan', $this->filterPengendapan);
        }

        if ($this->filterProses) {
            $query->where('status_proses', $this->filterProses);
        }

        $batches = $query->orderBy('tanggal_penerimaan', 'desc')->paginate(10);

        return view('livewire.admin.drum-batch-index', [
            'batches' => $batches,
        ]);
    }
}
