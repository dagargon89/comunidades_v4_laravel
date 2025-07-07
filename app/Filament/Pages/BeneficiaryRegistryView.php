<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\BeneficiaryRegistry;

class BeneficiaryRegistryView extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.beneficiary-registry-view';

    protected function getTableQuery()
    {
        return BeneficiaryRegistry::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('last_name')->label('Apellido Paterno'),
            Tables\Columns\TextColumn::make('mother_last_name')->label('Apellido Materno'),
            Tables\Columns\TextColumn::make('first_names')->label('Nombres'),
            Tables\Columns\TextColumn::make('birth_year')->label('Año Nacimiento'),
            Tables\Columns\TextColumn::make('gender')->label('Género'),
            Tables\Columns\TextColumn::make('phone')->label('Teléfono'),
            Tables\Columns\TextColumn::make('created_at')->label('Registrado el')->dateTime('d/m/Y H:i'),
        ];
    }
}
