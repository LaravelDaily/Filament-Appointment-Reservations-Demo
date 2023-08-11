<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $date             = Carbon::parse($data['date']);
        [$trackId, $hour] = explode('-', $data['track']);
        $startTime        = $date->copy()->hour($hour);
        $endTime          = $startTime->copy()->addHour();

        $dateTimeFormat = 'Y-m-d H:i:s';

        return [
            'user_id'    => auth()->id(),
            'track_id'   => $trackId,
            'start_time' => $startTime->format($dateTimeFormat),
            'end_time'   => $endTime->format($dateTimeFormat),
        ];
    }
}
