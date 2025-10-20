<?php

namespace App\Mail;

use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceProgressNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $maintenance;
    public $assignedUser;
    public $processedBy;
    public $action; // 'created' or 'updated'

    /**
     * Create a new message instance.
     */
    public function __construct(Maintenance $maintenance, User $assignedUser = null, User $processedBy, string $action = 'created')
    {
        $this->maintenance = $maintenance;
        $this->assignedUser = $assignedUser;
        $this->processedBy = $processedBy;
        $this->action = $action;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->action === 'created' 
            ? 'New Maintenance Record Created - ' . $this->maintenance->asset->asset_tag
            : 'Maintenance Record Updated - ' . $this->maintenance->asset->asset_tag;
            
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
            view: 'emails.maintenance-progress-notification',
            with: [
                'maintenance' => $this->maintenance,
                'assignedUser' => $this->assignedUser,
                'processedBy' => $this->processedBy,
                'action' => $this->action,
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