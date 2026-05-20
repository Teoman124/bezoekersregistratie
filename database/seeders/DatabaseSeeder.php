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
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $employee1User = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Employee User',
                'password' => bcrypt('password'),
                'role' => 'employee',
            ]
        );

        $employee2User = User::firstOrCreate(
            ['email' => 'sarah@example.com'],
            [
                'name' => 'Sarah van HR',
                'password' => bcrypt('password'),
                'role' => 'employee',
            ]
        );

        $visitor1User = User::firstOrCreate(
            ['email' => 'visitor@example.com'],
            [
                'name' => 'Visitor User',
                'password' => bcrypt('password'),
                'role' => 'visitor',
            ]
        );

        $visitor2User = User::firstOrCreate(
            ['email' => 'maria@external.com'],
            [
                'name' => 'Maria Garcia',
                'password' => bcrypt('password'),
                'role' => 'visitor',
            ]
        );

        // ===== DEPARTMENTS =====
        $it = Department::firstOrCreate(['name' => 'IT']);
        $hr = Department::firstOrCreate(['name' => 'HR']);
        Department::firstOrCreate(['name' => 'Sales']);

        // ===== EMPLOYEES =====
        $emp1 = Employee::firstOrCreate(
            ['user_id' => $employee1User->id],
            [
                'user_id' => $employee1User->id,
                'department_id' => $it->id,
                'function' => 'Developer',
            ]
        );

        $emp2 = Employee::firstOrCreate(
            ['user_id' => $employee2User->id],
            [
                'user_id' => $employee2User->id,
                'department_id' => $hr->id,
                'function' => 'HR Manager',
            ]
        );

        // ===== VISITORS =====
        $vis1 = Visitor::firstOrCreate(['user_id' => $visitor1User->id]);
        $vis2 = Visitor::firstOrCreate(['user_id' => $visitor2User->id]);

        // ===== VISITS =====
        Visit::updateOrCreate(
            [
                'visitor_id' => $vis1->id,
                'host_employee_id' => $emp1->id,
                'reason_of_visit' => 'Security audit',
            ],
            [
                'expected_arrival_time' => now()->subDays(5)->setHour(10),
                'expected_departure_time' => now()->subDays(5)->setHour(12),
                'check_in_time' => now()->subDays(5)->setHour(10),
                'check_out_time' => now()->subDays(5)->setHour(11),
            ]
        );

        Visit::updateOrCreate(
            [
                'visitor_id' => $vis2->id,
                'host_employee_id' => $emp1->id,
                'reason_of_visit' => 'Project meeting',
            ],
            [
                'expected_arrival_time' => now()->subHours(2),
                'expected_departure_time' => now()->addHours(1),
                'check_in_time' => now()->subHours(2),
                'check_out_time' => null,
            ]
        );

        Visit::updateOrCreate(
            [
                'visitor_id' => $vis1->id,
                'host_employee_id' => $emp2->id,
                'reason_of_visit' => 'HR interview',
            ],
            [
                'expected_arrival_time' => now()->addDays(1)->setHour(14),
                'expected_departure_time' => now()->addDays(1)->setHour(15),
                'check_in_time' => null,
                'check_out_time' => null,
            ]
        );

        Visit::updateOrCreate(
            [
                'visitor_id' => $vis2->id,
                'host_employee_id' => $emp2->id,
                'reason_of_visit' => 'Softwaredemonstatie en kennismaken',
            ],
            [
                'expected_arrival_time' => now()->addMinutes(5),
                'expected_departure_time' => now()->addMinutes(35),
                'check_in_time' => null,
                'check_out_time' => null,
            ]
        );

        // ===== NOTIFICATIONS =====
        Notification::updateOrCreate(
            [
                'user_id' => $emp1->user_id,
                'title' => 'Bezoeker ingecheckt',
            ],
            [
                'message' => 'Je bezoeker Maria Garcia is aangekomen.',
                'read' => false,
            ]
        );

        Notification::updateOrCreate(
            [
                'user_id' => $emp2->user_id,
                'title' => 'Nieuw bezoek gepland',
            ],
            [
                'message' => 'Je hebt een bezoek gepland voor morgen om 14:00.',
                'read' => true,
            ]
        );
    }
}
