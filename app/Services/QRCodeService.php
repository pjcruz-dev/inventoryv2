<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\Storage;

// Check if QR Code package is available
if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
    class_alias('SimpleSoftwareIO\QrCode\Facades\QrCode', 'QrCode');
}

class QRCodeService
{
    /**
     * Generate QR code for an asset
     */
    public function generateAssetQRCode(Asset $asset, $size = 200)
    {
        if (!class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            throw new \Exception('QR Code package not installed. Please run: composer require simplesoftwareio/simple-qrcode');
        }
        
        $qrData = $this->getAssetQRData($asset);
        
        // Generate QR code as SVG
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size($size)
            ->margin(1)
            ->generate($qrData);
            
        return $qrCode;
    }
    
    /**
     * Generate QR code and save to storage
     */
    public function generateAndSaveAssetQRCode(Asset $asset, $size = 200)
    {
        $qrCode = $this->generateAssetQRCode($asset, $size);
        
        // Create directory if it doesn't exist
        $directory = 'qrcodes/assets';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        
        // Save QR code
        $filename = "asset_{$asset->id}_qr.svg";
        $path = "{$directory}/{$filename}";
        
        Storage::put($path, $qrCode);
        
        return $path;
    }
    
    /**
     * Get QR code data for an asset
     */
    public function getAssetQRData(Asset $asset)
    {
        $baseUrl = config('app.url');
        
        return json_encode([
            'type' => 'asset',
            'id' => $asset->id,
            'asset_tag' => $asset->asset_tag,
            'name' => $asset->name,
            'url' => "{$baseUrl}/assets/{$asset->id}",
            'serial_number' => $asset->serial_number,
            'status' => $asset->status,
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Generate QR code for asset labels
     */
    public function generateAssetLabelQRCode(Asset $asset, $size = 150)
    {
        if (!class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            throw new \Exception('QR Code package not installed. Please run: composer require simplesoftwareio/simple-qrcode');
        }
        
        $qrData = $this->getAssetQRData($asset);
        
        // Generate QR code optimized for printing
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size($size)
            ->margin(0.5)
            ->errorCorrection('M')
            ->generate($qrData);
            
        return $qrCode;
    }
    
    /**
     * Generate bulk QR codes for multiple assets
     */
    public function generateBulkAssetQRCodes($assetIds, $size = 200)
    {
        $assets = Asset::whereIn('id', $assetIds)->get();
        $qrCodes = [];
        
        foreach ($assets as $asset) {
            $qrCodes[] = [
                'asset' => $asset,
                'qr_code' => $this->generateAssetQRCode($asset, $size),
                'qr_data' => $this->getAssetQRData($asset)
            ];
        }
        
        return $qrCodes;
    }
    
    /**
     * Parse QR code data
     */
    public function parseQRCodeData($qrData)
    {
        try {
            $data = json_decode($qrData, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }
            
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Validate QR code data
     */
    public function validateQRCodeData($data)
    {
        return isset($data['type']) && 
               $data['type'] === 'asset' && 
               isset($data['id']) && 
               isset($data['asset_tag']);
    }
}
