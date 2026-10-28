<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DrumBatch extends Model
{
    use HasFactory;

    protected $table = 'drum_batches';

    protected $fillable = [
        'user_id',
        'kode_batch',
        'tanggal_penerimaan',
        'jumlah_diterima',
        'kategori_jenis',
        'status_pengendapan',
        'status_proses',
        'sumber_laporan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_penerimaan' => 'date',
            'jumlah_diterima' => 'integer',
        ];
    }

    /**
     * Relasi N:1 — Batch diinput oleh Admin Kantor
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi 1:N — Satu Batch menjalani banyak tahap log proses rekondisi
     */
    public function drumProcessLogs(): HasMany
    {
        return $this->hasMany(DrumProcessLog::class)->orderBy('tanggal_aktivitas', 'asc');
    }

    /**
     * Relasi M:N — Batch diangkut oleh Kunjungan Truk Armada
     */
    public function vehicleVisits(): BelongsToMany
    {
        return $this->belongsToMany(VehicleVisit::class, 'vehicle_visit_batches')
            ->withPivot('jumlah_drum', 'keterangan_muatan')
            ->withTimestamps();
    }

    /**
     * Generator Kode Batch Otomatis: BATCH-YYYYMM-001
     */
    public static function generateKodeBatch(): string
    {
        $period = now()->format('Ym');
        $prefix = "BATCH-{$period}-";

        $lastBatch = self::where('kode_batch', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBatch) {
            $lastNumber = (int) substr($lastBatch->kode_batch, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }
}
