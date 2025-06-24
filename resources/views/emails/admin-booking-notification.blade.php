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
    <h1>New Booking Notification</h1>
</div>

<div class="content">
    <p>A new appointment has been booked at Clipperly!</p>

    <div class="info">
        <h3>Appointment Details:</h3>
        <p><strong>Service:</strong> {{ $appointment->service->name }}</p>
        <p><strong>Date & Time:</strong> {{ $appointment->appointment_time->format('l, F j, Y \a\t g:i A') }}</p>
        <p><strong>Duration:</strong> {{ $appointment->service->duration }} minutes</p>
        <p><strong>Customer:</strong> {{ $appointment->customer_name }}</p>
        <p><strong>Email:</strong> {{ $appointment->customer_email }}</p>
        @if($appointment->customer_phone)
            <p><strong>Phone:</strong> {{ $appointment->customer_phone }}</p>
        @endif
        <p><strong>Booking Code:</strong> {{ $appointment->booking_code }}</p>
        <p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>
        @if($appointment->notes)
            <p><strong>Notes:</strong> {{ $appointment->notes }}</p>
        @endif
    </div>

    <div style="text-align: center;">
        <a href="{{ url('/admin/appointments/' . $appointment->id . '/edit') }}" class="button">View in Admin Panel</a>
    </div>
</div>

<div class="footer">
    <p>&copy; {{ date('Y') }} Clipperly. All rights reserved.</p>
</div>
</body>
</html>
