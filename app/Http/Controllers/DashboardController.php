<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $startOfWeek = now()->startOfWeek();

        $visitsToday = Visit::whereDate('expected_arrival_time', $today)->count();
        $visitsYesterday = Visit::whereDate('expected_arrival_time', $yesterday)->count();
        $visitsThisWeek = Visit::whereBetween('expected_arrival_time', [$startOfWeek, $today->copy()->endOfDay()])->count();
        $plannedVisits = Visit::whereNull('check_in_time')->whereDate('expected_arrival_time', '>=', $today)->count();

        return view('dashboard', [
            'stats' => [
                'users' => User::count(),
                'employees' => Employee::count(),
                'visitors' => Visitor::count(),
                'visits' => Visit::count(),
                'active_visits' => Visit::active()->count(),
                'departments' => Department::count(),
                'visits_today' => $visitsToday,
                'visits_yesterday' => $visitsYesterday,
                'visits_this_week' => $visitsThisWeek,
                'planned_visits' => $plannedVisits,
            ],
        ]);
    }
}
