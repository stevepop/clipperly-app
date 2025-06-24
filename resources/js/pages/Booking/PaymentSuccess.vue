<!-- resources/js/Pages/Booking/PaymentSuccess.vue -->
<template>
    <div class="max-w-4xl mx-auto p-6">
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center bg-green-100 rounded-full p-4 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-4">Payment Successful!</h1>
            <p class="text-xl text-gray-600">Your appointment has been confirmed.</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-medium mb-4">Appointment Details</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                    <div>
                        <span class="text-gray-500">Service:</span>
                        <span class="ml-2 font-medium">{{ service.name }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500">Date & Time:</span>
                        <span class="ml-2 font-medium">{{ formatDateTime(appointment.appointment_time) }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500">Duration:</span>
                        <span class="ml-2">{{ service.duration }} minutes</span>
                    </div>

                    <div>
                        <span class="text-gray-500">Status:</span>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              Confirmed
            </span>
                    </div>

                    <div>
                        <span class="text-gray-500">Booking Code:</span>
                        <span class="ml-2 font-medium">{{ appointment.booking_code }}</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <p class="text-gray-600">
                        A confirmation email has been sent to your email address with all the details of your appointment.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-center">
            <a href="/" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Return to Home
            </a>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        appointment: Object,
        service: Object
    },

    methods: {
        formatDateTime(datetime) {
            return new Date(datetime).toLocaleString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }
    }
}
</script>
