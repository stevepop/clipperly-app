<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #10b981;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9fafb;
        }
        .info {
            margin-bottom: 20px;
            background-color: #ffffff;
            padding: 15px;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Your Appointment is Confirmed!</h1>
</div>

<div class="content">
    <p>Hello {{ $appointment->customer_name }},</p>

    <p>Your appointment has been confirmed! We look forward to seeing you soon.</p>

    <div class="info">
        <h3>Appointment Details:</h3>
        <p><strong>Service:</strong> {{ $appointment->service->name }}</p>
        <p><strong>Date & Time:</strong> {{ $appointment->appointment_time->format('l, F j, Y \a\t g:i A') }}</p>
        <p><strong>Duration:</strong> {{ $appointment->service->duration }} minutes</p>
        <p><strong>Booking Code:</strong> {{ $appointment->booking_code }}</p>
    </div>

    <p>If you need to cancel or reschedule your appointment, please contact us as soon as possible.</p>

    <div style="text-align: center;">
        <a href="{{ route('booking.status') }}?code={{ $appointment->booking_code }}" class="button">Check Appointment Status</a>
    </div>

    <p>Best regards,<br>
        The Clipperly Team</p>
</div>

<div class="footer">
    <p>This email was sent to {{ $appointment->customer_email }}. If you did not make this appointment, please disregard this email.</p>
    <p>&copy; {{ date('Y') }} Clipperly. All rights reserved.</p>
</div>
</body>
</html>
