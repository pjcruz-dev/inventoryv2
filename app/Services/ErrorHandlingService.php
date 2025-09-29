<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;
use Throwable;

class ErrorHandlingService
{
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Log error with context
     */
    public static function logError(Throwable $exception, array $context = [], string $category = 'general')
    {
        $severity = self::determineSeverity($exception, $context);
        
        $errorData = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'severity' => $severity,
            'category' => $category,
            'context' => $context,
            'user_id' => auth()->id(),
            'url' => request()->fullUrl(),
            'timestamp' => Carbon::now()
        ];

        // Log based on severity
        switch ($severity) {
            case self::SEVERITY_CRITICAL:
                Log::critical("Critical error occurred", $errorData);
                break;
            case self::SEVERITY_HIGH:
                Log::error("High severity error occurred", $errorData);
                break;
            case self::SEVERITY_MEDIUM:
                Log::warning("Medium severity error occurred", $errorData);
                break;
            case self::SEVERITY_LOW:
                Log::info("Low severity error occurred", $errorData);
                break;
        }

        return $errorData;
    }

    /**
     * Determine error severity
     */
    private static function determineSeverity(Throwable $exception, array $context = []): string
    {
        $message = strtolower($exception->getMessage());
        
        if (str_contains($message, 'memory') || str_contains($message, 'fatal')) {
            return self::SEVERITY_CRITICAL;
        }
        
        if (str_contains($message, 'sql') || str_contains($message, 'database')) {
            return self::SEVERITY_HIGH;
        }
        
        if (str_contains($message, 'validation') || str_contains($message, 'file')) {
            return self::SEVERITY_MEDIUM;
        }

        return self::SEVERITY_LOW;
    }

    /**
     * Get error statistics
     */
    public static function getErrorStatistics($days = 7)
    {
        return Cache::remember("error_stats_{$days}", 300, function() use ($days) {
            // Mock data for now - would typically query error logs
            $statistics = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $statistics[] = [
                    'date' => Carbon::now()->subDays($i)->format('Y-m-d'),
                    'total_errors' => rand(0, 10),
                    'critical' => rand(0, 2),
                    'high' => rand(0, 3),
                    'medium' => rand(0, 5),
                    'low' => rand(0, 8)
                ];
            }
            return $statistics;
        });
    }
}