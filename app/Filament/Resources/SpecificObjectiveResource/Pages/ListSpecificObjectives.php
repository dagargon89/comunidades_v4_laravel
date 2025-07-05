<?php

namespace App\Filament\Resources\SpecificObjectiveResource\Pages;

use App\Filament\Resources\SpecificObjectiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecificObjectives extends ListRecords
{
    protected static string $resource = SpecificObjectiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
