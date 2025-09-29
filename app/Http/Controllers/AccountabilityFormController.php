<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\AssetAssignment;
use App\Models\AssetAssignmentConfirmation;
use App\Models\AssetTimeline;
use App\Models\Log;
use Carbon\Carbon;

class AccountabilityFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_accountability_forms')->only(['index']);
        $this->middleware('permission:generate_accountability_forms')->only(['generate']);
        $this->middleware('permission:print_accountability_forms')->only(['print']);
        $this->middleware('permission:bulk_accountability_forms')->only(['generateBulk']);
    }

    /**
     * Display the accountability form index
     */
    public function index(Request $request)
    {
        $query = Asset::with(['assignedUser', 'category', 'vendor', 'department']);

        // Filter by assigned assets only
        $query->whereNotNull('assigned_to');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('assignedUser', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('assigned_to', $request->user_id);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by print status
        if ($request->has('print_status') && $request->print_status) {
            if ($request->print_status === 'printed') {
                $query->whereHas('assignments', function($q) {
                    $q->where('accountability_printed', true);
                });
            } elseif ($request->print_status === 'not_printed') {
                $query->whereHas('assignments', function($q) {
                    $q->where('accountability_printed', false);
                });
            }
        }

        $assets = $query->orderBy('assigned_date', 'desc')->paginate(20);
        
        // Get all asset IDs for efficient querying
        $assetIds = $assets->pluck('id');
        $assignedUserIds = $assets->pluck('assigned_to');
        
        // Load all current assignments in a single query
        $currentAssignments = AssetAssignment::whereIn('asset_id', $assetIds)
            ->whereIn('user_id', $assignedUserIds)
            ->where('status', '!=', 'declined')
            ->with('accountabilityPrintedBy')
            ->get()
            ->groupBy('asset_id')
            ->map(function($assignments) {
                return $assignments->sortByDesc('created_at')->first();
            });
        
        // Attach current assignments to assets
        foreach ($assets as $asset) {
            $asset->currentAssignment = $currentAssignments->get($asset->id);
        }
        $users = User::orderBy('first_name')->get();
        $departments = \App\Models\Department::orderBy('name')->get();

        return view('accountability.index', compact('assets', 'users', 'departments'));
    }

    /**
     * Generate accountability form for a specific asset
     */
    public function generate(Request $request, $assetId)
    {
        $asset = Asset::with([
            'assignedUser.department',
            'assignedUser.role',
            'category',
            'vendor',
            'department',
            'timeline' => function($query) {
                $query->orderBy('performed_at', 'desc');
            }
        ])->findOrFail($assetId);

        // Get assignment history
        $assignments = AssetAssignment::where('asset_id', $assetId)
            ->with(['user', 'assignedBy'])
            ->orderBy('assigned_date', 'desc')
            ->get();

        // Get confirmation history
        $confirmations = AssetAssignmentConfirmation::where('asset_id', $assetId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get activity logs
        $activityLogs = Log::where('asset_id', $assetId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Generate form data
        $formData = [
            'generated_at' => now(),
            'generated_by' => auth()->user(),
            'asset' => $asset,
            'assignments' => $assignments,
            'confirmations' => $confirmations,
            'activity_logs' => $activityLogs,
            'timeline' => $asset->timeline,
            'form_id' => 'ACC-' . strtoupper(substr(md5($assetId . now()), 0, 8))
        ];

        return view('accountability.form', compact('formData'));
    }

    /**
     * Generate accountability form for multiple assets
     */
    public function generateBulk(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'exists:assets,id'
        ]);

        $assets = Asset::with([
            'assignedUser.department',
            'assignedUser.role',
            'category',
            'vendor',
            'department'
        ])->whereIn('id', $request->asset_ids)->get();

        if ($assets->isEmpty()) {
            return redirect()->back()->with('error', 'No valid assets selected.');
        }

        $formData = [
            'generated_at' => now(),
            'generated_by' => auth()->user(),
            'assets' => $assets,
            'form_id' => 'BULK-ACC-' . strtoupper(substr(md5(implode(',', $request->asset_ids) . now()), 0, 8))
        ];

        return view('accountability.bulk-form', compact('formData'));
    }

    /**
     * Print accountability form
     */
    public function print($assetId)
    {
        $asset = Asset::with([
            'assignedUser.department',
            'assignedUser.role',
            'category',
            'vendor',
            'department',
            'timeline' => function($query) {
                $query->orderBy('performed_at', 'desc');
            }
        ])->findOrFail($assetId);

        $assignments = AssetAssignment::where('asset_id', $assetId)
            ->with(['user', 'assignedBy', 'accountabilityPrintedBy'])
            ->orderBy('assigned_date', 'desc')
            ->get();

        $confirmations = AssetAssignmentConfirmation::where('asset_id', $assetId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activityLogs = Log::where('asset_id', $assetId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Mark the current assignment as printed
        $currentAssignment = AssetAssignment::where('asset_id', $assetId)
            ->where('user_id', $asset->assigned_to)
            ->where('status', '!=', 'declined')
            ->latest()
            ->first();

        if ($currentAssignment && !$currentAssignment->accountability_printed) {
            $currentAssignment->update([
                'accountability_printed' => true,
                'accountability_printed_at' => now(),
                'accountability_printed_by' => auth()->id()
            ]);

            // Log the print action
            Log::create([
                'asset_id' => $assetId,
                'user_id' => auth()->id(),
                'category' => 'Accountability',
                'event_type' => 'printed',
                'description' => 'Accountability form printed',
                'remarks' => "Accountability form for asset {$asset->asset_tag} printed by " . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        $formData = [
            'generated_at' => now(),
            'generated_by' => auth()->user(),
            'asset' => $asset,
            'assignments' => $assignments,
            'confirmations' => $confirmations,
            'activity_logs' => $activityLogs,
            'timeline' => $asset->timeline,
            'form_id' => 'ACC-' . strtoupper(substr(md5($assetId . now()), 0, 8))
        ];

        return view('accountability.print', compact('formData'));
    }

    /**
     * Mark accountability form as printed without actually printing
     */
    public function markAsPrinted($assetId)
    {
        $asset = Asset::findOrFail($assetId);
        
        $currentAssignment = AssetAssignment::where('asset_id', $assetId)
            ->where('user_id', $asset->assigned_to)
            ->where('status', '!=', 'declined')
            ->latest()
            ->first();

        if ($currentAssignment && !$currentAssignment->accountability_printed) {
            $currentAssignment->update([
                'accountability_printed' => true,
                'accountability_printed_at' => now(),
                'accountability_printed_by' => auth()->id()
            ]);

            // Log the manual mark as printed action
            Log::create([
                'asset_id' => $assetId,
                'user_id' => auth()->id(),
                'category' => 'Accountability',
                'event_type' => 'marked_printed',
                'description' => 'Accountability form marked as printed manually',
                'remarks' => "Accountability form for asset {$asset->asset_tag} marked as printed by " . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Accountability form marked as printed successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Form already marked as printed or no assignment found.'
        ]);
    }
}
