<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Department;
use App\Services\MailtrapApiService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;


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
                'Akkoord NDA/Huisregels',
                'Akkoord datum/tijd',
                'Akkoord IP-adres',
            ]);

            foreach ($visits as $visit) {
                // Bepaal status handmatig (fix voor stdClass error)
                if ($visit->check_in_time === null) {
                    $status = 'planned';
                } elseif ($visit->check_out_time === null) {
                    $status = 'active';
                } else {
                    $status = 'checked_out';
                }

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
                    $status,
                    $visit->agreed_to_rules ? 'Ja' : 'Nee',
                    $visit->agreed_at?->format('Y-m-d H:i:s') ?? '-',
                    $visit->agreed_ip ?? '-',
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function activeExport(): StreamedResponse
    {
        $visits = $this->getActiveVisits();
        $employees = $this->getPresentEmployees($visits);
        $fileName = 'noodlijst-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($visits, $employees): void {
            $output = fopen('php://output', 'wb');

            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'Type',
                'Naam',
                'E-mail',
                'Bedrijf',
                'Afdeling',
                'Functie',
                'Gastheer',
                'Reden',
                'Aankomst',
                'Status',
            ]);

            foreach ($visits as $visit) {
                fputcsv($output, [
                    'Bezoeker',
                    $visit->visitor?->user?->name ?? '-',
                    $visit->visitor?->user?->email ?? '-',
                    $visit->visitor?->company_name ?? '-',
                    $visit->employee?->department?->name ?? '-',
                    '-',
                    $visit->employee?->user?->name ?? '-',
                    $visit->reason_of_visit ?: '-',
                    $visit->check_in_time?->format('Y-m-d H:i:s') ?? '-',
                    'aanwezig',
                ]);
            }

            foreach ($employees as $employee) {
                fputcsv($output, [
                    'Medewerker',
                    $employee->user?->name ?? '-',
                    $employee->user?->email ?? '-',
                    '-',
                    $employee->department?->name ?? '-',
                    $employee->function ?: '-',
                    '-',
                    '-',
                    '-',
                    'aanwezig',
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

  public function index(Request $request)
    {
        // Haal bezoeken op met de benodigde relaties
        $query = Visit::with(['visitor.user', 'employee.user', 'employee.department']);

        // 1. Zoeken op naam (bezoeker of gastheer)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('visitor.user', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', '%' . $search . '%');
                })->orWhereHas('employee.user', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', '%' . $search . '%');
                });
            });
        }

        // 2. Filteren op Datum
        if ($request->filled('date')) {
            $query->whereDate('expected_arrival_time', $request->date);
        }

        // 3. Filteren op Afdeling
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // 4. Filteren op Gastheer (Host)
        if ($request->filled('host_id')) {
            $query->where('host_employee_id', $request->host_id);
        }

        // 5. Filteren op Status (Inclusief de acties uit je oude code)
        if ($request->filled('status')) {
            if ($request->status === 'planned') {
                $query->whereNull('check_in_time');
            } elseif ($request->status === 'in') {
                $query->active(); // Roept jouw bestaande scopeActive() aan
            } elseif ($request->status === 'out') {
                $query->whereNotNull('check_out_time');
            }
        }

        // Sorteer op meest recente (of toekomstige) bezoeken eerst
        $visits = $query->orderBy('expected_arrival_time', 'desc')->get();

        // Haal extra data op die we nodig hebben voor de dropdown-filters in de view
        $departments = Department::orderBy('name')->get();
        $employees = Employee::with('user')->get()->sortBy(fn ($e) => $e->user?->name);

        return view('visits.index', compact('visits', 'departments', 'employees'));
    
    }

    public function active(): View
    {
        $visits = $this->getActiveVisits();
        $employees = $this->getPresentEmployees($visits);

        return view('visits.active', compact('visits', 'employees'));
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

        // Sorting - fix voor deprecated get()
        $sortBy = $request->input('sort', 'expected_arrival_time');
        $sortOrder = $request->input('order', 'desc');

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

        // Standaard NDA akkoord op false bij aanmaken
        $validated['agreed_to_rules'] = false;
        $validated['agreed_at'] = null;
        $validated['agreed_ip'] = null;

        $visit = Visit::create($validated);
        $visit->load(['visitor.user', 'employee.user']);

        $mailtrapApiService = app(MailtrapApiService::class);
        $this->sendMail($visit, $mailtrapApiService);

        return redirect()->route('visits.index')
            ->with('success', 'Visit created successfully.');
    }

    public function show(Visit $visit)
    {
        $checkinQrUrl = null;

        if (! $visit->check_in_time) {
            $checkinQrUrl = URL::temporarySignedRoute(
                'visits.checkin.qr',
                now()->addHours(8),
                ['visit' => $visit],
            );
        }

        return view('visits.show', compact('visit', 'checkinQrUrl'));
    }

    /**
     * Toon de check-in pagina met NDA/huisregels
     */
    public function showCheckinForm(Visit $visit)
    {
        // Controleer of de bezoeker al is ingecheckt
        if ($visit->check_in_time) {
            return redirect()->route('visits.show', $visit)
                ->with('info', 'Deze bezoeker is al ingecheckt.');
        }

        return view('visits.checkin', compact('visit'));
    }

    /**
     * Verwerk de check-in met NDA akkoord
     */
    public function processCheckin(Request $request, Visit $visit)
    {
        // Valideer dat de bezoeker akkoord gaat met de huisregels/NDA
        $request->validate([
            'agreed_to_rules' => 'required|accepted',
        ], [
            'agreed_to_rules.required' => 'Je moet akkoord gaan met de huisregels en NDA om in te checken.',
            'agreed_to_rules.accepted' => 'Je moet de huisregels en NDA accepteren om in te checken.',
        ]);

        // Voorkom dubbel inchecken
        if ($visit->check_in_time) {
            return redirect()->route('visits.show', $visit)
                ->with('error', 'Deze bezoeker is al ingecheckt.');
        }

        // Update met check-in en NDA akkoord
        $visit->update([
            'check_in_time' => now(),
            'check_out_time' => null,
            'agreed_to_rules' => true,
            'agreed_at' => now(),
            'agreed_ip' => $request->ip(),
        ]);

        // Stuur notificatie naar de host employee
        if ($visit->employee && $visit->visitor && $visit->visitor->user) {
            Notification::create([
                'user_id' => $visit->employee->user_id,
                'title' => 'Bezoeker ingecheckt',
                'message' => 'Je bezoeker ' . $visit->visitor->user->name . ' is aangekomen en heeft akkoord gegeven op de NDA/huisregels.',
            ]);
        }

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Bezoeker succesvol ingecheckt en NDA akkoord vastgelegd.');
    }

    public function checkInViaQr(Visit $visit)
    {
        if ($visit->check_in_time) {
            return redirect()->route('visits.show', $visit)
                ->with('error', 'Visitor is already checked in.');
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

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Visitor checked in via QR.');
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

    private function getActiveVisits(): Collection
    {
        return Visit::active()
            ->with(['visitor.user', 'employee.user', 'employee.department'])
            ->latest('check_in_time')
            ->get();
    }

    private function getPresentEmployees(Collection $visits): Collection
    {
        $employeeIds = $visits
            ->pluck('host_employee_id')
            ->filter()
            ->unique()
            ->values();

        if ($employeeIds->isEmpty()) {
            return collect();
        }

        return Employee::query()
            ->with(['user', 'department'])
            ->whereIn('id', $employeeIds)
            ->get()
            ->sortBy(fn (Employee $employee) => $employee->user?->name ?? '')
            ->values();
    }

    private function sendMail(Visit $visit, MailtrapApiService $mailtrapApiService): void    {
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
    

        /**
     * Toon de NDA pagina voor visitors (verplicht!)
     */
    /**
 * Toon de NDA pagina voor visitors (verplicht!)
 */
    public function showNdaPage(Request $request, Visit $visit): View|RedirectResponse
    {
        $user = $request->user();
        
        if (!$user || !$user->visitor || $user->visitor->id !== $visit->visitor_id) {
            abort(403, 'Je hebt geen toegang tot dit bezoek.');
        }

        if ($visit->agreed_to_rules) {
            return redirect()->route('visits.myvisits')->with('success', 'Je hebt de NDA al geaccepteerd.');
        }

        // 🔥 Gewijzigd: view in visits map
        return view('visits.nda', compact('visit'));
    }

    /**
 * Verwerk de NDA acceptatie
 */
    public function acceptNda(Request $request, Visit $visit): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user || !$user->visitor || $user->visitor->id !== $visit->visitor_id) {
            abort(403, 'Je hebt geen toegang tot dit bezoek.');
        }

        $request->validate([
            'agreed_to_rules' => 'required|accepted',
        ], [
            'agreed_to_rules.required' => 'Je moet akkoord gaan met de NDA en huisregels.',
            'agreed_to_rules.accepted' => 'Je moet de NDA en huisregels accepteren om verder te gaan.',
        ]);

        $visit->update([
            'agreed_to_rules' => true,
            'agreed_at' => now(),
            'agreed_ip' => $request->ip(),
            'check_in_time' => $visit->check_in_time ?? now(),
        ]);

        $this->sendNdaConfirmationEmail($visit);

        if ($visit->employee && $visit->visitor && $visit->visitor->user) {
            Notification::create([
                'user_id' => $visit->employee->user_id,
                'title' => '✅ NDA getekend door bezoeker',
                'message' => $visit->visitor->user->name . ' heeft de NDA/huisregels geaccepteerd.',
                'link' => route('visits.show', $visit),
            ]);
        }

        // 🔥 Gewijzigd: redirect naar /Visits/my in plaats van dashboard
        return redirect()->route('visits.myvisits')
            ->with('success', '✅ Bedankt! Je hebt de NDA succesvol geaccepteerd. Welkom!');
    }

    /**
     * Stuur NDA bevestiging per email
     */
    private function sendNdaConfirmationEmail(Visit $visit): void
    {
        $visitor = $visit->visitor?->user;
        $employee = $visit->employee?->user;
        
        if (!$visitor || !$visitor->email) {
            return;
        }

        $mailtrapApiService = app(MailtrapApiService::class);
        
        $subject = '✅ Bevestiging NDA Akkoord - ' . now()->format('d-m-Y H:i');
        
        $text = "Beste {$visitor->name},\n\n"
            . "Je hebt op " . now()->format('d-m-Y H:i') . " akkoord gegeven op de NDA en huisregels.\n\n"
            . "📋 Bezoekgegevens:\n"
            . "- Gastheer: " . ($employee?->name ?? 'Onbekend') . "\n"
            . "- Datum: " . $visit->expected_arrival_time?->format('d-m-Y H:i') . "\n"
            . "- Reden: " . ($visit->reason_of_visit ?? 'Niet opgegeven') . "\n"
            . "- IP-adres: " . $visit->agreed_ip . "\n\n"
            . "Dit is een officiële bevestiging van je akkoord. Bewaar deze email als bewijs.\n\n"
            . "Met vriendelijke groet,\n"
            . "Bezoekersregistratie Systeem";
        
        $html = "<p>Beste {$visitor->name},</p>"
            . "<p>Je hebt op <strong>" . now()->format('d-m-Y H:i') . "</strong> akkoord gegeven op de NDA en huisregels.</p>"
            . "<h3>📋 Bezoekgegevens:</h3>"
            . "<ul>"
            . "<li><strong>Gastheer:</strong> " . ($employee?->name ?? 'Onbekend') . "</li>"
            . "<li><strong>Datum:</strong> " . $visit->expected_arrival_time?->format('d-m-Y H:i') . "</li>"
            . "<li><strong>Reden:</strong> " . ($visit->reason_of_visit ?? 'Niet opgegeven') . "</li>"
            . "<li><strong>IP-adres:</strong> " . $visit->agreed_ip . "</li>"
            . "</ul>"
            . "<p><em>Dit is een officiële bevestiging van je akkoord. Bewaar deze email als bewijs.</em></p>"
            . "<p>Met vriendelijke groet,<br>Bezoekersregistratie Systeem</p>";
        
        $mailtrapApiService->send(
            $visitor->email,
            $subject,
            $text,
            $html
        );
    }
}