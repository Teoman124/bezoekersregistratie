<?php

// User Story #3: Realtime overzicht bekijken
// Als receptionist wil ik een lijst zien van alle aanwezige bezoekers,
// zodat ik weet wie er in het pand is.
//
// Acceptatiecriteria:
// ✅ Alleen bezoekers zonder uitchecktijd zichtbaar bij status=in filter
// ✅ Overzicht toont de naam van de bezoeker en contactpersoon
// ❌ Overzicht toont bedrijfsnaam van bezoeker (veld bestaat nog niet in datamodel)

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

beforeEach(function () {
    $department = Department::create(['name' => 'IT']);

    $hostUser = User::factory()->create(['name' => 'Henk Medewerker']);
    $this->employee = Employee::create([
        'user_id'       => $hostUser->id,
        'department_id' => $department->id,
        'function'      => 'Developer',
    ]);

    $visitorUser = User::factory()->create(['name' => 'Jan Bezoeker']);
    $this->visitor = Visitor::create(['user_id' => $visitorUser->id]);
});

// ✅ SLAAGT — status=in filter geeft alleen ingecheckte bezoekers terug
test('overzicht toont alleen actieve bezoekers bij status=in filter', function () {
    // Actieve bezoeker (ingecheckt, nog aanwezig)
    Visit::create([
        'visitor_id'            => $this->visitor->id,
        'host_employee_id'      => $this->employee->id,
        'reason_of_visit'       => 'Overleg',
        'expected_arrival_time' => now()->subHour(),
        'check_in_time'         => now()->subHour(),
        'check_out_time'        => null,
    ]);

    // Uitgecheckte bezoeker (vertrokken)
    $checkedOutUser = User::factory()->create(['name' => 'Piet Vertrokken']);
    $checkedOutVisitor = Visitor::create(['user_id' => $checkedOutUser->id]);
    Visit::create([
        'visitor_id'            => $checkedOutVisitor->id,
        'host_employee_id'      => $this->employee->id,
        'reason_of_visit'       => 'Rondleiding',
        'expected_arrival_time' => now()->subHours(3),
        'check_in_time'         => now()->subHours(3),
        'check_out_time'        => now()->subHour(),
    ]);

    $actieveBezoeken = Visit::whereNull('check_out_time')->get();

    expect($actieveBezoeken)->toHaveCount(1);
    expect($actieveBezoeken->first()->visitor->user->name)->toBe('Jan Bezoeker');
});

// ✅ SLAAGT — overzicht zonder filter toont alle bezoeken (actief + afgerond)
test('overzicht zonder filter toont alle bezoeken', function () {
    $checkedOutUser = User::factory()->create(['name' => 'Piet Vertrokken']);
    $checkedOutVisitor = Visitor::create(['user_id' => $checkedOutUser->id]);

    Visit::create([
        'visitor_id'            => $this->visitor->id,
        'host_employee_id'      => $this->employee->id,
        'reason_of_visit'       => 'Overleg',
        'expected_arrival_time' => now()->subHour(),
        'check_in_time'         => now()->subHour(),
        'check_out_time'        => null,
    ]);

    Visit::create([
        'visitor_id'            => $checkedOutVisitor->id,
        'host_employee_id'      => $this->employee->id,
        'reason_of_visit'       => 'Rondleiding',
        'expected_arrival_time' => now()->subHours(3),
        'check_in_time'         => now()->subHours(3),
        'check_out_time'        => now()->subHour(),
    ]);

    $alleBezoeken = Visit::all();

    expect($alleBezoeken)->toHaveCount(2);
});

// ✅ SLAAGT — contactpersoon (host_employee) is opgeslagen bij het bezoek
test('overzicht toont de contactpersoon van elk bezoek', function () {
    $visit = Visit::create([
        'visitor_id'            => $this->visitor->id,
        'host_employee_id'      => $this->employee->id,
        'reason_of_visit'       => 'Vergadering',
        'expected_arrival_time' => now()->subHour(),
        'check_in_time'         => now()->subHour(),
        'check_out_time'        => null,
    ]);

    expect($visit->employee->user->name)->toBe('Henk Medewerker');
});

// ❌ FAALT — bedrijfsnaam staat nog niet in het datamodel
// De Visitor-tabel heeft geen 'company'-veld. Dit acceptatiecriterium
// is nog niet geïmplementeerd en laat zien wat er nog gebouwd moet worden.
test('overzicht toont de bedrijfsnaam van de bezoeker', function () {
    Visit::create([
        'visitor_id'            => $this->visitor->id,
        'host_employee_id'      => $this->employee->id,
        'reason_of_visit'       => 'Vergadering',
        'expected_arrival_time' => now()->subHour(),
        'check_in_time'         => now()->subHour(),
        'check_out_time'        => null,
    ]);

    $visit = Visit::whereNull('check_out_time')->first();

    // Verwacht een bedrijfsnaam op de bezoeker — maar dat veld bestaat nog niet!
    expect($visit->visitor->company)->not->toBeNull();
});
