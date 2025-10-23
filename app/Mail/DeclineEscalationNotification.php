<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use App\Models\User;

class DeclineEscalationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $unresolvedDeclines;
    public $daysThreshold;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $unresolvedDeclines, int $daysThreshold, User $recipient)
    {
        $this->unresolvedDeclines = $unresolvedDeclines;
        $this->daysThreshold = $daysThreshold;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 URGENT: ' . $this->unresolvedDeclines->count() . ' Unresolved High-Severity Declined Assets',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.decline-escalation-notification',
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

