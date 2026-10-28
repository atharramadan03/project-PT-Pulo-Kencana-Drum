<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'role',
    'shift_jaga',
    'status_akun',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Tipe data casting bawaan Laravel
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Helper untuk memeriksa apakah user adalah Admin Kantor
     */
    public function isAdminKantor(): bool
    {
        return $this->role === 'admin_kantor';
    }

    /**
     * Helper untuk memeriksa apakah user adalah Satpam
     */
    public function isSatpam(): bool
    {
        return $this->role === 'satpam';
    }

    /**
     * Relasi 1:N — User Satpam mencatat banyak Kunjungan Kendaraan
     */
    public function vehicleVisits(): HasMany
    {
        return $this->hasMany(VehicleVisit::class);
    }

    /**
     * Relasi 1:N — User Admin Kantor menginput banyak Batch Drum
     */
    public function drumBatches(): HasMany
    {
        return $this->hasMany(DrumBatch::class);
    }

    /**
     * Relasi 1:N — User Admin Kantor menginput banyak Log Proses Drum
     */
    public function drumProcessLogs(): HasMany
    {
        return $this->hasMany(DrumProcessLog::class);
    }
}
