<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;

class RedirectIfNdaNotSigned
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Alleen voor visitors
        if ($user && $user->visitor) {
            // Check of deze visitor een actief bezoek heeft dat nog geen NDA heeft
            $activeVisit = Visit::where('visitor_id', $user->visitor->id)
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();

            // Als er een actief bezoek is maar geen NDA akkoord
            if ($activeVisit && ! $activeVisit->agreed_to_rules) {
                // Niet redirecten op deze routes (voorkom oneindige loop)
                $excludeRoutes = [
                    'visitor.nda.show',
                    'visitor.nda.accept',
                    'logout',
                ];

                if (! in_array($request->route()->getName(), $excludeRoutes)) {
                    return redirect()->route('visitor.nda.show', $activeVisit)
                        ->with('error', __('⚠️ You must accept the NDA/house rules first!'));
                }
            }

            // Als er een bezoek is zonder check-in, redirect naar NDA
            $pendingVisit = Visit::where('visitor_id', $user->visitor->id)
                ->whereNull('check_in_time')
                ->where('expected_arrival_time', '>=', now()->subHours(2))
                ->first();

            if ($pendingVisit && ! $request->routeIs('visitor.nda.show')) {
                return redirect()->route('visitor.nda.show', $pendingVisit)
                    ->with('info', __('You must accept the NDA before you can check in.'));
            }
        }

        return $next($request);
    }
}
