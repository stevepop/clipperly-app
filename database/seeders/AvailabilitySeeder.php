<?php

namespace Database\Seeders;

use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing records to avoid duplicates
        Availability::truncate();

        // Generate availability for the next 14 days
        $startDate = Carbon::today()->subDays(7); // Include past week
        $endDate = Carbon::today()->addDays(14);

        $date = $startDate->copy();

        // Create slots for each day
        while ($date->lte($endDate)) {
            // Skip Sundays
            if ($date->dayOfWeek !== 0) {
                // Morning slot (9 AM - 1 PM)
                Availability::create([
                    'date' => $date->toDateString(),
                    'start_time' => '09:00:00',
                    'end_time' => '13:00:00',
                    'max_appointments' => 4,
                    'is_available' => true,
                ]);

                // Afternoon slot (2 PM - 6 PM)
                Availability::create([
                    'date' => $date->toDateString(),
                    'start_time' => '14:00:00',
                    'end_time' => '18:00:00',
                    'max_appointments' => 4,
                    'is_available' => true,
                ]);
            }

            $date->addDay();
        }

        $this->command->info('Created ' . Availability::count() . ' availability slots.');
    }
}
