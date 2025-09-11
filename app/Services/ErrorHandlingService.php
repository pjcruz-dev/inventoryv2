<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ErrorHandlingService
{
    private const ERROR_TYPES = [
        'validation' => 'Validation Error',
        'duplicate' => 'Duplicate Entry',
        'format' => 'Format Error',
        'required' => 'Required Field Missing',
        'relationship' => 'Related Data Not Found',
        'system' => 'System Error',
        'permission' => 'Permission Denied',
        'file' => 'File Error'
    ];
    
    private const SEVERITY_LEVELS = [
        'critical' => 1,
        'error' => 2,
        'warning' => 3,
        'info' => 4
    ];
    
    private array $errors = [];
    private array $warnings = [];
    private array $fieldErrors = [];
    private array $rowErrors = [];
    private array $systemErrors = [];
    
    /**
     * Add a validation error for a specific field
     */
    public function addFieldError(string $field, string $message, string $type = 'validation', ?int $row = null): void
    {
        $error = [
            'field' => $field,
            'message' => $this->formatErrorMessage($message, $type),
            'type' => $type,
            'severity' => $this->getErrorSeverity($type),
            'row' => $row,
            'timestamp' => now()->toISOString(),
            'suggestions' => $this->getErrorSuggestions($field, $type, $message)
        ];
        
        if ($row !== null) {
            $this->rowErrors[$row][$field] = $error;
        } else {
            $this->fieldErrors[$field][] = $error;
        }
        
        $this->errors[] = $error;
        
        $this->logError($error);
    }
    
    /**
     * Add a row-level error
     */
    public function addRowError(int $row, string $message, string $type = 'validation', ?string $field = null): void
    {
        $error = [
            'row' => $row,
            'field' => $field,
            'message' => $this->formatErrorMessage($message, $type),
            'type' => $type,
            'severity' => $this->getErrorSeverity($type),
            'timestamp' => now()->toISOString(),
            'suggestions' => $this->getRowErrorSuggestions($row, $type, $message)
        ];
        
        $this->rowErrors[$row]['_general'][] = $error;
        $this->errors[] = $error;
        
        $this->logError($error);
    }
    
    /**
     * Add a system-level error
     */
    public function addSystemError(string $message, string $type = 'system', array $context = []): void
    {
        $error = [
            'message' => $this->formatErrorMessage($message, $type),
            'type' => $type,
            'severity' => $this->getErrorSeverity($type),
            'context' => $context,
            'timestamp' => now()->toISOString(),
            'suggestions' => $this->getSystemErrorSuggestions($type, $message)
        ];
        
        $this->systemErrors[] = $error;
        $this->errors[] = $error;
        
        $this->logError($error, $context);
    }
    
    /**
     * Add a warning
     */
    public function addWarning(string $message, ?string $field = null, ?int $row = null): void
    {
        $warning = [
            'message' => $message,
            'field' => $field,
            'row' => $row,
            'timestamp' => now()->toISOString(),
            'suggestions' => $this->getWarningSuggestions($field, $message)
        ];
        
        $this->warnings[] = $warning;
        
        Log::warning('Import/Export Warning', $warning);
    }
    
    /**
     * Format error message with context
     */
    private function formatErrorMessage(string $message, string $type): string
    {
        $prefix = self::ERROR_TYPES[$type] ?? 'Error';
        
        // Add helpful context based on error type
        switch ($type) {
            case 'required':
                return "Required field missing: {$message}. Please ensure this field has a value.";
            case 'duplicate':
                return "Duplicate entry detected: {$message}. Please use a unique value.";
            case 'format':
                return "Invalid format: {$message}. Please check the expected format.";
            case 'relationship':
                return "Related data not found: {$message}. Please verify the referenced data exists.";
            case 'permission':
                return "Access denied: {$message}. Please check your permissions.";
            case 'file':
                return "File error: {$message}. Please check the file and try again.";
            default:
                return $message;
        }
    }
    
    /**
     * Get error severity level
     */
    private function getErrorSeverity(string $type): string
    {
        switch ($type) {
            case 'system':
            case 'permission':
            case 'file':
                return 'critical';
            case 'required':
            case 'duplicate':
                return 'error';
            case 'format':
            case 'relationship':
                return 'warning';
            default:
                return 'info';
        }
    }
    
    /**
     * Get error suggestions based on field and type
     */
    private function getErrorSuggestions(string $field, string $type, string $message): array
    {
        $suggestions = [];
        
        switch ($type) {
            case 'required':
                $suggestions[] = "Ensure the '{$field}' column has a value";
                $suggestions[] = "Check if the field name matches the template exactly";
                break;
                
            case 'duplicate':
                if (str_contains($field, 'serial')) {
                    $suggestions[] = "Use the serial number generator to create a unique value";
                    $suggestions[] = "Check existing records to avoid duplicates";
                } elseif (str_contains($field, 'email')) {
                    $suggestions[] = "Verify the email address is not already registered";
                    $suggestions[] = "Use a different email address";
                } else {
                    $suggestions[] = "Modify the value to make it unique";
                    $suggestions[] = "Check existing records for similar entries";
                }
                break;
                
            case 'format':
                if (str_contains($field, 'email')) {
                    $suggestions[] = "Use a valid email format (e.g., user@example.com)";
                } elseif (str_contains($field, 'date')) {
                    $suggestions[] = "Use the format YYYY-MM-DD (e.g., 2024-01-15)";
                } elseif (str_contains($field, 'phone')) {
                    $suggestions[] = "Use a valid phone number format";
                } else {
                    $suggestions[] = "Check the expected format in the template";
                    $suggestions[] = "Refer to the validation rules for this field";
                }
                break;
                
            case 'relationship':
                $suggestions[] = "Verify the referenced data exists in the system";
                $suggestions[] = "Check spelling and case sensitivity";
                $suggestions[] = "Import related data first if needed";
                break;
        }
        
        return $suggestions;
    }
    
    /**
     * Get row-specific error suggestions
     */
    private function getRowErrorSuggestions(int $row, string $type, string $message): array
    {
        $suggestions = [];
        
        $suggestions[] = "Check row {$row} in your import file";
        
        switch ($type) {
            case 'validation':
                $suggestions[] = "Review all fields in this row for accuracy";
                $suggestions[] = "Ensure all required fields are filled";
                break;
            case 'duplicate':
                $suggestions[] = "This row contains duplicate data";
                $suggestions[] = "Remove or modify duplicate entries";
                break;
            case 'format':
                $suggestions[] = "Check data formats in this row";
                $suggestions[] = "Ensure dates, emails, and numbers are properly formatted";
                break;
        }
        
        return $suggestions;
    }
    
    /**
     * Get system error suggestions
     */
    private function getSystemErrorSuggestions(string $type, string $message): array
    {
        $suggestions = [];
        
        switch ($type) {
            case 'system':
                $suggestions[] = "Try the operation again";
                $suggestions[] = "Contact system administrator if the problem persists";
                break;
            case 'permission':
                $suggestions[] = "Contact your administrator for proper permissions";
                $suggestions[] = "Ensure you have the required role for this operation";
                break;
            case 'file':
                $suggestions[] = "Check that the file is not corrupted";
                $suggestions[] = "Ensure the file format is supported (CSV, Excel)";
                $suggestions[] = "Try uploading a smaller file";
                break;
        }
        
        return $suggestions;
    }
    
    /**
     * Get warning suggestions
     */
    private function getWarningSuggestions(?string $field, string $message): array
    {
        $suggestions = [];
        
        if ($field) {
            $suggestions[] = "Review the '{$field}' field for potential issues";
        }
        
        $suggestions[] = "This is a warning and won't prevent import";
        $suggestions[] = "Consider reviewing and correcting if needed";
        
        return $suggestions;
    }
    
    /**
     * Log error with appropriate level
     */
    private function logError(array $error, array $context = []): void
    {
        $logContext = array_merge([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ], $context, $error);
        
        switch ($error['severity']) {
            case 'critical':
                Log::critical('Import/Export Critical Error', $logContext);
                break;
            case 'error':
                Log::error('Import/Export Error', $logContext);
                break;
            case 'warning':
                Log::warning('Import/Export Warning', $logContext);
                break;
            default:
                Log::info('Import/Export Info', $logContext);
        }
    }
    
    /**
     * Get all errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get errors by severity
     */
    public function getErrorsBySeverity(string $severity): array
    {
        return array_filter($this->errors, function ($error) use ($severity) {
            return $error['severity'] === $severity;
        });
    }
    
    /**
     * Get field-specific errors
     */
    public function getFieldErrors(?string $field = null): array
    {
        if ($field) {
            return $this->fieldErrors[$field] ?? [];
        }
        
        return $this->fieldErrors;
    }
    
    /**
     * Get row-specific errors
     */
    public function getRowErrors(?int $row = null): array
    {
        if ($row !== null) {
            return $this->rowErrors[$row] ?? [];
        }
        
        return $this->rowErrors;
    }
    
    /**
     * Get system errors
     */
    public function getSystemErrors(): array
    {
        return $this->systemErrors;
    }
    
    /**
     * Get all warnings
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }
    
    /**
     * Check if there are any errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Check if there are critical errors
     */
    public function hasCriticalErrors(): bool
    {
        return !empty($this->getErrorsBySeverity('critical'));
    }
    
    /**
     * Get error count by type
     */
    public function getErrorCount(string $type = null): int
    {
        if ($type) {
            return count(array_filter($this->errors, function ($error) use ($type) {
                return $error['type'] === $type;
            }));
        }
        
        return count($this->errors);
    }
    
    /**
     * Get formatted error summary
     */
    public function getErrorSummary(): array
    {
        $summary = [
            'total_errors' => count($this->errors),
            'total_warnings' => count($this->warnings),
            'by_severity' => [],
            'by_type' => [],
            'affected_rows' => count($this->rowErrors),
            'affected_fields' => count($this->fieldErrors),
            'has_critical' => $this->hasCriticalErrors(),
            'can_proceed' => !$this->hasCriticalErrors()
        ];
        
        // Count by severity
        foreach (array_keys(self::SEVERITY_LEVELS) as $severity) {
            $summary['by_severity'][$severity] = count($this->getErrorsBySeverity($severity));
        }
        
        // Count by type
        foreach (array_keys(self::ERROR_TYPES) as $type) {
            $summary['by_type'][$type] = $this->getErrorCount($type);
        }
        
        return $summary;
    }
    
    /**
     * Generate user-friendly error report
     */
    public function generateErrorReport(): array
    {
        $report = [
            'summary' => $this->getErrorSummary(),
            'errors' => [],
            'warnings' => $this->warnings,
            'recommendations' => $this->generateRecommendations()
        ];
        
        // Group errors for better presentation
        foreach ($this->errors as $error) {
            $key = $error['type'] . '_' . $error['severity'];
            $report['errors'][$key][] = $error;
        }
        
        return $report;
    }
    
    /**
     * Generate recommendations based on errors
     */
    private function generateRecommendations(): array
    {
        $recommendations = [];
        
        if ($this->hasCriticalErrors()) {
            $recommendations[] = [
                'type' => 'critical',
                'message' => 'Critical errors detected. Please resolve these before proceeding.',
                'action' => 'Fix critical errors and try again'
            ];
        }
        
        $duplicateCount = $this->getErrorCount('duplicate');
        if ($duplicateCount > 0) {
            $recommendations[] = [
                'type' => 'duplicate',
                'message' => "Found {$duplicateCount} duplicate entries.",
                'action' => 'Use the duplicate resolution tool or modify duplicate values'
            ];
        }
        
        $formatCount = $this->getErrorCount('format');
        if ($formatCount > 0) {
            $recommendations[] = [
                'type' => 'format',
                'message' => "Found {$formatCount} format errors.",
                'action' => 'Download the latest template and check field formats'
            ];
        }
        
        if (count($this->warnings) > 0) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => count($this->warnings) . ' warnings detected.',
                'action' => 'Review warnings - import can proceed but data should be verified'
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Clear all errors and warnings
     */
    public function clear(): void
    {
        $this->errors = [];
        $this->warnings = [];
        $this->fieldErrors = [];
        $this->rowErrors = [];
        $this->systemErrors = [];
    }
    
    /**
     * Export errors to CSV for download
     */
    public function exportErrorsToCSV(): string
    {
        $csvData = [];
        $csvData[] = ['Row', 'Field', 'Error Type', 'Severity', 'Message', 'Suggestions'];
        
        foreach ($this->errors as $error) {
            $csvData[] = [
                $error['row'] ?? 'N/A',
                $error['field'] ?? 'N/A',
                $error['type'],
                $error['severity'],
                $error['message'],
                implode('; ', $error['suggestions'])
            ];
        }
        
        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
    
    /**
     * Store errors in session for display
     */
    public function storeInSession(string $key = 'import_errors'): void
    {
        Session::put($key, [
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'summary' => $this->getErrorSummary(),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Load errors from session
     */
    public function loadFromSession(string $key = 'import_errors'): bool
    {
        $sessionData = Session::get($key);
        
        if (!$sessionData) {
            return false;
        }
        
        $this->errors = $sessionData['errors'] ?? [];
        $this->warnings = $sessionData['warnings'] ?? [];
        
        // Rebuild organized arrays
        foreach ($this->errors as $error) {
            if (isset($error['field']) && isset($error['row'])) {
                $this->rowErrors[$error['row']][$error['field']] = $error;
            } elseif (isset($error['field'])) {
                $this->fieldErrors[$error['field']][] = $error;
            } else {
                $this->systemErrors[] = $error;
            }
        }
        
        return true;
    }
}