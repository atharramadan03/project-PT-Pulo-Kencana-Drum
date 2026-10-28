<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrumProcessLog extends Model
{
    use HasFactory;

    protected $table = 'drum_process_logs';

    protected $fillable = [
        'drum_batch_id',
        'user_id',
        'jenis_proses',
        'tanggal_aktivitas',
        'tanggal_input',
        'jumlah_diproses',
        'jumlah_afkir',
        'pelapor_gudang',
        'hasil_catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_aktivitas' => 'date',
            'tanggal_input' => 'datetime',
            'jumlah_diproses' => 'integer',
            'jumlah_afkir' => 'integer',
        ];
    }

    /**
     * Relasi N:1 — Log proses ini milik Batch Drum tertentu
     */
    public function drumBatch(): BelongsTo
    {
        return $this->belongsTo(DrumBatch::class);
    }

    /**
     * Relasi N:1 — Log proses direkam ke sistem oleh Admin Kantor
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper nama label proses untuk tampilan UI
     */
    public function getLabelProsesAttribute(): string
    {
        return match ($this->jenis_proses) {
            'penyortiran' => 'Penyortiran (Sorting)',
            'peniupan' => 'Peniupan (Denting)',
            'pencucian' => 'Pencucian (Washing)',
            'pengecatan' => 'Pengecatan (Painting)',
            default => ucfirst($this->jenis_proses),
        };
    }
}
