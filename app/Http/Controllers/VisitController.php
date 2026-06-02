<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Visit;
use App\Models\Visitor;
use App\Services\MailtrapApiService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisitController extends Controller
{
    public function export(): StreamedResponse
    {
        $fileName = 'bezoekers-historie-'.now()->format('Y-m-d_His').'.csv';

        $visits = Visit::query()
            ->with(['visitor.user', 'employee.user', 'employee.department'])
            ->orderByDesc('expected_arrival_time')
            ->get();

        return response()->streamDownload(function () use ($visits): void {
            $output = fopen('php://output', 'wb');

            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'Datum',
                'Bezoeker',
                'Bezoeker e-mail',
                'Bedrijf',
                'Medewerker',
                'Afdeling',
                'Reden',
                'Verwachte aankomst',
                'Werkelijke aankomst',
                'Verwacht vertrek',
                'Werkelijke vertrek',
                'Status',
            ]);

            foreach ($visits as $visit) {
                fputcsv($output, [
                    $visit->expected_arrival_time?->format('Y-m-d'),
                    $visit->visitor?->user?->name ?? '-',
                    $visit->visitor?->user?->email ?? '-',
                    $visit->visitor?->company_name ?? '-',
                    $visit->employee?->user?->name ?? '-',
                    $visit->employee?->department?->name ?? '-',
                    $visit->reason_of_visit ?: '-',
                    $visit->expected_arrival_time?->format('Y-m-d H:i:s') ?? '-',
                    $visit->check_in_time?->format('Y-m-d H:i:s') ?? '-',
                    $visit->expected_departure_time?->format('Y-m-d H:i:s') ?? '-',
                    $visit->check_out_time?->format('Y-m-d H:i:s') ?? '-',
                    $visit->currentStatus(),
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function index(Request $request)
    {
        $query = Visit::with(['visitor.user', 'employee.user']);

        if ($request->filled('status')) {
            if ($request->status === 'in') {
                $query->active();
            }

            if ($request->status === 'out') {
                $query->whereNotNull('check_out_time');
            }
        }

        $visits = $query->get();

        return view('visits.index', compact('visits'));
    }

    public function active()
    {
        $visits = Visit::active()
            ->with(['visitor.user', 'employee.user'])
            ->latest('check_in_time')
            ->get();

        return view('visits.active', compact('visits'));
    }

    public function history(Request $request)
    {
        $query = Visit::with(['visitor.user', 'employee.user', 'employee.department']);

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('check_out_time');
            } elseif ($request->status === 'active') {
                $query->whereNotNull('check_in_time')
                    ->whereNull('check_out_time');
            } elseif ($request->status === 'planned') {
                $query->whereNull('check_in_time');
            }
        }

        // Date filter
        if ($request->filled('date_filter')) {
            if ($request->date_filter === 'yesterday') {
                $query->whereDate('expected_arrival_time', now()->subDay());
            } elseif ($request->date_filter === 'week') {
                $query->whereBetween('expected_arrival_time', [
                    now()->subDays(7)->startOfDay(),
                    now()->endOfDay(),
                ]);
            } elseif ($request->date_filter === 'month') {
                $query->whereDate('expected_arrival_time', '>=', now()->startOfMonth())
                    ->whereDate('expected_arrival_time', '<=', now()->endOfMonth());
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'expected_arrival_time');
        $sortOrder = $request->get('order', 'desc');

        if (in_array($sortBy, ['expected_arrival_time', 'check_in_time', 'check_out_time', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $visits = $query->get();

        return view('visits.history', compact('visits'));
    }

    public function myVisits(Request $request)
    {
        $user = $request->user();

        $query = Visit::with(['visitor.user', 'employee.user'])
            ->where(function ($q) use ($user) {

                // visitor ownership
                if ($user->visitor) {
                    $q->orWhere('visitor_id', $user->visitor->id);
                }

                // employee ownership
                if ($user->employee) {
                    $q->orWhere('host_employee_id', $user->employee->id);
                }

                // optional: direct user link (als je dat gebruikt in DB)
                $q->orWhere('user_id', $user->id);
            });

        if ($request->filled('status')) {
            if ($request->status === 'planned') {
                $query->whereNull('check_in_time');
            }

            if ($request->status === 'in') {
                $query->active();
            }

            if ($request->status === 'out') {
                $query->whereNotNull('check_out_time');
            }
        }

        $visits = $query->latest('expected_arrival_time')->get();

        return view('visits.MyVisits', compact('visits'));
    }

    public function create()
    {
        $employees = Employee::with('department')->get();
        $visitors = Visitor::all();

        return view('visits.create', compact('employees', 'visitors'));
    }

    public function store(StoreVisitRequest $request)
    {
        $validated = $request->validated();
        $status = $validated['status'] ?? 'planned';

        unset($validated['status']);

        if ($status === 'planned') {
            $validated['check_in_time'] = null;
            $validated['check_out_time'] = null;
        }

        if ($status === 'active') {
            $validated['check_in_time'] = now();
            $validated['check_out_time'] = null;
        }

        if ($status === 'checked_out') {
            $validated['check_in_time'] = now();
            $validated['check_out_time'] = now();
        }

        $visit = Visit::create($validated);
        $visit->load(['visitor.user', 'employee.user']);

        $mailtrapApiService = app(MailtrapApiService::class);
        $this->sendMail($visit, $mailtrapApiService);

        return redirect()->route('visits.index')
            ->with('success', 'Visit created successfully.');
    }

    public function show(Visit $visit)
    {
        return view('visits.show', compact('visit'));
    }

    public function edit(Visit $visit)
    {
        $employees = Employee::all();
        $visitors = Visitor::all();

        return view('visits.edit', compact('visit', 'employees', 'visitors'));
    }

    public function update(UpdateVisitRequest $request, Visit $visit)
    {
        $validated = $request->validated();
        $status = $validated['status'];

        unset($validated['status']);

        $visit->update($validated);

        if ($status === 'planned') {
            $visit->update([
                'check_in_time' => null,
                'check_out_time' => null,
            ]);
        }

        if ($status === 'active') {
            $visit->update([
                'check_in_time' => $visit->check_in_time ?? now(),
                'check_out_time' => null,
            ]);
        }

        if ($status === 'checked_out') {
            $visit->update([
                'check_in_time' => $visit->check_in_time ?? now(),
                'check_out_time' => $visit->check_out_time ?? now(),
            ]);
        }

        return redirect()->route('visits.index')
            ->with('success', 'Visit updated successfully.');
    }

    public function destroy(Visit $visit)
    {
        $visit->delete();

        return redirect()->route('visits.index')
            ->with('success', 'Visit deleted successfully.');
    }

    public function checkIn(Visit $visit)
    {
        // voorkom dubbel inchecken
        if ($visit->check_in_time) {
            return back()->with('error', 'Visitor is already checked in.');
        }

        $visit->update([
            'check_in_time' => now(),
            'check_out_time' => null,
        ]);

        if ($visit->employee && $visit->visitor && $visit->visitor->user) {
            Notification::create([
                'user_id' => $visit->employee->user_id,
                'title' => 'Bezoeker ingecheckt',
                'message' => 'Je bezoeker '.$visit->visitor->user->name.' is aangekomen.',
            ]);
        }

        return back()->with('success', 'Visitor checked in.');
    }

    public function checkOut(Visit $visit)
    {
        // eerst ingecheckt?
        if (! $visit->check_in_time) {
            return back()->with('error', 'Visitor has not checked in yet.');
        }

        // voorkom dubbel uitchecken
        if ($visit->check_out_time) {
            return back()->with('error', 'Visitor is already checked out.');
        }

        $visit->update([
            'check_out_time' => now(),
        ]);

        return back()->with('success', 'Visitor checked out.');
    }

    private function sendMail($visit, MailtrapApiService $mailtrapApiService): void
    {
          $visitor = $visit->visitor?->user;
        $employee = $visit->employee?->user;
        $visitorName = $visitor?->name ?? 'Bezoeker';
        $employeeName = $employee?->name ?? 'de gastheer';
        $arrivalTime = $visit->expected_arrival_time?->format('d-m-Y H:i') ?? 'onbekend';
        $departureTime = $visit->expected_departure_time?->format('d-m-Y H:i');

        $recipientMails = [
            [
                'email' => $visitor?->email,
                'subject' => 'Bevestiging van je bezoek',
                'text' => "Hallo {$visitorName},\n\nJe bezoek is ingepland bij {$employeeName}.\nVerwachte aankomst: {$arrivalTime}.",
                'html' => '<p>Hallo '.e($visitorName).',</p>'
                    .'<p>Je bezoek is ingepland bij '.e($employeeName).'.</p>'
                    .'<p><strong>Verwachte aankomst:</strong> '.e($arrivalTime).'</p>',
            ],
            [
                'email' => $employee?->email,
                'subject' => 'Nieuwe afspraak ingepland',
                'text' => "Hallo {$employeeName},\n\nEr is een bezoek ingepland door {$visitorName}.\nVerwachte aankomst: {$arrivalTime}.",
                'html' => '<p>Hallo '.e($employeeName).',</p>'
                    .'<p>Er is een bezoek ingepland door '.e($visitorName).'.</p>'
                    .'<p><strong>Verwachte aankomst:</strong> '.e($arrivalTime).'</p>',
            ],
        ];

        if ($departureTime) {
            foreach ($recipientMails as &$recipientMail) {
                $recipientMail['text'] .= "\nVerwacht vertrek: {$departureTime}.";
                $recipientMail['html'] .= '<p><strong>Verwacht vertrek:</strong> '.e($departureTime).'</p>';
            }

            unset($recipientMail);
        }

        foreach ($recipientMails as $recipientMail) {
            if (blank($recipientMail['email'])) {
                continue;
            }

            $recipientMail['text'] .= "\n\nTot snel.";
            $recipientMail['html'] .= '<p>Tot snel.</p>';

            $mailtrapApiService->send(
                $recipientMail['email'],
                $recipientMail['subject'],
                $recipientMail['text'],
                $recipientMail['html'],
            );
        }
    }
}
