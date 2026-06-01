<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationEditAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_notifications_but_cannot_edit_them(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $targetUser = User::factory()->create(['role' => 'visitor']);

        $notification = Notification::create([
            'user_id' => $targetUser->id,
            'title' => 'Test notificatie',
            'message' => 'Test bericht',
            'read' => false,
        ]);

        $this->actingAs($employee)->get(route('notifications.index'))->assertOk();
        $this->actingAs($employee)->get(route('notifications.show', $notification))->assertOk();
        $this->actingAs($employee)->get(route('notifications.edit', $notification))->assertForbidden();
        $this->actingAs($employee)->put(route('notifications.update', $notification), [
            'user_id' => $targetUser->id,
            'title' => 'Aangepast',
            'message' => 'Aangepast bericht',
            'read' => true,
        ])->assertForbidden();
    }
}
