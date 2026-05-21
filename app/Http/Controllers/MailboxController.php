<?php

namespace App\Http\Controllers;

use App\Models\MailboxMessage;
use App\Models\User;
use Illuminate\Http\Request;

class MailboxController extends Controller
{
    public function index(Request $request)
    {
        $folder = $request->string('folder')->lower()->toString();

        $notifications = MailboxMessage::with(['sender', 'recipient'])
            ->when(
                $folder === 'sent',
                fn ($query) => $query->where('sender_id', auth()->id()),
                fn ($query) => $query->where('recipient_id', auth()->id())
            )
            ->latest()
            ->get();

        $inboxCount = MailboxMessage::where('recipient_id', auth()->id())->count();
        $sentCount = MailboxMessage::where('sender_id', auth()->id())->count();

        return view('mailbox.index', compact('notifications', 'folder', 'inboxCount', 'sentCount'));
    }

    public function create(Request $request)
    {
        $users = User::query()
            ->whereKeyNot(auth()->id())
            ->orderBy('name')
            ->get();

        return view('mailbox.create', [
            'users' => $users,
            'selectedUserId' => $request->integer('to'),
        ]);
    }

    public function show(MailboxMessage $mailboxMessage)
    {
        $notification = $mailboxMessage;

        abort_unless(
            $notification->recipient_id === auth()->id() || $notification->sender_id === auth()->id(),
            403
        );

        if ($notification->recipient_id === auth()->id() && ! $notification->read) {
            $notification->update(['read' => true]);
        }

        return view('mailbox.show', compact('notification'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        MailboxMessage::create([
            'recipient_id' => $data['recipient_id'],
            'sender_id' => auth()->id(),
            'title' => $data['subject'],
            'message' => $data['body'],
            'read' => false,
        ]);

        return redirect()
            ->route('mailbox.index', ['folder' => 'sent'])
            ->with('success', 'Bericht toegevoegd aan de mailbox.');
    }

    public function destroy(MailboxMessage $mailboxMessage)
    {
        $notification = $mailboxMessage;

        abort_unless($notification->recipient_id === auth()->id(), 403);

        $notification->delete();

        return redirect()
            ->route('mailbox.index')
            ->with('success', 'Bericht verwijderd.');
    }
}
