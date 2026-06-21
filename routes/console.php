<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Visit;
use App\Models\Notification;
use App\Services\MailtrapApiService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:notify-employee-of-arriving-visitor')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('app:anonymize-old-visitor-data')
    ->daily()
    ->withoutOverlapping();

// Automatische uitcheck-herinnering — bezoeken die actief zijn na sluitingstijd
Schedule::call(function (MailtrapApiService $mailtrapApiService) {
    // Haal alle actieve bezoeken op (ingecheckt, maar niet uitgecheckt)
    $activeVisits = Visit::active()
        ->with(['visitor.user', 'employee.user'])
        ->get();

    foreach ($activeVisits as $visit) {
        $employee = $visit->employee;
        $visitor = $visit->visitor;

        // 1. Maak een database notificatie aan voor de medewerker (gastheer)
        if ($employee && $employee->user_id) {
            Notification::create([
                'user_id' => $employee->user_id,
                'title' => 'Actief bezoek na sluitingstijd',
                'message' => 'Vergeet niet je bezoeker ' . ($visitor?->user?->name ?? 'onbekend') . ' uit te checken. Het is inmiddels na sluitingstijd.',
            ]);
        }

        // 2. Stuur een e-mail naar de medewerker via MailtrapApiService
        if ($employee && $employee->user?->email) {
            $employeeName = $employee->user->name ?? 'gastheer';
            $visitorName = $visitor?->user?->name ?? 'een bezoeker';

            $subject = 'Herinnering: Bezoeker niet uitgecheckt';
            $text = "Hallo {$employeeName},\n\nJe bezoeker {$visitorName} is na sluitingstijd nog steeds niet uitgecheckt. Vergeet niet om deze in het systeem af te melden.\n\nMet vriendelijke groet,\nHet Systeem";
            
            $html = "<p>Hallo " . e($employeeName) . ",</p>"
                . "<p>Je bezoeker <strong>" . e($visitorName) . "</strong> is na sluitingstijd nog steeds niet uitgecheckt.</p>"
                . "<p>Vergeet niet om deze in het systeem af te melden via het dashboard.</p>";

            $mailtrapApiService->send(
                $employee->user->email,
                $subject,
                $text,
                $html
            );
        }
    }
})->dailyAt('18:00')->name('remind:active-visits-checkout');