<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

function activeVisitorsHostEmployee(): Employee
{
    $department = Department::create(['name' => 'IT']);

    $hostUser = User::factory()->create(['name' => 'Henk Medewerker']);

    return Employee::create([
        'user_id' => $hostUser->id,
        'department_id' => $department->id,
        'function' => 'Developer',
    ]);
}

test('actieve bezoekerspagina toont alleen ingecheckte bezoekers', function () {
    $this->actingAs(User::factory()->create([
        'email_verified_at' => now(),
        'role' => 'employee',
    ]));

    $employee = activeVisitorsHostEmployee();

    $activeUser = User::factory()->create(['name' => 'Jan Bezoeker']);
    $activeVisitor = Visitor::create(['user_id' => $activeUser->id]);

    Visit::create([
        'visitor_id' => $activeVisitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Overleg',
        'expected_arrival_time' => now()->subHour(),
        'check_in_time' => now()->subHour(),
        'check_out_time' => null,
    ]);

    $plannedUser = User::factory()->create(['name' => 'Piet Gepland']);
    $plannedVisitor = Visitor::create(['user_id' => $plannedUser->id]);

    Visit::create([
        'visitor_id' => $plannedVisitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Rondleiding',
        'expected_arrival_time' => now()->addHour(),
        'check_in_time' => null,
        'check_out_time' => null,
    ]);

    $response = $this->get(route('visits.active'));

    $response->assertSuccessful();
    $response->assertSee('Jan Bezoeker');
    $response->assertSee('Henk Medewerker');
    $response->assertDontSee('Piet Gepland');
});
