<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_201_and_verification_url()
    {
        $payload = [
            'full_name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token', 'verification_url']);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'username' => 'testuser',
        ]);
    }

    public function test_login_returns_token()
    {
        $user = User::create([
            'full_name' => 'Login User',
            'username' => 'loginuser',
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_logout_deletes_token()
    {
        $user = User::create([
            'full_name' => 'Logout User',
            'username' => 'logoutuser',
            'email' => 'logout@example.com',
            'password' => 'password123',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200)->assertJson(['message' => 'Logged out']);
    }

    public function test_reset_password_flow()
    {
        $user = User::create([
            'full_name' => 'Reset User',
            'username' => 'resetuser',
            'email' => 'reset@example.com',
            'password' => 'oldpassword',
        ]);

        $forgot = $this->postJson('/api/v1/forgot', ['email' => 'reset@example.com']);
        $forgot->assertStatus(200)->assertJsonStructure(['message', 'token']);

        $token = $forgot->json('token');

        $reset = $this->postJson('/api/v1/reset', [
            'email' => 'reset@example.com',
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $reset->assertStatus(200)->assertJson(['message' => 'Password reset successful']);

        // Ensure we can login with new password
        $login = $this->postJson('/api/v1/login', [
            'email' => 'reset@example.com',
            'password' => 'newpassword',
        ]);

        $login->assertStatus(200)->assertJsonStructure(['user', 'token']);
    }
    public function test_verify_email_marks_verified()
    {
        $response = $this->postJson('/api/v1/register', [
            'full_name' => 'Verify User',
            'username' => 'verifyuser',
            'email' => 'verify@example.com',
            'password' => 'verify123',
            'password_confirmation' => 'verify123',
        ]);

        $response->assertStatus(201);

        $verificationUrl = $response->json('verification_url');

        // Call the verification URL
        $verify = $this->get($verificationUrl);

        $verify->assertStatus(200)->assertJson(['message' => 'Email verified']);

        $this->assertDatabaseHas('users', [
            'email' => 'verify@example.com',
        ]);

        $user = User::where('email', 'verify@example.com')->first();
        $this->assertNotNull($user->email_verified_at);
    }

        public function test_login_with_invalid_credentials_returns_401()
        {
            $user = User::create([
                'full_name' => 'Bad Login',
                'username' => 'badlogin',
                'email' => 'bad@example.com',
                'password' => 'rightpassword',
            ]);

            $response = $this->postJson('/api/v1/login', [
                'email' => 'bad@example.com',
                'password' => 'wrongpassword',
            ]);

            $response->assertStatus(401)->assertJson(['message' => 'Invalid credentials']);
        }

        public function test_reset_with_expired_token_returns_400()
        {
            $user = User::create([
                'full_name' => 'Expired Reset',
                'username' => 'expiredreset',
                'email' => 'expired@example.com',
                'password' => 'oldpass',
            ]);

            $raw = Str::random(64);
            PasswordResetToken::create([
                'email' => $user->email,
                'token' => Hash::make($raw),
                'created_at' => Carbon::now()->subHours(2),
            ]);

            $reset = $this->postJson('/api/v1/reset', [
                'email' => $user->email,
                'token' => $raw,
                'password' => 'newpass',
                'password_confirmation' => 'newpass',
            ]);

            $reset->assertStatus(400)->assertJson(['message' => 'Token expired']);
        }

        public function test_rate_limiting_triggers_429()
        {
            // Make limiter strict for test: 1 request per minute by IP
            RateLimiter::for('api', function (Request $request) {
                return Limit::perMinute(1)->by($request->ip());
            });

            $user = User::create([
                'full_name' => 'Rate Limit',
                'username' => 'ratelimit',
                'email' => 'rate@example.com',
                'password' => 'password123',
            ]);

            // First request should pass
            $first = $this->postJson('/api/v1/login', [
                'email' => 'rate@example.com',
                'password' => 'password123',
            ]);
            $first->assertStatus(200)->assertJsonStructure(['user', 'token']);

            // Second immediate request should be rate limited
            $second = $this->postJson('/api/v1/login', [
                'email' => 'rate@example.com',
                'password' => 'password123',
            ]);

            $second->assertStatus(429);
        }
}
