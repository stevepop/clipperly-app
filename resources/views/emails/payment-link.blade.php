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
            background-color: #3b82f6;
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
            background-color: #3b82f6;
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
    <h1>Complete Your Booking Payment</h1>
</div>

<div class="content">
    <p>Hello {{ $appointment->customer_name }},</p>

    <p>Thank you for booking an appointment with Clipperly. To confirm your appointment, please complete your payment by clicking the link below:</p>

    <div style="text-align: center;">
        <a href="{{ route('payment.process', ['booking_code' => $appointment->booking_code]) }}" class="button">Complete Payment</a>
    </div>

    <div class="info">
        <h3>Appointment Details:</h3>
        <p><strong>Service:</strong> {{ $appointment->service->name }}</p>
        <p><strong>Date & Time:</strong> {{ $appointment->appointment_time->format('l, F j, Y \a\t g:i A') }}</p>
        <p><strong>Duration:</strong> {{ $appointment->service->duration }} minutes</p>
        <p><strong>Price:</strong> £{{ $appointment->service->price }}</p>
        <p><strong>Booking Code:</strong> {{ $appointment->booking_code }}</p>
    </div>

    <p>Your appointment will be confirmed once payment is completed. If you have any questions, please contact us.</p>

    <p>Best regards,<br>
        The Clipperly Team</p>
</div>

<div class="footer">
    <p>This email was sent to {{ $appointment->customer_email }}. If you did not make this appointment, please disregard this email.</p>
    <p>&copy; {{ date('Y') }} Clipperly. All rights reserved.</p>
</div>
</body>
</html>
