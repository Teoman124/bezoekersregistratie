<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitIndexButtonsTest extends TestCase
{
    use RefreshDatabase;

    public function test_visit_index_shows_export_and_history_buttons_for_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('visits.index'));

        $response->assertOk();
        $response->assertSee('Export CSV');
        $response->assertSee('Bezoekgeschiedenis');
    }
}
