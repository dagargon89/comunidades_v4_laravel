<?php

namespace App\Filament\Resources\DataCollectorResource\Pages;

use App\Filament\Resources\DataCollectorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataCollectors extends ListRecords
{
    protected static string $resource = DataCollectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
