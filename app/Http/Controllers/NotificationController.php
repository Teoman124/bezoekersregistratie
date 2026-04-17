<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::all();

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update([
            'read' => true,
        ]);

        return back();
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return back();
    }
}