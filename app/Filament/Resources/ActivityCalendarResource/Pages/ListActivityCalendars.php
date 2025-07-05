<?php

namespace App\Filament\Resources\ActivityCalendarResource\Pages;

use App\Filament\Resources\ActivityCalendarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityCalendars extends ListRecords
{
    protected static string $resource = ActivityCalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
