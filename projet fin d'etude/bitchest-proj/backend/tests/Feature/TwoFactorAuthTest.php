<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorAuthTest extends TestCase
{
    use RefreshDatabase;

    protected $google2fa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->google2fa = new Google2FA();
        $this->withoutMiddleware(\App\Http\Middleware\Authenticate::class);
    }

    public function test_seeder_creates_admin_and_user()
    {
        // Run the seeder
        $this->artisan('db:seed');

        // Check admin account
        $admin = User::where('email', 'admin@bitchest.com')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('admin', $admin->role);
        $this->assertTrue($admin->is_active);

        // Check user account
        $user = User::where('email', 'user@bitchest.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('client', $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_user_can_enable_and_verify_2fa()
    {
        // Create and authenticate a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'client',
            'is_active' => true,
            'two_factor_enabled' => false,
            'two_factor_secret' => null
        ]);

        // Login and get token
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $token = $response->json('access_token');
        $this->assertNotNull($token);

        // Enable 2FA
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Accept', 'application/json')
            ->postJson('/api/auth/2fa/enable');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'qr_code',
                'secret'
            ]);

        $secret = $response->json('secret');

        // Confirm 2FA with valid code
        $validCode = $this->google2fa->getCurrentOtp($secret);
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Accept', 'application/json')
            ->postJson('/api/auth/2fa/confirm', [
            'code' => $validCode,
            'secret' => $secret
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success'
            ]);

        // Log response and user state for debugging
        info('2FA Confirm Response:', [
            'response' => $response->json(),
            'status' => $response->status(),
            'user' => $user->fresh()->toArray()
        ]);

        // Reload user and check 2FA is enabled
        $user->refresh();
        $this->assertTrue($user->two_factor_enabled);
        $this->assertNotNull($user->two_factor_secret);

        // Verify with valid code
        $validCode = $this->google2fa->getCurrentOtp($user->two_factor_secret);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/2fa/verify', [
            'code' => $validCode
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success'
            ]);

        // Try to verify with invalid code
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/2fa/verify', [
            'code' => '000000'
        ]);

        $response->assertStatus(400);

        // Disable 2FA
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/2fa/disable');

        $response->assertStatus(200);

        // Reload user and check 2FA is disabled
        $user->refresh();
        $this->assertFalse($user->two_factor_enabled);
        $this->assertNull($user->two_factor_secret);
    }
}