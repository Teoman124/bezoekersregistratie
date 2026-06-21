<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Notification;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    public function index()
    {
        return view('kiosk.index');
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2'
        ]);

        $searchName = $request->input('name');

        // Zoek een gepland bezoek op naam binnen nu +/- 30 minuten
        $visit = Visit::whereNull('check_in_time')
            ->whereDate('expected_arrival_time', today())
            ->whereBetween('expected_arrival_time', [
                now()->subMinutes(30), // Max 30 minuten te laat
                now()->addMinutes(30)  // Max 30 minuten te vroeg
            ])
            ->whereHas('visitor.user', function ($query) use ($searchName) {
                $query->where('name', 'LIKE', '%' . $searchName . '%');
            })
            ->first();

        // Geen bezoek gevonden?
        if (! $visit) {
            return back()->with('error', 'We konden geen afspraak vinden onder deze naam op dit tijdstip. Controleer de spelling of meld je bij de receptie.');
        }

        // Wel gevonden! Inchecken.
        $visit->update([
            'check_in_time' => now(),
        ]);

        // Stuur notificatie naar de medewerker (gastheer)
        if ($visit->employee && $visit->employee->user_id) {
            Notification::create([
                'user_id' => $visit->employee->user_id,
                'title' => 'Bezoeker in de hal',
                'message' => 'Je bezoeker ' . $visit->visitor->user->name . ' heeft zich zojuist aangemeld via de kiosk.',
            ]);
        }

        // Redirect naar de succes pagina en geef de naam van de gastheer mee
        return redirect()->route('kiosk.success')->with('host_name', $visit->employee->user->name ?? 'je gastheer');
    }

    public function success()
    {
        // Als iemand de success pagina direct bezoekt zonder in te checken, stuur ze terug
        if (! session()->has('host_name')) {
            return redirect()->route('kiosk.index');
        }

        return view('kiosk.success');
    }
    
    public function reset()
    {
        // Zet de taal keihard terug naar Nederlands
        session()->put('locale', 'nl');
        
        // Stuur terug naar het Kiosk startscherm
        return redirect()->route('kiosk.index');
    }
}