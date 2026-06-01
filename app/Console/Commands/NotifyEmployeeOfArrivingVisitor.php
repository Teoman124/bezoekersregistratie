<?php

namespace App\Console\Commands;

use App\Models\Notification as VisitorNotification;
use App\Models\Visit;
use App\Services\MailtrapApiService;
use Illuminate\Console\Command;

class NotifyEmployeeOfArrivingVisitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-employee-of-arriving-visitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify employees and visitors about visits arriving in the next 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle(MailtrapApiService $mailtrapApiService): void
    {
        $now = now();
        $windowStart = $now->copy()->startOfMinute();
        $windowEnd = $now->copy()->addMinutes(5)->endOfMinute();

        $visits = Visit::query()
            ->whereNull('check_in_time')
            ->whereBetween('expected_arrival_time', [$windowStart, $windowEnd])
            ->with(['employee.user', 'visitor.user'])
            ->get();

        $notifiedCount = 0;

        $currentMinute = $now->copy()->startOfMinute();
        $reminderMinute = $now->copy()->addMinutes(5)->startOfMinute();

        foreach ($visits as $visit) {
            $arrivalMinute = $visit->expected_arrival_time->copy()->startOfMinute();
            $type = null;

            if ($arrivalMinute->equalTo($currentMinute)) {
                $type = 'arrival';
            }

            if ($arrivalMinute->equalTo($reminderMinute)) {
                $type = 'reminder';
            }

            if ($type === null) {
                continue;
            }

            $notificationKey = "visitor_notification_{$visit->id}_{$type}";

            if (! cache()->has($notificationKey)) {
                $visitor = $visit->visitor?->user;
                $employee = $visit->employee?->user;
                $arrivalTime = $visit->expected_arrival_time->format('H:i');
                $visitorName = $visitor?->name ?? 'Onbekende bezoeker';
                $employeeName = $employee?->name ?? 'de gastheer';

                $recipientMails = [
                    [
                        'user' => $employee,
                        'title' => $type === 'arrival'
                            ? "Bezoeker aangekomen om {$arrivalTime}"
                            : "Reminder afspraak om {$arrivalTime} met {$visitorName}",
                        'subject' => $type === 'arrival'
                            ? "Bezoeker {$visitorName} is aangekomen om {$arrivalTime}"
                            : "Reminder: afspraak om {$arrivalTime} met {$visitorName}",
                        'message' => $type === 'arrival'
                            ? "Bezoeker {$visitorName} is om {$arrivalTime} gearriveerd. Reden: {$visit->reason_of_visit}."
                            : "Reminder: afspraak om {$arrivalTime} met {$visitorName}. Reden: {$visit->reason_of_visit}.",
                        'html' => $type === 'arrival'
                            ? "<p>Bezoeker {$visitorName} is om {$arrivalTime} gearriveerd.</p><p><strong>Reden:</strong> {$visit->reason_of_visit}</p><p><strong>Medewerker:</strong> {$employeeName}</p>"
                            : "<p>Reminder: afspraak om {$arrivalTime} met {$visitorName}.</p><p><strong>Reden:</strong> {$visit->reason_of_visit}</p><p><strong>Medewerker:</strong> {$employeeName}</p>",
                    ],
                    [
                        'user' => $visitor,
                        'title' => $type === 'arrival'
                            ? "Je afspraak is gestart om {$arrivalTime}"
                            : "Reminder afspraak om {$arrivalTime} met {$employeeName}",
                        'subject' => $type === 'arrival'
                            ? "Je afspraak is gestart om {$arrivalTime}"
                            : "Reminder: afspraak om {$arrivalTime} met {$employeeName}",
                        'message' => $type === 'arrival'
                            ? "Je afspraak is om {$arrivalTime} gestart met {$employeeName}. Reden: {$visit->reason_of_visit}."
                            : "Reminder: afspraak om {$arrivalTime} met {$employeeName}. Reden: {$visit->reason_of_visit}.",
                        'html' => $type === 'arrival'
                            ? "<p>Je afspraak is om {$arrivalTime} gestart met {$employeeName}.</p><p><strong>Reden:</strong> {$visit->reason_of_visit}</p>"
                            : "<p>Reminder: afspraak om {$arrivalTime} met {$employeeName}.</p><p><strong>Reden:</strong> {$visit->reason_of_visit}</p>",
                    ],
                ];

                foreach ($recipientMails as $recipientMail) {
                    if (! $recipientMail['user'] || blank($recipientMail['user']->email)) {
                        continue;
                    }

                    VisitorNotification::updateOrCreate(
                        [
                            'user_id' => $recipientMail['user']->id,
                            'title' => $recipientMail['title'],
                        ],
                        [
                            'message' => $recipientMail['message'],
                            'read' => false,
                        ]
                    );

                    $mailtrapApiService->send(
                        $recipientMail['user']->email,
                        $recipientMail['subject'],
                        $recipientMail['message'],
                        $recipientMail['html'],
                    );
                }

                cache()->put($notificationKey, true, now()->addHours(1));
                $notifiedCount++;
            }
        }

        if ($notifiedCount > 0) {
            $this->info("Notified $notifiedCount visit(s) about visitor reminders/arrivals.");
        }
    }
}
