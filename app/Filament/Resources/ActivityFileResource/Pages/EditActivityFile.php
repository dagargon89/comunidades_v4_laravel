<?php

namespace App\Filament\Resources\ActivityFileResource\Pages;

use App\Filament\Resources\ActivityFileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityFile extends EditRecord
{
    protected static string $resource = ActivityFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
