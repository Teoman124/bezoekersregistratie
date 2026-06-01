<?php

use App\Models\MailboxMessage;
use App\Models\User;
use App\Services\MailtrapApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('mailtrap api send stores a mailbox message for the recipient user', function () {
    $recipient = User::factory()->create([
        'email' => 'visitor@example.com',
        'role' => 'visitor',
    ]);

    Http::fake([
        'send.api.mailtrap.io/*' => Http::response(['success' => true], 200),
    ]);

    config([
        'mail.mailtrap_api_token' => 'fake-token',
        'mail.from.address' => 'noreplyvoorschool1197@gmail.com',
        'mail.from.name' => 'Bezoekersregistratie',
    ]);

    $service = new MailtrapApiService;

    $result = $service->send(
        toEmail: 'visitor@example.com',
        subject: 'Afspraak bevestiging',
        text: 'Je afspraak is succesvol ingepland.'
    );

    expect($result)->toBeTrue();

    $this->assertDatabaseHas('mailbox_messages', [
        'recipient_id' => $recipient->id,
        'title' => 'Afspraak bevestiging',
        'message' => 'Je afspraak is succesvol ingepland.',
        'read' => false,
    ]);
});

test('mailtrap service falls back to smtp and still stores mailbox message when api token is missing', function () {
    $recipient = User::factory()->create([
        'email' => 'visitor2@example.com',
        'role' => 'visitor',
    ]);

    config([
        'mail.default' => 'array',
        'mail.mailtrap_api_token' => null,
        'mail.from.address' => 'noreplyvoorschool1197@gmail.com',
        'mail.from.name' => 'Bezoekersregistratie',
    ]);

    $service = new MailtrapApiService;

    $result = $service->send(
        toEmail: 'visitor2@example.com',
        subject: 'Herinnering bezoek',
        text: 'Je bezoek start over 5 minuten.'
    );

    expect($result)->toBeTrue();

    $this->assertDatabaseHas('mailbox_messages', [
        'recipient_id' => $recipient->id,
        'title' => 'Herinnering bezoek',
        'message' => 'Je bezoek start over 5 minuten.',
        'read' => false,
    ]);

    expect(MailboxMessage::count())->toBe(1);
});
