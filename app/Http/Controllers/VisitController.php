<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitController extends Controller
{
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

        Visit::create($validated);

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
        $visit->update([
            'check_in_time' => now(),
            'check_out_time' => null,
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
