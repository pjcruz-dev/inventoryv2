<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\AssetAssignment;

class SignedAccountabilityFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $assignment;
    public $description;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct(AssetAssignment $assignment, $description = '', $subject = '')
    {
        $this->assignment = $assignment;
        $this->description = $description;
        $this->subject = $subject ?: "Signed Accountability Form - {$assignment->asset->asset_tag}";
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
            view: 'emails.signed-accountability-form',
            with: [
                'assignment' => $this->assignment,
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
        
        if ($this->assignment->signed_form_path) {
            $filePath = storage_path('app/public/' . $this->assignment->signed_form_path);
            if (file_exists($filePath)) {
                $attachments[] = Attachment::fromPath($filePath)
                    ->as("signed_accountability_form_{$this->assignment->asset->asset_tag}.pdf");
            }
        }
        
        return $attachments;
    }
}