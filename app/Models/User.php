<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Employee;
use App\Models\Visitor;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function visitor()
    {
        return $this->hasOne(Visitor::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Auto employee/visitor maken
    protected static function booted()
    {
        static::created(function ($user) {
            if ($user->role === 'employee') {
                Employee::create([
                    'user_id' => $user->id,
                ]);
            }

            if ($user->role === 'visitor') {
                Visitor::create([
                    'user_id' => $user->id,
                ]);
            }
        });
    }
};