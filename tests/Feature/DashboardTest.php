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

    public function test_visitors_without_company_name_see_the_company_prompt(): void
    {
        $user = User::factory()->create([
            'role' => 'visitor',
        ]);

        $user->visitor()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Van welk bedrijf kom je?');
    }

    public function test_visitors_with_company_name_do_not_see_the_company_prompt(): void
    {
        $user = User::factory()->create([
            'role' => 'visitor',
        ]);

        $user->visitor()->create([
            'company_name' => 'ACME B.V.',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Van welk bedrijf kom je?');
    }

    public function test_visitors_can_skip_the_company_prompt_for_the_current_session(): void
    {
        $user = User::factory()->create([
            'role' => 'visitor',
        ]);

        $user->visitor()->create();

        $this->actingAs($user)
            ->post(route('visitor.company-info.skip'))
            ->assertRedirect(route('dashboard', absolute: false));

        $this->get('/dashboard')->assertOk()->assertDontSee('Van welk bedrijf kom je?');
    }
}
