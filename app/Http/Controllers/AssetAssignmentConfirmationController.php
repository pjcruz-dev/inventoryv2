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
        $query = AssetAssignmentConfirmation::with(['asset', 'user']);
        
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
        
        $confirmations = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Log activity
        Log::info('Asset assignment confirmations index accessed', [
            'user_id' => Auth::id(),
            'search' => $request->search,
            'status_filter' => $request->status
        ]);
        
        return view('asset-assignment-confirmations.index', compact('confirmations'));
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
        if ($assetAssignmentConfirmation->status !== 'pending') {
            return redirect()->back()
                           ->with('error', 'Cannot send reminder for non-pending confirmation.');
        }
        
        // Update reminder count and timestamp
        $assetAssignmentConfirmation->update([
            'reminder_count' => $assetAssignmentConfirmation->reminder_count + 1,
            'last_reminder_sent_at' => now()
        ]);
        
        // Log activity
        Log::info('Asset assignment confirmation reminder sent', [
            'user_id' => Auth::id(),
            'confirmation_id' => $assetAssignmentConfirmation->id,
            'reminder_count' => $assetAssignmentConfirmation->reminder_count
        ]);
        
        return redirect()->back()
                        ->with('success', 'Reminder sent successfully.');
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