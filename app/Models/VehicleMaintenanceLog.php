<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'vehicle_visit_id',
    'nomor_polisi',
    'jenis_kendaraan',
    'tanggal_perbaikan',
    'jenis_perbaikan',
    'nama_bengkel',
    'biaya',
    'status_perbaikan',
    'keterangan',
])]
class VehicleMaintenanceLog extends Model
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
            'tanggal_perbaikan' => 'date',
            'biaya' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke User (Admin Keuangan yang mencatat)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke VehicleVisit (opsional)
     */
    public function vehicleVisit(): BelongsTo
    {
        return $this->belongsTo(VehicleVisit::class);
    }

    /**
     * Format biaya ke format Rupiah standar
     */
    public function getFormattedBiayaAttribute(): string
    {
        return 'Rp '.number_format((float) $this->biaya, 0, ',', '.');
    }
}
