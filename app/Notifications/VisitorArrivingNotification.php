<?php

namespace App\Notifications;

use App\Models\Visit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitorArrivingNotification extends Notification
{
    public function __construct(
        public Visit $visit,
        public string $type = 'reminder',
    ) {
    }

    public function subject(): string
    {
        $visitorName = $this->visitorName();
        $arrivalTime = $this->arrivalTime();

        return $this->type === 'arrival'
            ? "Bezoeker {$visitorName} is aangekomen om {$arrivalTime}"
            : "Reminder: afspraak om {$arrivalTime} met {$visitorName}";
    }

    public function title(): string
    {
        $visitorName = $this->visitorName();
        $arrivalTime = $this->arrivalTime();

        return $this->type === 'arrival'
            ? "Bezoeker aangekomen om {$arrivalTime}"
            : "Reminder afspraak om {$arrivalTime} met {$visitorName}";
    }

    public function message(): string
    {
        $visitorName = $this->visitorName();
        $reasonOfVisit = $this->reasonOfVisit();
        $arrivalTime = $this->arrivalTime();

        if ($this->type === 'arrival') {
            return "Bezoeker {$visitorName} is om {$arrivalTime} gearriveerd. Reden: {$reasonOfVisit}.";
        }

        return "Reminder: afspraak om {$arrivalTime} met {$visitorName}. Reden: {$reasonOfVisit}.";
    }

    public function html(): string
    {
        $title = $this->title();
        $visitorName = $this->visitorName();
        $reasonOfVisit = $this->reasonOfVisit();
        $arrivalTime = $this->arrivalTime();

        return "<p>{$title}</p><p><strong>Naam bezoeker:</strong> {$visitorName}</p><p><strong>Reden:</strong> {$reasonOfVisit}</p><p><strong>Tijd:</strong> {$arrivalTime}</p>";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $visitorName = $this->visitorName();
        $reasonOfVisit = $this->reasonOfVisit();
        $arrivalTime = $this->arrivalTime();

        return (new MailMessage)
            ->greeting("Hallo {$notifiable->name},")
            ->line($this->message())
            ->line("Naam bezoeker: {$visitorName}")
            ->line("Reden van bezoek: {$reasonOfVisit}")
            ->line("Verwachte aankomst: {$arrivalTime}")
            ->salutation('Met vriendelijke groeten');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $visitorName = $this->visitorName();

        return [
            'type' => $this->type,
            'visit_id' => $this->visit->id,
            'visitor_name' => $visitorName,
            'visitor_id' => $this->visit->visitor_id,
            'reason_of_visit' => $this->visit->reason_of_visit,
            'expected_arrival_time' => $this->visit->expected_arrival_time,
            'message' => $this->message(),
        ];
    }

    protected function visitorName(): string
    {
        return $this->visit->visitor?->user?->name ?? 'Onbekende bezoeker';
    }

    protected function reasonOfVisit(): string
    {
        return $this->visit->reason_of_visit ?? 'Geen reden opgegeven';
    }

    protected function arrivalTime(): string
    {
        return $this->visit->expected_arrival_time->format('H:i');
    }
}
