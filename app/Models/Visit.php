<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // Voeg deze toe

class Visit extends Model
{
    protected $fillable = [
        'visitor_id',
        'host_employee_id',
        'reason_of_visit',
        'expected_arrival_time',
        'expected_departure_time',
        'check_in_time',
        'check_out_time',
        'agreed_to_rules',
        'agreed_at',
        'agreed_ip',
    ];

    protected $casts = [
        'expected_arrival_time' => 'datetime',
        'expected_departure_time' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'agreed_at' => 'datetime', // Voeg deze toe
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'host_employee_id');
    }

    public function currentStatus(): string
    {
        if ($this->check_in_time === null) {
            return 'planned';
        }

        if ($this->check_out_time === null) {
            return 'active';
        }

        return 'checked_out';
    }

    // Voeg type hint toe voor de $query parameter
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('check_in_time')
            ->whereNull('check_out_time');
    }

    // Optionele scope voor NDA status
    public function scopeAgreedToRules(Builder $query): Builder
    {
        return $query->where('agreed_to_rules', true);
    }

    public function scopeNotAgreedToRules(Builder $query): Builder
    {
        return $query->where('agreed_to_rules', false);
    }
}