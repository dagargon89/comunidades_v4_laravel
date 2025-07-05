<?php

namespace App\Filament\Resources\ProgramIndicatorResource\Pages;

use App\Filament\Resources\ProgramIndicatorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramIndicator extends EditRecord
{
    protected static string $resource = ProgramIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
