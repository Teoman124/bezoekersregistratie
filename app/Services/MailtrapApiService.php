<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MailtrapApiService
{
    protected string $endpoint = 'https://send.api.mailtrap.io/api/send';

    public function send(string $toEmail, string $subject, string $text, ?string $html = null, ?string $fromEmail = null, ?string $fromName = null): bool
    {
        $token = config('mail.mailtrap_api_token');

        if (empty($token)) {
            return false;
        }

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

        $response = Http::withToken($token)
            ->acceptJson()
            ->post($this->endpoint, $payload);

        return $response->successful();
    }
}
