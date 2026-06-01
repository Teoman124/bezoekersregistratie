<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Employee;
use App\Models\User;
use App\Services\WelcomeMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected function ensureUserViewer(): void
    {
        abort_unless(in_array(auth()->user()?->role, ['admin', 'employee'], true), 403);
    }

    protected function ensureUserManager(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->ensureUserViewer();

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        $users = $query->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->ensureUserManager();

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, WelcomeMessageService $welcomeMessageService)
    {
        $this->ensureUserManager();

        $validatedData = $request->validated();

        // ✅ Password hashing
        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        $welcomeMessageService->send($user, $request->user());

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->ensureUserViewer();

        $user = User::findOrFail($user->id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->ensureUserManager();

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->ensureUserManager();

        $user = User::findOrFail($user->id);

        $validatedData = $request->validated();

        // ✅ Alleen hashen als password is ingevuld
        if (! empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $oldRole = $user->role;
        $user->update($validatedData);

        $newRole = $validatedData['role'] ?? $user->role;

        // Als de rol nu 'employee' is, maak Employee-record aan als die nog niet bestaat
        if ($newRole === 'employee') {
            if (! Employee::where('user_id', $user->id)->exists()) {
                Employee::create([
                    'user_id' => $user->id,
                    // afdeling en functie worden later handmatig toegevoegd
                ]);
            }
        } else {
            // Als de rol niet meer 'employee' is, verwijder Employee-record indien aanwezig
            Employee::where('user_id', $user->id)->delete();
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->ensureUserManager();

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
