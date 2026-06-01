<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    protected function ensureNotificationManager(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    public function index()
    {
        $notifications = Notification::with('user')->latest()->get();

        return view('notifications.index', compact('notifications'));
    }

    public function show(Notification $notification)
    {
        return view('notifications.show', compact('notification'));
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update([
            'read' => true,
        ]);

        return back();
    }

    public function edit(Notification $notification)
    {
        $this->ensureNotificationManager();

        $users = User::all();

        return view('notifications.edit', compact('notification', 'users'));
    }

    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        $this->ensureNotificationManager();

        $validated = $request->validated();
        $validated['read'] = $request->boolean('read');

        $notification->update($validated);

        return redirect()->route('notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    public function destroy(Notification $notification)
    {
        $this->ensureNotificationManager();

        $notification->delete();

        return back();
    }
}
