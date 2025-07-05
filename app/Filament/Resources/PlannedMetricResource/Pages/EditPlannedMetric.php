<?php

namespace App\Filament\Resources\PlannedMetricResource\Pages;

use App\Filament\Resources\PlannedMetricResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlannedMetric extends EditRecord
{
    protected static string $resource = PlannedMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
