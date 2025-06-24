<!-- resources/js/Pages/Home.vue -->
<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-16 md:py-24 text-center">
                    <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">Clipperly</h1>
                    <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                        Professional grooming services for every occasion
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link
                            :href="route('booking.form')"
                            class="inline-block bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition duration-200"
                        >
                            Book an Appointment
                        </Link>
                        <Link
                            :href="route('booking.status')"
                            class="inline-block border border-blue-600 text-blue-600 font-bold py-3 px-8 rounded-lg hover:bg-blue-50 transition duration-200"
                        >
                            Check Appointment Status
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Preview -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Our Services</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Haircuts</h3>
                        <p class="text-gray-600 mb-4">
                            Precision cuts tailored to your style and hair type
                        </p>
                        <p class="text-gray-900 font-medium">Starting at £25</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Beard Trims</h3>
                        <p class="text-gray-600 mb-4">
                            Expert beard grooming and shaping services
                        </p>
                        <p class="text-gray-900 font-medium">Starting at £20</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Shaves</h3>
                        <p class="text-gray-600 mb-4">
                            Classic hot towel shaves for a smooth finish
                        </p>
                        <p class="text-gray-900 font-medium">Starting at £15</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-10">
                <Link
                    :href="route('booking.form')"
                    class="text-blue-600 hover:text-blue-800 font-medium"
                >
                    View all services and book now →
                </Link>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="text-center md:text-left">
                        <h3 class="text-xl font-bold text-gray-900">Clipperly</h3>
                        <p class="mt-2 text-gray-600">123 High Street, UKtown</p>
                        <p class="text-gray-600">Phone: (01) 123-4567</p>
                    </div>

                    <div class="mt-8 md:mt-0">
                        <h4 class="text-lg font-medium text-gray-900">Hours</h4>
                        <div class="mt-2 text-gray-600">
                            <p>Monday - Friday: 9am - 7pm</p>
                            <p>Saturday: 9am - 5pm</p>
                            <p>Sunday: Closed</p>
                        </div>
                    </div>

                    <div class="mt-8 md:mt-0 text-center md:text-right">
                        <Link
                            :href="route('booking.status')"
                            class="text-blue-600 hover:text-blue-800 block mb-2"
                        >
                            Check Appointment Status
                        </Link>
                        <Link
                            :href="route('booking.form')"
                            class="inline-block bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition duration-200 mt-2"
                        >
                            Book Now
                        </Link>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-8 text-center text-sm text-gray-600">
                    <p>&copy; {{ new Date().getFullYear() }} Clipperly. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
    components: {
        Link
    }
}
</script>
