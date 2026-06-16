<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class VisitorDataRetentionTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployee(): Employee
    {
        $department = Department::create(['name' => 'IT']);
        $user = User::factory()->create([
            'name' => 'Henk Medewerker',
            'email' => 'henk@example.com',
            'role' => 'employee',
        ]);

        return Employee::create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'function' => 'Developer',
        ]);
    }

    private function createVisitor(string $name, string $email, string $companyName): Visitor
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'role' => 'visitor',
        ]);

        return Visitor::create([
            'user_id' => $user->id,
            'company_name' => $companyName,
        ]);
    }

    public function test_oude_bezoekersdata_wordt_geanonimiseerd(): void
    {
        config()->set('retention.visitor_data_retention_days', 30);

        $employee = $this->createEmployee();

        $oldVisitDate = now()->subDays(45);
        $oldVisitor = $this->createVisitor('Jan Bezoeker', 'jan@example.com', 'Acme BV');
        Visit::create([
            'visitor_id' => $oldVisitor->id,
            'host_employee_id' => $employee->id,
            'reason_of_visit' => 'Overleg',
            'expected_arrival_time' => $oldVisitDate,
            'expected_departure_time' => $oldVisitDate->copy()->addHour(),
            'check_in_time' => $oldVisitDate->copy()->addMinutes(5),
            'check_out_time' => $oldVisitDate->copy()->addHour(),
        ]);

        $recentVisitor = $this->createVisitor('Piet Bezoeker', 'piet@example.com', 'Beta BV');
        Visit::create([
            'visitor_id' => $recentVisitor->id,
            'host_employee_id' => $employee->id,
            'reason_of_visit' => 'Nieuwe afspraak',
            'expected_arrival_time' => now()->subDays(5),
            'expected_departure_time' => now()->subDays(5)->copy()->addHour(),
            'check_in_time' => now()->subDays(5)->copy()->addMinutes(10),
            'check_out_time' => now()->subDays(5)->copy()->addHour(),
        ]);

        Artisan::call('app:anonymize-old-visitor-data');

        $oldVisitor->refresh();
        $oldVisit = $oldVisitor->visits()->first();

        $this->assertSame('Geanonimiseerde bezoeker', $oldVisitor->user->name);
        $this->assertSame('geanonimiseerd-bezoeker-'.$oldVisitor->user->id.'@example.invalid', $oldVisitor->user->email);
        $this->assertNull($oldVisitor->company_name);
        $this->assertNull($oldVisit->reason_of_visit);

        $recentVisitor->refresh();
        $recentVisit = $recentVisitor->visits()->first();

        $this->assertSame('Piet Bezoeker', $recentVisitor->user->name);
        $this->assertSame('piet@example.com', $recentVisitor->user->email);
        $this->assertSame('Beta BV', $recentVisitor->company_name);
        $this->assertSame('Nieuwe afspraak', $recentVisit->reason_of_visit);
    }
}
