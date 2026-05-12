<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Facades\Auth;

function visitStatusUpdateHostEmployee(): Employee
{
    $department = Department::create(['name' => 'IT']);

    $hostUser = User::factory()->create(['name' => 'Henk Medewerker']);

    return Employee::create([
        'user_id' => $hostUser->id,
        'department_id' => $department->id,
        'function' => 'Developer',
    ]);
}

function visitStatusUpdateVisitor(string $name = 'Jan Bezoeker'): Visitor
{
    $visitorUser = User::factory()->create(['name' => $name]);

    return Visitor::create(['user_id' => $visitorUser->id]);
}

beforeEach(function () {
    Auth::login(User::factory()->create([
        'email_verified_at' => now(),
        'role' => 'employee',
    ]));
});

test('status wijzigen naar aanwezig zet de check-in tijd', function () {
    $employee = visitStatusUpdateHostEmployee();
    $visitor = visitStatusUpdateVisitor();

    $visit = Visit::create([
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Overleg',
        'expected_arrival_time' => now()->addHour(),
        'check_in_time' => null,
        'check_out_time' => null,
    ]);

    $this->put(route('visits.update', $visit), [
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Overleg',
        'expected_arrival_time' => now()->addHour()->format('Y-m-d\TH:i'),
        'expected_departure_time' => now()->addHours(2)->format('Y-m-d\TH:i'),
        'check_in_time' => null,
        'check_out_time' => null,
        'status' => 'active',
    ])->assertRedirect(route('visits.index'));

    expect($visit->fresh()->currentStatus())->toBe('active');
    expect($visit->fresh()->check_in_time)->not->toBeNull();
    expect($visit->fresh()->check_out_time)->toBeNull();
});

test('status wijzigen naar uitgecheckt zet de check-out tijd', function () {
    $employee = visitStatusUpdateHostEmployee();
    $visitor = visitStatusUpdateVisitor();

    $visit = Visit::create([
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Vergadering',
        'expected_arrival_time' => now()->subHour(),
        'check_in_time' => now()->subHour(),
        'check_out_time' => null,
    ]);

    $this->put(route('visits.update', $visit), [
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Vergadering',
        'expected_arrival_time' => now()->subHour()->format('Y-m-d\TH:i'),
        'expected_departure_time' => now()->addHour()->format('Y-m-d\TH:i'),
        'check_in_time' => now()->subHour()->format('Y-m-d\TH:i'),
        'check_out_time' => null,
        'status' => 'checked_out',
    ])->assertRedirect(route('visits.index'));

    expect($visit->fresh()->currentStatus())->toBe('checked_out');
    expect($visit->fresh()->check_in_time)->not->toBeNull();
    expect($visit->fresh()->check_out_time)->not->toBeNull();
});

test('status wijzigen naar ingepland wist de check-in en check-out tijd', function () {
    $employee = visitStatusUpdateHostEmployee();
    $visitor = visitStatusUpdateVisitor();

    $visit = Visit::create([
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Rondleiding',
        'expected_arrival_time' => now()->subHour(),
        'check_in_time' => now()->subHour(),
        'check_out_time' => now()->subMinutes(15),
    ]);

    $this->put(route('visits.update', $visit), [
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Rondleiding',
        'expected_arrival_time' => now()->subHour()->format('Y-m-d\TH:i'),
        'expected_departure_time' => now()->addHour()->format('Y-m-d\TH:i'),
        'check_in_time' => now()->subHour()->format('Y-m-d\TH:i'),
        'check_out_time' => now()->subMinutes(15)->format('Y-m-d\TH:i'),
        'status' => 'planned',
    ])->assertRedirect(route('visits.index'));

    expect($visit->fresh()->currentStatus())->toBe('planned');
    expect($visit->fresh()->check_in_time)->toBeNull();
    expect($visit->fresh()->check_out_time)->toBeNull();
});
