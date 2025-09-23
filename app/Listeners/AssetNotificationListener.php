<?php

namespace App\Listeners;

use App\Events\AssetCreated;
use App\Events\AssetUpdated;
use App\Events\AssetAssigned;
use App\Events\AssetUnassigned;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssetNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle asset created event.
     */
    public function handleAssetCreated(AssetCreated $event)
    {
        $asset = $event->asset;
        $user = $event->user;

        $this->notificationService->createAssetNotification(
            $asset,
            'created',
            $user,
            [
                'created_by' => $user->name,
                'created_at' => $asset->created_at->toISOString(),
            ]
        );
    }

    /**
     * Handle asset updated event.
     */
    public function handleAssetUpdated(AssetUpdated $event)
    {
        $asset = $event->asset;
        $user = $event->user;
        $changes = $event->changes;

        $this->notificationService->createAssetNotification(
            $asset,
            'updated',
            $user,
            [
                'updated_by' => $user->name,
                'updated_at' => $asset->updated_at->toISOString(),
                'changes' => $changes,
            ]
        );
    }

    /**
     * Handle asset assigned event.
     */
    public function handleAssetAssigned(AssetAssigned $event)
    {
        $asset = $event->asset;
        $user = $event->user;
        $assignedTo = $event->assignedTo;

        $this->notificationService->createAssetNotification(
            $asset,
            'assigned',
            $user,
            [
                'assigned_by' => $user->name,
                'assigned_to' => $assignedTo->name,
                'assigned_at' => now()->toISOString(),
            ]
        );
    }

    /**
     * Handle asset unassigned event.
     */
    public function handleAssetUnassigned(AssetUnassigned $event)
    {
        $asset = $event->asset;
        $user = $event->user;
        $unassignedFrom = $event->unassignedFrom;

        $this->notificationService->createAssetNotification(
            $asset,
            'unassigned',
            $user,
            [
                'unassigned_by' => $user->name,
                'unassigned_from' => $unassignedFrom->name,
                'unassigned_at' => now()->toISOString(),
            ]
        );
    }
}
