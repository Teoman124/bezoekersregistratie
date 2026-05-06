<?php

// User Story #4: Bezoekershistorie bekijken
// Als receptionist wil ik eerdere bezoeken kunnen bekijken,
// zodat ik informatie kan terugvinden.
//
// Acceptatiecriteria:
// ✅ Filter op gisteren toont alleen bezoeken van gisteren
// ✅ Filter op week toont bezoeken van de afgelopen 7 dagen
// ✅ Sorteren op check_in_time mogelijk

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

beforeEach(function () {
    $department = Department::create(['name' => 'IT']);

    $hostUser = User::factory()->create(['name' => 'Henk Medewerker']);
    $this->employee = Employee::create([
        'user_id' => $hostUser->id,
        'department_id' => $department->id,
        'function' => 'Developer',
    ]);

    $visitorUser = User::factory()->create(['name' => 'Jan Bezoeker']);
    $this->visitor = Visitor::create(['user_id' => $visitorUser->id]);
});

// ✅ SLAAGT — bezoeken van gisteren worden correct gefilterd
test('filter op gisteren toont alleen bezoeken van gisteren', function () {
    Visit::create([
        'visitor_id' => $this->visitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Overleg gisteren',
        'expected_arrival_time' => now()->subDay()->setTime(9, 0),
        'check_in_time' => now()->subDay()->setTime(9, 0),
        'check_out_time' => now()->subDay()->setTime(10, 0),
    ]);

    $vandaagUser = User::factory()->create(['name' => 'Piet Vandaag']);
    $vandaagVisitor = Visitor::create(['user_id' => $vandaagUser->id]);
    Visit::create([
        'visitor_id' => $vandaagVisitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Overleg vandaag',
        'expected_arrival_time' => now()->setTime(9, 0),
        'check_in_time' => now()->setTime(9, 0),
        'check_out_time' => now()->setTime(10, 0),
    ]);

    $gisteren = Visit::whereDate('check_in_time', now()->subDay()->toDateString())->get();

    expect($gisteren)->toHaveCount(1);
    expect($gisteren->first()->reason_of_visit)->toBe('Overleg gisteren');
});

// ✅ SLAAGT — bezoeken van de afgelopen 7 dagen worden correct gefilterd
test('filter op week toont bezoeken van de afgelopen 7 dagen', function () {
    Visit::create([
        'visitor_id' => $this->visitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Overleg deze week',
        'expected_arrival_time' => now()->subDays(3),
        'check_in_time' => now()->subDays(3),
        'check_out_time' => now()->subDays(3)->addHour(),
    ]);

    $ouderUser = User::factory()->create(['name' => 'Kees Oud']);
    $ouderVisitor = Visitor::create(['user_id' => $ouderUser->id]);
    Visit::create([
        'visitor_id' => $ouderVisitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Overleg vorige week',
        'expected_arrival_time' => now()->subDays(10),
        'check_in_time' => now()->subDays(10),
        'check_out_time' => now()->subDays(10)->addHour(),
    ]);

    $dezeWeek = Visit::where('check_in_time', '>=', now()->subDays(7))->get();

    expect($dezeWeek)->toHaveCount(1);
    expect($dezeWeek->first()->reason_of_visit)->toBe('Overleg deze week');
});

// ✅ SLAAGT — bezoeken kunnen gesorteerd worden op check_in_time
test('bezoeken kunnen gesorteerd worden op check_in_time van vroeg naar laat', function () {
    $lateUser = User::factory()->create(['name' => 'Late Bezoeker']);
    $lateVisitor = Visitor::create(['user_id' => $lateUser->id]);

    Visit::create([
        'visitor_id' => $this->visitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Vroeg overleg',
        'expected_arrival_time' => now()->setTime(8, 0),
        'check_in_time' => now()->setTime(8, 0),
        'check_out_time' => now()->setTime(9, 0),
    ]);

    Visit::create([
        'visitor_id' => $lateVisitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Laat overleg',
        'expected_arrival_time' => now()->setTime(14, 0),
        'check_in_time' => now()->setTime(14, 0),
        'check_out_time' => now()->setTime(15, 0),
    ]);

    $gesorteerd = Visit::orderBy('check_in_time')->get();

    expect($gesorteerd->first()->reason_of_visit)->toBe('Vroeg overleg');
    expect($gesorteerd->last()->reason_of_visit)->toBe('Laat overleg');
});
