<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuditService
{
    /**
     * Log user actions for audit trail
     */
    public static function logAction($action, $model = null, $modelId = null, $details = [], $userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        $auditData = [
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $model ? class_basename($model) : null,
            'model_id' => $modelId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
            'timestamp' => now()->toISOString()
        ];
        
        // Log to database
        \App\Models\AuditLog::create($auditData);
        
        // Also log to file for backup
        Log::channel('audit')->info('Audit Action', $auditData);
    }
    
    /**
     * Log data changes (before and after)
     */
    public static function logDataChange($model, $action, $oldData = null, $newData = null)
    {
        $changes = [];
        
        if ($oldData && $newData) {
            foreach ($newData as $key => $value) {
                if (!isset($oldData[$key]) || $oldData[$key] != $value) {
                    $changes[$key] = [
                        'old' => $oldData[$key] ?? null,
                        'new' => $value
                    ];
                }
            }
        }
        
        self::logAction($action, $model, $model->id ?? null, [
            'changes' => $changes,
            'old_data' => $oldData,
            'new_data' => $newData
        ]);
    }
    
    /**
     * Log authentication events
     */
    public static function logAuthEvent($event, $details = [])
    {
        self::logAction('auth_' . $event, null, null, $details);
    }
    
    /**
     * Log file operations
     */
    public static function logFileOperation($operation, $filename, $details = [])
    {
        self::logAction('file_' . $operation, null, null, array_merge([
            'filename' => $filename,
            'file_size' => $details['file_size'] ?? null,
            'file_type' => $details['file_type'] ?? null
        ], $details));
    }
    
    /**
     * Log system events
     */
    public static function logSystemEvent($event, $details = [])
    {
        self::logAction('system_' . $event, null, null, $details);
    }
    
    /**
     * Log permission changes
     */
    public static function logPermissionChange($userId, $permissions, $action = 'updated')
    {
        self::logAction('permission_' . $action, 'User', $userId, [
            'permissions' => $permissions
        ]);
    }
    
    /**
     * Log data export/import
     */
    public static function logDataTransfer($type, $module, $recordCount = null, $details = [])
    {
        self::logAction('data_' . $type, $module, null, array_merge([
            'record_count' => $recordCount
        ], $details));
    }
    
    /**
     * Get audit trail for a specific model
     */
    public static function getAuditTrail($modelType, $modelId)
    {
        return \App\Models\AuditLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Get user activity summary
     */
    public static function getUserActivity($userId, $days = 30)
    {
        return \App\Models\AuditLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Get security events
     */
    public static function getSecurityEvents($days = 7)
    {
        return \App\Models\AuditLog::whereIn('action', [
            'auth_login', 'auth_logout', 'auth_failed_login',
            'rate_limit_exceeded', 'suspicious_activity'
        ])
        ->where('created_at', '>=', now()->subDays($days))
        ->orderBy('created_at', 'desc')
        ->get();
    }
}
