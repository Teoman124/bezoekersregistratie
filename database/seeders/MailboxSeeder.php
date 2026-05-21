<?php

namespace Database\Seeders;

use App\Models\MailboxMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class MailboxSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::firstOrCreate([
            'email' => 'alice@example.com',
        ], [
            'name' => 'Alice Sender',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        $bob = User::firstOrCreate([
            'email' => 'bob@example.com',
        ], [
            'name' => 'Bob Recipient',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        MailboxMessage::updateOrCreate([
            'recipient_id' => $bob->id,
            'title' => 'Welkom bij Mailbox',
        ], [
            'message' => 'Hoi Bob, welkom! Dit is een voorbeeldbericht in je mailbox.',
            'sender_id' => $alice->id,
            'read' => false,
        ]);

        MailboxMessage::updateOrCreate([
            'recipient_id' => $bob->id,
            'title' => 'Onderhoud',
        ], [
            'message' => 'Systeemonderhoud gepland voor vannacht 02:00.',
            'sender_id' => null,
            'read' => true,
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $sender = User::where('id', '!=', $user->id)->inRandomOrder()->first();

            MailboxMessage::create([
                'recipient_id' => $user->id,
                'sender_id' => $sender->id,
                'title' => 'Bericht van ' . $sender->name,
                'message' => 'Hallo ' . $user->name . ', dit is een automatisch gegenereerd bericht.',
                'read' => false,
            ]);
        }
    }
}

