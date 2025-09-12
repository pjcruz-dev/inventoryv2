<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\User;
use App\Models\Asset;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_logs')->only(['index', 'show']);
    }

    /**
     * Display a listing of the logs.
     */
    public function index(Request $request)
    {
        $query = Log::with(['user', 'asset', 'department', 'role'])
                    ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_type', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('asset', function($assetQuery) use ($search) {
                      $assetQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('asset_tag', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by event type
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Get filter options
        $categories = Log::distinct()->pluck('category')->filter()->sort();
        $eventTypes = Log::distinct()->pluck('event_type')->filter()->sort();
        $users = User::where('status', 'Active')->orderBy('first_name')->get();
        $departments = Department::orderBy('name')->get();

        return view('logs.index', compact(
            'logs',
            'categories',
            'eventTypes',
            'users',
            'departments'
        ));
    }

    /**
     * Display the specified log.
     */
    public function show(Log $log)
    {
        $log->load(['user', 'asset', 'department', 'role', 'permission']);
        
        // Get related logs (same asset or same user)
        $relatedLogs = collect();
        
        if ($log->asset_id) {
            $assetLogs = Log::where('asset_id', $log->asset_id)
                           ->where('id', '!=', $log->id)
                           ->with(['user', 'asset'])
                           ->orderBy('created_at', 'desc')
                           ->limit(5)
                           ->get();
            $relatedLogs = $relatedLogs->merge($assetLogs);
        }
        
        if ($log->user_id && $relatedLogs->count() < 5) {
            $userLogs = Log::where('user_id', $log->user_id)
                          ->where('id', '!=', $log->id)
                          ->whereNotIn('id', $relatedLogs->pluck('id'))
                          ->with(['user', 'asset'])
                          ->orderBy('created_at', 'desc')
                          ->limit(5 - $relatedLogs->count())
                          ->get();
            $relatedLogs = $relatedLogs->merge($userLogs);
        }
        
        return view('logs.show', compact('log', 'relatedLogs'));
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $query = Log::with(['user', 'asset', 'department', 'role'])
                    ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_type', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('asset', function($assetQuery) use ($search) {
                      $assetQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('asset_tag', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Date/Time',
                'Category',
                'Event Type',
                'User',
                'Asset',
                'Department',
                'Role',
                'IP Address',
                'User Agent',
                'Remarks'
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '',
                    $log->category,
                    $log->event_type,
                    $log->user ? $log->user->first_name . ' ' . $log->user->last_name : '',
                    $log->asset ? $log->asset->name . ' (' . $log->asset->asset_tag . ')' : '',
                    $log->department ? $log->department->name : '',
                    $log->role ? $log->role->name : '',
                    $log->ip_address,
                    $log->user_agent,
                    $log->remarks
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clear old logs (optional - for maintenance)
     */
    public function clearOldLogs(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365'
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = Log::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->route('logs.index')
                        ->with('success', "Deleted {$deletedCount} log entries older than {$request->days} days.");
    }
}