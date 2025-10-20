<?php

namespace App\Mail;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $asset;
    public $assignedUser;
    public $processedBy;

    /**
     * Create a new message instance.
     */
    public function __construct(Asset $asset, User $assignedUser = null, User $processedBy)
    {
        $this->asset = $asset;
        $this->assignedUser = $assignedUser;
        $this->processedBy = $processedBy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Asset Sent to Maintenance - ' . $this->asset->asset_tag,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.maintenance-notification',
            with: [
                'asset' => $this->asset,
                'assignedUser' => $this->assignedUser,
                'processedBy' => $this->processedBy,
            ]
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