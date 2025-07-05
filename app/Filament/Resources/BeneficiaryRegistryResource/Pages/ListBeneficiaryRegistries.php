<?php

namespace App\Filament\Resources\BeneficiaryRegistryResource\Pages;

use App\Filament\Resources\BeneficiaryRegistryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBeneficiaryRegistries extends ListRecords
{
    protected static string $resource = BeneficiaryRegistryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
