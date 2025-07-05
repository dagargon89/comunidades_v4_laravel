<?php

namespace App\Filament\Resources\ActionLineResource\Pages;

use App\Filament\Resources\ActionLineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActionLines extends ListRecords
{
    protected static string $resource = ActionLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
