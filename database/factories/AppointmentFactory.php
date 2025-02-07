<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $doctor = Doctor::inRandomOrder()->limit(1)->first();
        $patient = Patient::inRandomOrder()->limit(1)->first();

        $calendar = $doctor->calendars()->first();
        $dayNumber = Carbon::parse($calendar->day_of_week)->dayOfWeek;

        $nextDate = Carbon::now()->next($dayNumber);

        return [
            'date' => $nextDate->toDateString(),
            'start_time' => Carbon::parse($calendar->start_time)->format('H:i'),
            'end_time' => Carbon::parse($calendar->start_time)->addMinutes(30)->format('H:i'),
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
        ];
    }
}
