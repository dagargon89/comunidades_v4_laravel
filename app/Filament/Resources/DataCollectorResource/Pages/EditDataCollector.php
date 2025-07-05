<?php

namespace App\Filament\Resources\DataCollectorResource\Pages;

use App\Filament\Resources\DataCollectorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataCollector extends EditRecord
{
    protected static string $resource = DataCollectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
