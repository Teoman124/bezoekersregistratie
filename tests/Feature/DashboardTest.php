<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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

    public function test_dashboard_shows_visit_statistics_for_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $department = Department::create(['name' => 'Algemeen']);

        $employeeUser = User::factory()->create(['role' => 'employee', 'name' => 'Peter']);
        $employee = Employee::create([
            'user_id' => $employeeUser->id,
            'department_id' => $department->id,
            'function' => 'Receptie',
        ]);

        $visitorUser = User::factory()->create(['role' => 'visitor', 'name' => 'Sofie']);
        $visitor = Visitor::create(['user_id' => $visitorUser->id]);

        Visit::create([
            'visitor_id' => $visitor->id,
            'host_employee_id' => $employee->id,
            'expected_arrival_time' => Carbon::now()->subDay(),
            'expected_departure_time' => Carbon::now()->subDay()->addHour(),
            'check_in_time' => Carbon::now()->subDay(),
            'check_out_time' => Carbon::now()->subDay()->addMinutes(90),
        ]);

        $response = $this->actingAs($admin)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('Bezoekersinzichten')
            ->assertSee('Drukste dag')
            ->assertSee('Gemiddelde duur')
            ->assertSee('Top-medewerkers')
            ->assertSee('Peter')
            ->assertSee('Bezoeken per dag (afgelopen week)')
            ->assertSee('Bezoeken per week (afgelopen 8 weken)')
            ->assertSee('Drukste afdelingen')
            ->assertSee('Verblijfsduur statistieken');

        // Verify chart data is passed to the view
        $this->assertTrue(isset($response->original->getData()['chartsData']));
        $chartsData = $response->original->getData()['chartsData'];
        $this->assertArrayHasKey('visitsPerDay', $chartsData);
        $this->assertArrayHasKey('visitsPerWeek', $chartsData);
        $this->assertArrayHasKey('busiestDepartments', $chartsData);
        $this->assertArrayHasKey('stayDurationStats', $chartsData);
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
