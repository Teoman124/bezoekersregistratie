<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEditAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_users_but_cannot_edit_them(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $targetUser = User::factory()->create(['role' => 'visitor']);

        $this->actingAs($employee)->get(route('users.index'))->assertOk();
        $this->actingAs($employee)->get(route('users.show', $targetUser))->assertOk();
        $this->actingAs($employee)->get(route('users.edit', $targetUser))->assertForbidden();
        $this->actingAs($employee)->put(route('users.update', $targetUser), [
            'name' => 'Nieuwe naam',
            'email' => 'nieuw@example.com',
            'role' => 'visitor',
        ])->assertForbidden();
    }
}
