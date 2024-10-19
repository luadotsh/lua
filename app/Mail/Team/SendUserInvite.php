<?php

declare(strict_types=1);

namespace App\Mail\Team;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

use App\Models\Invite;
use App\Models\Workspace;

class SendUserInvite extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public Workspace $workspace,
        public Invite $invite
    ) {
        $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: "You are invited to join the {$this->workspace->name} team.",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.invite',
            with: [
                'title' => "You are invited to join the {$this->workspace->name} team.",
                'workspace' => $this->workspace,
                'url' => route('auth.invites.show', $this->invite->id),
            ],
        );
    }
}
