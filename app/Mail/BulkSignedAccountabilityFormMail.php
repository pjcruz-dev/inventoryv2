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
        
        // Note: Attachment functionality simplified after AssetAssignment removal
        // Signed forms would need to be stored differently to be attached here
        
        return $attachments;
    }
}
