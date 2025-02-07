<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DoctorCalendar>
 */
class DoctorCalendarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_time_hours = $this->faker->numberBetween(8, 15);
        $start_time_minutes = $this->faker->randomElement([0, 30]);
        $start_time = sprintf('%02d:%02d', $start_time_hours, $start_time_minutes);

        $end_time_hours = $start_time_hours + 4;
        $end_time_minutes = $this->faker->randomElement([0, 30]);
        $end_time = sprintf('%02d:%02d', $end_time_hours, $end_time_minutes);

        return [
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];
    }
}
