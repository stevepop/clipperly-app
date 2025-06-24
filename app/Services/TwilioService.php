<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected Client $client;
    protected mixed $fromNumber;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->fromNumber = config('services.twilio.phone_number');
    }

    /**
     * Send an SMS message
     *
     * @param string $to Phone number to send to
     * @param string $message Message content
     * @return bool Success status
     */
    public function sendSMS(string $to, string $message): bool
    {
        try {
            $to = $this->formatPhoneNumber($to);

            $this->client->messages->create(
                $to,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Twilio SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number to E.164 format
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters
        $numeric = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Add + prefix if not present
        if (!str_starts_with($numeric, '+')) {
            if (strlen($numeric) === 10) {
                $numeric = '+44' . $numeric;
            } else {
                $numeric = '+' . $numeric;
            }
        }

        return $numeric;
    }
}
