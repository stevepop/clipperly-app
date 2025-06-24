<template>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Complete Your Payment</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-medium mb-4">Appointment Summary</h2>

            <div class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                    <div>
                        <span class="text-gray-500">Service:</span>
                        <span class="ml-2">{{ service.name }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500">Date & Time:</span>
                        <span class="ml-2">{{ formatDateTime(appointment.appointment_time) }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500">Duration:</span>
                        <span class="ml-2">{{ service.duration }} minutes</span>
                    </div>

                    <div>
                        <span class="text-gray-500">Booking Code:</span>
                        <span class="ml-2 font-medium">{{ appointment.booking_code }}</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-medium">Total:</span>
                        <span class="text-xl font-bold">£{{ service.price }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simulated Payment Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-medium mb-4">Payment Information</h2>

            <form @submit.prevent="submitPayment">
                <!-- This is a simulated payment form - in a real app, you'd integrate with Stripe, PayPal, etc. -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                        <input
                            type="text"
                            v-model="paymentForm.cardNumber"
                            class="w-full p-2 border border-gray-300 rounded-md"
                            placeholder="4242 4242 4242 4242"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiration Date</label>
                            <input
                                type="text"
                                v-model="paymentForm.expDate"
                                class="w-full p-2 border border-gray-300 rounded-md"
                                placeholder="MM/YY"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                            <input
                                type="text"
                                v-model="paymentForm.cvv"
                                class="w-full p-2 border border-gray-300 rounded-md"
                                placeholder="123"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name on Card</label>
                        <input
                            type="text"
                            v-model="paymentForm.name"
                            class="w-full p-2 border border-gray-300 rounded-md"
                            required
                        />
                    </div>
                </div>

                <div class="mt-6">
                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white font-medium py-3 px-4 rounded-md hover:bg-blue-700"
                        :disabled="isProcessing"
                    >
                        {{ isProcessing ? 'Processing...' : `Pay £${service.price}` }}
                    </button>
                </div>

                <p class="text-sm text-gray-500 mt-4 text-center">
                    This is a simulated payment page. No actual payment will be processed.
                </p>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        appointment: Object,
        service: Object
    },

    data() {
        return {
            paymentForm: {
                cardNumber: '',
                expDate: '',
                cvv: '',
                name: ''
            },
            isProcessing: false
        }
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
        },

        async submitPayment() {
            this.isProcessing = true;

            try {
                // In a real app, you'd use Stripe, PayPal, etc. for payment processing
                // Here we're just simulating a payment

                await axios.post('/payment/process', {
                    booking_code: this.appointment.booking_code
                });

                // Redirect to success page
                window.location.href = `/payment/success/${this.appointment.booking_code}`;
            } catch (error) {
                console.error('Error processing payment:', error);
                alert('There was an error processing your payment. Please try again.');
            } finally {
                this.isProcessing = false;
            }
        }
    }
}
</script>
