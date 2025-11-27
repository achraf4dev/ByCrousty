<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\QrCodeService;

$userId = isset($argv[1]) ? (int)$argv[1] : 1;

$user = User::find($userId);

if ($user) {
    $qrService = new QrCodeService();
    
    if (!$user->qr_code_data) {
        $qrData = $qrService->generateQrCodeData($user->id, $user->email, $user->name);
        $user->update(['qr_code_data' => $qrData]);
    }
    
    $qrImage = $qrService->generateQrCodeImage($user->qr_code_data);
    $filename = __DIR__ . "/../frontend_vue_js/public/user_{$userId}_qr.png";
    file_put_contents($filename, $qrImage);
    
    echo "✓ QR code generated for: {$user->full_name} (ID: {$user->id}, Role: {$user->role}, Points: {$user->points})\n";
    echo "✓ File saved to: frontend_vue_js/public/user_{$userId}_qr.png\n";
    echo "✓ Access it at: https://bycrousty.achraf.es/user_{$userId}_qr.png\n";
} else {
    echo "✗ User ID {$userId} not found\n";
}
