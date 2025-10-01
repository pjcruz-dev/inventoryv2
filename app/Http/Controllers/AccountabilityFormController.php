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
        $query = Asset::with(['assignedUser', 'category', 'vendor', 'department', 'currentAssignment']);

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

        $assets = $query->orderBy('assigned_date', 'desc')->paginate(20)->appends(request()->query());
        
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

    /**
     * Upload signed accountability form
     */
    public function uploadSignedForm(Request $request, $assetId)
    {
        $request->validate([
            'signed_form' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'email_subject' => 'nullable|string|max:255'
        ]);

        $asset = Asset::findOrFail($assetId);
        
        // Get the current assignment
        $assignment = AssetAssignment::with('user')
            ->where('asset_id', $assetId)
            ->where('user_id', $asset->assigned_to)
            ->where('status', '!=', 'declined')
            ->latest()
            ->first();

        if (!$assignment) {
            return redirect()->back()->with('error', 'No active assignment found for this asset.');
        }

        // Handle file upload
        if ($request->hasFile('signed_form')) {
            $file = $request->file('signed_form');
            
            // Validate file
            if (!$file->isValid()) {
                return redirect()->back()->with('error', 'Invalid file upload.');
            }
            
            $filename = 'signed_forms/' . $asset->asset_tag . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Ensure the directory exists
            $directory = storage_path('app/public/signed_forms');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Store the file using the public disk
            $storedPath = $file->storeAs('', $filename, 'public');
            
            if (!$storedPath) {
                return redirect()->back()->with('error', 'Failed to store file.');
            }
            
            // Verify file was actually stored
            $fullPath = storage_path('app/public/' . $storedPath);
            if (!file_exists($fullPath)) {
                return redirect()->back()->with('error', 'File was not stored properly.');
            }
            
            // Generate professional email subject and content
            $professionalSubject = "Asset Accountability Form - {$asset->asset_tag} - Confirmed & Signed";
            $professionalDescription = "Dear {$assignment->user->first_name},\n\n" .
                "This is to confirm that the signed accountability form for asset {$asset->asset_tag} ({$asset->name}) has been successfully processed and is now available for your records.\n\n" .
                "Asset Details:\n" .
                "• Asset Tag: {$asset->asset_tag}\n" .
                "• Asset Name: {$asset->name}\n" .
                "• Category: " . ($asset->category->name ?? 'Not specified') . "\n" .
                "• Department: " . ($asset->department->name ?? 'Not specified') . "\n" .
                "• Assigned Date: " . ($assignment->assigned_at ? $assignment->assigned_at->format('F d, Y') : 'Not specified') . "\n\n" .
                "The signed form is attached to this email. Please keep this document in a secure location for your records.\n\n" .
                "If you have any questions about this asset assignment, please contact your IT department.\n\n" .
                "Best regards,\n" .
                config('app.name') . " - IT Asset Management";

            // Update assignment with signed form details
            $assignment->update([
                'signed_form_path' => $storedPath, // Store the actual path returned by storeAs
                'signed_form_uploaded_at' => now(),
                'signed_form_uploaded_by' => auth()->id(),
                'signed_form_description' => $professionalDescription,
                'signed_form_email_subject' => $request->email_subject ?: $professionalSubject
            ]);

            // Log the upload action
            Log::create([
                'asset_id' => $assetId,
                'user_id' => auth()->id(),
                'category' => 'Accountability',
                'event_type' => 'signed_form_uploaded',
                'description' => 'Signed accountability form uploaded',
                'remarks' => "Signed accountability form for asset {$asset->asset_tag} uploaded by " . auth()->user()->first_name . ' ' . auth()->user()->last_name . " to path: {$storedPath}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->back()->with('success', 'Signed form uploaded successfully.');
        }

        return redirect()->back()->with('error', 'No file provided for upload.');
    }

    /**
     * Send email with signed form attachment
     */
    public function sendSignedFormEmail(Request $request, $assetId)
    {
        // Convert comma-separated recipients string to array
        $recipientsString = $request->input('recipients');
        $recipients = array_map('trim', explode(',', $recipientsString));
        $recipients = array_filter($recipients); // Remove empty values
        
        // Validate recipients
        if (empty($recipients)) {
            return redirect()->back()->with('error', 'At least one recipient email is required.');
        }
        
        // Validate each email
        foreach ($recipients as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', "Invalid email address: {$email}");
            }
        }
        
        $request->validate([
            'description' => 'nullable|string|max:1000',
            'subject' => 'nullable|string|max:255'
        ]);

        $asset = Asset::findOrFail($assetId);
        
        // Get the current assignment
        $assignment = AssetAssignment::where('asset_id', $assetId)
            ->where('user_id', $asset->assigned_to)
            ->where('status', '!=', 'declined')
            ->latest()
            ->first();

        if (!$assignment) {
            return redirect()->back()->with('error', 'No active assignment found for this asset.');
        }

        if (!$assignment->signed_form_path) {
            return redirect()->back()->with('error', 'No signed form uploaded for this asset.');
        }

        try {
            // Use professional content if no custom content provided
            $emailSubject = $request->subject ?: $assignment->signed_form_email_subject;
            $emailDescription = $request->description ?: $assignment->signed_form_description;
            
            // Send email to all recipients
            foreach ($recipients as $recipient) {
                \Illuminate\Support\Facades\Mail::to($recipient)
                    ->send(new \App\Mail\SignedAccountabilityFormMail(
                        $assignment,
                        $emailDescription,
                        $emailSubject
                    ));
            }

            // Update assignment to mark email as sent
            $assignment->update([
                'signed_form_email_sent' => true,
                'signed_form_email_sent_at' => now()
            ]);

            // Log the email action
            Log::create([
                'asset_id' => $assetId,
                'user_id' => auth()->id() ?: 1, // Fallback to admin user if auth fails
                'category' => 'Accountability',
                'event_type' => 'signed_form_email_sent',
                'description' => 'Signed accountability form email sent',
                'remarks' => "Signed accountability form email for asset {$asset->asset_tag} sent to: " . implode(', ', $recipients),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->back()->with('success', 'Signed form email sent successfully to ' . count($recipients) . ' recipient(s).');

        } catch (\Exception $e) {
            \Log::error('Failed to send signed form email: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Preview signed form
     */
    public function previewSignedForm($assetId)
    {
        $asset = Asset::findOrFail($assetId);
        
        $assignment = AssetAssignment::where('asset_id', $assetId)
            ->where('user_id', $asset->assigned_to)
            ->where('status', '!=', 'declined')
            ->latest()
            ->first();
            
        if (!$assignment || !$assignment->signed_form_path) {
            abort(404, 'Signed form not found');
        }

        // The file is stored in the public disk
        $filePath = storage_path('app/public/' . $assignment->signed_form_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Signed form file not found');
        }

        // Return the file for preview (PDF viewer)
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="signed_accountability_form_' . $asset->asset_tag . '.pdf"'
        ]);
    }

    /**
     * Download signed form
     */
    public function downloadSignedForm($assetId)
    {
        $asset = Asset::findOrFail($assetId);
        
        $assignment = AssetAssignment::where('asset_id', $assetId)
            ->where('user_id', $asset->assigned_to)
            ->where('status', '!=', 'declined')
            ->latest()
            ->first();

        if (!$assignment || !$assignment->signed_form_path) {
            return redirect()->back()->with('error', 'No signed form found for this asset.');
        }

        // The file is stored in the public disk
        $filePath = storage_path('app/public/' . $assignment->signed_form_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Signed form file not found at: ' . $filePath);
        }

        return response()->download($filePath, "signed_accountability_form_{$asset->asset_tag}.pdf");
    }
}
