<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BulkUploadSession extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'asset_ids',
        'uploaded_files',
        'pending_files',
        'total_files',
        'uploaded_count',
        'status',
        'last_activity_at',
    ];

    protected $casts = [
        'asset_ids' => 'array',
        'uploaded_files' => 'array',
        'pending_files' => 'array',
        'last_activity_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'bulk_upload_session_assets');
    }

    public static function createSession($assetIds, $userId)
    {
        return self::create([
            'session_id' => Str::uuid(),
            'user_id' => $userId,
            'asset_ids' => $assetIds,
            'uploaded_files' => [],
            'pending_files' => $assetIds,
            'total_files' => count($assetIds),
            'uploaded_count' => 0,
            'status' => 'in_progress',
            'last_activity_at' => now(),
        ]);
    }

    public function addUploadedFile($assetId, $fileInfo)
    {
        $uploadedFiles = $this->uploaded_files ?? [];
        $uploadedFiles[] = [
            'asset_id' => $assetId,
            'file_info' => $fileInfo,
            'uploaded_at' => now()->toISOString(),
        ];

        $pendingFiles = array_diff($this->pending_files, [$assetId]);

        $this->update([
            'uploaded_files' => $uploadedFiles,
            'pending_files' => array_values($pendingFiles),
            'uploaded_count' => count($uploadedFiles),
            'last_activity_at' => now(),
            'status' => empty($pendingFiles) ? 'completed' : 'in_progress',
        ]);

        return $this;
    }

    public function getProgressPercentage()
    {
        if ($this->total_files == 0) return 0;
        return round(($this->uploaded_count / $this->total_files) * 100, 2);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }
}
