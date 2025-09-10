<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

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
     * Log an activity for this model
     *
     * @param string $eventType
     * @param string $description
     * @param array $additionalData
     * @return Log
     */
    public function logActivity($eventType, $description = null, $additionalData = [])
    {
        $modelName = class_basename($this);
        $category = strtolower($modelName);
        
        // Default description if not provided
        if (!$description) {
            $description = ucfirst($eventType) . ' ' . $modelName;
        }

        // Prepare additional data
        $logData = [
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'model_data' => $this->toArray(),
        ];

        // Add any additional data
        if (!empty($additionalData)) {
            $logData['changes'] = $additionalData;
        }

        // Determine related asset and department
        $assetId = null;
        $departmentId = null;

        // If this model is an asset or has asset relationship
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

        // Skip logging if no authenticated user (e.g., during seeding)
        if (!Auth::check()) {
            return null;
        }

        // Get the authenticated user's role_id
        $roleId = null;
        if (Auth::user()->role_id) {
            $roleId = Auth::user()->role_id;
        }

        // Create the log entry
        return Log::create([
            'category' => $category,
            'event_type' => $eventType,
            'user_id' => Auth::id(),
            'role_id' => $roleId,
            'asset_id' => $assetId,
            'department_id' => $departmentId,
            'ip_address' => Request::ip(),
            'remarks' => $description, // Use description as remarks
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