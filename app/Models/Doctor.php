<?php

namespace App\Models;

use App\HasTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use SoftDeletes, HasTime, HasFactory;

    protected $fillable = [
        'name',
        'specialization_id'
    ];

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function calendars(): HasMany
    {
        return $this->hasMany(DoctorCalendar::class);
    }

    public function calendar_exemptions(): HasMany
    {
        return $this->hasMany(DoctorCalendarExemption::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getExcludedDates(): array
    {
        $days = $this->calendars()->get()->map(fn($entry) => ucfirst($entry->day_of_week))->toArray();

        $start = Carbon::now()->startOfMonth();
        $end = $start->copy()->addMonth(2)->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        $exemptions = $this
            ->calendar_exemptions()
            ->where('removed', '=', 1)
            ->where('date', '>=', Carbon::now()->format('Y-m-d'))
            ->get()
            ->pluck('date')
            ->toArray();

        $dates = [];

        foreach ($period as $date) {
            if (
                $date->isWeekend() ||
                !in_array($date->dayName, $days) ||
                in_array($date->format('Y-m-d'), $exemptions) ||
                $this->isDateFullyBooked($date, true)
            ) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        return $dates;
    }

    public function getAvailableTimes(string $date, ?Appointment $appointment = null): array
    {
        $day = strtolower(Carbon::parse($date)->dayName);
        $time_interval = $this->calendars()
            ->where('day_of_week', '=', $day)
            ->get(['start_time', 'end_time'])
            ->first()
                ?->toArray();

        if (!$time_interval) {
            return [];
        }

        // Create a list of possible time slots
        $possible_time_slots = $this->getTimeIntervals($time_interval['start_time'], $time_interval['end_time']);

        $exemptions = $this->calendar_exemptions()
            ->where('removed', '=', 0)
            ->where('date', '=', Carbon::parse($date)->format('Y-m-d'));

        // If no exemptions, then only return the times that haven't been booked for that date
        // Ensure you're also passing the $start_time, so that while editing an existing appointment, the previously selected start_time isn't empty (booked)
        if ($exemptions->count() === 0) {
            $appointments = $this->appointments()
                ->whereIn('start_time', $possible_time_slots);

            if ($appointments->count() === count($possible_time_slots)) {
                return [];
            }

            return $possible_time_slots;
        }

        $exempt_time_intervals = $exemptions->get(['start_time', 'end_time'])->first()->toArray();
        $exempt_times = $this->getTimeIntervals($exempt_time_intervals['start_time'], $exempt_time_intervals['end_time']);

        // This is a list of possible time slots minus the exempt times
        $possible_time_slots = array_diff($possible_time_slots, $exempt_times);

        // Now we need to filter these slots so we're only showing those that don't have an appointment
        // But make sure if we're editing an existing appointment that we're not removing its time slot

        //...

        return $possible_time_slots;
    }

    /**
     * Checks if the date is completely booked with appointments
     * @param string $date Date of the appointment
     * @param bool $skipExemptions We use this flag to avoid checking the exemptions table, as we might not need it in some places
     * @return bool
     */
    public function isDateFullyBooked(string $date, bool $skipExemptions = true): bool
    {
        if (!$skipExemptions) {
            $exemptions = $this->calendar_exemptions()
                ->where('removed', '=', 1)
                ->where('date', '=', Carbon::parse($date)->format('Y-m-d'));

            if ($exemptions->count() === 1) {
                return true;
            }
        }

        $day = strtolower(Carbon::parse($date)->dayName);
        $time_interval = $this->calendars()
            ->where('day_of_week', '=', $day)
            ->get(['start_time', 'end_time'])
            ->first()
                ?->toArray();

        $possible_time_slots = $this->getTimeIntervals($time_interval['start_time'], $time_interval['end_time']);

        $appointmentCount = $this->appointments()
            ->where('date', '=', $date)
            ->whereIn('start_time', $possible_time_slots)
            ->count();

        if ($appointmentCount === count($possible_time_slots)) {
            return true;
        }

        return false;
    }
}