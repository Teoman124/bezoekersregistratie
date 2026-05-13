<?php

// User Story #3: Realtime overzicht bekijken
// Als receptionist wil ik een lijst zien van alle aanwezige bezoekers,
// zodat ik weet wie er in het pand is.
//
// Acceptatiecriteria:
// ✅ Alleen actieve bezoekers tonen
// ✅ Overzicht toont de naam van de bezoeker, bedrijf en contactpersoon

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

function visitOverviewHostEmployee(): Employee
{
    $department = Department::create(['name' => 'IT']);

    $hostUser = User::factory()->create(['name' => 'Henk Medewerker']);

    return Employee::create([
        'user_id' => $hostUser->id,
        'department_id' => $department->id,
        'function' => 'Developer',
    ]);
}

function visitOverviewVisitor(string $name = 'Jan Bezoeker'): Visitor
{
    $visitorUser = User::factory()->create(['name' => $name]);

    return Visitor::create(['user_id' => $visitorUser->id]);
}

// ✅ SLAAGT — actieve scope geeft alleen ingecheckte bezoekers terug
test('actieve scope toont alleen ingecheckte bezoekers', function () {
    $employee = visitOverviewHostEmployee();
    $visitor = visitOverviewVisitor();

    Visit::create([
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Overleg',
        'expected_arrival_time' => now()->subHour(),
        'check_in_time' => now()->subHour(),
        'check_out_time' => null,
    ]);

    $checkedOutUser = User::factory()->create(['name' => 'Piet Vertrokken']);
    $checkedOutVisitor = Visitor::create(['user_id' => $checkedOutUser->id]);
    Visit::create([
        'visitor_id' => $checkedOutVisitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Rondleiding',
        'expected_arrival_time' => now()->subHours(3),
        'check_in_time' => now()->subHours(3),
        'check_out_time' => now()->subHour(),
    ]);

    $actieveBezoeken = Visit::active()->get();

    expect($actieveBezoeken)->toHaveCount(1);
    expect($actieveBezoeken->first()->visitor->user->name)->toBe('Jan Bezoeker');
});

// ✅ SLAAGT — actieve bezoeken bevatten bezoeker en contactpersoon
test('actieve bezoekgegevens bevatten bezoeker en contactpersoon', function () {
    $employee = visitOverviewHostEmployee();
    $visitor = visitOverviewVisitor();

    $visit = Visit::create([
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Vergadering',
        'expected_arrival_time' => now()->subHour(),
        'check_in_time' => now()->subHour(),
        'check_out_time' => null,
    ]);

    expect($visit->employee->user->name)->toBe('Henk Medewerker');
    expect($visit->visitor->user->name)->toBe('Jan Bezoeker');
});
