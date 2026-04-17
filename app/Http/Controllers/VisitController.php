<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Visit;
use App\Models\Employee;
use App\Models\Visitor;
use App\Models\Notification;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::query();

        if ($request->filled('status')) {
            if ($request->status === 'in') {
                $query->whereNull('check_out_time');
            }

            if ($request->status === 'out') {
                $query->whereNotNull('check_out_time');
            }
        }

        $visits = $query->get();

        return view('visits.index', compact('visits'));
    }

    public function create()
    {
        $employees = Employee::with('department')->get();
        $visitors = Visitor::all();

        return view('visits.create', compact('employees', 'visitors'));
    }

    public function store(StoreVisitRequest $request)
    {
        Visit::create($request->validated());

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
        $visit->update($request->validated());

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
        $visit->update([
            'check_in_time' => now(),
        ]);

        //  notificatie word naar employee gestuurd (host)
        Notification::create([
            'user_id' => $visit->employee->user_id,
            'title' => 'Bezoeker ingecheckt',
            'message' => 'Je bezoeker ' . $visit->visitor->user->name . ' is aangekomen.',
        ]);

        return back()->with('success', 'Visitor checked in.');
    }

    
    public function checkOut(Visit $visit)
    {
        $visit->update([
            'check_out_time' => now(),
        ]);

        return back()->with('success', 'Visitor checked out.');
    }
}