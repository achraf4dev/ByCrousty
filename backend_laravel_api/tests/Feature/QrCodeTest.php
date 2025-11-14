<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_gets_qr_code_on_registration()
    {
        $userData = [
            'full_name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'full_name',
                        'username',
                        'email',
                        'qr_code_url'
                    ],
                    'token',
                    'verification_url',
                    'qr_code_url'
                ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user->qr_code_data);
        $this->assertNotNull($user->qr_code_url);
    }

    public function test_qr_code_image_endpoint_returns_image()
    {
        $user = User::factory()->create();
        
        $qrCodeService = new QrCodeService();
        $qrCodeData = $qrCodeService->generateQrCodeData(
            $user->id,
            $user->email,
            $user->username
        );
        
        $user->update(['qr_code_data' => $qrCodeData]);

        // User accessing their own QR code through my-qr-code endpoint
        $response = $this->actingAs($user, 'sanctum')
                        ->get('/api/v1/users/my-qr-code');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'image/png');
    }

    public function test_my_qr_code_endpoint()
    {
        $user = User::factory()->create();
        
        $qrCodeService = new QrCodeService();
        $qrCodeData = $qrCodeService->generateQrCodeData(
            $user->id,
            $user->email,
            $user->username
        );
        
        $user->update(['qr_code_data' => $qrCodeData]);

        $response = $this->actingAs($user, 'sanctum')
                        ->get('/api/v1/users/my-qr-code');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'image/png');
    }

    public function test_qr_code_endpoint_requires_authentication()
    {
        // My QR code endpoint should require authentication
        $response = $this->getJson('/api/v1/users/my-qr-code');
        $response->assertStatus(401);
    }

    public function test_qr_code_service_generates_valid_data()
    {
        $qrCodeService = new QrCodeService();
        
        $qrCodeData = $qrCodeService->generateQrCodeData(1, 'test@example.com', 'testuser');
        $decodedData = $qrCodeService->decodeQrCodeData($qrCodeData);

        $this->assertIsArray($decodedData);
        $this->assertEquals(1, $decodedData['user_id']);
        $this->assertEquals('test@example.com', $decodedData['email']);
        $this->assertEquals('testuser', $decodedData['username']);
        $this->assertArrayHasKey('unique_id', $decodedData);
        $this->assertArrayHasKey('generated_at', $decodedData);
    }

    public function test_user_profile_includes_qr_code_url()
    {
        $user = User::factory()->create();
        
        $qrCodeService = new QrCodeService();
        $qrCodeData = $qrCodeService->generateQrCodeData(
            $user->id,
            $user->email,
            $user->username
        );
        
        $user->update(['qr_code_data' => $qrCodeData]);

        $response = $this->actingAs($user, 'sanctum')
                        ->getJson('/api/v1/profile');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'qr_code_url'
                    ],
                    'qr_code_url'
                ]);
    }
}