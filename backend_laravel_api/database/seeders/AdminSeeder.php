<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qrCodeService = new QrCodeService();
        
        // Create admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'full_name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Generate and assign QR code data if it doesn't exist
        if (empty($admin->qr_code_data)) {
            $qrCodeData = $qrCodeService->generateQrCodeData(
                $admin->id,
                $admin->email,
                $admin->username
            );
            $admin->update(['qr_code_data' => $qrCodeData]);
        }
    }
}