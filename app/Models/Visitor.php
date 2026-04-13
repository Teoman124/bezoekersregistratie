<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'company',
        'phone',
    ];

    /**
     * Alle bezoeken van deze gast
     */
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Laatste bezoek van deze gast
     */
    public function lastVisit()
    {
        return $this->hasOne(Visit::class)->latestOfMany('check_in_time');
    }

    /**
     * Scope: Zoek bezoekers op naam of bedrijf
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', "%{$term}%")
            ->orWhere('company', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%");
    }
}