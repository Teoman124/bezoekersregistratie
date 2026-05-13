<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== USERS =====
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $employee1User = User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        $employee2User = User::create([
            'name' => 'Sarah van HR',
            'email' => 'sarah@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        $visitor1User = User::create([
            'name' => 'Visitor User',
            'email' => 'visitor@example.com',
            'password' => bcrypt('password'),
            'role' => 'visitor',
        ]);

        $visitor2User = User::create([
            'name' => 'Maria Garcia',
            'email' => 'maria@external.com',
            'password' => bcrypt('password'),
            'role' => 'visitor',
        ]);

        // ===== DEPARTMENTS =====
        $it = Department::create(['name' => 'IT']);
        $hr = Department::create(['name' => 'HR']);
        $sales = Department::create(['name' => 'Sales']);

        // ===== EMPLOYEES =====
        $emp1 = Employee::create([
            'user_id' => $employee1User->id,
            'department_id' => $it->id,
            'function' => 'Developer',
        ]);

        $emp2 = Employee::create([
            'user_id' => $employee2User->id,
            'department_id' => $hr->id,
            'function' => 'HR Manager',
        ]);

        // ===== VISITORS =====
        $vis1 = Visitor::create(['user_id' => $visitor1User->id]);
        $vis2 = Visitor::create(['user_id' => $visitor2User->id]);

        // ===== VISITS =====
        // Past visit (completed)
        Visit::create([
            'visitor_id' => $vis1->id,
            'host_employee_id' => $emp1->id,
            'reason_of_visit' => 'Security audit',
            'expected_arrival_time' => now()->subDays(5)->setHour(10),
            'expected_departure_time' => now()->subDays(5)->setHour(12),
            'check_in_time' => now()->subDays(5)->setHour(10),
            'check_out_time' => now()->subDays(5)->setHour(11),
        ]);

        // Active visit (checked in, not checked out)
        Visit::create([
            'visitor_id' => $vis2->id,
            'host_employee_id' => $emp1->id,
            'reason_of_visit' => 'Project meeting',
            'expected_arrival_time' => now()->subHours(2),
            'expected_departure_time' => now()->addHours(1),
            'check_in_time' => now()->subHours(2),
            'check_out_time' => null,
        ]);

        // Planned visit (not yet checked in)
        Visit::create([
            'visitor_id' => $vis1->id,
            'host_employee_id' => $emp2->id,
            'reason_of_visit' => 'HR interview',
            'expected_arrival_time' => now()->addDays(1)->setHour(14),
            'expected_departure_time' => now()->addDays(1)->setHour(15),
            'check_in_time' => null,
            'check_out_time' => null,
        ]);

        // ===== NOTIFICATIONS =====
        Notification::create([
            'user_id' => $emp1->user_id,
            'title' => 'Bezoeker ingecheckt',
            'message' => 'Je bezoeker Maria Garcia is aangekomen.',
            'read' => false,
        ]);

        Notification::create([
            'user_id' => $emp2->user_id,
            'title' => 'Nieuw bezoek gepland',
            'message' => 'Je hebt een bezoek gepland voor morgen om 14:00.',
            'read' => true,
        ]);
    }
}
