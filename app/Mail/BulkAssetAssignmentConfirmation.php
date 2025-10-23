<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class BulkAssetAssignmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $assetsData;
    public $user;
    public $assignedDate;
    public $notes;

    /**
     * Create a new message instance.
     */
    public function __construct($assetsData, User $user, $assignedDate, $notes = null)
    {
        $this->assetsData = $assetsData; // Array of ['asset' => Asset, 'confirmation_token' => string]
        $this->user = $user;
        $this->assignedDate = $assignedDate;
        $this->notes = $notes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $assetCount = count($this->assetsData);
        $subject = $assetCount >= 2 
            ? "Multiple Asset Assignment Confirmation"
            : "Asset Assignment Confirmation Required";
            
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bulk-asset-assignment-confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

