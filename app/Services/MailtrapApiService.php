<?php

namespace App\Services;

use App\Models\MailboxMessage;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MailtrapApiService
{
    protected string $endpoint = 'https://send.api.mailtrap.io/api/send';

    public function send(string $toEmail, string $subject, string $text, ?string $html = null, ?string $fromEmail = null, ?string $fromName = null): bool
    {
        $token = config('mail.mailtrap_api_token');

        $payload = [
            'from' => [
                'email' => $fromEmail ?? config('mail.from.address'),
                'name' => $fromName ?? config('mail.from.name'),
            ],
            'to' => [
                [
                    'email' => $toEmail,
                ],
            ],
            'subject' => $subject,
            'text' => $text,
        ];

        if ($html !== null) {
            $payload['html'] = $html;
        }

        if (! empty($token)) {
            $response = Http::withHeaders([
                'Api-Token' => $token,
            ])
                ->acceptJson()
                ->post($this->endpoint, $payload);

            if ($response->successful()) {
                $this->storeInMailbox($toEmail, $subject, $text, $fromEmail);

                return true;
            }
        }

        // Fallback: probeer via SMTP (Laravel Mail) als de API faalt
        try {
            Mail::raw($text, function ($message) use ($toEmail, $subject, $fromEmail, $fromName) {
                $message->to($toEmail)
                    ->from($fromEmail ?? config('mail.from.address'), $fromName ?? config('mail.from.name'))
                    ->subject($subject);
            });

            $this->storeInMailbox($toEmail, $subject, $text, $fromEmail);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function storeInMailbox(string $toEmail, string $subject, string $text, ?string $fromEmail = null): void
    {
        $recipient = User::where('email', $toEmail)->first();

        if (! $recipient) {
            return;
        }

        $sender = null;

        if ($fromEmail) {
            $sender = User::where('email', $fromEmail)->first();
        }

        MailboxMessage::create([
            'recipient_id' => $recipient->id,
            'sender_id' => $sender?->id,
            'title' => $subject,
            'message' => $text,
            'read' => false,
        ]);
    }
}
