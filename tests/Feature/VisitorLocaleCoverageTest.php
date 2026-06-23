<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorLocaleCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_auth_pages_render_one_language_switcher(): void
    {
        $this->get('/lang/en');

        $response = $this->get(route('visitor.login'));

        $response->assertOk();
        $response->assertSee('Visitor Sign In');
        $this->assertSame(2, substr_count($response->getContent(), '/lang/'));
    }

    public function test_visitor_mailbox_and_notifications_pages_render_in_english(): void
    {
        $visitor = User::factory()->create(['role' => 'visitor']);

        $this->get('/lang/en');

        $mailboxResponse = $this->actingAs($visitor)->get(route('mailbox.index'));

        $mailboxResponse->assertOk();
        $mailboxResponse->assertSee('Mailbox');
        $mailboxResponse->assertSee('Your inbox is empty.');
        $this->assertSame(2, substr_count($mailboxResponse->getContent(), '/lang/'));

        $notificationsResponse = $this->actingAs($visitor)->get(route('notifications.index'));

        $notificationsResponse->assertOk();
        $notificationsResponse->assertSee('Notifications');
        $notificationsResponse->assertSee('No notifications found.');
        $this->assertSame(2, substr_count($notificationsResponse->getContent(), '/lang/'));
    }

    public function test_visitor_my_visits_page_renders_one_language_switcher_in_english(): void
    {
        $visitor = User::factory()->create(['role' => 'visitor']);

        $this->get('/lang/en');

        $response = $this->actingAs($visitor)->get(route('visits.myvisits'));

        $response->assertOk();
        $response->assertSee('My visits');
        $this->assertSame(2, substr_count($response->getContent(), '/lang/'));
    }
}
