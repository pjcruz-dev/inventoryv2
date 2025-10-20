<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\AssetAssignmentConfirmation;
use App\Models\AssetTimeline;
use App\Models\Log;
use App\Models\BulkUploadSession;
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

        $assets = $query->orderBy('assigned_date', 'desc')->paginate(20)->appends(request()->query());
        
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

        // Get assignment history from asset timeline
        $assignments = $asset->timeline()
            ->whereIn('action', ['assigned', 'transferred', 'unassigned'])
            ->with(['user'])
            ->orderBy('created_at', 'desc')
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

        $assignments = $asset->timeline()
            ->whereIn('action', ['assigned', 'transferred', 'unassigned'])
            ->with(['user'])
            ->orderBy('created_at', 'desc')
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
        
        // Get the current assignment from asset
        $assignment = null;
        if ($asset->assigned_to) {
            $assignment = (object) [
                'user' => $asset->assignedUser,
                'assigned_date' => $asset->assigned_date,
                'status' => 'active'
            ];
        }

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
        
        // Get the current assignment from asset
        $assignment = null;
        if ($asset->assigned_to) {
            $assignment = (object) [
                'user' => $asset->assignedUser,
                'assigned_date' => $asset->assigned_date,
                'status' => 'active'
            ];
        }

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
        
        $assignment = null;
        if ($asset->assigned_to) {
            $assignment = (object) [
                'user' => $asset->assignedUser,
                'assigned_date' => $asset->assigned_date,
                'status' => 'active',
                'signed_form_path' => $asset->signed_form_path ?? null
            ];
        }
            
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
        
        $assignment = null;
        if ($asset->assigned_to) {
            $assignment = (object) [
                'user' => $asset->assignedUser,
                'assigned_date' => $asset->assigned_date,
                'status' => 'active',
                'signed_form_path' => $asset->signed_form_path ?? null
            ];
        }

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

    /**
     * Show bulk upload form
     */
    public function showBulkUpload()
    {
        // Get all assets with assigned users
        $assets = Asset::whereNotNull('assigned_to')
            ->where('status', 'Active')
            ->with(['assignedUser', 'category', 'vendor'])
            ->orderBy('asset_tag')
            ->get();

        return view('accountability.bulk-upload', compact('assets'));
    }

    /**
     * Handle bulk upload of signed forms
     */
    public function bulkUpload(Request $request)
    {
        // Debug: Log the request data
        \Log::info('Bulk upload request data:', [
            'signed_forms_count' => $request->hasFile('signed_forms') ? count($request->file('signed_forms')) : 0,
            'asset_ids' => $request->input('asset_ids', []),
            'all_input' => $request->all()
        ]);

        try {
            $request->validate([
                'signed_forms' => 'required|array|min:1',
                'signed_forms.*' => 'required|file|mimes:pdf|max:10240', // 10MB max per file
                'asset_ids' => 'required|array|min:1',
                'asset_ids.*' => 'required|exists:assets,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Bulk upload validation failed:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            throw $e;
        }

        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('signed_forms') as $index => $file) {
            $assetId = $request->asset_ids[$index] ?? null;
            
            \Log::info("Processing file {$index}:", [
                'filename' => $file->getClientOriginalName(),
                'asset_id' => $assetId,
                'file_size' => $file->getSize(),
                'file_mime' => $file->getMimeType()
            ]);
            
            if (!$assetId) {
                $errors[] = "No asset ID provided for file: {$file->getClientOriginalName()}";
                continue;
            }

            try {
                $asset = Asset::findOrFail($assetId);

                // Generate unique filename
                $timestamp = time() + $index; // Add index to ensure uniqueness
                $filename = $asset->asset_tag . '_' . $timestamp . '.pdf';
                $filePath = 'signed_forms/' . $filename;

                \Log::info("Storing file:", [
                    'filename' => $filename,
                    'filepath' => $filePath,
                    'storage_path' => 'public/signed_forms'
                ]);

                // Store file
                $storedPath = $file->storeAs('public/signed_forms', $filename);
                
                \Log::info("File stored successfully:", [
                    'stored_path' => $storedPath,
                    'file_exists' => file_exists(storage_path('app/' . $storedPath))
                ]);

                $uploadedCount++;

                // Log the upload action
                Log::create([
                    'asset_id' => $assetId,
                    'user_id' => auth()->id() ?: 1,
                    'category' => 'Accountability',
                    'event_type' => 'bulk_signed_form_uploaded',
                    'description' => 'Bulk signed form uploaded',
                    'remarks' => "Bulk signed form for asset {$asset->asset_tag} uploaded successfully",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

            } catch (\Exception $e) {
                $errors[] = "Error uploading {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        \Log::info("Bulk upload completed:", [
            'uploaded_count' => $uploadedCount,
            'errors' => $errors,
            'total_files' => count($request->file('signed_forms'))
        ]);

        if ($uploadedCount > 0) {
            $message = "Successfully uploaded {$uploadedCount} signed form(s).";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', $errors);
            }
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'No files were uploaded. Errors: ' . implode('; ', $errors));
        }
    }

    /**
     * Show bulk email form
     */
    public function showBulkEmail()
    {
        // Get all assets with signed forms (using 'Active' status instead of 'Assigned')
        $assets = Asset::whereNotNull('assigned_to')
            ->where('status', 'Active')
            ->with(['assignedUser', 'category', 'vendor'])
            ->orderBy('asset_tag')
            ->get();

        // Generate default values
        $defaultRecipients = $assets->pluck('assignedUser.email')->unique()->filter()->implode(', ');
        $defaultSubject = 'Asset Accountability Forms - Confirmed & Signed';

        return view('accountability.bulk-email', compact('assets', 'defaultRecipients', 'defaultSubject'));
    }

    /**
     * Generate bulk email description
     */
    private function generateBulkEmailDescription($assets)
    {
        $assetCount = $assets->count();
        $assetTags = $assets->pluck('asset_tag')->implode(', ');
        
        $description = "Dear Employee,\n\n";
        $description .= "This is to confirm that {$assetCount} signed accountability form(s) have been successfully processed and are now available for your records.\n\n";
        $description .= "Asset Details:\n";
        $description .= "• Asset Tags: {$assetTags}\n";
        $description .= "• Total Assets: {$assetCount}\n\n";
        $description .= "The signed forms are attached to this email. Please keep these documents in a secure location for your records.\n\n";
        $description .= "If you have any questions about these asset assignments, please contact your IT department.\n\n";
        $description .= "Best regards,\n";
        $description .= "IT Asset Management Team";

        return $description;
    }

    private function generatePersonalizedBulkEmailDescription($userAssets, $user)
    {
        $assetCount = $userAssets->count();
        $assetTags = $userAssets->pluck('asset_tag')->implode(', ');
        $userName = $user->first_name . ' ' . $user->last_name;
        
        $description = "Dear {$userName},\n\n";
        $description .= "This is to confirm that {$assetCount} signed accountability form(s) have been successfully processed and are now available for your records.\n\n";
        $description .= "Your Asset Details:\n";
        $description .= "• Asset Tags: {$assetTags}\n";
        $description .= "• Total Assets: {$assetCount}\n\n";
        $description .= "The signed forms are attached to this email. Please keep these documents in a secure location for your records.\n\n";
        $description .= "If you have any questions about these asset assignments, please contact your IT department.\n\n";
        $description .= "Best regards,\n";
        $description .= "IT Asset Management Team";

        return $description;
    }

    /**
     * Handle bulk email sending
     */
    public function bulkSendEmail(Request $request)
    {
        // Debug: Log the incoming request
        \Log::info('Bulk email request received', [
            'selected_assets' => $request->selected_assets,
            'recipients' => $request->recipients,
            'subject' => $request->subject
        ]);

        $request->validate([
            'selected_assets' => 'required|array|min:1',
            'selected_assets.*' => 'exists:assets,id',
            'recipients' => 'required|string',
            'subject' => 'nullable|string|max:255',
        ]);

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

        $sentCount = 0;
        $errors = [];

        // Get all selected assets
        $selectedAssets = Asset::whereIn('id', $request->selected_assets)
            ->with(['assignedUser'])
            ->get();

        // Debug logging for selected assets
        \Log::info('Selected assets for bulk email', [
            'selected_asset_ids' => $request->selected_assets,
            'selected_assets_count' => $selectedAssets->count(),
            'selected_assets' => $selectedAssets->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'asset_tag' => $asset->asset_tag,
                    'assigned_to' => $asset->assigned_to,
                ];
            })->toArray()
        ]);

        // Use all selected assets (simplified without assignment filtering)
        $assetsWithForms = $selectedAssets;

        if ($assetsWithForms->isEmpty()) {
            return redirect()->back()->with('error', 'No assets found in the selected assets.');
        }

        // Send personalized email per recipient with only their assigned assets
        foreach ($recipients as $recipient) {
            try {
                // Find user by email to get their assigned assets
                $user = \App\Models\User::where('email', $recipient)->first();
                
                if (!$user) {
                    $errors[] = "User not found for email: {$recipient}";
                    continue;
                }

                // Get only assets that were selected AND assigned to this specific user
                $userAssets = $assetsWithForms->filter(function($asset) use ($user) {
                    return $asset->assigned_to == $user->id;
                });

                if ($userAssets->isEmpty()) {
                    $errors[] = "No assets assigned to user: {$recipient}";
                    continue;
                }

                // Generate personalized email content for this user
                $emailSubject = $request->subject ?: 'Asset Accountability Forms - Confirmed & Signed';
                
                // Generate personalized description based on this user's selected assets only
                $emailDescription = $this->generatePersonalizedBulkEmailDescription($userAssets, $user);

                // Debug logging
                \Log::info('Sending bulk email', [
                    'recipient' => $recipient,
                    'user_assets_count' => $userAssets->count(),
                    'user_assets' => $userAssets->pluck('asset_tag')->toArray(),
                    'has_signed_forms' => $userAssets->map(function($asset) {
                        return [
                            'asset_tag' => $asset->asset_tag,
                        ];
                    })->toArray()
                ]);

                try {
                    \Illuminate\Support\Facades\Mail::to($recipient)
                        ->send(new \App\Mail\BulkSignedAccountabilityFormMail(
                            $userAssets,
                            $emailDescription,
                            $emailSubject
                        ));

                    $sentCount++;
                    
                    // Log successful email sending
                    \Log::info('Email sent successfully', [
                        'recipient' => $recipient,
                        'asset_count' => $userAssets->count(),
                        'assets' => $userAssets->pluck('asset_tag')->toArray()
                    ]);
                    
                } catch (\Exception $mailException) {
                    \Log::error('Email sending failed', [
                        'recipient' => $recipient,
                        'error' => $mailException->getMessage(),
                        'trace' => $mailException->getTraceAsString()
                    ]);
                    $errors[] = "Failed to send email to {$recipient}: " . $mailException->getMessage();
                }

                // Log the email action
                Log::create([
                    'asset_id' => null, // Bulk email, no specific asset
                    'user_id' => auth()->id() ?: 1,
                    'category' => 'Accountability',
                    'event_type' => 'bulk_signed_form_email_sent',
                    'description' => 'Bulk signed form email sent',
                    'remarks' => "Bulk signed form email for " . $userAssets->count() . " assets sent to: {$recipient}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

            } catch (\Exception $e) {
                $errors[] = "Error sending email to {$recipient}: " . $e->getMessage();
            }
        }

        if ($sentCount > 0) {
            $message = "Successfully sent personalized bulk emails to {$sentCount} recipient(s). Each recipient received only their assigned assets.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', $errors);
            }
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'No emails were sent. Errors: ' . implode('; ', $errors));
        }
    }

    /**
     * Generate email description for assignment
     */
    private function generateEmailDescription($assignment)
    {
        $asset = $assignment->asset;
        $user = $assignment->assignedUser;

        return "Dear Employee,\n\n" .
               "This is to confirm that the signed accountability form for asset {$asset->asset_tag} has been successfully processed and is now available for your records.\n\n" .
               "Asset Details:\n" .
               "• Asset Tag: {$asset->asset_tag}\n" .
               "• Asset Name: {$asset->name}\n" .
               "• Assigned To: {$user->first_name} {$user->last_name}\n" .
               "• Assigned Date: " . ($assignment->assigned_at ? $assignment->assigned_at->format('F d, Y') : 'Not specified') . "\n\n" .
               "The signed form is attached to this email. Please keep this document in a secure location for your records.\n\n" .
               "If you have any questions about this asset assignment, please contact your IT department.\n\n" .
               "Best regards,\n" .
               "IT Asset Management Team";
    }

    /**
     * Start a new bulk upload session
     */
    public function startBulkUploadSession(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'exists:assets,id',
        ]);

        $session = BulkUploadSession::createSession(
            $request->asset_ids,
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'session_id' => $session->session_id,
            'total_files' => $session->total_files,
            'uploaded_count' => $session->uploaded_count,
            'progress_percentage' => $session->getProgressPercentage(),
        ]);
    }

    /**
     * Upload files to an existing session
     */
    public function uploadToSession(Request $request, $sessionId)
    {
        try {
            // Debug file properties
            $fileDebug = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $file) {
                    $fileDebug[] = [
                        'index' => $index,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                        'is_valid' => $file->isValid(),
                        'error' => $file->getError()
                    ];
                }
            }

            \Log::info('Upload to session called', [
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
                'files_count' => $request->hasFile('files') ? count($request->file('files')) : 0,
                'file_debug' => $fileDebug
            ]);

            $session = BulkUploadSession::where('session_id', $sessionId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

        if ($session->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'This upload session is already completed.',
            ], 400);
        }

        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|max:10240', // Temporarily removed mimes:pdf to debug
            'asset_indices' => 'required|array|min:1',
            'asset_indices.*' => 'integer|min:0|max:' . (count($session->asset_ids) - 1),
        ]);

        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('files') as $index => $file) {
            $assetIndex = $request->asset_indices[$index] ?? null;
            
            if ($assetIndex === null || !isset($session->asset_ids[$assetIndex])) {
                $errors[] = "Invalid asset index for file: {$file->getClientOriginalName()}";
                continue;
            }

            $assetId = $session->asset_ids[$assetIndex];
            
            // Check if this asset already has a file uploaded
            $alreadyUploaded = collect($session->uploaded_files)
                ->pluck('asset_id')
                ->contains($assetId);

            if ($alreadyUploaded) {
                $errors[] = "File already uploaded for asset index {$assetIndex}";
                continue;
            }

            try {
                $asset = Asset::findOrFail($assetId);

                // Generate unique filename
                $timestamp = time() + $index;
                $filename = $asset->asset_tag . '_' . $timestamp . '.pdf';
                $filePath = 'signed_forms/' . $filename;

                // Store file
                $file->storeAs('public/signed_forms', $filename);

                // Add to session
                $session->addUploadedFile($assetId, [
                    'filename' => $filename,
                    'filepath' => $filePath,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);

                $uploadedCount++;

                // Log the upload action
                Log::create([
                    'asset_id' => $assetId,
                    'user_id' => auth()->id() ?: 1,
                    'category' => 'Accountability Form',
                    'event_type' => 'bulk_upload',
                    'description' => 'Bulk upload of signed form',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'remarks' => "Bulk upload session: {$sessionId}, File: {$file->getClientOriginalName()}",
                    'action_details' => [
                        'session_id' => $sessionId,
                        'filename' => $filename,
                        'filepath' => $filePath,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                    ],
                    'action_timestamp' => now(),
                ]);

            } catch (\Exception $e) {
                $errors[] = "Error uploading {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        // Refresh session data
        $session->refresh();

        \Log::info('Upload to session completed', [
            'session_id' => $sessionId,
            'uploaded_count' => $uploadedCount,
            'total_uploaded' => $session->uploaded_count,
            'errors' => $errors
        ]);

            return response()->json([
                'success' => true,
                'uploaded_count' => $uploadedCount,
                'total_uploaded' => $session->uploaded_count,
                'total_files' => $session->total_files,
                'progress_percentage' => $session->getProgressPercentage(),
                'is_completed' => $session->isCompleted(),
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            \Log::error('Upload to session failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get session status
     */
    public function getSessionStatus($sessionId)
    {
        $session = BulkUploadSession::where('session_id', $sessionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'session_id' => $session->session_id,
            'total_files' => $session->total_files,
            'uploaded_count' => $session->uploaded_count,
            'progress_percentage' => $session->getProgressPercentage(),
            'status' => $session->status,
            'is_completed' => $session->isCompleted(),
            'uploaded_files' => $session->uploaded_files,
            'pending_files' => $session->pending_files,
            'last_activity' => $session->last_activity_at,
        ]);
    }

    /**
     * Resume a bulk upload session
     */
    public function resumeBulkUpload($sessionId)
    {
        $session = BulkUploadSession::where('session_id', $sessionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($session->isCompleted()) {
            return redirect()->route('accountability.index')
                ->with('success', 'This upload session is already completed.');
        }

        // Get assets for this session
        $assets = Asset::whereIn('id', $session->asset_ids)
            ->with(['assignedUser', 'category', 'vendor'])
            ->orderBy('asset_tag')
            ->get();

        return view('accountability.bulk-upload-resume', compact('session', 'assets'));
    }
}
