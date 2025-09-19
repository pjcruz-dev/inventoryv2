<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class AssetApiController extends Controller
{
    /**
     * Display a listing of assets
     * 
     * @queryParam search string Search term for assets. Example: "laptop"
     * @queryParam category_id integer Filter by category ID. Example: 1
     * @queryParam status string Filter by status. Example: "Available"
     * @queryParam movement string Filter by movement. Example: "New Arrival"
     * @queryParam assigned boolean Filter by assignment status. Example: true
     * @queryParam entity string Filter by entity. Example: "MIDC"
     * @queryParam page integer Page number for pagination. Example: 1
     * @queryParam per_page integer Items per page (max 100). Example: 15
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Asset::class);
        
        $query = Asset::with(['category', 'vendor', 'assignedUser']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('movement')) {
            $query->where('movement', $request->movement);
        }
        
        if ($request->filled('assigned')) {
            if ($request->assigned) {
                $query->whereNotNull('assigned_to');
            } else {
                $query->whereNull('assigned_to');
            }
        }
        
        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
        }
        
        $perPage = min($request->get('per_page', 15), 100);
        $assets = $query->paginate($perPage);
        
        return response()->json($assets);
    }

    /**
     * Store a newly created asset
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Asset::class);
        
        $validated = $request->validate(Asset::validationRules());
        
        $asset = Asset::create($validated);
        
        return response()->json([
            'message' => 'Asset created successfully',
            'data' => $asset->load(['category', 'vendor'])
        ], 201);
    }

    /**
     * Display the specified asset
     */
    public function show(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        
        $asset->load(['category', 'vendor', 'assignedUser', 'timeline']);
        
        return response()->json(['data' => $asset]);
    }

    /**
     * Update the specified asset
     */
    public function update(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('update', $asset);
        
        $validated = $request->validate(Asset::updateValidationRules($asset->id));
        
        $asset->update($validated);
        
        return response()->json([
            'message' => 'Asset updated successfully',
            'data' => $asset->load(['category', 'vendor'])
        ]);
    }

    /**
     * Remove the specified asset
     */
    public function destroy(Asset $asset): JsonResponse
    {
        $this->authorize('delete', $asset);
        
        if ($asset->assigned_to) {
            return response()->json([
                'message' => 'Cannot delete asset that is currently assigned to a user'
            ], 422);
        }
        
        $asset->delete();
        
        return response()->json([
            'message' => 'Asset deleted successfully'
        ]);
    }

    /**
     * Get asset statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Asset::class);
        
        $query = Asset::query();
        
        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
        }
        
        $totalAssets = $query->count();
        $assignedAssets = $query->clone()->whereNotNull('assigned_to')->count();
        $availableAssets = $query->clone()->whereNull('assigned_to')->count();
        
        $assetsByStatus = $query->clone()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
            
        $assetsByCategory = $query->clone()
            ->join('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
            ->selectRaw('asset_categories.name, COUNT(*) as count')
            ->groupBy('asset_categories.name')
            ->pluck('count', 'asset_categories.name');
            
        $totalValue = $query->clone()->sum('cost');
        
        return response()->json([
            'total_assets' => $totalAssets,
            'assigned_assets' => $assignedAssets,
            'available_assets' => $availableAssets,
            'assets_by_status' => $assetsByStatus,
            'assets_by_category' => $assetsByCategory,
            'total_value' => number_format($totalValue, 2)
        ]);
    }

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