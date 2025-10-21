<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignmentConfirmation;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetAssignmentConfirmationExport;
use App\Imports\AssetAssignmentConfirmationImport;

class AssetAssignmentConfirmationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_assignment_confirmations')->only(['index', 'show']);
        $this->middleware('permission:create_assignment_confirmations')->only(['create', 'store']);
        $this->middleware('permission:edit_assignment_confirmations')->only(['edit', 'update']);
        $this->middleware('permission:delete_assignment_confirmations')->only(['destroy']);
        $this->middleware('permission:manage_assignment_confirmations')->only(['export', 'import', 'sendReminder']);
        // confirmByToken and declineByToken are public routes without middleware
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetAssignmentConfirmation::with(['asset.assignedUser', 'asset.department', 'asset.vendor', 'asset.category', 'user.department']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%");
            })->orWhereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Asset filter
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }
        
        // Date range filter
        if ($request->filled('assigned_from')) {
            $query->where('assigned_at', '>=', $request->assigned_from);
        }
        
        if ($request->filled('assigned_to')) {
            $query->where('assigned_at', '<=', $request->assigned_to . ' 23:59:59');
        }
        
        // Reminder count filter
        if ($request->filled('reminder_count')) {
            if ($request->reminder_count === '0') {
                $query->where('reminder_count', 0);
            } elseif ($request->reminder_count === '1+') {
                $query->where('reminder_count', '>', 0);
            }
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['assigned_at', 'status', 'reminder_count', 'created_at', 'confirmed_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $confirmations = $query->paginate(15)->withQueryString();
        
        // Get filter data
        $statuses = ['pending', 'confirmed', 'declined'];
        $users = User::where('status', 1)->orderBy('first_name')->get();
        $assets = Asset::orderBy('name')->get();
        
        // Log activity
        Log::info('Asset assignment confirmations index accessed', [
            'user_id' => Auth::id(),
            'search' => $request->search,
            'status_filter' => $request->status
        ]);
        
        return view('asset-assignment-confirmations.index', compact('confirmations', 'statuses', 'users', 'assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::all();
        $users = User::where('status', 1)->get();
        
        // Log activity
        Log::info('Asset assignment confirmation create form accessed', [
            'user_id' => Auth::id()
        ]);
        
        return view('asset-assignment-confirmations.create', compact('assets', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $confirmation = AssetAssignmentConfirmation::create([
            'asset_id' => $request->asset_id,
            'user_id' => $request->user_id,
            'confirmation_token' => AssetAssignmentConfirmation::generateToken(),
            'status' => 'pending',
            'assigned_at' => $request->assigned_at,
            'notes' => $request->notes,
            'reminder_count' => 0
        ]);
        
        // Log activity
        Log::info('Asset assignment confirmation created', [
            'user_id' => Auth::id(),
            'confirmation_id' => $confirmation->id,
            'asset_id' => $confirmation->asset_id,
            'assigned_to' => $confirmation->user_id
        ]);
        
        return redirect()->route('asset-assignment-confirmations.index')
                        ->with('success', 'Asset assignment confirmation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetAssignmentConfirmation $assetAssignmentConfirmation)
    {
        $assetAssignmentConfirmation->load(['asset', 'user']);
        
        // Log activity
        Log::info('Asset assignment confirmation viewed', [
            'user_id' => Auth::id(),
            'confirmation_id' => $assetAssignmentConfirmation->id
        ]);
        
        return view('asset-assignment-confirmations.show', compact('assetAssignmentConfirmation'))
            ->with('confirmation', $assetAssignmentConfirmation);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetAssignmentConfirmation $assetAssignmentConfirmation)
    {
        $assets = Asset::all();
        $users = User::where('status', 1)->get();
        
        // Log activity
        Log::info('Asset assignment confirmation edit form accessed', [
            'user_id' => Auth::id(),
            'confirmation_id' => $assetAssignmentConfirmation->id
        ]);
        
        return view('asset-assignment-confirmations.edit', compact('assetAssignmentConfirmation', 'assets', 'users'))
            ->with('confirmation', $assetAssignmentConfirmation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetAssignmentConfirmation $assetAssignmentConfirmation)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,confirmed,declined,expired',
            'assigned_at' => 'required|date',
            'confirmed_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $oldData = $assetAssignmentConfirmation->toArray();
        
        $assetAssignmentConfirmation->update([
            'asset_id' => $request->asset_id,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'assigned_at' => $request->assigned_at,
            'confirmed_at' => $request->confirmed_at,
            'notes' => $request->notes
        ]);
        
        // Log activity
        Log::info('Asset assignment confirmation updated', [
            'user_id' => Auth::id(),
            'confirmation_id' => $assetAssignmentConfirmation->id,
            'old_data' => $oldData,
            'new_data' => $assetAssignmentConfirmation->fresh()->toArray()
        ]);
        
        return redirect()->route('asset-assignment-confirmations.index')
                        ->with('success', 'Asset assignment confirmation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetAssignmentConfirmation $assetAssignmentConfirmation)
    {
        $confirmationData = $assetAssignmentConfirmation->toArray();
        
        $assetAssignmentConfirmation->delete();
        
        // Log activity
        Log::info('Asset assignment confirmation deleted', [
            'user_id' => Auth::id(),
            'deleted_confirmation' => $confirmationData
        ]);
        
        return redirect()->route('asset-assignment-confirmations.index')
                        ->with('success', 'Asset assignment confirmation deleted successfully.');
    }
    
    /**
     * Confirm assignment via token
     */
    public function confirm($token)
    {
        $confirmation = AssetAssignmentConfirmation::where('confirmation_token', $token)
                                                  ->where('status', 'pending')
                                                  ->first();
        
        if (!$confirmation) {
            return redirect()->route('home')
                           ->with('error', 'Invalid or expired confirmation token.');
        }
        
        $confirmation->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);
        
        // Update related AssetAssignment status from 'pending' to 'confirmed'
        \App\Models\AssetAssignment::where('asset_id', $confirmation->asset_id)
            ->where('user_id', $confirmation->user_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'confirmed',
                'return_date' => null // Clear return date as asset is now assigned
            ]);
        
        // Update Asset status from 'Pending Confirmation' to 'Assigned'
        \App\Models\Asset::where('id', $confirmation->asset_id)
            ->where('status', 'Pending Confirmation')
            ->update([
                'status' => 'Assigned'
            ]);
        
        // Log activity
        Log::info('Asset assignment confirmed via token', [
            'confirmation_id' => $confirmation->id,
            'user_id' => $confirmation->user_id,
            'asset_id' => $confirmation->asset_id
        ]);
        
        return redirect()->route('home')
                        ->with('success', 'Asset assignment confirmed successfully.');
    }
    
    /**
     * Decline assignment via token
     */
    public function decline($token)
    {
        $confirmation = AssetAssignmentConfirmation::where('confirmation_token', $token)
                                                  ->where('status', 'pending')
                                                  ->first();
        
        if (!$confirmation) {
            return redirect()->route('home')
                           ->with('error', 'Invalid or expired confirmation token.');
        }
        
        $confirmation->update([
            'status' => 'declined',
            'confirmed_at' => now()
        ]);
        
        // Update related AssetAssignment status from 'pending' to 'declined'
        \App\Models\AssetAssignment::where('asset_id', $confirmation->asset_id)
            ->where('user_id', $confirmation->user_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'declined',
                'return_date' => now() // Set return date as assignment is rejected
            ]);
        
        // Update Asset status back to 'Available' and clear assignment
        \App\Models\Asset::where('id', $confirmation->asset_id)
            ->update([
                'status' => 'Available',
                'assigned_to' => null,
                'assigned_date' => null
            ]);
        
        // Log activity
        Log::info('Asset assignment declined via token', [
            'confirmation_id' => $confirmation->id,
            'user_id' => $confirmation->user_id,
            'asset_id' => $confirmation->asset_id
        ]);
        
        return redirect()->route('home')
                        ->with('success', 'Asset assignment declined.');
    }
    
    /**
     * Send reminder for pending confirmations
     */
    public function sendReminder(AssetAssignmentConfirmation $assetAssignmentConfirmation)
    {
        // Debug logging
        \Log::info('SendReminder method called', [
            'confirmation_id' => $assetAssignmentConfirmation->id,
            'is_ajax' => request()->ajax(),
            'wants_json' => request()->wantsJson(),
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip()
        ]);
        
        if ($assetAssignmentConfirmation->status !== 'pending') {
            $statusMessage = match($assetAssignmentConfirmation->status) {
                'confirmed' => 'This confirmation has already been confirmed.',
                'declined' => 'This confirmation has been declined.',
                'expired' => 'This confirmation has expired.',
                default => 'This confirmation is not in pending status.'
            };
            
            return redirect()->back()
                           ->with('warning', "Cannot send reminder: {$statusMessage}");
        }
        
        // Load the asset and user relationships
        $assetAssignmentConfirmation->load(['asset', 'user']);
        
        // Update reminder count and timestamp
        $assetAssignmentConfirmation->update([
            'reminder_count' => $assetAssignmentConfirmation->reminder_count + 1,
            'last_reminder_sent_at' => now()
        ]);
        
        // Send reminder email
        try {
            \Illuminate\Support\Facades\Mail::to($assetAssignmentConfirmation->user->email)
                ->send(new \App\Mail\AssetAssignmentConfirmation(
                    $assetAssignmentConfirmation->asset,
                    $assetAssignmentConfirmation->user,
                    $assetAssignmentConfirmation->confirmation_token,
                    true // This is a follow-up/reminder email
                ));
            
            $emailSent = true;
        } catch (\Exception $e) {
            // Log email error but don't fail the reminder
            Log::error('Failed to send asset assignment confirmation reminder email', [
                'confirmation_id' => $assetAssignmentConfirmation->id,
                'user_email' => $assetAssignmentConfirmation->user->email,
                'error' => $e->getMessage()
            ]);
            $emailSent = false;
        }
        
        // Log activity to custom Log model
        \App\Models\Log::create([
            'loggable_type' => get_class($assetAssignmentConfirmation),
            'loggable_id' => $assetAssignmentConfirmation->id,
            'category' => 'Asset Assignment Confirmation',
            'event_type' => 'reminder_sent',
            'description' => 'Individual reminder sent for asset assignment confirmation',
            'user_id' => Auth::id(),
            'asset_id' => $assetAssignmentConfirmation->asset_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => "Reminder sent to {$assetAssignmentConfirmation->user->email} for asset {$assetAssignmentConfirmation->asset->asset_tag}. Reminder count: {$assetAssignmentConfirmation->reminder_count}. Email sent: " . ($emailSent ? 'Yes' : 'No'),
            'action_details' => [
                'confirmation_id' => $assetAssignmentConfirmation->id,
                'reminder_count' => $assetAssignmentConfirmation->reminder_count,
                'email_sent' => $emailSent,
                'recipient_email' => $assetAssignmentConfirmation->user->email,
                'asset_tag' => $assetAssignmentConfirmation->asset->asset_tag,
                'action_type' => 'individual_reminder'
            ],
            'action_timestamp' => now(),
        ]);
        
        $message = $emailSent 
            ? 'Reminder sent successfully to ' . $assetAssignmentConfirmation->user->email
            : 'Reminder count updated, but email failed to send. Please check logs.';
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => $emailSent,
                'message' => $message,
                'reminder_count' => $assetAssignmentConfirmation->reminder_count,
                'recipient_email' => $assetAssignmentConfirmation->user->email
            ]);
        }
        
        return redirect()->back()
                        ->with($emailSent ? 'success' : 'warning', $message);
    }
    
    /**
     * Send bulk reminders for pending confirmations
     */
    public function sendBulkReminders(Request $request)
    {
        $request->validate([
            'confirmation_ids' => 'required|array|min:1',
            'confirmation_ids.*' => 'exists:asset_assignment_confirmations,id'
        ]);
        
        // Get all selected confirmations first
        $allConfirmations = AssetAssignmentConfirmation::whereIn('id', $request->confirmation_ids)
            ->with(['asset', 'user'])
            ->get();
        
        // Filter only pending confirmations
        $confirmations = $allConfirmations->where('status', 'pending');
        $skippedCount = $allConfirmations->where('status', '!=', 'pending')->count();
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // Add info about skipped confirmations
        if ($skippedCount > 0) {
            $skippedConfirmations = $allConfirmations->where('status', '!=', 'pending');
            $skippedStatuses = $skippedConfirmations->groupBy('status')->map->count();
            $skippedMessage = 'Skipped ' . $skippedCount . ' non-pending confirmations: ' . 
                $skippedStatuses->map(function($count, $status) {
                    return $count . ' ' . $status;
                })->join(', ');
            $errors[] = $skippedMessage;
        }
        
        foreach ($confirmations as $confirmation) {
            try {
                // Update reminder count and timestamp
                $confirmation->update([
                    'reminder_count' => $confirmation->reminder_count + 1,
                    'last_reminder_sent_at' => now()
                ]);
                
                // Send reminder email
                \Illuminate\Support\Facades\Mail::to($confirmation->user->email)
                    ->send(new \App\Mail\AssetAssignmentConfirmation(
                        $confirmation->asset,
                        $confirmation->user,
                        $confirmation->confirmation_token,
                        true // This is a follow-up/reminder email
                    ));
                
                $successCount++;
                
                // Log individual success to custom Log model
                \App\Models\Log::create([
                    'loggable_type' => get_class($confirmation),
                    'loggable_id' => $confirmation->id,
                    'category' => 'Asset Assignment Confirmation',
                    'event_type' => 'bulk_reminder_sent',
                    'description' => 'Bulk reminder sent for asset assignment confirmation',
                    'user_id' => Auth::id(),
                    'asset_id' => $confirmation->asset_id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'remarks' => "Bulk reminder sent to {$confirmation->user->email} for asset {$confirmation->asset->asset_tag}. Reminder count: {$confirmation->reminder_count}",
                    'action_details' => [
                        'confirmation_id' => $confirmation->id,
                        'reminder_count' => $confirmation->reminder_count,
                        'recipient_email' => $confirmation->user->email,
                        'asset_tag' => $confirmation->asset->asset_tag,
                        'action_type' => 'bulk_reminder'
                    ],
                    'action_timestamp' => now(),
                ]);
                
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Failed to send reminder to {$confirmation->user->email}: " . $e->getMessage();
                
                // Log individual error to custom Log model
                \App\Models\Log::create([
                    'loggable_type' => get_class($confirmation),
                    'loggable_id' => $confirmation->id,
                    'category' => 'Asset Assignment Confirmation',
                    'event_type' => 'bulk_reminder_failed',
                    'description' => 'Bulk reminder failed for asset assignment confirmation',
                    'user_id' => Auth::id(),
                    'asset_id' => $confirmation->asset_id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'remarks' => "Bulk reminder failed for {$confirmation->user->email} for asset {$confirmation->asset->asset_tag}. Error: {$e->getMessage()}",
                    'action_details' => [
                        'confirmation_id' => $confirmation->id,
                        'recipient_email' => $confirmation->user->email,
                        'asset_tag' => $confirmation->asset->asset_tag,
                        'error_message' => $e->getMessage(),
                        'action_type' => 'bulk_reminder_failed'
                    ],
                    'action_timestamp' => now(),
                ]);
            }
        }
        
        // Log bulk operation summary to custom Log model
        \App\Models\Log::create([
            'loggable_type' => 'App\Models\AssetAssignmentConfirmation',
            'loggable_id' => null,
            'category' => 'Asset Assignment Confirmation',
            'event_type' => 'bulk_reminder_operation',
            'description' => 'Bulk reminder operation completed',
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => "Bulk reminder operation completed. Total selected: " . count($request->confirmation_ids) . ", Success: {$successCount}, Errors: {$errorCount}, Skipped: {$skippedCount}",
            'action_details' => [
                'total_selected' => count($request->confirmation_ids),
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'skipped_count' => $skippedCount,
                'action_type' => 'bulk_reminder_operation'
            ],
            'action_timestamp' => now(),
        ]);
        
        if ($errorCount === 0 && $successCount > 0) {
            $message = "Successfully sent {$successCount} reminder emails.";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} non-pending confirmations were skipped.)";
            }
            return redirect()->back()->with('success', $message);
        } elseif ($successCount === 0) {
            $message = "No reminders were sent.";
            if ($skippedCount > 0) {
                $message .= " All {$skippedCount} selected confirmations were non-pending and were skipped.";
            }
            if ($errorCount > 0) {
                $message .= " Errors: " . implode('; ', $errors);
            }
            return redirect()->back()->with('warning', $message);
        } else {
            $message = "Sent {$successCount} reminders successfully";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} failed";
            }
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} non-pending confirmations were skipped";
            }
            $message .= ". Errors: " . implode('; ', $errors);
            return redirect()->back()->with('warning', $message);
        }
    }
    
    /**
     * Export asset assignment confirmations to Excel
     */
    public function export()
    {
        // Log activity
        Log::info('Asset assignment confirmations export initiated', [
            'user_id' => Auth::id()
        ]);
        
        return Excel::download(new AssetAssignmentConfirmationExport, 'asset-assignment-confirmations-' . date('Y-m-d') . '.xlsx');
    }
    
    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        // Log activity
        Log::info('Asset assignment confirmations template downloaded', [
            'user_id' => Auth::id()
        ]);
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="asset-assignment-confirmations-template.xlsx"'
        ];
        
        // Create a simple template with headers
        $templateData = [
            ['Asset Tag', 'User Email', 'Status', 'Assigned At', 'Notes'],
            ['AST001', 'user@example.com', 'pending', '2024-01-01', 'Sample confirmation']
        ];
        
        return Excel::download(new class($templateData) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, 'asset-assignment-confirmations-template.xlsx', null, $headers);
    }
    
    /**
     * Show import form
     */
    public function importForm()
    {
        // Log activity
        Log::info('Asset assignment confirmations import form accessed', [
            'user_id' => Auth::id()
        ]);
        
        return view('asset-assignment-confirmations.import');
    }
    
    /**
     * Import asset assignment confirmations from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);
        
        try {
            $import = new AssetAssignmentConfirmationImport;
            Excel::import($import, $request->file('file'));
            
            // Log activity
            Log::info('Asset assignment confirmations import completed', [
                'user_id' => Auth::id(),
                'imported_count' => $import->getRowCount(),
                'filename' => $request->file('file')->getClientOriginalName()
            ]);
            
            return redirect()->route('asset-assignment-confirmations.index')
                           ->with('success', 'Asset assignment confirmations imported successfully. ' . $import->getRowCount() . ' confirmations processed.');
        } catch (\Exception $e) {
            // Log error
            Log::error('Asset assignment confirmations import failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'filename' => $request->file('file')->getClientOriginalName()
            ]);
            
            return redirect()->back()
                           ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Confirm asset assignment by token (public access)
     */
    public function confirmByToken($token)
    {
        return $this->confirm($token);
    }

    /**
     * Decline asset assignment by token (public access)
     */
    public function declineByToken($token)
    {
        return $this->decline($token);
    }
}