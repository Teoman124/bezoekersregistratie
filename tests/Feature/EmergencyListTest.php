<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmergencyListTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployee(string $name = 'Henk Medewerker', string $email = 'henk@example.com', string $department = 'IT', string $function = 'Developer'): Employee
    {
        $departmentModel = Department::create(['name' => $department]);

        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'role' => 'employee',
        ]);

        return Employee::create([
            'user_id' => $user->id,
            'department_id' => $departmentModel->id,
            'function' => $function,
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

    public function test_de_noodlijst_toon_t_aanwezige_bezoekers_en_medewerkers(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $employee = $this->createEmployee();

        $activeVisitor = $this->createVisitor('Jan Bezoeker', 'jan@example.com', 'Acme BV');
        Visit::create([
            'visitor_id' => $activeVisitor->id,
            'host_employee_id' => $employee->id,
            'reason_of_visit' => 'Overleg',
            'expected_arrival_time' => now()->subHour(),
            'check_in_time' => now()->subHour(),
            'check_out_time' => null,
        ]);

        $plannedVisitor = $this->createVisitor('Piet Gepland', 'piet@example.com', 'Beta BV');
        Visit::create([
            'visitor_id' => $plannedVisitor->id,
            'host_employee_id' => $employee->id,
            'reason_of_visit' => 'Rondleiding',
            'expected_arrival_time' => now()->addHour(),
            'check_in_time' => null,
            'check_out_time' => null,
        ]);

        $response = $this->get(route('visits.active'));

        $response->assertOk();
        $response->assertSee('Noodlijst');
        $response->assertSee('Aanwezige bezoekers');
        $response->assertSee('Aanwezige medewerkers');
        $response->assertSee('Jan Bezoeker');
        $response->assertSee('Henk Medewerker');
        $response->assertDontSee('Piet Gepland');
    }

    public function test_de_noodlijst_kan_als_csv_worden_geexporteerd(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $employee = $this->createEmployee();
        $arrivalTime = now()->subHour();

        $activeVisitor = $this->createVisitor('Jan Bezoeker', 'jan@example.com', 'Acme BV');
        Visit::create([
            'visitor_id' => $activeVisitor->id,
            'host_employee_id' => $employee->id,
            'reason_of_visit' => 'Overleg',
            'expected_arrival_time' => $arrivalTime,
            'check_in_time' => $arrivalTime,
            'check_out_time' => null,
        ]);

        $response = $this->get(route('visits.active.export'));

        $response->assertOk();
        $this->assertSame('text/csv; charset=UTF-8', $response->headers->get('content-type'));

        $csv = ltrim($response->streamedContent(), "\xEF\xBB\xBF");
        $lines = preg_split('/\r\n|\r|\n/', trim($csv));

        $this->assertCount(3, $lines);
        $this->assertSame([
            'Type',
            'Naam',
            'E-mail',
            'Bedrijf',
            'Afdeling',
            'Functie',
            'Gastheer',
            'Reden',
            'Aankomst',
            'Status',
        ], str_getcsv($lines[0]));

        $this->assertSame([
            'Bezoeker',
            'Jan Bezoeker',
            'jan@example.com',
            'Acme BV',
            'IT',
            '-',
            'Henk Medewerker',
            'Overleg',
            $arrivalTime->format('Y-m-d H:i:s'),
            'aanwezig',
        ], str_getcsv($lines[1]));

        $this->assertSame([
            'Medewerker',
            'Henk Medewerker',
            'henk@example.com',
            '-',
            'IT',
            'Developer',
            '-',
            '-',
            '-',
            'aanwezig',
        ], str_getcsv($lines[2]));
    }
}
