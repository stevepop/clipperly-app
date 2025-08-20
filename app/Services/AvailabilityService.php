<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Get available time slots for a given date and service
     *
     * @param Carbon $date The date to check
     * @param Service $service The service being booked
     * @return Collection Collection of available time slots as ['start_time' => Carbon]
     */
    public function getAvailableTimeSlots(Carbon $date, Service $service): Collection
    {
        // Find all availability blocks for this date
        $availabilities = Availability::where('is_available', true)
            ->where(function ($query) use ($date) {
                // One-time availabilities for this date
                $query->whereDate('start_time', $date->toDateString())
                    ->whereNull('recurrence_type');
                    
                // Daily recurring availabilities
                $query->orWhere(function ($q) use ($date) {
                    $q->where('recurrence_type', 'daily')
                        ->where(function ($q2) use ($date) {
                            $q2->whereNull('recurrence_end_date')
                                ->orWhereDate('recurrence_end_date', '>=', $date->toDateString());
                        });
                });
                
                // Weekly recurring availabilities for this day of week
                $query->orWhere(function ($q) use ($date) {
                    $q->where('recurrence_type', 'weekly')
                        ->whereJsonContains('recurrence_days', (string) $date->dayOfWeek)
                        ->where(function ($q2) use ($date) {
                            $q2->whereNull('recurrence_end_date')
                                ->orWhereDate('recurrence_end_date', '>=', $date->toDateString());
                        });
                });
            })
            ->get();
            
        if ($availabilities->isEmpty()) {
            return collect([]);
        }
        
        // Get existing appointments for this date to check conflicts
        $existingAppointments = Appointment::whereDate('appointment_time', $date->toDateString())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->with('service')
            ->get();
        
        // For each availability block, generate time slots of the required duration
        $allTimeSlots = collect();
        
        foreach ($availabilities as $availability) {
            // Normalize start and end times to the requested date
            $startTime = Carbon::parse($availability->start_time)
                ->setDateFrom($date);
                
            $endTime = Carbon::parse($availability->end_time)
                ->setDateFrom($date);
                
            // Generate time slots in 15-minute increments
            $slotInterval = 15; // minutes
            $periodEnd = $endTime->copy()->subMinutes($service->duration);
            
            // Only proceed if there's enough time for at least one slot
            if ($startTime->greaterThanOrEqualTo($periodEnd)) {
                continue;
            }
            
            $period = new CarbonPeriod(
                $startTime, 
                "{$slotInterval} minutes", 
                $periodEnd
            );
            
            foreach ($period as $slotStart) {
                $slotEnd = $slotStart->copy()->addMinutes($service->duration);
                
                // Check if this slot conflicts with any booked appointments
                $hasConflict = $this->hasAppointmentConflict($slotStart, $slotEnd, $existingAppointments);
                
                if (!$hasConflict) {
                    $allTimeSlots->push([
                        'start_time' => $slotStart->format('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
        
        return $allTimeSlots->sortBy('start_time');
    }
    
    /**
     * Check if a proposed time slot conflicts with existing appointments
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param Collection $existingAppointments
     * @return bool
     */
    protected function hasAppointmentConflict(Carbon $startTime, Carbon $endTime, Collection $existingAppointments): bool
    {
        foreach ($existingAppointments as $appointment) {
            $appointmentEndTime = $appointment->appointment_time->copy()->addMinutes($appointment->service->duration);
            
            // Check for overlap
            if (
                // New appointment starts during existing appointment
                ($startTime >= $appointment->appointment_time && $startTime < $appointmentEndTime) ||
                // New appointment ends during existing appointment
                ($endTime > $appointment->appointment_time && $endTime <= $appointmentEndTime) ||
                // New appointment completely contains existing appointment
                ($startTime <= $appointment->appointment_time && $endTime >= $appointmentEndTime)
            ) {
                return true;
            }
        }
        
        return false;
    }
}
