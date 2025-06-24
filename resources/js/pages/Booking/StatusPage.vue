<template>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Check Appointment Status</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <div v-if="!appointment">
                <p class="mb-4">Enter your booking code to check your appointment status.</p>

                <div class="flex space-x-2">
                    <input
                        type="text"
                        v-model="bookingCode"
                        class="flex-1 p-2 border border-gray-300 rounded-md uppercase"
                        placeholder="e.g., ABC12345"
                    />

                    <button
                        @click="checkStatus"
                        :disabled="!bookingCode || isChecking"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ isChecking ? 'Checking...' : 'Check Status' }}
                    </button>
                </div>

                <p v-if="error" class="mt-2 text-red-600">{{ error }}</p>
            </div>

            <div v-else>
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-2xl font-medium">Appointment Details</h2>
                    <div
                        class="px-3 py-1 rounded-full text-sm font-medium"
                        :class="statusClass"
                    >
                        {{ statusLabel }}
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        <div>
                            <span class="text-gray-500">Booking Code:</span>
                            <span class="ml-2 font-medium">{{ appointment.booking_code }}</span>
                        </div>

                        <div>
                            <span class="text-gray-500">Service:</span>
                            <span class="ml-2">{{ appointment.service.name }}</span>
                        </div>

                        <div>
                            <span class="text-gray-500">Date:</span>
                            <span class="ml-2">{{ formatDate(appointment.appointment_time) }}</span>
                        </div>

                        <div>
                            <span class="text-gray-500">Time:</span>
                            <span class="ml-2">{{ formatTime(appointment.appointment_time) }}</span>
                        </div>

                        <div>
                            <span class="text-gray-500">Customer:</span>
                            <span class="ml-2">{{ appointment.customer_name }}</span>
                        </div>

                        <div v-if="appointment.notes">
                            <span class="text-gray-500">Notes:</span>
                            <span class="ml-2">{{ appointment.notes }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="clearAppointment"
                        class="text-blue-600 hover:underline"
                    >
                        Check another booking
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            bookingCode: '',
            appointment: null,
            error: '',
            isChecking: false
        }
    },

    computed: {
        statusLabel() {
            const labels = {
                'pending_payment': 'Pending Payment',
                'confirmed': 'Confirmed',
                'completed': 'Completed',
                'cancelled': 'Cancelled',
                'no_show': 'No Show'
            };

            return labels[this.appointment?.status] || this.appointment?.status;
        },

        statusClass() {
            const classes = {
                'pending_payment': 'bg-yellow-100 text-yellow-800',
                'confirmed': 'bg-blue-100 text-blue-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800',
                'no_show': 'bg-gray-100 text-gray-800'
            };

            return classes[this.appointment?.status] || '';
        }
    },

    methods: {
        async checkStatus() {
            if (!this.bookingCode) return;

            this.isChecking = true;
            this.error = '';

            try {
                const response = await axios.post('/api/v1/check-status', {
                    booking_code: this.bookingCode
                });

                if (response.data.found) {
                    this.appointment = response.data.appointment;
                } else {
                    this.error = 'No appointment found with this booking code.';
                }
            } catch (error) {
                console.error('Error checking status:', error);
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.isChecking = false;
            }
        },

        clearAppointment() {
            this.appointment = null;
            this.bookingCode = '';
        },

        formatDate(datetime) {
            return new Date(datetime).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        formatTime(datetime) {
            return new Date(datetime).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }
    }
}
</script>
