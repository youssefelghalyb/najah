<?php

namespace App\Services;

use App\Models\QrCode;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use ZipArchive;

class QrCodeService
{
    /**
     * Generate a single QR code
     */
    public function generate(array $data): QrCode
    {
        $qrCode = QrCode::create([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'foreground_color' => $data['foreground_color'] ?? '#000000',
            'background_color' => $data['background_color'] ?? '#ffffff',
            'style' => $data['style'] ?? 'square',
            'size' => $data['size'] ?? 300,
            'error_correction' => $data['error_correction'] ?? 'M',
            'expires_at' => $data['expires_at'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Handle logo upload if provided
        if (isset($data['logo']) && $data['logo']) {
            $logoPath = $data['logo']->store('qr-logos', 'public');
            $qrCode->update(['logo_path' => $logoPath]);
        }

        // Generate QR image
        $this->generateQrImage($qrCode);

        return $qrCode->fresh();
    }

    /**
     * Generate bulk QR codes
     */
    public function generateBulk(array $data): array
    {
        $count = $data['count'] ?? 1;
        $qrCodes = [];

        for ($i = 0; $i < $count; $i++) {
            $qrData = array_merge($data, [
                'title' => ($data['title'] ?? 'QR Code') . ' #' . ($i + 1),
            ]);

            unset($qrData['count']);

            $qrCodes[] = $this->generate($qrData);
        }

        return $qrCodes;
    }

    /**
     * Generate QR code image
     */
    public function generateQrImage(QrCode $qrCode): string
    {
        $url = $qrCode->full_url;

        // Map error correction level (using enums)
        $errorCorrectionMap = [
            'L' => ErrorCorrectionLevel::Low,
            'M' => ErrorCorrectionLevel::Medium,
            'Q' => ErrorCorrectionLevel::Quartile,
            'H' => ErrorCorrectionLevel::High,
        ];

        $errorCorrection = $errorCorrectionMap[$qrCode->error_correction] ?? ErrorCorrectionLevel::Medium;

        // Prepare logo path if exists
        $builderOptions = [
            'writer' => new PngWriter(),
            'writerOptions' => [],
            'validateResult' => false,
            'data' => $url,
            'encoding' => new Encoding('UTF-8'),
            'errorCorrectionLevel' => $errorCorrection,
            'size' => $qrCode->size,
            'margin' => 10,
            'roundBlockSizeMode' => RoundBlockSizeMode::Margin,
        ];

        // Add logo options ONLY if a logo exists
        if ($qrCode->logo_path && Storage::disk('public')->exists($qrCode->logo_path)) {
            $logoPath = Storage::disk('public')->path($qrCode->logo_path);

            $builderOptions['logoPath'] = $logoPath;
            $builderOptions['logoResizeToWidth'] = intval($qrCode->size * 0.2);
            $builderOptions['logoPunchoutBackground'] = true;
        }

        $builder = new Builder(...$builderOptions);


        // Generate result
        $result = $builder->build();

        // Save QR image
        $filename = "qr-codes/{$qrCode->uuid}.png";
        Storage::disk('public')->put($filename, $result->getString());

        // Update QR code with image path
        $qrCode->update(['qr_image_path' => $filename]);

        return $filename;
    }

    /**
     * Regenerate QR code image with new design
     */
    public function regenerate(QrCode $qrCode, array $data): QrCode
    {
        // Delete old image if exists
        if ($qrCode->qr_image_path) {
            Storage::disk('public')->delete($qrCode->qr_image_path);
        }

        // Delete old logo if new one is uploaded
        if (isset($data['logo']) && $data['logo'] && $qrCode->logo_path) {
            Storage::disk('public')->delete($qrCode->logo_path);
            $logoPath = $data['logo']->store('qr-logos', 'public');
            $qrCode->update(['logo_path' => $logoPath]);
        }

        // Update design settings
        $qrCode->update([
            'foreground_color' => $data['foreground_color'] ?? $qrCode->foreground_color,
            'background_color' => $data['background_color'] ?? $qrCode->background_color,
            'style' => $data['style'] ?? $qrCode->style,
            'size' => $data['size'] ?? $qrCode->size,
            'error_correction' => $data['error_correction'] ?? $qrCode->error_correction,
        ]);

        // Generate new image
        $this->generateQrImage($qrCode);

        return $qrCode->fresh();
    }

    /**
     * Export selected QR codes as ZIP
     */
    public function exportAsZip(array $qrCodeIds): string
    {
        $qrCodes = QrCode::whereIn('id', $qrCodeIds)->get();

        $zipFileName = 'qr-codes-' . now()->format('Y-m-d-His') . '.zip';
        $zipPath = storage_path('app/public/exports/' . $zipFileName);

        // Create exports directory if it doesn't exist
        if (!file_exists(storage_path('app/public/exports'))) {
            mkdir(storage_path('app/public/exports'), 0755, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($qrCodes as $qrCode) {
                if ($qrCode->qr_image_path && Storage::disk('public')->exists($qrCode->qr_image_path)) {
                    $filename = "QR-{$qrCode->code}-{$qrCode->uuid}.png";
                    $zip->addFile(
                        Storage::disk('public')->path($qrCode->qr_image_path),
                        $filename
                    );

                    // Add info text file
                    $info = "QR Code Information\n";
                    $info .= "==================\n";
                    $info .= "Title: " . ($qrCode->title ?? 'N/A') . "\n";
                    $info .= "Code: {$qrCode->code}\n";
                    $info .= "UUID: {$qrCode->uuid}\n";
                    $info .= "URL: {$qrCode->full_url}\n";
                    $info .= "Created: {$qrCode->created_at->format('Y-m-d H:i:s')}\n";
                    $info .= "Scans: {$qrCode->scan_count}\n";

                    $zip->addFromString("QR-{$qrCode->code}-info.txt", $info);
                }
            }
            $zip->close();
        }

        return 'exports/' . $zipFileName;
    }

    /**
     * Delete QR code and its files
     */
    public function delete(QrCode $qrCode): bool
    {
        // Delete QR image
        if ($qrCode->qr_image_path) {
            Storage::disk('public')->delete($qrCode->qr_image_path);
        }

        // Delete logo
        if ($qrCode->logo_path) {
            Storage::disk('public')->delete($qrCode->logo_path);
        }

        return $qrCode->delete();
    }

    /**
     * Bulk delete QR codes
     */
    public function bulkDelete(array $qrCodeIds): int
    {
        $qrCodes = QrCode::whereIn('id', $qrCodeIds)->get();

        foreach ($qrCodes as $qrCode) {
            $this->delete($qrCode);
        }

        return $qrCodes->count();
    }
}
