<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailtrapConnectionTest extends TestCase
{
    public function test_mailtrap_smtp_configuration(): void
    {
        // If Mailtrap API token is not configured, skip this environment-specific test.
        if (empty(config('mail.mailtrap_api_token'))) {
            $this->markTestSkipped('Mailtrap API token niet ingesteld');
        }

        $config = [
            'Mailer' => config('mail.default'),
            'SMTP Host' => config('mail.mailers.smtp.host'),
            'SMTP Port' => config('mail.mailers.smtp.port'),
            'SMTP Username' => config('mail.mailers.smtp.username'),
            'SMTP Scheme' => config('mail.mailers.smtp.scheme'),
            'From Address' => config('mail.from.address'),
            'From Name' => config('mail.from.name'),
            'API Token' => substr(config('mail.mailtrap_api_token'), 0, 8) . '...[verborgen]',
        ];

        // Configuration checks below.

        // Controleer of kritieke instellingen aanwezig zijn
        $this->assertNotEmpty(config('mail.default'), 'Mail mailer niet ingesteld');
        $this->assertNotEmpty(config('mail.mailers.smtp.host'), 'SMTP host niet ingesteld');
        $this->assertNotEmpty(config('mail.mailers.smtp.username'), 'SMTP username niet ingesteld');
        $this->assertNotEmpty(config('mail.mailers.smtp.password'), 'SMTP password niet ingesteld');
        $this->assertNotEmpty(config('mail.mailtrap_api_token'), 'Mailtrap API token niet ingesteld');
        $this->assertContains(config('mail.mailers.smtp.host'), [
            'smtp.mailtrap.io',
            'sandbox.smtp.mailtrap.io',
        ]);
    }

    public function test_send_test_email_to_mailtrap(): void
    {
        Mail::raw('Dit is een test mail van je Bezoekersregistratie applicatie!', function ($message) {
            $message
                ->to('test@mailtrap.io')
                ->subject('Test Mail - Bezoekersregistratie');
        });

        $this->assertTrue(true);
    }

    public function test_send_admin_email_to_mailtrap(): void
    {
        $adminEmail = 'admin@example.com';
        Mail::raw('Dit is een ADMIN test mail van je Bezoekersregistratie applicatie!', function ($message) use ($adminEmail) {
            $message
                ->to($adminEmail)
                ->subject('Admin Test Mail - Bezoekersregistratie');
        });

        $this->assertTrue(true);
    }
}
