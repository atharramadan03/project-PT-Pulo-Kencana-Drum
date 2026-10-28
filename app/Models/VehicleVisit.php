<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VehicleVisit extends Model
{
    use HasFactory;

    protected $table = 'vehicle_visits';

    /**
     * Kolom yang diizinkan untuk diisi secara massal
     */
    protected $fillable = [
        'user_id',
        'nomor_kunjungan',
        'nomor_polisi',
        'nama_pengemudi',
        'asal_tujuan',
        'keperluan',
        'waktu_masuk',
        'waktu_keluar',
        'pos_gerbang',
        'informasi_surat_jalan',
        'status_pemeriksaan_surat_jalan_b3',
        'keterangan_muatan',
        'catatan',
    ];

    /**
     * Casting tipe data tanggal ke objek Carbon DateTime
     */
    protected function casts(): array
    {
        return [
            'waktu_masuk' => 'datetime',
            'waktu_keluar' => 'datetime',
        ];
    }

    /**
     * Relasi N:1 — Kunjungan dicatat oleh satu User (Satpam)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi M:N — Truk mengangkut Batch Drum (Tabel Pivot vehicle_visit_batches)
     */
    public function drumBatches(): BelongsToMany
    {
        return $this->belongsToMany(DrumBatch::class, 'vehicle_visit_batches')
            ->withPivot('jumlah_drum', 'keterangan_muatan')
            ->withTimestamps();
    }

    /**
     * Scope Eloquent: Menyaring truk yang masih aktif di dalam pabrik (belum keluar)
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('waktu_keluar');
    }

    /**
     * Generator Nomor Kunjungan Otomatis: KJ-YYYYMMDD-0001
     */
    public static function generateNomorKunjungan(): string
    {
        $today = now()->format('Ymd');
        $prefix = "KJ-{$today}-";

        // Cari nomor kunjungan terakhir pada hari ini
        $lastVisit = self::where('nomor_kunjungan', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastVisit) {
            $lastNumber = (int) substr($lastVisit->nomor_kunjungan, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
}
