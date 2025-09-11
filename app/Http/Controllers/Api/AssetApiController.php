<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Asset;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class AssetApiController extends Controller
{
    /**
     * Verify and retrieve detailed asset information assigned to a specific user.
     * Also returns notification data containing names of all users assigned to assets.
     *
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function verifyUserAssets(Request $request, int $userId): JsonResponse
    {
        try {
            // Authentication check (handled by middleware, but double-check)
            if (!Auth::guard('sanctum')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $authenticatedUser = Auth::guard('sanctum')->user();

            // Find the target user
            $targetUser = User::findOrFail($userId);

            // Authorization check - users can only view their own assets unless they have admin privileges
            if ($authenticatedUser->id !== $targetUser->id && !$this->hasAdminAccess($authenticatedUser)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                    'error' => 'You are not authorized to view assets for this user'
                ], 403);
            }

            // Verify user exists and is active
            if (strtolower($targetUser->status) !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'User verification failed - user is not active',
                    'error' => 'User is not active or does not exist'
                ], 404);
            }

            // Get detailed asset information for the user
            $assets = $targetUser->assignedAssets()
                ->with([
                    'category',
                    'vendor',
                    'computer',
                    'monitor',
                    'printer',
                    'peripheral',
                    'department',
                    'timeline' => function ($query) {
                        $query->with(['fromUser', 'toUser', 'performedBy'])->latest('performed_at');
                    }
                ])
                ->get()
                ->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'asset_tag' => $asset->asset_tag,
                        'name' => $asset->name,
                        'description' => $asset->description,
                        'serial_number' => $asset->serial_number,
                        'status' => $asset->status,
                        'movement' => $asset->movement,
                        'purchase_date' => $asset->purchase_date,
                        'warranty_end' => $asset->warranty_end,
                        'cost' => $asset->cost,
                        'assigned_date' => $asset->assigned_date,
                        'category' => $asset->category ? [
                            'id' => $asset->category->id,
                            'name' => $asset->category->name
                        ] : null,
                        'vendor' => $asset->vendor ? [
                            'id' => $asset->vendor->id,
                            'name' => $asset->vendor->name
                        ] : null,
                        'department' => $asset->department ? [
                            'id' => $asset->department->id,
                            'name' => $asset->department->name
                        ] : null,
                        'device_details' => $this->getDeviceDetails($asset),
                        'timeline' => $asset->timeline->take(5)->map(function ($timeline) {
                            return [
                                'id' => $timeline->id,
                                'action' => $timeline->action,
                                'notes' => $timeline->notes,
                                'performed_at' => $timeline->performed_at,
                                'performed_by' => $timeline->performedBy ? [
                                    'id' => $timeline->performedBy->id,
                                    'name' => $timeline->performedBy->name
                                ] : null,
                                'from_user' => $timeline->fromUser ? [
                                    'id' => $timeline->fromUser->id,
                                    'name' => $timeline->fromUser->name
                                ] : null,
                                'to_user' => $timeline->toUser ? [
                                    'id' => $timeline->toUser->id,
                                    'name' => $timeline->toUser->name
                                ] : null
                            ];
                        })
                    ];
                });

            // Get notification data containing names of all users assigned to assets
            $allAssignedUsers = User::whereHas('assignedAssets')
                ->with('department')
                ->get()
                ->map(function ($assignedUser) {
                    return [
                        'id' => $assignedUser->id,
                        'name' => $assignedUser->name,
                        'email' => $assignedUser->email,
                        'employee_no' => $assignedUser->employee_no,
                        'department' => $assignedUser->department ? [
                            'id' => $assignedUser->department->id,
                            'name' => $assignedUser->department->name
                        ] : null,
                        'assets_count' => $assignedUser->assignedAssets()->count()
                    ];
                });

            // Get recent notifications for the user related to assets
            $notifications = Notification::where('user_id', $targetUser->id)
                ->where('type', 'asset')
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'data' => $notification->data,
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Asset verification completed successfully',
                'data' => [
                    'user' => [
                        'id' => $targetUser->id,
                        'name' => $targetUser->name,
                        'email' => $targetUser->email,
                        'employee_no' => $targetUser->employee_no,
                        'department' => $targetUser->department ? [
                            'id' => $targetUser->department->id,
                            'name' => $targetUser->department->name
                        ] : null
                    ],
                    'assets' => $assets,
                    'assets_count' => $assets->count(),
                    'all_assigned_users' => $allAssignedUsers,
                    'notifications' => $notifications,
                    'verification_timestamp' => now()->toISOString()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying user assets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if the authenticated user has admin access
     *
     * @param User $user
     * @return bool
     */
    private function hasAdminAccess(User $user): bool
    {
        // Check if user has admin role or specific permissions
        return $user->hasRole('Admin') || 
               $user->hasPermissionTo('view all assets') ||
               $user->hasPermissionTo('manage assets');
    }

    /**
     * Get device-specific details based on asset type
     *
     * @param Asset $asset
     * @return array|null
     */
    private function getDeviceDetails(Asset $asset): ?array
    {
        if ($asset->computer) {
            return [
                'type' => 'computer',
                'details' => [
                    'processor' => $asset->computer->processor,
                    'memory' => $asset->computer->memory,
                    'storage' => $asset->computer->storage,
                    'operating_system' => $asset->computer->operating_system,
                    'computer_name' => $asset->computer->computer_name,
                    'ip_address' => $asset->computer->ip_address
                ]
            ];
        }

        if ($asset->monitor) {
            return [
                'type' => 'monitor',
                'details' => [
                    'screen_size' => $asset->monitor->screen_size,
                    'resolution' => $asset->monitor->resolution,
                    'panel_type' => $asset->monitor->panel_type,
                    'refresh_rate' => $asset->monitor->refresh_rate
                ]
            ];
        }

        if ($asset->printer) {
            return [
                'type' => 'printer',
                'details' => [
                    'printer_type' => $asset->printer->type,
                    'connectivity' => $asset->printer->connectivity,
                    'color_support' => $asset->printer->color_support,
                    'duplex_support' => $asset->printer->duplex_support
                ]
            ];
        }

        if ($asset->peripheral) {
            return [
                'type' => 'peripheral',
                'details' => [
                    'peripheral_type' => $asset->peripheral->type,
                    'connectivity' => $asset->peripheral->connectivity
                ]
            ];
        }

        return null;
    }
}