<?php

namespace App\Filament\Resources\AxeResource\Pages;

use App\Filament\Resources\AxeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAxe extends EditRecord
{
    protected static string $resource = AxeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
