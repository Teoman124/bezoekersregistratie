<?php

namespace App\Console\Commands;

use App\Models\Notification as VisitorNotification;
use App\Models\Visit;
use App\Notifications\VisitorArrivingNotification;
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
    protected $description = 'Notify employees about visitors arriving in the next 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle(): void
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

        $mailtrap = new MailtrapApiService;
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

            if (!cache()->has($notificationKey)) {
                $employee = $visit->employee;

                if ($employee && $employee->user) {
                    $notification = new VisitorArrivingNotification($visit, $type);

                    VisitorNotification::updateOrCreate(
                        [
                            'user_id' => $employee->user->id,
                            'title' => $notification->title(),
                        ],
                        [
                            'message' => $notification->message(),
                            'read' => false,
                        ]
                    );

                    $mailtrap->send(
                        $employee->user->email,
                        $notification->subject(),
                        $notification->message(),
                        $notification->html()
                    );

                    cache()->put($notificationKey, true, now()->addHours(1));
                    $notifiedCount++;
                }
            }
        }

        if ($notifiedCount > 0) {
            $this->info("Notified $notifiedCount employee(s) about visitor reminders/arrivals.");
        }
    }
}
