<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\DeviceDetector;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    protected $deviceDetector;
    protected $request;

    public function __construct()
    {
        $this->deviceDetector = new DeviceDetector();
        $this->request = request();
    }

    /**
     * Log a comprehensive activity with full context
     *
     * @param Model|null $model
     * @param string $eventType
     * @param string $description
     * @param array $oldValues
     * @param array $newValues
     * @param array $additionalData
     * @return Log|null
     */
    public function logActivity(
        ?Model $model,
        string $eventType,
        string $description,
        array $oldValues = [],
        array $newValues = [],
        array $additionalData = []
    ): ?Log {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        $category = $model ? strtolower(class_basename($model)) : 'system';
        
        // Analyze changes
        $affectedFields = $this->analyzeChanges($oldValues, $newValues);
        
        // Prepare action details
        $actionDetails = array_merge([
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null,
            'action_performed' => $eventType,
            'total_fields_changed' => count($affectedFields),
            'user_initiated' => true,
            'timestamp' => Carbon::now()->toISOString(),
        ], $additionalData);

        // Get comprehensive metadata
        $metadata = $this->getRequestMetadata();

        // Determine asset and department context
        [$assetId, $departmentId] = $this->getAssetContext($model);

        // Filter sensitive request parameters
        $requestParameters = $this->filterSensitiveData($this->request->all());

        return Log::create([
            'loggable_type' => $model ? get_class($model) : null,
            'loggable_id' => $model ? $model->getKey() : null,
            'category' => $category,
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => $user->id,
            'role_id' => $user->role_id ?? null,
            'asset_id' => $assetId,
            'department_id' => $departmentId,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'remarks' => $description,
            // Enhanced fields
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'action_details' => $actionDetails,
            'affected_fields' => $affectedFields,
            'metadata' => $metadata,
            'session_id' => $this->request->session()->getId(),
            'request_method' => $this->request->method(),
            'request_url' => $this->request->fullUrl(),
            'request_parameters' => $requestParameters,
            'browser_name' => $this->deviceDetector->browser(),
            'operating_system' => $this->deviceDetector->platform(),
            'action_timestamp' => Carbon::now(),
        ]);
    }

    /**
     * Log user authentication events
     *
     * @param string $eventType (login, logout, failed_login, etc.)
     * @param array $additionalData
     * @return Log|null
     */
    public function logAuthEvent(string $eventType, array $additionalData = []): ?Log
    {
        $user = Auth::user();
        $description = $this->getAuthDescription($eventType);
        
        $actionDetails = array_merge([
            'action_performed' => $eventType,
            'auth_event' => true,
            'user_initiated' => true,
            'timestamp' => Carbon::now()->toISOString(),
        ], $additionalData);

        return Log::create([
            'category' => 'authentication',
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => $user ? $user->id : null,
            'role_id' => $user ? $user->role_id : null,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'remarks' => $description,
            'action_details' => $actionDetails,
            'metadata' => $this->getRequestMetadata(),
            'session_id' => $this->request->session()->getId(),
            'request_method' => $this->request->method(),
            'request_url' => $this->request->fullUrl(),
            'request_parameters' => $this->filterSensitiveData($this->request->all()),
            'browser_name' => $this->deviceDetector->browser(),
            'operating_system' => $this->deviceDetector->platform(),
            'action_timestamp' => Carbon::now(),
        ]);
    }

    /**
     * Log system events (imports, exports, maintenance, etc.)
     *
     * @param string $eventType
     * @param string $description
     * @param array $additionalData
     * @return Log|null
     */
    public function logSystemEvent(string $eventType, string $description, array $additionalData = []): ?Log
    {
        $user = Auth::user();
        
        $actionDetails = array_merge([
            'action_performed' => $eventType,
            'system_event' => true,
            'user_initiated' => $user !== null,
            'timestamp' => Carbon::now()->toISOString(),
        ], $additionalData);

        return Log::create([
            'category' => 'system',
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => $user ? $user->id : null,
            'role_id' => $user ? $user->role_id : null,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'remarks' => $description,
            'action_details' => $actionDetails,
            'metadata' => $this->getRequestMetadata(),
            'session_id' => $this->request->session() ? $this->request->session()->getId() : null,
            'request_method' => $this->request->method(),
            'request_url' => $this->request->fullUrl(),
            'request_parameters' => $this->filterSensitiveData($this->request->all()),
            'browser_name' => $this->deviceDetector->browser(),
            'operating_system' => $this->deviceDetector->platform(),
            'action_timestamp' => Carbon::now(),
        ]);
    }

    /**
     * Analyze changes between old and new values
     *
     * @param array $oldValues
     * @param array $newValues
     * @return array
     */
    protected function analyzeChanges(array $oldValues, array $newValues): array
    {
        $changes = [];
        $allFields = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allFields as $field) {
            $oldValue = $oldValues[$field] ?? null;
            $newValue = $newValues[$field] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$field] = [
                    'field_name' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'data_type' => gettype($newValue),
                    'changed_at' => Carbon::now()->toISOString(),
                    'is_sensitive' => $this->isSensitiveField($field),
                    'change_type' => $this->getChangeType($oldValue, $newValue),
                ];

                // Add JSON diff for JSON fields
                if ($this->isJsonData($oldValue) || $this->isJsonData($newValue)) {
                    $changes[$field]['is_json'] = true;
                    $changes[$field]['json_diff'] = $this->getJsonDiff($oldValue, $newValue);
                }
            }
        }

        return $changes;
    }

    /**
     * Get comprehensive request metadata
     *
     * @return array
     */
    protected function getRequestMetadata(): array
    {
        return [
            'request_id' => $this->request->header('X-Request-ID', uniqid()),
            'referrer' => $this->request->header('referer'),
            'user_agent_full' => $this->request->userAgent(),
            'device_type' => $this->deviceDetector->deviceType(),
            'platform' => $this->deviceDetector->platform(),
            'platform_version' => 'Unknown',
            'browser_version' => 'Unknown',
            'is_mobile' => $this->deviceDetector->isMobile(),
            'is_tablet' => $this->deviceDetector->isTablet(),
            'is_desktop' => $this->deviceDetector->isDesktop(),
            'is_robot' => false,
            'languages' => $this->request->getLanguages(),
            'accept_encoding' => $this->request->header('accept-encoding'),
            'content_type' => $this->request->header('content-type'),
            'request_size' => strlen($this->request->getContent()),
            'has_files' => $this->request->hasFile('*'),
        ];
    }

    /**
     * Get asset and department context from model
     *
     * @param Model|null $model
     * @return array [assetId, departmentId]
     */
    protected function getAssetContext(?Model $model): array
    {
        $assetId = null;
        $departmentId = null;

        if (!$model) {
            return [$assetId, $departmentId];
        }

        if ($model instanceof \App\Models\Asset) {
            $assetId = $model->id;
            $departmentId = $model->department_id;
        } elseif (method_exists($model, 'asset') && $model->asset) {
            $assetId = $model->asset->id;
            $departmentId = $model->asset->department_id;
        } elseif (method_exists($model, 'department') && $model->department) {
            $departmentId = $model->department->id;
        } elseif (isset($model->department_id)) {
            $departmentId = $model->department_id;
        }

        return [$assetId, $departmentId];
    }

    /**
     * Get authentication event description
     *
     * @param string $eventType
     * @return string
     */
    protected function getAuthDescription(string $eventType): string
    {
        $descriptions = [
            'login' => 'User logged in successfully',
            'logout' => 'User logged out',
            'failed_login' => 'Failed login attempt',
            'password_reset' => 'Password reset requested',
            'password_changed' => 'Password changed successfully',
            'account_locked' => 'Account locked due to multiple failed attempts',
            'account_unlocked' => 'Account unlocked',
        ];

        return $descriptions[$eventType] ?? ucfirst(str_replace('_', ' ', $eventType));
    }

    /**
     * Determine the type of change
     *
     * @param mixed $oldValue
     * @param mixed $newValue
     * @return string
     */
    protected function getChangeType($oldValue, $newValue): string
    {
        if ($oldValue === null && $newValue !== null) {
            return 'created';
        }
        
        if ($oldValue !== null && $newValue === null) {
            return 'deleted';
        }
        
        if ($oldValue !== $newValue) {
            return 'updated';
        }
        
        return 'unchanged';
    }

    /**
     * Check if data is JSON
     *
     * @param mixed $data
     * @return bool
     */
    protected function isJsonData($data): bool
    {
        if (!is_string($data)) {
            return false;
        }
        
        json_decode($data);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Get JSON diff between old and new values
     *
     * @param mixed $oldValue
     * @param mixed $newValue
     * @return array
     */
    protected function getJsonDiff($oldValue, $newValue): array
    {
        $oldArray = is_string($oldValue) ? json_decode($oldValue, true) : $oldValue;
        $newArray = is_string($newValue) ? json_decode($newValue, true) : $newValue;

        if (!is_array($oldArray)) $oldArray = [];
        if (!is_array($newArray)) $newArray = [];

        return [
            'added_keys' => array_keys(array_diff_key($newArray, $oldArray)),
            'removed_keys' => array_keys(array_diff_key($oldArray, $newArray)),
            'modified_keys' => array_keys(array_intersect_key(
                array_diff_assoc($newArray, $oldArray),
                array_diff_assoc($oldArray, $newArray)
            ))
        ];
    }

    /**
     * Check if a field contains sensitive information
     *
     * @param string $field
     * @return bool
     */
    protected function isSensitiveField(string $field): bool
    {
        $sensitiveFields = [
            'password', 'password_confirmation', 'token', 'api_key',
            'secret', 'private_key', 'credit_card', 'ssn', 'social_security'
        ];

        return in_array(strtolower($field), $sensitiveFields) ||
               str_contains(strtolower($field), 'password') ||
               str_contains(strtolower($field), 'secret') ||
               str_contains(strtolower($field), 'token');
    }

    /**
     * Filter sensitive data from request parameters
     *
     * @param array $data
     * @return array
     */
    protected function filterSensitiveData(array $data): array
    {
        $filtered = [];
        $sensitiveKeys = [
            'password', 'password_confirmation', 'token', 'api_key',
            'secret', 'private_key', 'credit_card', 'ssn', '_token'
        ];

        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys) ||
                str_contains(strtolower($key), 'password') ||
                str_contains(strtolower($key), 'secret') ||
                str_contains(strtolower($key), 'token')) {
                $filtered[$key] = '[FILTERED]';
            } else {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Bulk log multiple activities
     *
     * @param array $activities
     * @return array
     */
    public function logBulkActivities(array $activities): array
    {
        $logs = [];
        
        foreach ($activities as $activity) {
            $log = $this->logActivity(
                $activity['model'] ?? null,
                $activity['event_type'],
                $activity['description'],
                $activity['old_values'] ?? [],
                $activity['new_values'] ?? [],
                $activity['additional_data'] ?? []
            );
            
            if ($log) {
                $logs[] = $log;
            }
        }
        
        return $logs;
    }
}