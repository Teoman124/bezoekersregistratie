<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->get('/dashboard')->assertStatus(200);
    }

    public function test_visitors_are_forbidden_from_viewing_the_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'visitor',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_homepage_is_accessible_for_visitors(): void
    {
        $user = User::factory()->create([
            'role' => 'visitor',
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('Bezoekersregistratie');
    }

    public function test_visitors_can_skip_the_company_prompt_for_the_current_session(): void
    {
        $user = User::factory()->create([
            'role' => 'visitor',
        ]);

        $user->visitor()->create();

        $this->actingAs($user)
            ->post(route('visitor.company-info.skip'))
            ->assertRedirect(route('visits.myvisits', absolute: false));

        $this->get('/')
            ->assertOk()
            ->assertSee('Bezoekersregistratie');
    }
}
