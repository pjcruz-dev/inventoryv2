<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AssetAssignmentConfirmation;

class AssetConfirmationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $confirmation;
    public $action;
    public $details;

    /**
     * Create a new message instance.
     */
    public function __construct(AssetAssignmentConfirmation $confirmation, string $action, array $details = [])
    {
        $this->confirmation = $confirmation;
        $this->action = $action; // 'confirmed' or 'declined'
        $this->details = $details;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->action === 'confirmed' 
            ? 'Asset Assignment Confirmed - ' . $this->confirmation->asset->name
            : 'Asset Assignment Declined - ' . $this->confirmation->asset->name;

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
            view: 'emails.asset-confirmation-notification',
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
