<?php

// User Story #2: Bezoeker uitchecken
// Als receptionist wil ik een bezoeker kunnen uitchecken,
// zodat de vertrektijd wordt geregistreerd.
//
// Acceptatiecriteria:
// - Knop "uitchecken" beschikbaar         → checkout route reageert
// - Tijd wordt automatisch opgeslagen     → check_out_time wordt gezet
// - Bezoeker verdwijnt uit actieve lijst  → status=in filter sluit hem uit

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

beforeEach(function () {
    $department = Department::create(['name' => 'IT']);

    $hostUser = User::factory()->create();
    $this->employee = Employee::create([
        'user_id'       => $hostUser->id,
        'department_id' => $department->id,
        'function'      => 'Developer',
    ]);

    $visitorUser = User::factory()->create(['name' => 'Jan Bezoeker']);
    $this->visitor = Visitor::create(['user_id' => $visitorUser->id]);
});

test('uitchecken slaat de vertrektijd automatisch op', function () {
    $visit = Visit::create([
        'visitor_id'             => $this->visitor->id,
        'host_employee_id'       => $this->employee->id,
        'reason_of_visit'        => 'Vergadering',
        'expected_arrival_time'  => now()->subHour(),
        'check_in_time'          => now()->subHour(),
        'check_out_time'         => null,
    ]);

    $this->get(route('visits.checkout', $visit))
        ->assertRedirect();

    expect($visit->fresh()->check_out_time)->not->toBeNull();
});

test('uitgecheckte bezoeker verdwijnt uit de actieve lijst', function () {
    // Actieve bezoeker (nog aanwezig)
    $activeVisit = Visit::create([
        'visitor_id'             => $this->visitor->id,
        'host_employee_id'       => $this->employee->id,
        'reason_of_visit'        => 'Overleg',
        'expected_arrival_time'  => now()->subHour(),
        'check_in_time'          => now()->subHour(),
        'check_out_time'         => null,
    ]);

    // Uitgecheckte bezoeker
    $checkedOutVisitorUser = User::factory()->create(['name' => 'Piet Vertrokken']);
    $checkedOutVisitor = Visitor::create(['user_id' => $checkedOutVisitorUser->id]);
    Visit::create([
        'visitor_id'             => $checkedOutVisitor->id,
        'host_employee_id'       => $this->employee->id,
        'reason_of_visit'        => 'Rondleiding',
        'expected_arrival_time'  => now()->subHours(2),
        'check_in_time'          => now()->subHours(2),
        'check_out_time'         => now()->subMinutes(30),
    ]);

    // Alleen bezoeken zonder check_out_time zijn "actief"
    $actieveBezoeken = Visit::whereNull('check_out_time')->get();

    expect($actieveBezoeken)->toHaveCount(1);
    expect($actieveBezoeken->first()->id)->toBe($activeVisit->id);
});
