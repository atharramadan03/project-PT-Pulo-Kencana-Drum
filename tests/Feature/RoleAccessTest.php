<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Admin Kantor dapat mengakses dashboard admin.
     */
    public function test_admin_kantor_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_kantor',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrasi Operasional');
    }

    /**
     * Satpam dapat mengakses dashboard pos gerbang.
     */
    public function test_satpam_can_access_satpam_dashboard(): void
    {
        $satpam = User::factory()->create([
            'role' => 'satpam',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($satpam)->get(route('satpam.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pos Gerbang Utama');
    }

    /**
     * Satpam dilarang mengakses dashboard admin (otomatis dibelokkan ke dashboard satpam).
     */
    public function test_satpam_cannot_access_admin_dashboard(): void
    {
        $satpam = User::factory()->create([
            'role' => 'satpam',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($satpam)->get(route('admin.dashboard'));

        $response->assertRedirect(route('satpam.dashboard'));
    }

    /**
     * Admin Kantor dilarang mengakses dashboard satpam (otomatis dibelokkan ke dashboard admin).
     */
    public function test_admin_cannot_access_satpam_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_kantor',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('satpam.dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Pengguna belum login diarahkan ke login.
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $responseAdmin = $this->get(route('admin.dashboard'));
        $responseAdmin->assertRedirect(route('login'));

        $responseSatpam = $this->get(route('satpam.dashboard'));
        $responseSatpam->assertRedirect(route('login'));
    }

    /**
     * URL /dashboard mengarahkan otomatis sesuai role pengguna.
     */
    public function test_central_dashboard_redirects_by_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin_kantor', 'status_akun' => 'aktif']);
        $satpam = User::factory()->create(['role' => 'satpam', 'status_akun' => 'aktif']);
        $keuangan = User::factory()->create(['role' => 'admin_keuangan', 'status_akun' => 'aktif']);

        $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($satpam)->get(route('dashboard'))->assertRedirect(route('satpam.dashboard'));
        $this->actingAs($keuangan)->get(route('dashboard'))->assertRedirect(route('keuangan.dashboard'));
    }

    /**
     * Admin Keuangan dapat mengakses dashboard keuangan.
     */
    public function test_admin_keuangan_can_access_finance_dashboard(): void
    {
        $keuangan = User::factory()->create([
            'role' => 'admin_keuangan',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($keuangan)->get(route('keuangan.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Keuangan');
    }

    /**
     * Admin Kantor dan Satpam juga dapat mengakses dashboard rekapitulasi keuangan.
     */
    public function test_admin_kantor_and_satpam_can_access_finance_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin_kantor', 'status_akun' => 'aktif']);
        $satpam = User::factory()->create(['role' => 'satpam', 'status_akun' => 'aktif']);

        $this->actingAs($admin)->get(route('keuangan.dashboard'))->assertStatus(200);
        $this->actingAs($satpam)->get(route('keuangan.dashboard'))->assertStatus(200);
    }

    /**
     * Admin Keuangan dilarang mengakses dashboard admin kantor (otomatis dibelokkan ke dashboard keuangan).
     */
    public function test_admin_keuangan_cannot_access_admin_dashboard(): void
    {
        $keuangan = User::factory()->create([
            'role' => 'admin_keuangan',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($keuangan)->get(route('admin.dashboard'));

        $response->assertRedirect(route('keuangan.dashboard'));
    }
}
