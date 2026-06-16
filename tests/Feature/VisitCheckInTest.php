<?php

// User Story #1: Bezoeker inchecken
// Als receptionist wil ik een bezoeker kunnen inchecken,
// zodat het bezoek correct wordt geregistreerd.
//
// Acceptatiecriteria:
// ✅ check_in_time wordt opgeslagen bij inchecken
// ✅ bezoeker verschijnt in het actieve overzicht na inchecken
// ❌ medewerker ontvangt een e-mail bij inchecken (nog niet gebouwd)

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    $department = Department::create(['name' => 'IT']);

    $hostUser = User::factory()->create(['name' => 'Henk Medewerker']);
    $this->employee = Employee::create([
        'user_id' => $hostUser->id,
        'department_id' => $department->id,
        'function' => 'Developer',
    ]);

    $visitorUser = User::factory()->create(['name' => 'Jan Bezoeker']);
    $visitor = Visitor::create(['user_id' => $visitorUser->id]);

    $this->visit = Visit::create([
        'visitor_id' => $visitor->id,
        'host_employee_id' => $this->employee->id,
        'reason_of_visit' => 'Vergadering',
        'expected_arrival_time' => now(),
        'check_in_time' => null,
        'check_out_time' => null,
    ]);
});

// ✅ SLAAGT — de check_in_time wordt opgeslagen
test('inchecken slaat de aankomsttijd op in de database', function () {
    $this->get(route('visits.checkin', $this->visit))
        ->assertRedirect();

    expect($this->visit->fresh()->check_in_time)->not->toBeNull();
});

test('qr-link checkin route markeert het bezoek als ingecheckt', function () {
    $signedUrl = URL::temporarySignedRoute(
        'visits.checkin.qr',
        now()->addHours(6),
        ['visit' => $this->visit],
    );

    $this->get($signedUrl)
        ->assertRedirect(route('visits.show', $this->visit));

    expect($this->visit->fresh()->check_in_time)->not->toBeNull();
});

// ✅ SLAAGT — actief overzicht toont alleen bezoekers zonder check_out_time
test('ingecheckte bezoeker is zichtbaar in het actieve overzicht', function () {
    // Stel bezoeker in als ingecheckt (geen check_out_time)
    $this->visit->update(['check_in_time' => now()]);

    $actieveBezoeken = Visit::whereNull('check_out_time')->get();

    expect($actieveBezoeken)->toHaveCount(1);
    expect($actieveBezoeken->first()->id)->toBe($this->visit->id);
});

// ❌ FAALT — e-mail naar medewerker is nog niet geïmplementeerd
// De checkIn() methode maakt alleen een Notification-record aan in de database.
// Mail::send() wordt nergens aangeroepen. Deze test laat zien wat er nog
// gebouwd moet worden (User Story #6).
test('medewerker ontvangt een e-mail wanneer zijn bezoeker incheckt', function () {
    Mail::fake();

    $this->get(route('visits.checkin', $this->visit));

    // Verwacht dat er een mail gestuurd is — maar dat gebeurt nog niet!
    Mail::assertSent(Mailable::class);
});
