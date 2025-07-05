<?php

namespace App\Filament\Resources\ActivityFileResource\Pages;

use App\Filament\Resources\ActivityFileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityFiles extends ListRecords
{
    protected static string $resource = ActivityFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
