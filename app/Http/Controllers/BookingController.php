<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Appointment;
use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function bookingForm()
    {
        $services = Service::where('is_active', true)
            ->select(['id', 'name', 'description', 'price', 'duration'])
            ->get();

        $dates = [];
        $currentDate = Carbon::today();

        for ($i = 0; $i < 14; $i++) {
            // Check if there are slots for this day
            $hasSlots = Availability::whereDate('date', $currentDate->format('Y-m-d'))
                ->where('is_available', true)
                ->exists();

            if ($hasSlots) {
                $dates[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'formatted' => $currentDate->format('l, F j, Y')
                ];
            }

            $currentDate->addDay();
        }

        return Inertia::render('Booking/BookingForm', [
            'services' => $services,
            'dates' => $dates
        ]);
    }

    /**
     * Get available time slots for a specific date and service
     */
    public function getTimeSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'service_id' => 'required|exists:services,id'
        ]);

        $date = Carbon::parse($request->date);
        $service = Service::findOrFail($request->service_id);

        // Get all available slots for this date
        $availabilities = Availability::whereDate('date', $date->format('Y-m-d'))
            ->where('is_available', true)
            ->get();

        // Get existing appointments for this date
        $bookedAppointments = Appointment::whereDate('appointment_time', $date->format('Y-m-d'))
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->get();

        // Generate time slots at 30-minute intervals
        $timeSlots = [];

        foreach ($availabilities as $slot) {
            $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $slot->start_time);
            $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $slot->end_time);

            while ($startTime->copy()->addMinutes($service->duration) <= $endTime) {
                $slotEndTime = $startTime->copy()->addMinutes($service->duration);

                // Check if this time slot conflicts with any booked appointments
                $isAvailable = true;

                foreach ($bookedAppointments as $appointment) {
                    $appointmentStart = $appointment->appointment_time;
                    $appointmentEnd = $appointmentStart->copy()->addMinutes($service->duration);

                    // Check for overlap
                    if (
                        ($startTime >= $appointmentStart && $startTime < $appointmentEnd) ||
                        ($slotEndTime > $appointmentStart && $slotEndTime <= $appointmentEnd) ||
                        ($startTime <= $appointmentStart && $slotEndTime >= $appointmentEnd)
                    ) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($isAvailable) {
                    $timeSlots[] = [
                        'time' => $startTime->format('H:i'),
                        'formatted' => $startTime->format('g:i A'),
                        'full_datetime' => $startTime->format('Y-m-d H:i:s')
                    ];
                }

                $startTime->addMinutes(30); // 30-minute intervals
            }
        }

        return response()->json(['time_slots' => $timeSlots]);
    }

    /**
     * Store a new booking
     */
    public function storeBooking(Request $request)
    {

    }

    /**
     * Show the booking status page
     */
    public function statusPage()
    {
        return Inertia::render('Booking/StatusPage');
    }

    /**
     * Check appointment status
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string'
        ]);

        $appointment = Appointment::where('booking_code', $request->booking_code)
            ->with('service')
            ->first();

        if (!$appointment) {
            return response()->json([
                'found' => false
            ]);
        }

        return response()->json([
            'found' => true,
            'appointment' => $appointment
        ]);
    }

      /**
     * Notify admin about new booking
     */
    private function notifyAdmin(Appointment $appointment)
    {
        // Admin email notification
        $adminEmail = config('app.admin_email');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new AdminBookingNotification($appointment));
        }
    }
}
