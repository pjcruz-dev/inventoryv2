<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Asset;

class BulkSignedAccountabilityFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $assets;
    public $description;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($assets, $description = '', $subject = '')
    {
        $this->assets = $assets;
        $this->description = $description;
        $this->subject = $subject ?: 'Asset Accountability Forms - Confirmed & Signed';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bulk-signed-accountability-form',
            with: [
                'assets' => $this->assets,
                'description' => $this->description,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];
        
        foreach ($this->assets as $asset) {
            $assignment = $asset->currentAssignment;
            if ($assignment && $assignment->signed_form_path) {
                // Try different path constructions
                $filePath1 = storage_path('app/public/' . $assignment->signed_form_path);
                $filePath2 = storage_path('app/public/public/' . $assignment->signed_form_path);
                $filePath3 = storage_path('app/' . $assignment->signed_form_path);
                
                $filePath = null;
                if (file_exists($filePath1)) {
                    $filePath = $filePath1;
                } elseif (file_exists($filePath2)) {
                    $filePath = $filePath2;
                } elseif (file_exists($filePath3)) {
                    $filePath = $filePath3;
                } else {
                    // Try to find any file with the asset tag in the signed_forms directory
                    $signedFormsDir = storage_path('app/public/signed_forms/');
                    if (is_dir($signedFormsDir)) {
                        $files = glob($signedFormsDir . '*' . $asset->asset_tag . '*');
                        if (!empty($files)) {
                            $filePath = $files[0]; // Use the first matching file
                            \Log::info('Found file using glob search', [
                                'asset_tag' => $asset->asset_tag,
                                'found_file' => $filePath,
                                'all_matches' => $files
                            ]);
                        } else {
                            \Log::warning('No files found for asset tag', [
                                'asset_tag' => $asset->asset_tag,
                                'search_pattern' => $signedFormsDir . '*' . $asset->asset_tag . '*',
                                'directory_contents' => array_slice(scandir($signedFormsDir), 2) // Skip . and ..
                            ]);
                        }
                    }
                }
                
                if ($filePath) {
                    $attachments[] = Attachment::fromPath($filePath)
                        ->as("signed_accountability_form_{$asset->asset_tag}.pdf");
                } else {
                    // Log for debugging
                    \Log::error('Attachment file not found', [
                        'asset_tag' => $asset->asset_tag,
                        'signed_form_path' => $assignment->signed_form_path,
                        'tried_paths' => [$filePath1, $filePath2, $filePath3],
                        'signed_forms_dir' => $signedFormsDir ?? 'not found'
                    ]);
                }
            }
        }
        
        return $attachments;
    }
}
