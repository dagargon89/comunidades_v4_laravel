<?php

namespace App\Filament\Resources\PlannedMetricResource\Pages;

use App\Filament\Resources\PlannedMetricResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlannedMetrics extends ListRecords
{
    protected static string $resource = PlannedMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
