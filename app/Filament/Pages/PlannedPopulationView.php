<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PlannedPopulation;
use Filament\Tables\Filters\MultiSelectFilter;

class PlannedPopulationView extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Población Planificada';
    protected static ?string $title = 'Población Planificada';
    protected static ?string $slug = 'planned-population';

    protected static string $view = 'filament.pages.planned-population-view';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('activity_name')
                    ->label('Actividad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('responsible_name')
                    ->label('Responsable')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('unit')
                    ->label('Unidad')
                    ->sortable(),
                TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),
                TextColumn::make('month')
                    ->label('Mes')
                    ->sortable(),
                TextColumn::make('target_value')
                    ->label('Valor Objetivo')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('real_value')
                    ->label('Valor Real')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                MultiSelectFilter::make('responsible_name')
                    ->label('Responsable')
                    ->options(fn () => PlannedPopulation::query()->distinct()->pluck('responsible_name', 'responsible_name')->toArray()),
                MultiSelectFilter::make('year')
                    ->label('Año')
                    ->options(fn () => PlannedPopulation::query()->distinct()->orderBy('year', 'desc')->pluck('year', 'year')->toArray()),
                MultiSelectFilter::make('month')
                    ->label('Mes')
                    ->options(fn () => PlannedPopulation::query()->distinct()->orderBy('month')->pluck('month', 'month')->toArray()),
                MultiSelectFilter::make('activity_name')
                    ->label('Actividad')
                    ->options(fn () => PlannedPopulation::query()->distinct()->pluck('activity_name', 'activity_name')->toArray()),
            ])
            ->defaultSort('year', 'desc')
            ->defaultSort('month', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getTableQuery(): Builder
    {
        return PlannedPopulation::query();
    }
}
