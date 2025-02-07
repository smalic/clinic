<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorCalendar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        $doctors = Doctor::factory(count: 20)
            ->create()
            ->each(function ($doctor) use ($days) {
                $availableDays = collect($days)->shuffle()->take(3);

                foreach ($availableDays as $day) {
                    DoctorCalendar::factory()->for($doctor)->create([
                        'day_of_week' => strtolower($day),
                    ]);
                }
            });
    }
}
