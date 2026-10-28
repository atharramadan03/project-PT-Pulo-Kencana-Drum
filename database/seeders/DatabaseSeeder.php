<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Eksekusi seeder untuk mengisi data awal database pkdi_gudang.
     */
    public function run(): void
    {
        // 1. Akun Resmi Admin Kantor
        User::updateOrCreate(
            ['email' => 'admin@pkdi.co.id'],
            [
                'name' => 'Admin Kantor Gudang',
                'username' => 'admin_kantor',
                'password' => Hash::make('password123'),
                'role' => 'admin_kantor',
                'shift_jaga' => null,
                'status_akun' => 'aktif',
            ]
        );

        // 2. Akun Resmi Satpam Pos Gerbang
        User::updateOrCreate(
            ['email' => 'satpam@pkdi.co.id'],
            [
                'name' => 'Satpam Pos Gerbang',
                'username' => 'satpam_gerbang',
                'password' => Hash::make('password123'),
                'role' => 'satpam',
                'shift_jaga' => 'Pagi',
                'status_akun' => 'aktif',
            ]
        );
    }
}
