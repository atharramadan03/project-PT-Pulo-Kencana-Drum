<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'employee_id',
    'kode_biaya',
    'kategori_biaya',
    'nama_biaya',
    'nominal',
    'tanggal_pengeluaran',
    'metode_pembayaran',
    'nomor_bukti',
    'penerima_dana',
    'keterangan',
])]
class OperationalExpenseLog extends Model
{
    use HasFactory;

    /**
     * Tipe data casting atribut
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_pengeluaran' => 'date',
            'nominal' => 'decimal:2',
        ];
    }

    /**
     * Generator kode unik biaya operasional: BOP-YYYYMM-001
     */
    public static function generateKodeBiaya(): string
    {
        $prefix = 'BOP-'.now()->format('Ym');
        $lastLog = self::where('kode_biaya', 'like', "{$prefix}-%")
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($lastLog && preg_match('/-(\d+)$/', $lastLog->kode_biaya, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%03d', $prefix, $nextNumber);
    }

    /**
     * Relasi ke User (Admin Keuangan yang mencatat)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Karyawan Pengelola / Penanggung Jawab Biaya Operasional
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Label kategori dalam Bahasa Indonesia
     */
    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori_biaya) {
            'listrik_air' => 'Listrik & Air',
            'bbm_solar' => 'BBM Solar Armada',
            'perlengkapan_apd' => 'Perlengkapan & APD',
            'konsumsi_karyawan' => 'Konsumsi Karyawan',
            'atk_kantor' => 'ATK & Kantor',
            'pemeliharaan_fasilitas' => 'Pemeliharaan Fasilitas',
            default => 'Lain-lain',
        };
    }

    /**
     * Format biaya Rupiah
     */
    public function getFormattedNominalAttribute(): string
    {
        return 'Rp '.number_format((float) $this->nominal, 0, ',', '.');
    }
}
