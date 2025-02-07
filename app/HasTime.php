<?php

namespace App;

use Carbon\Carbon;

trait HasTime
{
    public function getTimeIntervals(string $start_time, string $end_time, int $interval = 30): array
    {
        $times = [];
        $start = Carbon::parse($start_time);
        $end = Carbon::parse($end_time);

        while ($start <= $end) {
            $times[] = $start->format('H:i');
            $start->addMinutes($interval);
        }

        return $times;
    }

    public function formatTimeArrayForSelect(array $times): array
    {
        return array_combine($times, $times);
    }
}
