<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Notification as VisitorNotification;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

test('visitor reminder and arrival are stored in notifications and sent to Mailtrap', function () {
    Http::fake();

    $baseTime = Carbon::parse('2026-05-18 11:55:00');
    Carbon::setTestNow($baseTime);

    $department = Department::create(['name' => 'IT']);

    $employeeUser = User::create([
        'name' => 'Jan de Medewerker',
        'email' => 'jan@example.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);

    $employee = Employee::create([
        'user_id' => $employeeUser->id,
        'department_id' => $department->id,
        'function' => 'Developer',
    ]);

    $visitorUser = User::create([
        'name' => 'Piet de Bezoeker',
        'email' => 'piet@example.com',
        'password' => bcrypt('password'),
        'role' => 'visitor',
    ]);

    $visitor = Visitor::create([
        'user_id' => $visitorUser->id,
        'company_name' => 'Test Company',
    ]);

    $visit = Visit::create([
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Software demonstration',
        'expected_arrival_time' => now()->addMinutes(5),
        'expected_departure_time' => now()->addMinutes(35),
    ]);

    $this->artisan('app:notify-employee-of-arriving-visitor')->assertSuccessful();

    $reminderTitle = 'Reminder afspraak om 12:00 met Piet de Bezoeker';
    $arrivalTitle = 'Bezoeker aangekomen om 12:00';

    expect(VisitorNotification::where('user_id', $employeeUser->id)->where('title', $reminderTitle)->exists())->toBeTrue();

    Http::assertSentCount(1);

    Carbon::setTestNow($baseTime->copy()->addMinutes(5));

    $this->artisan('app:notify-employee-of-arriving-visitor')->assertSuccessful();

    expect(VisitorNotification::where('user_id', $employeeUser->id)->where('title', $arrivalTitle)->exists())->toBeTrue();

    Http::assertSentCount(2);

    Carbon::setTestNow();
});

test('checked in visits do not create reminder or arrival notifications', function () {
    Http::fake();

    Carbon::setTestNow(Carbon::parse('2026-05-18 11:55:00'));

    $department = Department::create(['name' => 'IT']);

    $employeeUser = User::create([
        'name' => 'Jan de Medewerker',
        'email' => 'jan2@example.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);

    $employee = Employee::create([
        'user_id' => $employeeUser->id,
        'department_id' => $department->id,
        'function' => 'Developer',
    ]);

    $visitorUser = User::create([
        'name' => 'Piet de Bezoeker',
        'email' => 'piet2@example.com',
        'password' => bcrypt('password'),
        'role' => 'visitor',
    ]);

    $visitor = Visitor::create([
        'user_id' => $visitorUser->id,
        'company_name' => 'Test Company',
    ]);

    Visit::create([
        'visitor_id' => $visitor->id,
        'host_employee_id' => $employee->id,
        'reason_of_visit' => 'Software demonstration',
        'expected_arrival_time' => now()->addMinutes(5),
        'expected_departure_time' => now()->addMinutes(35),
        'check_in_time' => now(),
    ]);

    $this->artisan('app:notify-employee-of-arriving-visitor')->assertSuccessful();

    expect(VisitorNotification::count())->toBe(0);
    Http::assertNothingSent();

    Carbon::setTestNow();
});
