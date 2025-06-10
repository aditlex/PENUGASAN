<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_and_are_redirected_to_login(): void // Nama method disesuaikan
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Assert bahwa pengguna dialihkan ke halaman login (bukan dashboard)
        $response->assertRedirect(route('login')); 

        // Assert bahwa pengguna TIDAK terautentikasi setelah register
        $this->assertGuest(); 

        // Assert bahwa data pengguna sudah ada di database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}

