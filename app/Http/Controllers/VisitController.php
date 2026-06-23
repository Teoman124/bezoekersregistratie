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
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
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

    public function myvisits(Request $request)
    {
        $user = $request->user();

        // Haal bezoeken op met de benodigde relaties
        $query = Visit::with(['visitor.user', 'employee.user', 'employee.department']);

        $query->where(function ($q) use ($user) {
            $heeftProfiel = false;

            // Heeft deze gebruiker een bezoekersprofiel?
            if ($user->visitor) {
                $q->orWhere('visitor_id', $user->visitor->id);
                $heeftProfiel = true;
            }

            // Heeft deze gebruiker een medewerkersprofiel?
            if ($user->employee) {
                $q->orWhere('host_employee_id', $user->employee->id);
                $heeftProfiel = true;
            }

          
            // Als dit een Admin is (of een medewerker waarbij vergeten is een medewerkers-profiel aan te maken)
            // dwingen we de query om NIKS te vinden. Anders lekken we alle bezoeken van het hele bedrijf!
            if (!$heeftProfiel) {
                $q->where('id', '<', 0);
            }
        });

        // --- STATUS FILTERS ---
        if ($request->filled('status')) {
            if ($request->status === 'planned') {
                $query->whereNull('check_in_time');
            } elseif ($request->status === 'in') {
                $query->active(); // Zorg dat scopeActive() bestaat in je Visit model, anders gebruik: ->whereNotNull('check_in_time')->whereNull('check_out_time')
            } elseif ($request->status === 'out') {
                $query->whereNotNull('check_out_time');
            }
        }

        // Haal de bezoeken op (nieuwste eerst)
        $visits = $query->orderByDesc('expected_arrival_time')->get();

        return view('visits.myvisits', compact('visits'));
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
                ->with('info', __('This visitor is already checked in.'));
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
            'agreed_to_rules.required' => __('You must agree to the house rules and NDA to check in.'),
            'agreed_to_rules.accepted' => __('You must accept the house rules and NDA to check in.'),
        ]);

        // Voorkom dubbel inchecken
        if ($visit->check_in_time) {
            return redirect()->route('visits.show', $visit)
                ->with('error', __('This visitor is already checked in.'));
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
                'message' => __('Your visitor :name has arrived and agreed to the NDA/house rules.', ['name' => $visit->visitor->user->name]),
            ]);
        }

        return redirect()->route('visits.show', $visit)
            ->with('success', __('Visitor successfully checked in and NDA agreement recorded.'));
    }

    public function checkInViaQr(Visit $visit)
    {
        if ($visit->check_in_time) {
            return redirect()->route('visits.show', $visit)
                ->with('error', __('Visitor is already checked in.'));
        }

        $visit->update([
            'check_in_time' => now(),
            'check_out_time' => null,
        ]);

        // 🔥 VERBETERD: Stuur notificatie + e-mail naar host en bezoeker
        $this->sendCheckinNotifications($visit);

        return redirect()->route('visits.show', $visit)
            ->with('success', __('Visitor checked in via QR.'));
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

    /**
     * 🔥 VERBETERDE checkIn() methode
     * - Stuurt systeemnotificatie naar host
     * - Stuurt e-mail naar host
     * - Stuurt systeemnotificatie naar bezoeker (bevestiging)
     * - Stuurt e-mail naar bezoeker (bevestiging)
     */
    public function checkIn(Visit $visit)
    {
        // voorkom dubbel inchecken
        if ($visit->check_in_time) {
            return back()->with('error', __('Visitor is already checked in.'));
        }

        $visit->update([
            'check_in_time' => now(),
            'check_out_time' => null,
        ]);

        // 🔥 VERBETERD: Stuur notificaties + e-mails naar host en bezoeker
        $this->sendCheckinNotifications($visit);

        return back()->with('success', __('Visitor checked in.'));
    }

    public function checkOut(Visit $visit)
    {
        // eerst ingecheckt?
        if (! $visit->check_in_time) {
            return back()->with('error', __('Visitor has not checked in yet.'));
        }

        // voorkom dubbel uitchecken
        if ($visit->check_out_time) {
            return back()->with('error', __('Visitor is already checked out.'));
        }

        $visit->update([
            'check_out_time' => now(),
        ]);

        return back()->with('success', __('Visitor checked out.'));
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

    private function sendMail(Visit $visit, MailtrapApiService $mailtrapApiService): void
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

    /**
     * 🔥 NIEUWE METHODE: Stuur notificaties + e-mails bij check-in
     * - Host: systeemnotificatie + e-mail ("Uw bezoeker is gearriveerd")
     * - Bezoeker: systeemnotificatie + e-mail ("U bent ingecheckt")
     */
    private function sendCheckinNotifications(Visit $visit): void
    {
        $visitorUser = $visit->visitor?->user;
        $employeeUser = $visit->employee?->user;
        $visitorName = $visitorUser?->name ?? 'een bezoeker';
        $employeeName = $employeeUser?->name ?? 'de gastheer';
        $companyName = $visit->visitor?->company_name ?? 'Geen bedrijf opgegeven';
        $reason = $visit->reason_of_visit ?? 'Geen reden opgegeven';
        $checkInTime = $visit->check_in_time?->format('d-m-Y H:i') ?? now()->format('d-m-Y H:i');
        $arrivalTime = $visit->expected_arrival_time?->format('d-m-Y H:i') ?? 'onbekend';

        $mailtrapApiService = app(MailtrapApiService::class);

        // ──────────────────────────────────────────────
        // 1. NOTIFICATIE + E-MAIL NAAR DE HOST (medewerker)
        // ──────────────────────────────────────────────
        if ($employeeUser && $employeeUser->id) {
            // Systeemnotificatie voor host
            Notification::create([
                'user_id' => $employeeUser->id,
                'title' => '✅ Bezoeker gearriveerd',
                'message' => "Je bezoeker {$visitorName} is gearriveerd om {$checkInTime}. Reden: {$reason}.",
            ]);

            // E-mail naar host
            if ($employeeUser->email) {
                $hostSubject = "✅ Bezoeker gearriveerd: {$visitorName}";

                $hostText = "Beste {$employeeName},\n\n"
                    . "Je bezoeker {$visitorName} is gearriveerd!\n\n"
                    . "📋 Bezoekgegevens:\n"
                    . "- Naam: {$visitorName}\n"
                    . "- Bedrijf: {$companyName}\n"
                    . "- Reden: {$reason}\n"
                    . "- Aankomsttijd: {$checkInTime}\n"
                    . "- Verwachte tijd: {$arrivalTime}\n\n"
                    . "Je kunt de bezoeker ophalen bij de receptie.\n\n"
                    . "Met vriendelijke groet,\n"
                    . "Bezoekersregistratie Systeem";

                $hostHtml = "<p>Beste {$employeeName},</p>"
                    . "<p>Je bezoeker <strong>{$visitorName}</strong> is gearriveerd!</p>"
                    . "<h3>📋 Bezoekgegevens:</h3>"
                    . "<ul>"
                    . "<li><strong>Naam:</strong> {$visitorName}</li>"
                    . "<li><strong>Bedrijf:</strong> {$companyName}</li>"
                    . "<li><strong>Reden:</strong> {$reason}</li>"
                    . "<li><strong>Aankomsttijd:</strong> {$checkInTime}</li>"
                    . "<li><strong>Verwachte tijd:</strong> {$arrivalTime}</li>"
                    . "</ul>"
                    . "<p>Je kunt de bezoeker ophalen bij de receptie.</p>"
                    . "<p>Met vriendelijke groet,<br>Bezoekersregistratie Systeem</p>";

                $mailtrapApiService->send(
                    $employeeUser->email,
                    $hostSubject,
                    $hostText,
                    $hostHtml
                );
            }
        }

        // ──────────────────────────────────────────────
        // 2. NOTIFICATIE + E-MAIL NAAR DE BEZOEKER
        // ──────────────────────────────────────────────
        if ($visitorUser && $visitorUser->id) {
            // Systeemnotificatie voor bezoeker
            Notification::create([
                'user_id' => $visitorUser->id,
                'title' => '✅ Je bent ingecheckt',
                'message' => "Je bent succesvol ingecheckt bij {$employeeName} om {$checkInTime}.",
            ]);

            // E-mail naar bezoeker
            if ($visitorUser->email) {
                $visitorSubject = "✅ Je bent ingecheckt bij {$employeeName}";

                $visitorText = "Beste {$visitorName},\n\n"
                    . "Je bent succesvol ingecheckt bij {$employeeName}!\n\n"
                    . "📋 Bezoekgegevens:\n"
                    . "- Gastheer: {$employeeName}\n"
                    . "- Bedrijf: {$companyName}\n"
                    . "- Reden: {$reason}\n"
                    . "- Aankomsttijd: {$checkInTime}\n"
                    . "- Verwachte tijd: {$arrivalTime}\n\n"
                    . "Welkom en veel succes met je afspraak!\n\n"
                    . "Met vriendelijke groet,\n"
                    . "Bezoekersregistratie Systeem";

                $visitorHtml = "<p>Beste {$visitorName},</p>"
                    . "<p>Je bent succesvol ingecheckt bij <strong>{$employeeName}</strong>!</p>"
                    . "<h3>📋 Bezoekgegevens:</h3>"
                    . "<ul>"
                    . "<li><strong>Gastheer:</strong> {$employeeName}</li>"
                    . "<li><strong>Bedrijf:</strong> {$companyName}</li>"
                    . "<li><strong>Reden:</strong> {$reason}</li>"
                    . "<li><strong>Aankomsttijd:</strong> {$checkInTime}</li>"
                    . "<li><strong>Verwachte tijd:</strong> {$arrivalTime}</li>"
                    . "</ul>"
                    . "<p>Welkom en veel succes met je afspraak!</p>"
                    . "<p>Met vriendelijke groet,<br>Bezoekersregistratie Systeem</p>";

                $mailtrapApiService->send(
                    $visitorUser->email,
                    $visitorSubject,
                    $visitorText,
                    $visitorHtml
                );
            }
        }
    }

    /**
     * Toon de NDA pagina voor visitors (verplicht!)
     */
    public function showNdaPage(Request $request, Visit $visit): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $user->visitor || $user->visitor->id !== $visit->visitor_id) {
            abort(403, 'Je hebt geen toegang tot dit bezoek.');
        }

        if ($visit->agreed_to_rules) {
            return redirect()->route('visits.myvisits')->with('success', __('You have already accepted the NDA.'));
        }

        return view('visits.nda', compact('visit'));
    }

    /**
     * Verwerk de NDA acceptatie
     */
    public function acceptNda(Request $request, Visit $visit): RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $user->visitor || $user->visitor->id !== $visit->visitor_id) {
            abort(403, 'Je hebt geen toegang tot dit bezoek.');
        }

        $request->validate([
            'agreed_to_rules' => 'required|accepted',
        ], [
            'agreed_to_rules.required' => __('You must agree to the NDA and house rules to continue.'),
            'agreed_to_rules.accepted' => __('You must accept the NDA and house rules to continue.'),
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
                'message' => $visit->visitor->user->name.' heeft de NDA/huisregels geaccepteerd.',
                'link' => route('visits.show', $visit),
            ]);
        }

        return redirect()->route('visits.myvisits')
            ->with('success', __('✅ Thank you! You have successfully accepted the NDA. Welcome!'));
    }

    /**
     * Stuur NDA bevestiging per email
     */
    private function sendNdaConfirmationEmail(Visit $visit): void
    {
        $visitor = $visit->visitor?->user;
        $employee = $visit->employee?->user;

        if (! $visitor || ! $visitor->email) {
            return;
        }

        $mailtrapApiService = app(MailtrapApiService::class);

        $subject = '✅ Bevestiging NDA Akkoord - '.now()->format('d-m-Y H:i');

        $text = "Beste {$visitor->name},\n\n"
            .'Je hebt op '.now()->format('d-m-Y H:i')." akkoord gegeven op de NDA en huisregels.\n\n"
            ."📋 Bezoekgegevens:\n"
            .'- Gastheer: '.($employee?->name ?? 'Onbekend')."\n"
            .'- Datum: '.$visit->expected_arrival_time?->format('d-m-Y H:i')."\n"
            .'- Reden: '.($visit->reason_of_visit ?? 'Niet opgegeven')."\n"
            .'- IP-adres: '.$visit->agreed_ip."\n\n"
            ."Dit is een officiële bevestiging van je akkoord. Bewaar deze email als bewijs.\n\n"
            ."Met vriendelijke groet,\n"
            .'Bezoekersregistratie Systeem';

        $html = "<p>Beste {$visitor->name},</p>"
            .'<p>Je hebt op <strong>'.now()->format('d-m-Y H:i').'</strong> akkoord gegeven op de NDA en huisregels.</p>'
            .'<h3>📋 Bezoekgegevens:</h3>'
            .'<ul>'
            .'<li><strong>Gastheer:</strong> '.($employee?->name ?? 'Onbekend').'</li>'
            .'<li><strong>Datum:</strong> '.$visit->expected_arrival_time?->format('d-m-Y H:i').'</li>'
            .'<li><strong>Reden:</strong> '.($visit->reason_of_visit ?? 'Niet opgegeven').'</li>'
            .'<li><strong>IP-adres:</strong> '.$visit->agreed_ip.'</li>'
            .'</ul>'
            .'<p><em>Dit is een officiële bevestiging van je akkoord. Bewaar deze email als bewijs.</em></p>'
            .'<p>Met vriendelijke groet,<br>Bezoekersregistratie Systeem</p>';

        $mailtrapApiService->send(
            $visitor->email,
            $subject,
            $text,
            $html
        );
    }
}