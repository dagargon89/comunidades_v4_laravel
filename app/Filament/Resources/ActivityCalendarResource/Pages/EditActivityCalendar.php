<?php

namespace App\Filament\Resources\ActivityCalendarResource\Pages;

use App\Filament\Resources\ActivityCalendarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityCalendar extends EditRecord
{
    protected static string $resource = ActivityCalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
