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
        $request->validate([
            'booking_code' => 'required|exists:appointments,booking_code',
        ]);

        $appointment = Appointment::where('booking_code', $request->booking_code)
            ->firstOrFail();

        // Update the appointment status to confirmed
        $appointment->update(['status' => 'confirmed']);

        // Send confirmation email
        Mail::to($appointment->customer_email)->send(new AppointmentConfirmedMail($appointment));

        if ($appointment->customer_phone) {
            $this->sendConfirmationSMS($appointment);
        }

        return redirect()->route('payment.success', ['booking_code' => $appointment->booking_code]);
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

    /**
     * Send confirmation SMS
     */
    private function sendConfirmationSMS(Appointment $appointment)
    {
        $dateTime = $appointment->appointment_time->format('D, M j \a\t g:i A');

        $message = "Your appointment at Clipperly has been confirmed! ".
            "{$appointment->service->name} on {$dateTime}. ".
            "Booking code: {$appointment->booking_code}";

        $this->twilioService->sendSMS($appointment->customer_phone, $message);
    }
}
