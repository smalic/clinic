<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['end_time'] = Carbon::createFromTimeString($data['start_time'])->addMinutes(30)->format('H:i');

        return $data;
    }
}
