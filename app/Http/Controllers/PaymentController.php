<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentConfirmedMail;
use App\Models\Appointment;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class PaymentController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function showPaymentPage(Request $request)
    {
        $bookingCode = $request->booking_code;
        $appointment = Appointment::where('booking_code', $bookingCode)
            ->with('service')
            ->firstOrFail();

        // Only show payment page for pending payments
        if ($appointment->status !== 'pending_payment') {
            return redirect()->route('booking.status', ['code' => $bookingCode]);
        }

        return Inertia::render('Booking/PaymentPage', [
            'appointment' => $appointment,
            'service' => $appointment->service
        ]);
    }

    /**
     * Process the payment
     */
    public function processPayment(Request $request)
    {

    }

    /**
     * Show payment success page
     */
    public function showSuccessPage(Request $request)
    {
        $bookingCode = $request->booking_code;
        $appointment = Appointment::where('booking_code', $bookingCode)
            ->with('service')
            ->firstOrFail();

        return Inertia::render('Booking/PaymentSuccess', [
            'appointment' => $appointment,
            'service' => $appointment->service
        ]);
    }
}
