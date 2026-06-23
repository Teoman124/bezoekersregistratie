<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;

class VisitController extends Controller
{
    public function active(): JsonResponse
    {
        $visits = Visit::active()
            ->with(['visitor.user', 'employee.user', 'employee.department'])
            ->latest('check_in_time')
            ->get();

        return response()->json([
            'visits' => $visits,
            'count'  => $visits->count(),
        ]);
    }
}