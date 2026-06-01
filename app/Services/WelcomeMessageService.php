<?php

namespace App\Services;

use App\Mail\WelcomeToBezoekersregistratieMail;
use App\Models\MailboxMessage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class WelcomeMessageService
{
    public function send(User $user, ?User $sender = null): void
    {
        Mail::to($user->email)->send(new WelcomeToBezoekersregistratieMail($user));

        MailboxMessage::create([
            'recipient_id' => $user->id,
            'sender_id' => $sender?->id,
            'title' => 'Account successfully made',
            'message' => "Welcome to Bezoekersregistratie\n{$user->name}\n\nBezoekersregistratie team",
            'read' => false,
        ]);
    }
}
