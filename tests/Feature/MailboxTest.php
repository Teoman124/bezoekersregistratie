<?php

use App\Models\MailboxMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('mailbox inbox shows only received messages', function () {
    $user = User::factory()->create();
    $sender = User::factory()->create();

    MailboxMessage::create([
        'recipient_id' => $user->id,
        'sender_id' => $sender->id,
        'title' => 'Welkom',
        'message' => 'Dit bericht hoort in de inbox.',
        'read' => false,
    ]);

    MailboxMessage::create([
        'recipient_id' => $sender->id,
        'sender_id' => $user->id,
        'title' => 'Verzonden bericht',
        'message' => 'Dit bericht hoort niet in de inbox.',
        'read' => false,
    ]);

    $response = $this->actingAs($user)->get(route('mailbox.index'));

    $response->assertOk();
    $response->assertSee('Inbox');
    $response->assertSee('Welkom');
    $response->assertDontSee('Verzonden bericht');
});

test('mailbox sent folder shows only sent messages', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();

    MailboxMessage::create([
        'recipient_id' => $recipient->id,
        'sender_id' => $user->id,
        'title' => 'Verzonden bericht',
        'message' => 'Dit bericht hoort in verzonden.',
        'read' => false,
    ]);

    MailboxMessage::create([
        'recipient_id' => $user->id,
        'sender_id' => $recipient->id,
        'title' => 'Ontvangen bericht',
        'message' => 'Dit bericht hoort niet in verzonden.',
        'read' => false,
    ]);

    $response = $this->actingAs($user)->get(route('mailbox.index', ['folder' => 'sent']));

    $response->assertOk();
    $response->assertSee('Verzonden');
    $response->assertSee('Verzonden bericht');
    $response->assertDontSee('Ontvangen bericht');
});

test('mailbox create page opens for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('mailbox.create'));

    $response->assertStatus(200);
});

test('mailbox is accessible to visitors', function () {
    $visitor = User::factory()->create(['role' => 'visitor']);

    $response = $this->actingAs($visitor)->get(route('mailbox.index'));

    $response->assertOk();
    $response->assertSee('Mailbox');
});

test('visitor can see mailbox link in sidebar', function () {
    $visitor = User::factory()->create(['role' => 'visitor']);

    $response = $this->actingAs($visitor)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Mailbox');
});

test('mailbox store creates a message for the selected user', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $response = $this->actingAs($sender)->post(route('mailbox.store'), [
        'recipient_id' => $recipient->id,
        'subject' => 'Nieuwe melding',
        'body' => 'Hoi, dit is een testbericht.',
    ]);

    $response->assertRedirect(route('mailbox.index', ['folder' => 'sent']));

    $this->assertDatabaseHas('mailbox_messages', [
        'recipient_id' => $recipient->id,
        'sender_id' => $sender->id,
        'title' => 'Nieuwe melding',
        'message' => 'Hoi, dit is een testbericht.',
        'read' => false,
    ]);
});
