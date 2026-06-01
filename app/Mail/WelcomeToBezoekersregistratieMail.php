<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeToBezoekersregistratieMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account successfully made',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.welcome-to-bezoekersregistratie',
            with: [
                'name' => $this->user->name,
            ],
        );
    }
}
