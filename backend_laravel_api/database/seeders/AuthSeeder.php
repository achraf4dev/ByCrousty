<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qrCodeService = new QrCodeService();
        
        // Simple seed: create an auth user and an admin user directly.
        $user = User::firstOrCreate(
            ['email' => 'user@user.com'],
            [
                'full_name' => 'Auth User',
                'username' => 'user',
                'role' => 'user',
                'password' => 'password',
            ]
        );

        // Generate and assign QR code data if it doesn't exist
        if (empty($user->qr_code_data)) {
            $qrCodeData = $qrCodeService->generateQrCodeData(
                $user->id,
                $user->email,
                $user->username
            );
            $user->update(['qr_code_data' => $qrCodeData]);
        }

        // create a personal access token for dev/testing convenience
        if ($user->tokens()->count() === 0) {
            $user->createToken('seed-token');
        }
    }
}
