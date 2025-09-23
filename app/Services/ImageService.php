<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ImageService
{
    protected $imageManager;
    
    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }
    
    /**
     * Upload and process an asset image
     */
    public function uploadAssetImage(UploadedFile $file, $assetId, $assetName = null)
    {
        // Validate file
        $this->validateImageFile($file);
        
        // Generate unique filename
        $filename = $this->generateFilename($file, $assetId);
        
        // Create directory if it doesn't exist
        $directory = "assets/images/{$assetId}";
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        
        // Process and save image
        $image = $this->imageManager->read($file);
        
        // Resize image if too large (max 1920x1080)
        if ($image->width() > 1920 || $image->height() > 1080) {
            $image->scaleDown(1920, 1080);
        }
        
        // Optimize image quality and encode
        $encodedImage = $image->toJpeg(85);
        
        // Save original
        $originalPath = "{$directory}/{$filename}";
        Storage::disk('public')->put($originalPath, $encodedImage->toString());
        
        // Create thumbnail (300x300)
        $thumbnail = $this->imageManager->read($file);
        $thumbnail->scaleDown(300, 300);
        $encodedThumbnail = $thumbnail->toJpeg(85);
        
        $thumbnailFilename = 'thumb_' . $filename;
        $thumbnailPath = "{$directory}/{$thumbnailFilename}";
        Storage::disk('public')->put($thumbnailPath, $encodedThumbnail->toString());
        
        // Return image data
        return [
            'path' => $originalPath,
            'thumbnail_path' => $thumbnailPath,
            'alt' => $assetName ? "Image of {$assetName}" : "Asset Image",
            'size' => Storage::disk('public')->size($originalPath),
            'mime_type' => 'image/jpeg',
            'width' => $image->width(),
            'height' => $image->height()
        ];
    }
    
    /**
     * Delete asset image and thumbnail
     */
    public function deleteAssetImage($imagePath)
    {
        if (!$imagePath) {
            return false;
        }
        
        // Delete original image
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        
        // Delete thumbnail
        $thumbnailPath = $this->getThumbnailPath($imagePath);
        if (Storage::disk('public')->exists($thumbnailPath)) {
            Storage::disk('public')->delete($thumbnailPath);
        }
        
        return true;
    }
    
    /**
     * Get thumbnail path from original path
     */
    public function getThumbnailPath($originalPath)
    {
        $pathInfo = pathinfo($originalPath);
        return $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
    }
    
    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrl($originalPath)
    {
        $thumbnailPath = $this->getThumbnailPath($originalPath);
        
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return Storage::disk('public')->url($thumbnailPath);
        }
        
        // Fallback to original if thumbnail doesn't exist
        return Storage::disk('public')->url($originalPath);
    }
    
    /**
     * Validate image file
     */
    private function validateImageFile(UploadedFile $file)
    {
        // Check file type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
        }
        
        // Check file size (max 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new \InvalidArgumentException('File size too large. Maximum size is 10MB.');
        }
        
        // Check image dimensions
        $imageInfo = getimagesize($file->getPathname());
        if (!$imageInfo) {
            throw new \InvalidArgumentException('Invalid image file.');
        }
        
        $maxWidth = 4000;
        $maxHeight = 4000;
        
        if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
            throw new \InvalidArgumentException("Image dimensions too large. Maximum size is {$maxWidth}x{$maxHeight} pixels.");
        }
    }
    
    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file, $assetId)
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);
        
        return "asset_{$assetId}_{$timestamp}_{$random}.jpg";
    }
    
    /**
     * Get image dimensions
     */
    public function getImageDimensions($imagePath)
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return null;
        }
        
        $fullPath = Storage::disk('public')->path($imagePath);
        $imageInfo = getimagesize($fullPath);
        
        if (!$imageInfo) {
            return null;
        }
        
        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1]
        ];
    }
    
    /**
     * Create multiple image sizes
     */
    public function createImageSizes($imagePath, $assetId)
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return false;
        }
        
        $directory = dirname($imagePath);
        $filename = pathinfo($imagePath, PATHINFO_FILENAME);
        
        $sizes = [
            'small' => [150, 150],
            'medium' => [400, 400],
            'large' => [800, 800]
        ];
        
        $image = $this->imageManager->read(Storage::disk('public')->path($imagePath));
        
        foreach ($sizes as $sizeName => $dimensions) {
            $resized = $this->imageManager->read(Storage::disk('public')->path($imagePath));
            $resized->scaleDown($dimensions[0], $dimensions[1]);
            $encodedResized = $resized->toJpeg(85);
            
            $sizePath = "{$directory}/{$sizeName}_{$filename}.jpg";
            Storage::disk('public')->put($sizePath, $encodedResized->toString());
        }
        
        return true;
    }
}

