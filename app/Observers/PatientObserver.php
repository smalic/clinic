<?php

namespace App\Observers;

use App\Models\Patient;

class PatientObserver
{
    /**
     * Handle the Patient "created" event.
     */
    public function created(Patient $patient): void
    {
        $patient->name = $patient->first_name . ' ' . $patient->last_name;
        $patient->saveQuietly();
    }

    /**
     * Handle the Patient "updated" event.
     */
    public function updated(Patient $patient): void
    {
        if ($patient->isDirty('first_name') || $patient->isDirty('last_name')) {
            $patient->name = $patient->first_name . ' ' . $patient->last_name;
            $patient->saveQuietly();
        }
    }

    /**
     * Handle the Patient "deleted" event.
     */
    public function deleted(Patient $patient): void
    {
        //
    }

    /**
     * Handle the Patient "restored" event.
     */
    public function restored(Patient $patient): void
    {
        //
    }

    /**
     * Handle the Patient "force deleted" event.
     */
    public function forceDeleted(Patient $patient): void
    {
        //
    }
}
