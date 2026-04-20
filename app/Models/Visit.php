<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'expected_arrival_time' => 'datetime',
        'expected_departure_time' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'host_employee_id');
    }
}