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
use App\Services\MailtrapApiService;
use Illuminate\Support\Facades\Auth;

function preRegistrationHostEmployee(): Employee
{
    $department = Department::create(['name' => 'Sales']);

    $hostUser = User::factory()->create([
        'name' => 'Lisa Medewerker',
        'email' => 'lisa@example.com',
    ]);

    return Employee::create([
        'user_id' => $hostUser->id,
        'department_id' => $department->id,
        'function' => 'Accountmanager',
    ]);
}

function preRegistrationVisitor(): Visitor
{
    $visitorUser = User::factory()->create(['name' => 'Tom Bezoeker']);

    return Visitor::create(['user_id' => $visitorUser->id]);
}

beforeEach(function () {
    Auth::login(User::factory()->create([
        'email_verified_at' => now(),
        'role' => 'employee',
    ]));
});

// ✅ SLAAGT — bezoek wordt opgeslagen in de database na POST
test('vooraf aangemeld bezoek wordt opgeslagen in de database', function () {
    $employee = preRegistrationHostEmployee();
    $visitor = preRegistrationVisitor();

    $this->post(route('visits.store'), [
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Kennismaking',
        'expected_arrival_time' => now()->addDay()->format('Y-m-d H:i:s'),
    ])->assertRedirect(route('visits.index'));

    expect(Visit::count())->toBe(1);
    expect(Visit::first()->reason_of_visit)->toBe('Kennismaking');
    expect(Visit::first()->currentStatus())->toBe('planned');
});

// ✅ SLAAGT — vooraf aangemeld bezoek heeft geen check_in_time
test('vooraf aangemeld bezoek is nog niet ingecheckt', function () {
    $employee = preRegistrationHostEmployee();
    $visitor = preRegistrationVisitor();

    $this->post(route('visits.store'), [
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Productdemo',
        'expected_arrival_time' => now()->addDay()->format('Y-m-d H:i:s'),
    ]);

    expect(Visit::first()->check_in_time)->toBeNull();
    expect(Visit::first()->check_out_time)->toBeNull();
});

// ✅ SLAAGT — een actief aangemeld bezoek krijgt een check-in tijd
test('actief aangemeld bezoek krijgt automatisch een check_in_time', function () {
    $employee = preRegistrationHostEmployee();
    $visitor = preRegistrationVisitor();

    $this->post(route('visits.store'), [
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Productdemo',
        'expected_arrival_time' => now()->addDay()->format('Y-m-d H:i:s'),
        'status' => 'active',
    ]);

    expect(Visit::first()->check_in_time)->not->toBeNull();
    expect(Visit::first()->check_out_time)->toBeNull();
    expect(Visit::first()->currentStatus())->toBe('active');
});

// ✅ SLAAGT — vooraf aangemeld bezoek verschijnt in het algemene overzicht
test('vooraf aangemeld bezoek verschijnt in het bezoekersoverzicht', function () {
    $employee = preRegistrationHostEmployee();
    $visitor = preRegistrationVisitor();

    $this->post(route('visits.store'), [
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Contractbespreking',
        'expected_arrival_time' => now()->addDay()->format('Y-m-d H:i:s'),
    ]);

    $this->get(route('visits.index'))->assertSuccessful();

    expect(Visit::count())->toBe(1);
});

test('het boeken van een bezoek verstuurt een bevestiging naar de bezoeker', function () {
    $employee = preRegistrationHostEmployee();
    $visitorUser = User::factory()->create([
        'name' => 'Tom Bezoeker',
        'email' => 'tom@example.com',
    ]);

    $visitor = Visitor::create(['user_id' => $visitorUser->id]);

    $this->mock(MailtrapApiService::class, function ($mock) {
        $mock->shouldReceive('send')
            ->twice()
            ->withArgs(function (string $toEmail, string $subject, string $text, ?string $html = null): bool {
                return in_array($toEmail, ['tom@example.com', 'lisa@example.com'], true)
                    && in_array($subject, ['Bevestiging van je bezoek', 'Nieuwe afspraak ingepland'], true)
                    && str_contains($text, 'Tom Bezoeker')
                    && str_contains($text, 'Lisa Medewerker')
                    && $html !== null;
            })
            ->andReturnTrue();
    });

    $this->post(route('visits.store'), [
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Kennismaking',
        'expected_arrival_time' => now()->addDay()->format('Y-m-d H:i:s'),
    ])->assertRedirect(route('visits.index'));

    expect(Visit::count())->toBe(1);
});
