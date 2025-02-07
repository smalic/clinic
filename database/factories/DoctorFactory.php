<?php

namespace Database\Factories;

use App\Models\Specialization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specialization = Specialization::inRandomOrder()->limit(1)->first();

        return [
            'name' => $this->faker->name,
            'specialization_id' => $specialization->id,
        ];
    }
}
