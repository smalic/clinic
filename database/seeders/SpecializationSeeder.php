<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            'General Physician',
            'Cardiologist',
            'Dermatologist',
            'Neurologist',
            'Orthopedic Surgeon',
            'Pediatrician',
            'Psychiatrist',
            'Endocrinologist',
            'Gastroenterologist',
            'Ophthalmologist',
            'Otolaryngologist (ENT)',
            'Pulmonologist',
            'Nephrologist',
            'Urologist',
            'Oncologist',
            'Rheumatologist',
            'Hematologist',
            'Anesthesiologist',
            'Radiologist',
            'Plastic Surgeon',
        ];

        foreach ($specializations as $specialization) {
            Specialization::create([
                'name' => $specialization
            ]);
        }
    }
}
