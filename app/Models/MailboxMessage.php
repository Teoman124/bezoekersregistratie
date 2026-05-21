<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailboxMessage extends Model
{
    use HasFactory;

    protected $table = 'mailbox_messages';

    protected $fillable = [
        'recipient_id',
        'sender_id',
        'title',
        'message',
        'read',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
