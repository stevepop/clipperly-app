<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Haircut',
                'description' => 'Standard haircut with styling',
                'price' => 30.00,
                'duration' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Haircut & Beard Trim',
                'description' => 'Haircut with beard trimming and styling',
                'price' => 45.00,
                'duration' => 45,
                'is_active' => true,
            ],
            [
                'name' => 'Beard Trim',
                'description' => 'Beard trimming and shaping',
                'price' => 20.00,
                'duration' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Hair Coloring',
                'description' => 'Full hair coloring service',
                'price' => 75.00,
                'duration' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Shave',
                'description' => 'Traditional straight razor shave',
                'price' => 35.00,
                'duration' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Kid\'s Haircut',
                'description' => 'Haircut for children under 12',
                'price' => 20.00,
                'duration' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
