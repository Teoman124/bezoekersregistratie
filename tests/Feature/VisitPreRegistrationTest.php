<?php

// User Story #8: Bezoek vooraf aanmelden
// Als medewerker wil ik een bezoek vooraf kunnen registreren,
// zodat de receptie voorbereid is.
//
// Acceptatiecriteria:
// ✅ Formulier voor invoer bezoekgegevens werkt (POST slaat bezoek op)
// ✅ Vooraf aangemeld bezoek verschijnt in het overzicht bij de receptionist
// ✅ Vooraf aangemeld bezoek heeft geen check_in_time (nog niet ingecheckt)

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

beforeEach(function () {
    $department = Department::create(['name' => 'Sales']);

    $hostUser = User::factory()->create(['name' => 'Lisa Medewerker']);
    $this->employee = Employee::create([
        'user_id' => $hostUser->id,
        'department_id' => $department->id,
        'function' => 'Accountmanager',
    ]);

    $visitorUser = User::factory()->create(['name' => 'Tom Bezoeker']);
    $this->visitor = Visitor::create(['user_id' => $visitorUser->id]);
});

// ✅ SLAAGT — bezoek wordt opgeslagen in de database na POST
test('vooraf aangemeld bezoek wordt opgeslagen in de database', function () {
    $this->post(route('visits.store'), [
        'visitor_id' => $this->visitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Kennismaking',
        'expected_arrival_time' => now()->addDay()->format('Y-m-d H:i:s'),
    ])->assertRedirect(route('visits.index'));

    expect(Visit::count())->toBe(1);
    expect(Visit::first()->reason_of_visit)->toBe('Kennismaking');
});

// ✅ SLAAGT — vooraf aangemeld bezoek heeft geen check_in_time
test('vooraf aangemeld bezoek is nog niet ingecheckt', function () {
    $this->post(route('visits.store'), [
        'visitor_id' => $this->visitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Productdemo',
        'expected_arrival_time' => now()->addDay()->format('Y-m-d H:i:s'),
    ]);

    expect(Visit::first()->check_in_time)->toBeNull();
    expect(Visit::first()->check_out_time)->toBeNull();
});

// ✅ SLAAGT — vooraf aangemeld bezoek verschijnt in het algemene overzicht
test('vooraf aangemeld bezoek verschijnt in het bezoekersoverzicht', function () {
    $this->post(route('visits.store'), [
        'visitor_id' => $this->visitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Contractbespreking',
        'expected_arrival_time' => now()->addDay()->format('Y-m-d H:i:s'),
    ]);

    $this->get(route('visits.index'))->assertSuccessful();

    expect(Visit::count())->toBe(1);
});
