<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
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
     * @param string $data (base64 encoded)
     * @return string (PNG image content)
     */
    public function generateQrCodeImage($data)
    {
        // Use the base64 data directly (don't decode it)
        // The scanner will read the base64 string, which the API expects
        // Use zero margin and no round-block margin mode so the generated
        // PNG has minimal/zero white padding around the QR modules.
        // Also set a transparent background so clients can render it over any
        // page background. Alpha uses 0-127 (127 = fully transparent).
        $qrCode = new QrCode(
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 0,
            roundBlockSizeMode: RoundBlockSizeMode::None,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255, 127)
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