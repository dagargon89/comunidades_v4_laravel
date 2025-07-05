<?php

namespace App\Filament\Resources\ProgramIndicatorResource\Pages;

use App\Filament\Resources\ProgramIndicatorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramIndicators extends ListRecords
{
    protected static string $resource = ProgramIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
