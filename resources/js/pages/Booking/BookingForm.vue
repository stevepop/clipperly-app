<template>
    <div class="max-w-4xl mx-auto p-6">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Book an Appointment</h1>
            <Link href="/" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Home
            </Link>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Step 1: Select Service -->
                <div>
                    <h2 class="text-xl font-medium mb-4">1. Select a Service</h2>
                    <div class="space-y-4">
                        <div
                            v-for="service in services"
                            :key="service.id"
                            class="border rounded-lg p-4 cursor-pointer"
                            :class="selectedService && selectedService.id === service.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                            @click="selectService(service)"
                        >
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="font-medium">{{ service.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ service.duration }} minutes</p>
                                </div>
                                <div class="font-medium">£{{ service.price }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Select Date and Time -->
                <div>
                    <h2 class="text-xl font-medium mb-4">2. Select Date & Time</h2>

                    <!-- Date Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <select
                            v-model="selectedDate"
                            class="w-full p-2 border border-gray-300 rounded-md"
                            :disabled="!selectedService"
                        >
                            <option value="">Select a date</option>
                            <option v-for="date in dates" :key="date.date" :value="date.date">
                                {{ date.formatted }}
                            </option>
                        </select>
                    </div>

                    <!-- Time Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                        <div v-if="loadingTimeSlots" class="text-center p-4">
                            Loading available times...
                        </div>
                        <div v-else-if="!selectedDate || !selectedService" class="text-gray-500 p-4 border border-gray-200 rounded-md">
                            Please select a service and date first
                        </div>
                        <div v-else-if="timeSlots.length === 0" class="text-gray-500 p-4 border border-gray-200 rounded-md">
                            No available time slots for this date
                        </div>
                        <div v-else class="grid grid-cols-3 gap-2">
                            <button
                                v-for="slot in timeSlots"
                                :key="slot.time"
                                @click="selectedTimeSlot = slot"
                                class="p-2 border rounded-md text-center"
                                :class="selectedTimeSlot && selectedTimeSlot.time === slot.time ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                            >
                                {{ slot.formatted }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Customer Information -->
            <div class="mt-8" v-if="selectedService && selectedTimeSlot">
                <h2 class="text-xl font-medium mb-4">3. Your Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input
                            type="text"
                            v-model="form.customer_name"
                            class="w-full p-2 border border-gray-300 rounded-md"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input
                            type="email"
                            v-model="form.customer_email"
                            class="w-full p-2 border border-gray-300 rounded-md"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input
                            type="tel"
                            v-model="form.customer_phone"
                            class="w-full p-2 border border-gray-300 rounded-md"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Special Requests (Optional)</label>
                        <textarea
                            v-model="form.notes"
                            class="w-full p-2 border border-gray-300 rounded-md"
                            rows="2"
                        ></textarea>
                    </div>
                </div>

                <div class="text-sm text-gray-500 mt-2">
                    * Email is required for payment processing and booking confirmation
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end">
                <button
                    @click="submitBooking"
                    :disabled="!canSubmit || isSubmitting"
                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ isSubmitting ? 'Processing...' : 'Book Appointment' }}
                </button>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="showConfirmation" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-green-600 mb-4">Booking Request Received!</h2>
                    <p class="mb-2">Your appointment request has been submitted successfully.</p>
                    <p class="font-medium">Booking Code: {{ bookingCode }}</p>
                    <p class="text-sm text-gray-500 mt-4">
                        A payment link has been sent to your email address. Your appointment will be confirmed once payment is completed.
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        Please reference your booking code if you need to contact us about this appointment.
                    </p>
                    <div class="mt-6">
                        <button
                            @click="closeConfirmation"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700"
                        >
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link
    },
    props: {
        services: Array,
        dates: Array
    },
    data() {
        return {
            selectedService: null,
            selectedDate: '',
            selectedTimeSlot: null,
            timeSlots: [],
            loadingTimeSlots: false,
            isSubmitting: false,
            showConfirmation: false,
            bookingCode: '',
            form: {
                customer_name: '',
                customer_email: '',
                customer_phone: '',
                notes: ''
            }
        }
    },

    computed: {
        canSubmit() {
            return this.selectedService &&
                this.selectedTimeSlot &&
                this.form.customer_name &&
                this.form.customer_email
        }
    },

    watch: {
        selectedDate() {
            this.selectedTimeSlot = null;
            if (this.selectedDate && this.selectedService) {
                this.fetchTimeSlots();
            }
        },

        selectedService() {
            this.selectedTimeSlot = null;
            if (this.selectedDate && this.selectedService) {
                this.fetchTimeSlots();
            }
        }
    },

    methods: {
        selectService(service) {
            this.selectedService = service;
        },

        async fetchTimeSlots() {
            this.loadingTimeSlots = true;

            try {
                const response = await axios.get('/api/v1/time-slots', {
                    params: {
                        date: this.selectedDate,
                        service_id: this.selectedService.id
                    }
                });

                this.timeSlots = response.data.time_slots;
            } catch (error) {
                console.error('Error fetching time slots:', error);
            } finally {
                this.loadingTimeSlots = false;
            }
        },

        async submitBooking() {
            if (!this.canSubmit || this.isSubmitting) return;

            this.isSubmitting = true;

            try {
                const response = await axios.post('/api/v1/bookings', {
                    service_id: this.selectedService.id,
                    appointment_time: this.selectedTimeSlot.full_datetime,
                    customer_name: this.form.customer_name,
                    customer_email: this.form.customer_email || null,
                    customer_phone: this.form.customer_phone || null,
                    notes: this.form.notes || null
                });

                this.bookingCode = response.data.booking_code;
                this.showConfirmation = true;
            } catch (error) {
                console.error('Error booking appointment:', error);
                alert('There was an error booking your appointment. Please try again.');
            } finally {
                this.isSubmitting = false;
            }
        },

        closeConfirmation() {
            this.showConfirmation = false;
            // Reset form
            this.selectedService = null;
            this.selectedDate = '';
            this.selectedTimeSlot = null;
            this.timeSlots = [];
            this.form = {
                customer_name: '',
                customer_email: '',
                customer_phone: '',
                notes: ''
            };
        }
    }
}
</script>
