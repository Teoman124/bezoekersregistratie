<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $startOfWeek = now()->startOfWeek();

        return view('dashboard', [
            'stats' => array_merge(
                $this->getCoreStats(),
                $this->getVisitOverviewStats($today, $yesterday, $startOfWeek),
                $this->getBehaviorStats(),
            ),
        ]);
    }

    private function getCoreStats(): array
    {
        return [
            'users' => User::count(),
            'employees' => Employee::count(),
            'visitors' => Visitor::count(),
            'visits' => Visit::count(),
            'active_visits' => Visit::active()->count(),
            'departments' => Department::count(),
        ];
    }

    private function getVisitOverviewStats(Carbon $today, Carbon $yesterday, Carbon $startOfWeek): array
    {
        return [
            'visits_today' => Visit::whereDate('expected_arrival_time', $today)->count(),
            'visits_yesterday' => Visit::whereDate('expected_arrival_time', $yesterday)->count(),
            'visits_this_week' => Visit::whereBetween('expected_arrival_time', [$startOfWeek, $today->copy()->endOfDay()])->count(),
            'planned_visits' => Visit::whereNull('check_in_time')->whereDate('expected_arrival_time', '>=', $today)->count(),
        ];
    }

    private function getBehaviorStats(): array
    {
        $busiestDay = $this->getBusiestDay();
        $averageDurationSeconds = $this->getAverageVisitDuration();

        return [
            'busiest_day' => $busiestDay ? Carbon::parse($busiestDay->day)->format('d-m-Y') : null,
            'busiest_day_count' => $busiestDay ? $busiestDay->total : 0,
            'average_visit_duration' => $averageDurationSeconds > 0
                ? $this->formatDuration($averageDurationSeconds)
                : __('Nog geen afgeronde bezoeken'),
            'top_employees' => $this->getTopEmployees(),
        ];
    }

    private function getBusiestDay(): ?Visit
    {
        return Visit::whereNotNull('expected_arrival_time')
            ->selectRaw('DATE(expected_arrival_time) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderByDesc('total')
            ->orderByDesc('day')
            ->first();
    }

    private function getAverageVisitDuration(): int
    {
        $completedVisits = Visit::whereNotNull('check_in_time')
            ->whereNotNull('check_out_time')
            ->get(['check_in_time', 'check_out_time']);

        return (int) $completedVisits
            ->map(fn(Visit $visit) => $visit->check_in_time->diffInSeconds($visit->check_out_time))
            ->avg();
    }

    private function getTopEmployees()
    {
        return Employee::with('user')
            ->withCount('visits')
            ->has('visits')
            ->orderByDesc('visits_count')
            ->limit(3)
            ->get();
    }

    private function formatDuration(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($hours > 0) {
            return sprintf('%su %sm', $hours, $minutes);
        }

        return sprintf('%sm', $minutes);
    }
}
