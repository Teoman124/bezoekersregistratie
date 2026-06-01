<?php

use App\Models\MailboxMessage;
use App\Models\User;
use App\Services\MailtrapApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'mail.from.address' => 'noreplyvoorschool1197@gmail.com',
        'mail.from.name' => 'Bezoekersregistratie',
    ]);
});

test('mailtrap api service sends an email and stores it in the mailbox', function (): void {
    $recipient = User::factory()->create([
        'email' => 'visitor@example.com',
        'role' => 'visitor',
    ]);

    config(['mail.mailtrap_api_token' => 'fake-token']);

    Http::preventStrayRequests();
    Http::fake([
        'send.api.mailtrap.io/*' => Http::response(['success' => true], 200),
    ]);

    $service = new MailtrapApiService;

    $result = $service->send(
        toEmail: 'visitor@example.com',
        subject: 'API Test - Bezoekersregistratie',
        text: 'Dit is een test mail verstuurd via de Mailtrap API service!'
    );

    expect($result)->toBeTrue();

    $this->assertDatabaseHas('mailbox_messages', [
        'recipient_id' => $recipient->id,
        'title' => 'API Test - Bezoekersregistratie',
        'message' => 'Dit is een test mail verstuurd via de Mailtrap API service!',
        'read' => false,
    ]);
});

test('mailtrap api service stores a mailbox message when it falls back to smtp', function (): void {
    $recipient = User::factory()->create([
        'email' => 'visitor2@example.com',
        'role' => 'visitor',
    ]);

    config(['mail.mailtrap_api_token' => null]);

    Http::preventStrayRequests();
    Http::fake();
    Mail::fake();

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

test('mailtrap api service supports custom sender details', function (): void {
    $recipient = User::factory()->create([
        'email' => 'visitor3@example.com',
        'role' => 'visitor',
    ]);

    config(['mail.mailtrap_api_token' => 'fake-token']);

    Http::preventStrayRequests();
    Http::fake([
        'send.api.mailtrap.io/*' => Http::response(['success' => true], 200),
    ]);

    $service = new MailtrapApiService;

    $result = $service->send(
        toEmail: 'visitor3@example.com',
        subject: 'Custom From Test',
        text: 'Test met custom sender',
        fromEmail: 'custom@bezoekersregistratie.test',
        fromName: 'Bezoekersregistratie Support'
    );

    expect($result)->toBeTrue();

    $this->assertDatabaseHas('mailbox_messages', [
        'recipient_id' => $recipient->id,
        'title' => 'Custom From Test',
        'message' => 'Test met custom sender',
        'read' => false,
    ]);
});
