<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Maak gebruikers aan
        $admin = User::create([
            'name' => 'Admin Gebruiker',
            'email' => 'admin@bedrijf.nl',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'IT Beheer',
        ]);

        $receptionist = User::create([
            'name' => 'Sanne Receptie',
            'email' => 'receptie@bedrijf.nl',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
            'department' => 'Facilitaire Zaken',
        ]);

        $employee1 = User::create([
            'name' => 'Peter de Vries',
            'email' => 'peter@bedrijf.nl',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department' => 'Marketing',
        ]);

        $employee2 = User::create([
            'name' => 'Lisa van den Berg',
            'email' => 'lisa@bedrijf.nl',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department' => 'HR',
        ]);

        $employee3 = User::create([
            'name' => 'Mohammed Ait',
            'email' => 'mohammed@bedrijf.nl',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department' => 'Software Development',
        ]);

        // 2. Maak terugkerende bezoekers aan
        $visitor1 = Visitor::create([
            'name' => 'Jan Jansen',
            'email' => 'jan@consultancy.nl',
            'company' => 'Consultancy BV',
            'phone' => '0612345678',
        ]);

        $visitor2 = Visitor::create([
            'name' => 'Emma Willems',
            'email' => 'emma@designstudio.nl',
            'company' => 'Design Studio',
            'phone' => '0687654321',
        ]);

        $visitor3 = Visitor::create([
            'name' => 'Thomas Bakker',
            'email' => 'thomas@softwarehuis.nl',
            'company' => 'SoftwareHuis BV',
            'phone' => '0645678912',
        ]);

        $visitor4 = Visitor::create([
            'name' => 'Fatima Yilmaz',
            'email' => 'fatima@hr-advies.nl',
            'company' => 'HR Adviesgroep',
        ]);

        // 3. Maak bezoeken aan
        // Actieve bezoeken (nu in pand)
        Visit::create([
            'visitor_id' => $visitor1->id,
            'host_employee_id' => $employee1->id,
            'registered_by_user_id' => $receptionist->id,
            'status' => 'active',
            'check_in_time' => now()->subMinutes(15),
            'check_out_time' => null, // Nog in pand!
            'reason_of_visit' => 'Marketing strategie bespreken',
        ]);

        Visit::create([
            'visitor_id' => $visitor2->id,
            'host_employee_id' => $employee2->id,
            'registered_by_user_id' => $receptionist->id,
            'status' => 'active',
            'check_in_time' => now()->subHour(),
            'check_out_time' => null, // Nog in pand!
            'reason_of_visit' => 'Sollicitatiegesprek',
        ]);

        // Afgeronde bezoeken
        Visit::create([
            'visitor_id' => $visitor3->id,
            'host_employee_id' => $employee3->id,
            'registered_by_user_id' => $receptionist->id,
            'status' => 'completed',
            'check_in_time' => now()->subDays(1)->setTime(10, 30),
            'check_out_time' => now()->subDays(1)->setTime(12, 45),
            'reason_of_visit' => 'Technische demo',
        ]);

        Visit::create([
            'visitor_id' => $visitor1->id, // Jan Jansen komt vaker
            'host_employee_id' => $employee1->id,
            'registered_by_user_id' => $receptionist->id,
            'status' => 'completed',
            'check_in_time' => now()->subDays(3)->setTime(14, 0),
            'check_out_time' => now()->subDays(3)->setTime(15, 30),
            'reason_of_visit' => 'Wekelijkse update',
        ]);

        Visit::create([
            'visitor_id' => $visitor4->id,
            'host_employee_id' => $employee2->id,
            'registered_by_user_id' => $employee2->id, // Medewerker heeft zelf aangemeld
            'status' => 'expected',
            'expected_arrival_time' => now()->addDay()->setTime(9, 0), // Toekomstig bezoek!
            'check_in_time' => null,
            'check_out_time' => null,
            'reason_of_visit' => 'HR adviesgesprek',
        ]);

        echo "✅ Database seeded successfully!\n";
        echo "📧 Test Accounts:\n";
        echo "   - Admin: admin@bedrijf.nl / password\n";
        echo "   - Receptie: receptie@bedrijf.nl / password\n";
        echo "   - Medewerker: peter@bedrijf.nl / password\n";
    }
}