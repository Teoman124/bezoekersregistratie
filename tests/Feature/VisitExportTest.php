<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitExportTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployee(): Employee
    {
        $department = Department::create(['name' => 'IT']);

        $hostUser = User::factory()->create(['name' => 'Henk Medewerker']);

        return Employee::create([
            'user_id' => $hostUser->id,
            'department_id' => $department->id,
            'function' => 'Developer',
        ]);
    }

    private function createVisitor(string $name, string $email, string $companyName): Visitor
    {
        $visitorUser = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'role' => 'visitor',
        ]);

        return Visitor::create([
            'user_id' => $visitorUser->id,
            'company_name' => $companyName,
        ]);
    }

    public function test_bezoekersdata_kan_als_csv_worden_geexporteerd(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = $this->createEmployee();

        $olderVisitor = $this->createVisitor('Jan Bezoeker', 'jan@example.com', 'Acme BV');
        Visit::create([
            'visitor_id' => $olderVisitor->id,
            'host_employee_id' => $employee->id,
            'reason_of_visit' => 'Oud overleg',
            'expected_arrival_time' => now()->subDay()->setTime(9, 0),
            'expected_departure_time' => now()->subDay()->setTime(10, 0),
            'check_in_time' => now()->subDay()->setTime(9, 5),
            'check_out_time' => now()->subDay()->setTime(10, 0),
        ]);

        $newerVisitor = $this->createVisitor('Piet Bezoeker', 'piet@example.com', 'Beta BV');
        Visit::create([
            'visitor_id' => $newerVisitor->id,
            'host_employee_id' => $employee->id,
            'reason_of_visit' => 'Nieuw overleg',
            'expected_arrival_time' => now()->setTime(14, 0),
            'expected_departure_time' => now()->setTime(15, 0),
            'check_in_time' => now()->setTime(14, 10),
            'check_out_time' => now()->setTime(15, 0),
        ]);

        $response = $this->actingAs($admin)->get(route('visits.export'));

        $response->assertOk();
        $this->assertSame('text/csv; charset=UTF-8', $response->headers->get('content-type'));

        $csv = ltrim($response->streamedContent(), "\xEF\xBB\xBF");
        $lines = preg_split('/\r\n|\r|\n/', trim($csv));

        $this->assertCount(3, $lines);
        $this->assertSame([
            'Datum',
            'Bezoeker',
            'Bezoeker e-mail',
            'Bedrijf',
            'Medewerker',
            'Afdeling',
            'Reden',
            'Verwachte aankomst',
            'Werkelijke aankomst',
            'Verwacht vertrek',
            'Werkelijke vertrek',
            'Status',
        ], str_getcsv($lines[0]));

        $this->assertSame([
            now()->format('Y-m-d'),
            'Piet Bezoeker',
            'piet@example.com',
            'Beta BV',
            'Henk Medewerker',
            'IT',
            'Nieuw overleg',
            now()->setTime(14, 0)->format('Y-m-d H:i:s'),
            now()->setTime(14, 10)->format('Y-m-d H:i:s'),
            now()->setTime(15, 0)->format('Y-m-d H:i:s'),
            now()->setTime(15, 0)->format('Y-m-d H:i:s'),
            'checked_out',
        ], str_getcsv($lines[1]));

        $this->assertSame([
            now()->subDay()->format('Y-m-d'),
            'Jan Bezoeker',
            'jan@example.com',
            'Acme BV',
            'Henk Medewerker',
            'IT',
            'Oud overleg',
            now()->subDay()->setTime(9, 0)->format('Y-m-d H:i:s'),
            now()->subDay()->setTime(9, 5)->format('Y-m-d H:i:s'),
            now()->subDay()->setTime(10, 0)->format('Y-m-d H:i:s'),
            now()->subDay()->setTime(10, 0)->format('Y-m-d H:i:s'),
            'checked_out',
        ], str_getcsv($lines[2]));
    }
}
