<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'host_employee_id',
        'registered_by_user_id',
        'status',
        'expected_arrival_time',
        'check_in_time',
        'expected_departure_time',
        'check_out_time',
        'reason_of_visit',
        'badge_sent',
    ];

    protected $casts = [
        'expected_arrival_time' => 'datetime',
        'check_in_time' => 'datetime',
        'expected_departure_time' => 'datetime',
        'check_out_time' => 'datetime',
        'badge_sent' => 'boolean',
    ];

    /**
     * Relatie: Wie is de bezoeker?
     */
    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * Relatie: Wie is de gastheer/ontvanger?
     */
    public function host()
    {
        return $this->belongsTo(User::class, 'host_employee_id');
    }

    /**
     * Relatie: Wie heeft dit bezoek geregistreerd?
     */
    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    /**
     * Scope: Bezoekers die momenteel in het pand zijn
     */
    public function scopeCurrentlyInBuilding($query)
    {
        return $query->whereNull('check_out_time');
    }

    /**
     * Scope: Bezoekers die zijn uitgecheckt
     */
    public function scopeCheckedOut($query)
    {
        return $query->whereNotNull('check_out_time');
    }

    /**
     * Scope: Bezoeken voor een specifieke medewerker
     */
    public function scopeForHost($query, $userId)
    {
        return $query->where('host_employee_id', $userId);
    }

    /**
     * Scope: Bezoeken van vandaag
     */
    public function scopeToday($query)
    {
        return $query->whereDate('check_in_time', today());
    }

    /**
     * Scope: Bezoeken van deze week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('check_in_time', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Check of bezoeker nog in het pand is
     */
    public function isActive(): bool
    {
        return is_null($this->check_out_time);
    }

    /**
     * Bereken duur van het bezoek in minuten
     */
    public function getDurationInMinutesAttribute(): ?int
    {
        if (!$this->check_out_time) {
            return null;
        }

        return $this->check_in_time->diffInMinutes($this->check_out_time);
    }

    /**
     * Formatteer duur voor weergave
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration_in_minutes;

        if ($minutes === null) {
            return 'Nog in pand';
        }

        if ($minutes < 60) {
            return "{$minutes} minuten";
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours} uur";
        }

        return "{$hours} uur {$remainingMinutes} min";
    }

    /**
     * Check de bezoeker uit
     */
    public function checkout(): bool
    {
        if ($this->isActive()) {
            return $this->update([
                'check_out_time' => now()
            ]);
        }

        return false;
    }
}