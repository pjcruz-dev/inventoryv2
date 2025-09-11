<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ValidateFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $maxSize  Maximum file size in KB
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $maxSize = '10240')
    {
        // Convert KB to bytes
        $maxSizeBytes = (int)$maxSize * 1024;
        
        // Check if request has file uploads
        if (!$request->hasFile('csv_file') && !$request->hasFile('import_file')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded.',
                'error_code' => 'NO_FILE'
            ], 400);
        }
        
        // Get the uploaded file
        $file = $request->file('csv_file') ?? $request->file('import_file');
        
        if (!$file instanceof UploadedFile) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file upload.',
                'error_code' => 'INVALID_FILE'
            ], 400);
        }
        
        // Validate file size
        if ($file->getSize() > $maxSizeBytes) {
            Log::warning('File upload rejected: size too large', [
                'file_size' => $file->getSize(),
                'max_size' => $maxSizeBytes,
                'user_id' => auth()->id(),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'File size exceeds maximum allowed size of ' . $maxSize . 'KB.',
                'error_code' => 'FILE_TOO_LARGE',
                'details' => [
                    'file_size' => $this->formatFileSize($file->getSize()),
                    'max_size' => $this->formatFileSize($maxSizeBytes)
                ]
            ], 413);
        }
        
        // Validate file type
        $allowedMimes = ['text/csv', 'application/csv', 'text/plain'];
        $allowedExtensions = ['csv', 'xlsx', 'xls'];
        
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Check for Excel files
        if (in_array($extension, ['xlsx', 'xls'])) {
            $excelMimes = [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/excel',
                'application/x-excel',
                'application/x-msexcel'
            ];
            $allowedMimes = array_merge($allowedMimes, $excelMimes);
        }
        
        if (!in_array($mimeType, $allowedMimes) && !in_array($extension, $allowedExtensions)) {
            Log::warning('File upload rejected: invalid type', [
                'mime_type' => $mimeType,
                'extension' => $extension,
                'user_id' => auth()->id(),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid file type. Only CSV and Excel files are allowed.',
                'error_code' => 'INVALID_FILE_TYPE',
                'details' => [
                    'detected_type' => $mimeType,
                    'detected_extension' => $extension,
                    'allowed_types' => $allowedExtensions
                ]
            ], 415);
        }
        
        // Validate file content (basic checks)
        $validationResult = $this->validateFileContent($file);
        if (!$validationResult['valid']) {
            Log::warning('File upload rejected: content validation failed', [
                'reason' => $validationResult['reason'],
                'user_id' => auth()->id(),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $validationResult['message'],
                'error_code' => 'INVALID_CONTENT',
                'details' => $validationResult['details'] ?? []
            ], 422);
        }
        
        // Security scan for malicious content
        $securityResult = $this->performSecurityScan($file);
        if (!$securityResult['safe']) {
            Log::alert('File upload rejected: security scan failed', [
                'reason' => $securityResult['reason'],
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'file_name' => $file->getClientOriginalName()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'File failed security validation.',
                'error_code' => 'SECURITY_VIOLATION'
            ], 403);
        }
        
        // Add file metadata to request
        $request->merge([
            'file_metadata' => [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $mimeType,
                'extension' => $extension,
                'hash' => hash_file('sha256', $file->getPathname()),
                'uploaded_at' => now()->toISOString()
            ]
        ]);
        
        Log::info('File upload validated successfully', [
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'user_id' => auth()->id(),
            'ip' => $request->ip()
        ]);
        
        return $next($request);
    }
    
    /**
     * Validate file content for basic integrity
     */
    private function validateFileContent(UploadedFile $file): array
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            
            if ($extension === 'csv') {
                return $this->validateCsvContent($file);
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                return $this->validateExcelContent($file);
            }
            
            return ['valid' => true];
            
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'reason' => 'content_read_error',
                'message' => 'Unable to read file content.',
                'details' => ['error' => $e->getMessage()]
            ];
        }
    }
    
    /**
     * Validate CSV file content
     */
    private function validateCsvContent(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        
        if (!$handle) {
            return [
                'valid' => false,
                'reason' => 'file_read_error',
                'message' => 'Unable to read CSV file.'
            ];
        }
        
        // Check if file is empty
        if (feof($handle)) {
            fclose($handle);
            return [
                'valid' => false,
                'reason' => 'empty_file',
                'message' => 'CSV file is empty.'
            ];
        }
        
        // Read first line to check for headers
        $firstLine = fgetcsv($handle);
        fclose($handle);
        
        if (!$firstLine || empty($firstLine)) {
            return [
                'valid' => false,
                'reason' => 'no_headers',
                'message' => 'CSV file must contain headers in the first row.'
            ];
        }
        
        // Check for minimum number of columns
        if (count($firstLine) < 2) {
            return [
                'valid' => false,
                'reason' => 'insufficient_columns',
                'message' => 'CSV file must contain at least 2 columns.',
                'details' => ['column_count' => count($firstLine)]
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Validate Excel file content
     */
    private function validateExcelContent(UploadedFile $file): array
    {
        // Basic validation - check if file can be opened
        try {
            $fileHandle = fopen($file->getPathname(), 'rb');
            if (!$fileHandle) {
                return [
                    'valid' => false,
                    'reason' => 'file_read_error',
                    'message' => 'Unable to read Excel file.'
                ];
            }
            
            // Read first few bytes to validate Excel signature
            $signature = fread($fileHandle, 8);
            fclose($fileHandle);
            
            // Check for Excel file signatures
            $validSignatures = [
                '\x50\x4B\x03\x04', // XLSX (ZIP-based)
                '\xD0\xCF\x11\xE0', // XLS (OLE2-based)
                '\x09\x08\x06\x00', // XLS alternative
            ];
            
            $isValidExcel = false;
            foreach ($validSignatures as $validSig) {
                if (strpos($signature, $validSig) === 0) {
                    $isValidExcel = true;
                    break;
                }
            }
            
            if (!$isValidExcel) {
                return [
                    'valid' => false,
                    'reason' => 'invalid_excel_format',
                    'message' => 'File does not appear to be a valid Excel file.'
                ];
            }
            
            return ['valid' => true];
            
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'reason' => 'excel_validation_error',
                'message' => 'Error validating Excel file.',
                'details' => ['error' => $e->getMessage()]
            ];
        }
    }
    
    /**
     * Perform security scan on uploaded file
     */
    private function performSecurityScan(UploadedFile $file): array
    {
        try {
            // Check file size against system limits
            $maxSystemSize = ini_get('upload_max_filesize');
            if ($file->getSize() > $this->parseSize($maxSystemSize)) {
                return [
                    'safe' => false,
                    'reason' => 'exceeds_system_limit'
                ];
            }
            
            // Scan for suspicious patterns in filename
            $filename = $file->getClientOriginalName();
            $suspiciousPatterns = [
                '/\.\./', // Directory traversal
                '/[<>:"|?*]/', // Invalid filename characters
                '/\.(php|exe|bat|cmd|sh|py|pl|rb)$/i', // Executable extensions
                '/^(con|prn|aux|nul|com[1-9]|lpt[1-9])$/i' // Reserved Windows names
            ];
            
            foreach ($suspiciousPatterns as $pattern) {
                if (preg_match($pattern, $filename)) {
                    return [
                        'safe' => false,
                        'reason' => 'suspicious_filename'
                    ];
                }
            }
            
            // Basic content scan for CSV files
            if (strtolower($file->getClientOriginalExtension()) === 'csv') {
                $content = file_get_contents($file->getPathname());
                
                // Check for suspicious content patterns
                $suspiciousContent = [
                    '/<script/i',
                    '/javascript:/i',
                    '/vbscript:/i',
                    '/on\w+\s*=/i', // Event handlers
                    '/\x00/', // Null bytes
                ];
                
                foreach ($suspiciousContent as $pattern) {
                    if (preg_match($pattern, $content)) {
                        return [
                            'safe' => false,
                            'reason' => 'suspicious_content'
                        ];
                    }
                }
            }
            
            return ['safe' => true];
            
        } catch (\Exception $e) {
            Log::error('Security scan error', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            
            // Fail safe - reject file if scan fails
            return [
                'safe' => false,
                'reason' => 'scan_error'
            ];
        }
    }
    
    /**
     * Format file size for human reading
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Parse size string to bytes
     */
    private function parseSize(string $size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        
        return (int)$size;
    }
}