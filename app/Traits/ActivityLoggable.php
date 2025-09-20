<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Services\DeviceDetector;
use Carbon\Carbon;

trait ActivityLoggable
{
    /**
     * Boot the trait and register model events
     */
    public static function bootActivityLoggable()
    {
        // Log when a model is created
        static::created(function ($model) {
            $model->logActivity('created', 'Created new ' . class_basename($model));
        });

        // Log when a model is updated
        static::updated(function ($model) {
            $changes = $model->getChanges();
            if (!empty($changes)) {
                $model->logActivity('updated', 'Updated ' . class_basename($model), $changes);
            }
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            $model->logActivity('deleted', 'Deleted ' . class_basename($model));
        });
    }

    /**
     * Log an activity for this model with enhanced tracking
     *
     * @param string $eventType
     * @param string $description
     * @param array $additionalData
     * @return Log|null
     */
    public function logActivity($eventType, $description = null, $additionalData = [])
    {
        // Skip logging if no authenticated user (e.g., during seeding)
        if (!Auth::check()) {
            return null;
        }

        $modelName = class_basename($this);
        $category = strtolower($modelName);
        
        // Default description if not provided
        if (!$description) {
            $description = ucfirst($eventType) . ' ' . $modelName;
        }

        // Get detailed change information
        $affectedFields = $this->getDetailedChanges();
        $oldValues = $this->getOriginal();
        $newValues = $this->getAttributes();

        // Prepare action details
        $actionDetails = [
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'action_performed' => $eventType,
            'total_fields_changed' => count($affectedFields),
            'user_initiated' => true,
        ];

        // Add any additional action details
        if (!empty($additionalData)) {
            $actionDetails = array_merge($actionDetails, $additionalData);
        }

        // Get request information
        $request = request();
        $agent = new DeviceDetector();
        
        // Prepare metadata
        $metadata = [
            'request_id' => $request->header('X-Request-ID', uniqid()),
            'referrer' => $request->header('referer'),
            'user_agent_full' => $request->userAgent(),
            'device_type' => $agent->deviceType(),
            'platform' => $agent->platform(),
            'platform_version' => 'Unknown',
            'browser_version' => 'Unknown',
            'is_mobile' => $agent->isMobile(),
            'is_tablet' => $agent->isTablet(),
            'is_desktop' => $agent->isDesktop(),
            'languages' => $request->getLanguages(),
        ];

        // Determine related asset and department
        $assetId = null;
        $departmentId = null;

        if ($this instanceof \App\Models\Asset) {
            $assetId = $this->id;
            $departmentId = $this->department_id;
        } elseif (method_exists($this, 'asset') && $this->asset) {
            $assetId = $this->asset->id;
            $departmentId = $this->asset->department_id;
        } elseif (method_exists($this, 'department') && $this->department) {
            $departmentId = $this->department->id;
        } elseif (isset($this->department_id)) {
            $departmentId = $this->department_id;
        }

        // Get user information
        $user = Auth::user();
        $roleId = $user->role_id ?? null;

        // Filter sensitive request parameters
        $requestParameters = $this->filterSensitiveData($request->all());

        // Create the enhanced log entry
        return Log::create([
            'loggable_type' => get_class($this),
            'loggable_id' => $this->getKey(),
            'category' => $category,
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => $user->id,
            'role_id' => $roleId,
            'asset_id' => $assetId,
            'department_id' => $departmentId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'remarks' => $description,
            // Enhanced fields
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'action_details' => $actionDetails,
            'affected_fields' => $affectedFields,
            'metadata' => $metadata,
            'session_id' => $request->session()->getId(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
            'request_parameters' => $requestParameters,
            'browser_name' => $agent->browser(),
            'operating_system' => $agent->platform(),
            'action_timestamp' => Carbon::now(),
        ]);
    }

    /**
     * Get detailed changes with field-level information
     *
     * @return array
     */
    protected function getDetailedChanges()
    {
        $changes = [];
        $dirty = $this->getDirty();
        $original = $this->getOriginal();

        foreach ($dirty as $field => $newValue) {
            $oldValue = $original[$field] ?? null;
            
            $changes[$field] = [
                'field_name' => $field,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'data_type' => gettype($newValue),
                'changed_at' => Carbon::now()->toISOString(),
                'is_sensitive' => $this->isSensitiveField($field),
            ];

            // Add additional context for specific field types
            if ($this->isRelationshipField($field)) {
                $changes[$field]['is_relationship'] = true;
                $changes[$field]['relationship_type'] = $this->getRelationshipType($field);
            }

            if ($this->isJsonField($field)) {
                $changes[$field]['is_json'] = true;
                $changes[$field]['json_diff'] = $this->getJsonDiff($oldValue, $newValue);
            }
        }

        return $changes;
    }

    /**
     * Check if a field contains sensitive information
     *
     * @param string $field
     * @return bool
     */
    protected function isSensitiveField($field)
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
     * Check if a field is a relationship field
     *
     * @param string $field
     * @return bool
     */
    protected function isRelationshipField($field)
    {
        return str_ends_with($field, '_id') || 
               in_array($field, $this->getRelationshipFields());
    }

    /**
     * Get relationship fields for this model
     *
     * @return array
     */
    protected function getRelationshipFields()
    {
        // Override this method in models to specify relationship fields
        return [];
    }

    /**
     * Get the relationship type for a field
     *
     * @param string $field
     * @return string
     */
    protected function getRelationshipType($field)
    {
        if (str_ends_with($field, '_id')) {
            return 'belongs_to';
        }
        
        return 'unknown';
    }

    /**
     * Check if a field stores JSON data
     *
     * @param string $field
     * @return bool
     */
    protected function isJsonField($field)
    {
        $casts = $this->getCasts();
        return isset($casts[$field]) && in_array($casts[$field], ['array', 'json', 'object', 'collection']);
    }

    /**
     * Get JSON diff between old and new values
     *
     * @param mixed $oldValue
     * @param mixed $newValue
     * @return array
     */
    protected function getJsonDiff($oldValue, $newValue)
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
     * Filter sensitive data from request parameters
     *
     * @param array $data
     * @return array
     */
    protected function filterSensitiveData($data)
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
     * Log a simple activity without detailed change tracking
     *
     * @param string $eventType
     * @param string $description
     * @param array $additionalData
     * @return Log|null
     */
    public function logSimpleActivity($eventType, $description, $additionalData = [])
    {
        if (!Auth::check()) {
            return null;
        }

        $request = request();
        $agent = new DeviceDetector();
        $user = Auth::user();

        return Log::create([
            'loggable_type' => get_class($this),
            'loggable_id' => $this->getKey(),
            'category' => strtolower(class_basename($this)),
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => $user->id,
            'role_id' => $user->role_id ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'remarks' => $description,
            'action_details' => array_merge([
                'model_type' => get_class($this),
                'model_id' => $this->getKey(),
                'action_performed' => $eventType,
                'user_initiated' => true,
            ], $additionalData),
            'session_id' => $request->session()->getId(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
            'browser_name' => $agent->browser(),
            'operating_system' => $agent->platform(),
            'action_timestamp' => Carbon::now(),
        ]);
     }

     /**
      * Log a custom activity
     *
     * @param string $eventType
     * @param string $description
     * @param array $additionalData
     * @return Log
     */
    public function logCustomActivity($eventType, $description, $additionalData = [])
    {
        return $this->logActivity($eventType, $description, $additionalData);
    }

    /**
     * Log assignment activities
     *
     * @param \Illuminate\Database\Eloquent\Model $assignedTo
     * @param string $type
     * @return Log
     */
    public function logAssignment($assignedTo, $type = 'assigned')
    {
        $assignedToName = '';
        if (method_exists($assignedTo, 'getFullNameAttribute')) {
            $assignedToName = $assignedTo->getFullNameAttribute();
        } elseif (isset($assignedTo->name)) {
            $assignedToName = $assignedTo->name;
        } elseif (isset($assignedTo->first_name) && isset($assignedTo->last_name)) {
            $assignedToName = $assignedTo->first_name . ' ' . $assignedTo->last_name;
        }

        $description = ucfirst($type) . ' ' . class_basename($this) . ' to ' . $assignedToName;
        
        $additionalData = [
            'assigned_to_type' => get_class($assignedTo),
            'assigned_to_id' => $assignedTo->getKey(),
            'assigned_to_name' => $assignedToName,
            'assignment_type' => $type,
        ];

        return $this->logActivity($type, $description, $additionalData);
    }

    /**
     * Log status change activities
     *
     * @param string $oldStatus
     * @param string $newStatus
     * @return Log
     */
    public function logStatusChange($oldStatus, $newStatus)
    {
        $description = 'Changed ' . class_basename($this) . ' status from ' . $oldStatus . ' to ' . $newStatus;
        
        $additionalData = [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ];

        return $this->logActivity('status_changed', $description, $additionalData);
    }

    /**
     * Get all activity logs for this model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activityLogs()
    {
        return $this->morphMany(Log::class, 'loggable');
    }

    /**
     * Get recent activity logs for this model
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivityLogs($limit = 10)
    {
        return Log::where('additional_data', 'like', '%"model_id":' . $this->getKey() . '%')
                  ->where('additional_data', 'like', '%"model_type":"' . addslashes(get_class($this)) . '"%')
                  ->with(['user', 'asset', 'department'])
                  ->orderBy('created_at', 'desc')
                  ->limit($limit)
                  ->get();
    }
}