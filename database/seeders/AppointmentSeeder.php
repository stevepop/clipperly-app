<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks before truncating (SQLite compatible)
        Schema::disableForeignKeyConstraints();

        // Clear existing records to avoid duplicates
        Appointment::truncate();

        // Re-enable foreign key checks after truncation
        Schema::enableForeignKeyConstraints();

        // Get all service IDs
        $services = Service::all();

        if ($services->isEmpty()) {
            $this->command->error('No services found. Please run the ServiceSeeder first.');
            return;
        }

        $this->command->info('Found ' . $services->count() . ' services.');

        // Get all availability slots
        $slots = Availability::all();

        if ($slots->isEmpty()) {
            $this->command->error('No availability slots found. Please run the AvailabilitySeeder first.');
            return;
        }

        $this->command->info('Found ' . $slots->count() . ' availability slots.');

        $count = 0;
        $skippedRandomly = 0;
        $skippedDuration = 0;
        $skippedTimeRange = 0;
        $appointmentsToCreate = 20; // Total appointments to create

        // FIRST - Ensure we have appointments for today
        $todayAppointments = $this->createTodayAppointments($services);
        $count += $todayAppointments;
        $this->command->info("Created {$todayAppointments} appointments for today.");

        // Create random appointments for other days
        $remainingAppointments = $appointmentsToCreate - $todayAppointments;

        foreach ($slots as $slot) {
            if ($count >= $appointmentsToCreate) break;

            // Skip today's slots since we've already created appointments for today
            if ($slot->date->isToday()) {
                continue;
            }

            // Debug slot information
            $this->command->info("Processing slot for date: {$slot->date->format('Y-m-d')}, time: {$slot->start_time} - {$slot->end_time}");

            // 50% chance to create an appointment for this slot
            if (rand(0, 1) === 0) {
                $skippedRandomly++;
                $this->command->info("Skipped randomly");
                continue;
            }

            $service = $services->random();
            $this->command->info("Selected service: {$service->name}, duration: {$service->duration} minutes");

            // Create appointment time between slot start and end time
            $dateString = $slot->date->format('Y-m-d');
            $slotStartTime = Carbon::parse($dateString . ' ' . $slot->start_time);
            $slotEndTime = Carbon::parse($dateString . ' ' . $slot->end_time);

            $this->command->info("Slot start: {$slotStartTime->format('Y-m-d H:i:s')}, end: {$slotEndTime->format('Y-m-d H:i:s')}");

            // Make sure there's enough time for the service
            $latestStartTime = $slotEndTime->copy()->subMinutes($service->duration);

            $this->command->info("Latest start time: {$latestStartTime->format('Y-m-d H:i:s')}");

            if ($latestStartTime->isBefore($slotStartTime)) {
                $this->command->info("Skipped: Service duration ({$service->duration} min) too long for slot");
                $skippedDuration++;
                continue; // Skip if slot is too short for service
            }

            // Calculate the number of 15-minute intervals available
            $minuteDiff = $slotStartTime->diffInMinutes($latestStartTime);
            $this->command->info("Minute difference: {$minuteDiff}");

            $intervalCount = (int)($minuteDiff / 15);
            $this->command->info("Available 15-minute intervals: {$intervalCount}");

            if ($intervalCount < 1) {
                $this->command->info("Skipped: Time range too small");
                $skippedTimeRange++;
                continue; // Skip if the range is too small
            }

            $randomInterval = rand(0, $intervalCount);
            $appointmentTime = $slotStartTime->copy()->addMinutes($randomInterval * 15);

            $this->command->info("Created appointment time: {$appointmentTime->format('Y-m-d H:i:s')}");

            // Determine status based on date
            $isPast = $appointmentTime->lt(Carbon::now());
            $status = $isPast
                ? (['completed', 'cancelled', 'no_show'][rand(0, 2)])
                : (['pending_payment', 'confirmed'][rand(0, 1)]);

            // Generate booking code
            $bookingCode = strtoupper(Str::random(8));

            try {
                // Create the appointment
                Appointment::create([
                    'service_id' => $service->id,
                    'customer_name' => $this->getRandomName(),
                    'customer_email' => $this->getRandomEmail(),
                    'customer_phone' => $this->getRandomPhone(),
                    'appointment_time' => $appointmentTime,
                    'booking_code' => $bookingCode,
                    'status' => $status,
                    'notes' => rand(0, 1) ? $this->getRandomNote() : null,
                ]);

                $this->command->info("SUCCESS: Created appointment for {$appointmentTime->format('Y-m-d H:i')} with status {$status}");
                $count++;
            } catch (\Exception $e) {
                $this->command->error("ERROR creating appointment: " . $e->getMessage());
            }
        }

        $this->command->info("Created {$count} appointments in total.");
        $this->command->info("Skipped: {$skippedRandomly} randomly, {$skippedDuration} due to duration, {$skippedTimeRange} due to small time range.");
    }

    /**
     * Create a set of appointments for today regardless of availability slots
     *
     * @param \Illuminate\Database\Eloquent\Collection $services
     * @return int Number of appointments created
     */
    private function createTodayAppointments($services)
    {
        $count = 0;
        $today = Carbon::today();

        // Create 5 appointments distributed throughout today
        $startHour = 9; // Start at 9 AM
        $endHour = 17;  // End at 5 PM

        // Create 5 appointments for today, evenly distributed
        for ($i = 0; $i < 5; $i++) {
            $hour = $startHour + ($i * (($endHour - $startHour) / 5));
            $minute = rand(0, 3) * 15; // 0, 15, 30, or 45 minutes

            $appointmentTime = $today->copy()->setHour((int)$hour)->setMinute($minute)->setSecond(0);

            // Skip times in the past
            if ($appointmentTime->isPast()) {
                // Move to a future time if in the past
                $appointmentTime = Carbon::now()->addHours(1 + $i)->setMinute($minute)->setSecond(0);
            }

            $service = $services->random();
            $status = ['pending_payment', 'confirmed'][rand(0, 1)];
            $bookingCode = strtoupper(Str::random(8));

            try {
                Appointment::create([
                    'service_id' => $service->id,
                    'customer_name' => $this->getRandomName(),
                    'customer_email' => $this->getRandomEmail(),
                    'customer_phone' => $this->getRandomPhone(),
                    'appointment_time' => $appointmentTime,
                    'booking_code' => $bookingCode,
                    'status' => $status,
                    'notes' => rand(0, 1) ? $this->getRandomNote() : null,
                ]);

                $this->command->info("SUCCESS: Created today appointment for {$appointmentTime->format('Y-m-d H:i')} with status {$status}");
                $count++;
            } catch (\Exception $e) {
                $this->command->error("ERROR creating today appointment: " . $e->getMessage());
            }
        }

        return $count;
    }

    private function getRandomName(): string
    {
        $firstNames = ['John', 'Jane', 'Michael', 'Emily', 'David', 'Sarah', 'Robert', 'Jennifer', 'William', 'Elizabeth'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis', 'Miller', 'Wilson', 'Moore', 'Taylor'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    private function getRandomEmail(): string
    {
        $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'example.com'];

        return strtolower(str_replace(' ', '', $this->getRandomName())) .
            rand(10, 99) . '@' . $domains[array_rand($domains)];
    }

    private function getRandomPhone(): string
    {
        return  '+447' . str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
    }

    private function getRandomNote(): string
    {
        $notes = [
            'First-time customer',
            'Prefers a specific style',
            'Allergic to certain products',
            'Has requested a specific stylist',
            'Will be 5 minutes late',
            'Wants product recommendations',
            'Bringing a reference photo',
            'Celebrating a special occasion',
        ];

        return $notes[array_rand($notes)];
    }
}
