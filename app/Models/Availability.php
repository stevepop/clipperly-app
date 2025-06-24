<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'max_appointments',
        'is_available',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'max_appointments' => 'integer',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getDayOfWeekAttribute()
    {
        return $this->date->dayOfWeek;
    }

     // Check if this availability has reached its booking limit
     public function hasReachedBookingLimit()
     {
         return $this->appointments()
             ->whereNotIn('status', ['cancelled', 'no_show'])
             ->count() >= $this->max_appointments;
     }
    
    
}
