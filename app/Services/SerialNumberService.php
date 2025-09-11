<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Computer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SerialNumberService
{
    private const CACHE_PREFIX = 'serial_check_';
    private const CACHE_TTL = 300; // 5 minutes
    
    /**
     * Generate a unique serial number with specified format
     */
    public function generateSerialNumber(string $prefix = 'SN', int $length = 8, string $format = 'alphanumeric'): string
    {
        $maxAttempts = 100;
        $attempt = 0;
        
        do {
            $serialNumber = $this->createSerialNumber($prefix, $length, $format);
            $attempt++;
            
            if ($attempt >= $maxAttempts) {
                throw new \Exception('Unable to generate unique serial number after ' . $maxAttempts . ' attempts');
            }
            
        } while (!$this->isSerialNumberUnique($serialNumber));
        
        // Log serial number generation
        Log::info('Serial number generated', [
            'serial_number' => $serialNumber,
            'prefix' => $prefix,
            'format' => $format,
            'attempts' => $attempt,
            'user_id' => auth()->id()
        ]);
        
        return $serialNumber;
    }
    
    /**
     * Create serial number based on format
     */
    private function createSerialNumber(string $prefix, int $length, string $format): string
    {
        $suffix = '';
        $suffixLength = $length - strlen($prefix);
        
        switch ($format) {
            case 'numeric':
                $suffix = $this->generateNumericSuffix($suffixLength);
                break;
            case 'alphabetic':
                $suffix = $this->generateAlphabeticSuffix($suffixLength);
                break;
            case 'alphanumeric':
                $suffix = $this->generateAlphanumericSuffix($suffixLength);
                break;
            case 'sequential':
                $suffix = $this->generateSequentialSuffix($prefix, $suffixLength);
                break;
            case 'timestamp':
                $suffix = $this->generateTimestampSuffix($suffixLength);
                break;
            default:
                $suffix = $this->generateAlphanumericSuffix($suffixLength);
        }
        
        return strtoupper($prefix . $suffix);
    }
    
    /**
     * Generate numeric suffix
     */
    private function generateNumericSuffix(int $length): string
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return str_pad(rand($min, $max), $length, '0', STR_PAD_LEFT);
    }
    
    /**
     * Generate alphabetic suffix
     */
    private function generateAlphabeticSuffix(int $length): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $suffix = '';
        for ($i = 0; $i < $length; $i++) {
            $suffix .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $suffix;
    }
    
    /**
     * Generate alphanumeric suffix
     */
    private function generateAlphanumericSuffix(int $length): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $suffix = '';
        for ($i = 0; $i < $length; $i++) {
            $suffix .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $suffix;
    }
    
    /**
     * Generate sequential suffix based on existing records
     */
    private function generateSequentialSuffix(string $prefix, int $length): string
    {
        $lastSerial = Asset::where('serial_number', 'LIKE', $prefix . '%')
            ->orderBy('serial_number', 'desc')
            ->value('serial_number');
            
        if (!$lastSerial) {
            return str_pad('1', $length, '0', STR_PAD_LEFT);
        }
        
        $lastNumber = (int) substr($lastSerial, strlen($prefix));
        $nextNumber = $lastNumber + 1;
        
        return str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
    }
    
    /**
     * Generate timestamp-based suffix
     */
    private function generateTimestampSuffix(int $length): string
    {
        $timestamp = now()->format('YmdHis');
        if (strlen($timestamp) > $length) {
            return substr($timestamp, -$length);
        }
        return str_pad($timestamp, $length, '0', STR_PAD_LEFT);
    }
    
    /**
     * Check if serial number is unique across all asset tables
     */
    public function isSerialNumberUnique(string $serialNumber, ?int $excludeAssetId = null): bool
    {
        if (empty($serialNumber)) {
            return true; // Empty serial numbers are allowed
        }
        
        // Check cache first for performance
        $cacheKey = self::CACHE_PREFIX . md5($serialNumber . '_' . ($excludeAssetId ?? 'new'));
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $isUnique = $this->performUniquenessCheck($serialNumber, $excludeAssetId);
        
        // Cache the result
        Cache::put($cacheKey, $isUnique, self::CACHE_TTL);
        
        return $isUnique;
    }
    
    /**
     * Perform actual uniqueness check in database
     */
    private function performUniquenessCheck(string $serialNumber, ?int $excludeAssetId = null): bool
    {
        // Check main assets table
        $assetQuery = Asset::where('serial_number', $serialNumber);
        if ($excludeAssetId) {
            $assetQuery->where('id', '!=', $excludeAssetId);
        }
        
        if ($assetQuery->exists()) {
            return false;
        }
        
        // Check computers table if it exists separately
        if (DB::getSchemaBuilder()->hasTable('computers')) {
            $computerQuery = DB::table('computers')->where('serial_number', $serialNumber);
            if ($excludeAssetId) {
                $computerQuery->where('asset_id', '!=', $excludeAssetId);
            }
            
            if ($computerQuery->exists()) {
                return false;
            }
        }
        
        // Check other asset type tables as needed
        $assetTables = ['monitors', 'printers', 'peripherals'];
        foreach ($assetTables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $query = DB::table($table)->where('serial_number', $serialNumber);
                if ($excludeAssetId) {
                    $query->where('asset_id', '!=', $excludeAssetId);
                }
                
                if ($query->exists()) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Validate serial number format
     */
    public function validateSerialNumberFormat(string $serialNumber): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => []
        ];
        
        // Check length
        if (strlen($serialNumber) < 3) {
            $result['valid'] = false;
            $result['errors'][] = 'Serial number must be at least 3 characters long';
        }
        
        if (strlen($serialNumber) > 100) {
            $result['valid'] = false;
            $result['errors'][] = 'Serial number cannot exceed 100 characters';
        }
        
        // Check format - only alphanumeric, hyphens, and underscores
        if (!preg_match('/^[A-Z0-9\-_]+$/i', $serialNumber)) {
            $result['valid'] = false;
            $result['errors'][] = 'Serial number can only contain letters, numbers, hyphens, and underscores';
        }
        
        // Check for common patterns that might indicate errors
        if (preg_match('/^(test|sample|example|temp)/i', $serialNumber)) {
            $result['warnings'][] = 'Serial number appears to be a test/sample value';
        }
        
        if (preg_match('/^(0+|1+|a+)$/i', $serialNumber)) {
            $result['warnings'][] = 'Serial number appears to be a placeholder value';
        }
        
        return $result;
    }
    
    /**
     * Batch validate serial numbers for import
     */
    public function batchValidateSerialNumbers(array $serialNumbers, array $excludeAssetIds = []): array
    {
        $results = [
            'valid' => [],
            'duplicates_in_batch' => [],
            'duplicates_in_db' => [],
            'format_errors' => []
        ];
        
        $seenSerials = [];
        
        foreach ($serialNumbers as $index => $serialNumber) {
            if (empty($serialNumber)) {
                $results['valid'][$index] = true;
                continue;
            }
            
            // Check format
            $formatValidation = $this->validateSerialNumberFormat($serialNumber);
            if (!$formatValidation['valid']) {
                $results['format_errors'][$index] = $formatValidation['errors'];
                continue;
            }
            
            // Check for duplicates within the batch
            if (in_array($serialNumber, $seenSerials)) {
                $results['duplicates_in_batch'][$index] = $serialNumber;
                continue;
            }
            
            $seenSerials[] = $serialNumber;
            
            // Check for duplicates in database
            $excludeId = $excludeAssetIds[$index] ?? null;
            if (!$this->isSerialNumberUnique($serialNumber, $excludeId)) {
                $results['duplicates_in_db'][$index] = $serialNumber;
                continue;
            }
            
            $results['valid'][$index] = true;
        }
        
        return $results;
    }
    
    /**
     * Get serial number suggestions based on existing patterns
     */
    public function getSerialNumberSuggestions(string $prefix = '', int $count = 5): array
    {
        $suggestions = [];
        
        // If no prefix provided, analyze existing patterns
        if (empty($prefix)) {
            $commonPrefixes = $this->getCommonSerialPrefixes();
            $prefix = $commonPrefixes[0] ?? 'SN';
        }
        
        for ($i = 0; $i < $count; $i++) {
            try {
                $suggestion = $this->generateSerialNumber($prefix, 8, 'sequential');
                if (!in_array($suggestion, $suggestions)) {
                    $suggestions[] = $suggestion;
                }
            } catch (\Exception $e) {
                // If sequential fails, try alphanumeric
                try {
                    $suggestion = $this->generateSerialNumber($prefix, 8, 'alphanumeric');
                    if (!in_array($suggestion, $suggestions)) {
                        $suggestions[] = $suggestion;
                    }
                } catch (\Exception $e2) {
                    break; // Stop if we can't generate more
                }
            }
        }
        
        return $suggestions;
    }
    
    /**
     * Get common serial number prefixes from existing data
     */
    private function getCommonSerialPrefixes(int $limit = 5): array
    {
        $prefixes = Asset::whereNotNull('serial_number')
            ->where('serial_number', '!=', '')
            ->get()
            ->map(function ($asset) {
                // Extract prefix (letters at the beginning)
                preg_match('/^([A-Z]+)/i', $asset->serial_number, $matches);
                return $matches[1] ?? substr($asset->serial_number, 0, 2);
            })
            ->countBy()
            ->sortDesc()
            ->take($limit)
            ->keys()
            ->toArray();
            
        return array_filter($prefixes) ?: ['SN', 'AST', 'DEV'];
    }
    
    /**
     * Clear serial number cache
     */
    public function clearSerialNumberCache(?string $serialNumber = null): void
    {
        if ($serialNumber) {
            $cacheKey = self::CACHE_PREFIX . md5($serialNumber . '_new');
            Cache::forget($cacheKey);
        } else {
            // Clear all serial number cache entries
            $keys = Cache::getRedis()->keys(self::CACHE_PREFIX . '*');
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        }
    }
    
    /**
     * Get serial number statistics
     */
    public function getSerialNumberStatistics(): array
    {
        $totalAssets = Asset::count();
        $assetsWithSerial = Asset::whereNotNull('serial_number')
            ->where('serial_number', '!=', '')
            ->count();
            
        $duplicateSerials = Asset::select('serial_number')
            ->whereNotNull('serial_number')
            ->where('serial_number', '!=', '')
            ->groupBy('serial_number')
            ->havingRaw('COUNT(*) > 1')
            ->count();
            
        $commonPrefixes = $this->getCommonSerialPrefixes(10);
        
        return [
            'total_assets' => $totalAssets,
            'assets_with_serial' => $assetsWithSerial,
            'assets_without_serial' => $totalAssets - $assetsWithSerial,
            'coverage_percentage' => $totalAssets > 0 ? round(($assetsWithSerial / $totalAssets) * 100, 2) : 0,
            'duplicate_serials' => $duplicateSerials,
            'common_prefixes' => $commonPrefixes,
            'generated_at' => now()->toISOString()
        ];
    }
    
    /**
     * Find and report duplicate serial numbers
     */
    public function findDuplicateSerialNumbers(): array
    {
        $duplicates = Asset::select('serial_number', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as asset_ids'))
            ->whereNotNull('serial_number')
            ->where('serial_number', '!=', '')
            ->groupBy('serial_number')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->map(function ($item) {
                return [
                    'serial_number' => $item->serial_number,
                    'count' => $item->count,
                    'asset_ids' => explode(',', $item->asset_ids),
                    'assets' => Asset::whereIn('id', explode(',', $item->asset_ids))
                        ->select('id', 'asset_tag', 'name', 'created_at')
                        ->get()
                        ->toArray()
                ];
            })
            ->toArray();
            
        return $duplicates;
    }
    
    /**
     * Auto-fix duplicate serial numbers by generating new ones
     */
    public function fixDuplicateSerialNumbers(bool $dryRun = true): array
    {
        $duplicates = $this->findDuplicateSerialNumbers();
        $results = [
            'processed' => 0,
            'fixed' => 0,
            'errors' => [],
            'changes' => []
        ];
        
        foreach ($duplicates as $duplicate) {
            $assets = $duplicate['assets'];
            $originalSerial = $duplicate['serial_number'];
            
            // Keep the oldest asset with original serial, update others
            usort($assets, function ($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });
            
            for ($i = 1; $i < count($assets); $i++) {
                $asset = $assets[$i];
                $results['processed']++;
                
                try {
                    // Extract prefix from original serial
                    preg_match('/^([A-Z]+)/i', $originalSerial, $matches);
                    $prefix = $matches[1] ?? 'SN';
                    
                    $newSerial = $this->generateSerialNumber($prefix, 8, 'sequential');
                    
                    if (!$dryRun) {
                        Asset::where('id', $asset['id'])->update(['serial_number' => $newSerial]);
                        
                        Log::info('Fixed duplicate serial number', [
                            'asset_id' => $asset['id'],
                            'old_serial' => $originalSerial,
                            'new_serial' => $newSerial,
                            'user_id' => auth()->id()
                        ]);
                    }
                    
                    $results['fixed']++;
                    $results['changes'][] = [
                        'asset_id' => $asset['id'],
                        'asset_tag' => $asset['asset_tag'],
                        'old_serial' => $originalSerial,
                        'new_serial' => $newSerial
                    ];
                    
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'asset_id' => $asset['id'],
                        'error' => $e->getMessage()
                    ];
                }
            }
        }
        
        return $results;
    }
}