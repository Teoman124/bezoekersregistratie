<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // 🔥 CHECK: Is de gebruiker een visitor met een actief of gepland bezoek?
        if ($user && $user->visitor) {
            // Zoek een actief bezoek (ingecheckt maar nog niet uitgecheckt) zonder NDA
            $activeVisit = Visit::where('visitor_id', $user->visitor->id)
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();

            // Als er een actief bezoek is EN geen NDA akkoord
            if ($activeVisit && !$activeVisit->agreed_to_rules) {
                return redirect()->route('visitor.nda.show', $activeVisit)
                    ->with('error', '⚠️ Je moet eerst de NDA/huisregels accepteren!');
            }

            // Zoek een gepland bezoek (nog niet ingecheckt) van vandaag of morgen
            $pendingVisit = Visit::where('visitor_id', $user->visitor->id)
                ->whereNull('check_in_time')
                ->where('expected_arrival_time', '>=', now()->subHours(2))
                ->first();

            // Als er een gepland bezoek is, stuur naar NDA pagina
            if ($pendingVisit) {
                return redirect()->route('visitor.nda.show', $pendingVisit)
                    ->with('info', '📋 Je moet de NDA/huisregels accepteren voordat je kunt inchecken.');
            }
        }

        // 📊 Dashboard statistieken voor admin/employee
        $today = Carbon::now()->startOfDay();
        $yesterday = Carbon::now()->subDay()->startOfDay();
        $startOfWeek = Carbon::now()->startOfWeek();

        return view('dashboard', [
            'stats' => array_merge(
                $this->getCoreStats(),
                $this->getVisitOverviewStats($today, $yesterday, $startOfWeek),
                $this->getBehaviorStats(),
                $this->getPunctualityStats($today), 
            ),
            'chartsData' => [
                'visitsPerDay' => $this->getVisitsPerDay(),
                'visitsPerWeek' => $this->getVisitsPerWeek(),
                'busiestDepartments' => $this->getBusiestDepartments(),
                'stayDurationStats' => $this->getStayDurationStats(),
            ],
        ]);
    }


    private function getPunctualityStats(Carbon $today): array
    {
        // Haal de 5 meest recent ingecheckte bezoeken van vandaag op
        $recentVisits = Visit::with('visitor.user')
            ->whereDate('expected_arrival_time', $today)
            ->whereNotNull('check_in_time')
            ->orderByDesc('check_in_time')
            ->limit(5)
            ->get();

        $punctualityList = $recentVisits->map(function (Visit $visit) {
            $expected = $visit->expected_arrival_time;
            $actual = $visit->check_in_time;
            
            // Bereken verschil in minuten. 'false' zorgt voor negatieve waarden als ze te vroeg zijn.
            $diffInMinutes = (int) $expected->diffInMinutes($actual, false); 
            
            $statusText = 'Precies op tijd';
            $colorClass = 'text-green-600 dark:text-green-400 font-semibold'; // Op tijd

            if ($diffInMinutes > 0) {
                $statusText = "+{$diffInMinutes} min (te laat)";
                $colorClass = 'text-red-500 dark:text-red-400 font-semibold';
            } elseif ($diffInMinutes < 0) {
                $statusText = "{$diffInMinutes} min (te vroeg)";
                $colorClass = 'text-blue-500 dark:text-blue-400 font-semibold';
            }

            return [
                'name' => $visit->visitor?->user?->name ?? 'Onbekend',
                'status_text' => $statusText,
                'color_class' => $colorClass,
                'time' => $actual->format('H:i'),
            ];
        });

        return [
            'recent_punctuality' => $punctualityList,
        ];
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

    private function getVisitsPerDay(): array
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $days->push([
                'date' => $date->format('d-m'),
                'day' => $date->format('D'),
                'count' => Visit::whereDate('expected_arrival_time', $date)->count(),
            ]);
        }

        return [
            'labels' => $days->pluck('day')->toArray(),
            'data' => $days->pluck('count')->toArray(),
        ];
    }

    private function getVisitsPerWeek(): array
    {
        $weeks = collect();
        for ($i = 7; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            $weeks->push([
                'label' => 'W' . $startOfWeek->format('W'),
                'count' => Visit::whereBetween('expected_arrival_time', [$startOfWeek, $endOfWeek])->count(),
            ]);
        }

        return [
            'labels' => $weeks->pluck('label')->toArray(),
            'data' => $weeks->pluck('count')->toArray(),
        ];
    }

    private function getBusiestDepartments(): array
    {
        $departments = Department::withCount(['employees' => fn($q) => $q->withCount('visits')])
            ->get()
            ->map(fn($dept) => [
                'name' => $dept->name ?: __('Zonder afdeling'),
                'visits_count' => $dept->employees->sum(fn($emp) => $emp->visits_count ?? 0),
            ])
            ->sortByDesc('visits_count')
            ->take(5)
            ->values();

        return [
            'labels' => $departments->pluck('name')->toArray(),
            'data' => $departments->pluck('visits_count')->toArray(),
        ];
    }

    private function getStayDurationStats(): array
    {
        $completedVisits = Visit::whereNotNull('check_in_time')
            ->whereNotNull('check_out_time')
            ->get(['check_in_time', 'check_out_time'])
            ->map(fn(Visit $visit) => $visit->check_in_time->diffInMinutes($visit->check_out_time))
            ->filter(fn($minutes) => $minutes > 0);

        if ($completedVisits->isEmpty()) {
            return [
                'average' => 0,
                'min' => 0,
                'max' => 0,
                'median' => 0,
            ];
        }

        $sorted = $completedVisits->sort()->values();
        $count = $sorted->count();
        $median = $count % 2 === 0
            ? ($sorted[$count / 2 - 1] + $sorted[$count / 2]) / 2
            : $sorted[($count - 1) / 2];

        return [
            'average' => (int) $completedVisits->avg(),
            'min' => (int) $completedVisits->min(),
            'max' => (int) $completedVisits->max(),
            'median' => (int) $median,
        ];
    }
}