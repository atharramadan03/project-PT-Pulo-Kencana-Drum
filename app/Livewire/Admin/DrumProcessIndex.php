<?php

namespace App\Livewire\Admin;

use App\Models\DrumBatch;
use App\Models\DrumProcessLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DrumProcessIndex extends Component
{
    use WithPagination;

    // Filter
    public string $search = '';

    public string $filterJenisProses = '';

    public string $filterBatchId = '';

    // Modal Input Log Proses Baru
    public bool $showCreateModal = false;

    #[Validate('required|exists:drum_batches,id')]
    public ?int $drum_batch_id = null;

    #[Validate('required|in:penyortiran,peniupan,pencucian,pengecatan')]
    public string $jenis_proses = 'penyortiran';

    #[Validate('required|date')]
    public string $tanggal_aktivitas = '';

    #[Validate('required|integer|min:1')]
    public int $jumlah_diproses = 50;

    #[Validate('required|integer|min:0')]
    public int $jumlah_afkir = 0;

    #[Validate('required|string|max:100')]
    public string $pelapor_gudang = '';

    #[Validate('nullable|string')]
    public ?string $hasil_catatan = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterJenisProses(): void
    {
        $this->resetPage();
    }

    public function updatingFilterBatchId(): void
    {
        $this->resetPage();
    }

    public function bukaCreateModal(): void
    {
        $this->resetValidation();
        $firstBatch = DrumBatch::orderBy('id', 'desc')->first();
        $this->drum_batch_id = $firstBatch?->id;
        $this->jenis_proses = 'penyortiran';
        $this->tanggal_aktivitas = now()->format('Y-m-d');
        $this->jumlah_diproses = 50;
        $this->jumlah_afkir = 0;
        $this->pelapor_gudang = 'Mandor Gudang';
        $this->hasil_catatan = null;
        $this->showCreateModal = true;
    }

    public function tutupCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function simpanLog(): void
    {
        $this->validate();

        // Validasi SOP: Jumlah reject tidak boleh melebihi jumlah yang diproses
        if ($this->jumlah_afkir > $this->jumlah_diproses) {
            $this->addError('jumlah_afkir', 'Jumlah reject tidak boleh melebihi jumlah drum yang diproses.');

            return;
        }

        $log = DrumProcessLog::create([
            'drum_batch_id' => $this->drum_batch_id,
            'user_id' => auth()->id(),
            'jenis_proses' => $this->jenis_proses,
            'tanggal_aktivitas' => $this->tanggal_aktivitas,
            'tanggal_input' => now(), // Waktu nyata saat Admin mengetik ke sistem
            'jumlah_diproses' => $this->jumlah_diproses,
            'jumlah_afkir' => $this->jumlah_afkir,
            'pelapor_gudang' => trim($this->pelapor_gudang),
            'hasil_catatan' => $this->hasil_catatan ? trim($this->hasil_catatan) : null,
        ]);

        // Perbarui status batch jika sebelumnya masih 'Diterima'
        $batch = DrumBatch::find($this->drum_batch_id);
        if ($batch && $batch->status_proses === 'Diterima') {
            $batch->update(['status_proses' => 'Dalam Proses']);
        }

        session()->flash('success', "Log proses {$log->label_proses} untuk batch {$batch?->kode_batch} berhasil dicatat!");

        $this->tutupCreateModal();
    }

    public function render()
    {
        $query = DrumProcessLog::with(['drumBatch', 'user']);

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('pelapor_gudang', 'like', "%{$s}%")
                    ->orWhere('hasil_catatan', 'like', "%{$s}%")
                    ->orWhereHas('drumBatch', function ($b) use ($s) {
                        $b->where('kode_batch', 'like', "%{$s}%");
                    });
            });
        }

        if ($this->filterJenisProses) {
            $query->where('jenis_proses', $this->filterJenisProses);
        }

        if ($this->filterBatchId) {
            $query->where('drum_batch_id', $this->filterBatchId);
        }

        $logs = $query->orderBy('tanggal_aktivitas', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $daftarBatch = DrumBatch::orderBy('id', 'desc')->get();

        return view('livewire.admin.drum-process-index', [
            'logs' => $logs,
            'daftarBatch' => $daftarBatch,
        ]);
    }
}
