<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'service_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'appointment_time',
        'status',
        'booking_code',
        'notes'
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_NO_SHOW = 'no_show';
    

    // Generate a unique booking code
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($appointment) {
            // Generate a unique booking code if not provided
            if (empty($appointment->booking_code)) {
                $appointment->booking_code = strtoupper(Str::random(8));
                
                // Ensure the code is unique
                while (static::where('booking_code', $appointment->booking_code)->exists()) {
                    $appointment->booking_code = strtoupper(Str::random(8));
                }
            }
        });
    }


    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getEndTimeAttribute()
    {
        return $this->appointment_time->copy()->addMinutes($this->service->duration);
    }

    // Scope for upcoming appointments
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now())
            ->where('status', '!=', self::STATUS_CANCELLED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_time', Carbon::today())
            ->where('status', '!=', self::STATUS_CANCELLED);
    }

    public function canBeCancelled()
    {
        // Only allow cancellation if more than 24 hours before appointment
        return $this->start_time->diffInHours(now()) > 24 &&
            in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_PENDING]);
    }

    // Cancel appointment
    public function cancel()
    {
        $this->update(['status' => self::STATUS_CANCELLED]);

        if ($this->payment && $this->canBeCancelled()) {
            $this->payment->processRefund('Appointment cancelled by customer');
        }

        return $this;
    }

    public function complete()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
        return $this;
    }

    public function markAsNoShow()
    {
        $this->update(['status' => self::STATUS_NO_SHOW]);
        return $this;
    }

    public function getFormattedDateAttribute()
    {
        return $this->start_time->format('l, F j, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->start_time->format('g:i A') . ' - ' .
            $this->end_time->format('g:i A');
    }
}
