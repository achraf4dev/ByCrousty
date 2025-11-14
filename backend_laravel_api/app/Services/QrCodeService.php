<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate a unique QR code data for a user.
     *
     * @param int $userId
     * @param string $email
     * @param string $username
     * @return string
     */
    public function generateQrCodeData($userId, $email, $username)
    {
        // Create a unique identifier that includes user info and a random component
        $uniqueId = Str::uuid()->toString();
        
        // Create QR code data - you can customize this format based on your needs
        $qrData = json_encode([
            'user_id' => $userId,
            'email' => $email,
            'username' => $username,
            'unique_id' => $uniqueId,
            'generated_at' => now()->toISOString(),
            'app' => config('app.name', 'ByCrousty')
        ]);

        return base64_encode($qrData);
    }

    /**
     * Generate QR code image from data.
     *
     * @param string $data
     * @return string (PNG image content)
     */
    public function generateQrCodeImage($data)
    {
        // Decode the base64 data
        $decodedData = base64_decode($data);
        
        $qrCode = new QrCode(
            data: $decodedData,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return $result->getString();
    }

    /**
     * Decode QR code data to get user information.
     *
     * @param string $qrCodeData
     * @return array|null
     */
    public function decodeQrCodeData($qrCodeData)
    {
        try {
            $decodedData = base64_decode($qrCodeData);
            return json_decode($decodedData, true);
        } catch (\Exception $e) {
            return null;
        }
    }
}