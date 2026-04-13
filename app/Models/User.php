<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    /**
     * Bezoeken waarbij deze gebruiker de gastheer is
     */
    public function hostedVisits()
    {
        return $this->hasMany(Visit::class, 'host_employee_id');
    }

    /**
     * Bezoeken die door deze gebruiker zijn geregistreerd
     */
    public function registeredVisits()
    {
        return $this->hasMany(Visit::class, 'registered_by_user_id');
    }

    /**
     * Helper method: Is deze gebruiker een admin?
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper method: Is deze gebruiker een receptionist?
     */
    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    /**
     * Helper method: Is deze gebruiker een medewerker?
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }
}