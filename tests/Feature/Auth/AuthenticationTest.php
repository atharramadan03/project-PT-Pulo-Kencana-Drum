<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'admin_kantor',
            'status_akun' => 'aktif',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email'])
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_login_shows_indonesian_validation_errors_when_fields_are_empty(): void
    {
        $component = Volt::test('pages.auth.login')
            ->set('form.email', '')
            ->set('form.password', '');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email', 'form.password'])
            ->assertSee('Username atau alamat email wajib diisi.')
            ->assertSee('Password akun wajib diisi.');
    }

    public function test_login_shows_indonesian_error_message_when_credentials_are_invalid(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'salah123');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email'])
            ->assertSee('Email/Username atau password yang Anda masukkan salah. Silakan periksa kembali.');
    }

    public function test_navigation_menu_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'role' => 'admin_kantor',
            'status_akun' => 'aktif',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSeeVolt('layout.navigation');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('layout.navigation');

        $component->call('logout');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_user_can_logout_via_post_route(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_users_can_authenticate_using_username(): void
    {
        $user = User::factory()->create([
            'username' => 'admin_gudang_pkdi',
            'role' => 'admin_kantor',
            'status_akun' => 'aktif',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', 'admin_gudang_pkdi')
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }
}
