<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ActivitySummary;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ActivitySummaryView extends Page implements HasTable
{
    use InteractsWithTable, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Resumen de Actividades';
    protected static ?string $title = 'Resumen de Actividades';
    protected static ?string $slug = 'activity-summary';

    protected static string $view = 'filament.pages.activity-summary-view';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('activity_name')
                    ->label('Actividad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project_name')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('objective_description')
                    ->label('Objetivo Específico')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('responsible_name')
                    ->label('Responsable')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organization')
                    ->label('Organización')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Fecha Inicio')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Fecha Fin')
                    ->date()
                    ->sortable(),
                TextColumn::make('products_count')
                    ->label('Productos')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('population_count')
                    ->label('Población')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('beneficiaries_count')
                    ->label('Beneficiarios')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                MultiSelectFilter::make('project_name')
                    ->label('Proyecto')
                    ->options(fn () => ActivitySummary::query()->distinct()->pluck('project_name', 'project_name')->toArray()),
                MultiSelectFilter::make('responsible_name')
                    ->label('Responsable')
                    ->options(fn () => ActivitySummary::query()->distinct()->pluck('responsible_name', 'responsible_name')->toArray()),
                MultiSelectFilter::make('organization')
                    ->label('Organización')
                    ->options(fn () => ActivitySummary::query()->distinct()->pluck('organization', 'organization')->toArray()),
                Filter::make('start_date')
                    ->form([
                        DatePicker::make('start_from')->label('Desde'),
                        DatePicker::make('start_until')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_from'], fn ($q, $date) => $q->whereDate('start_date', '>=', $date))
                            ->when($data['start_until'], fn ($q, $date) => $q->whereDate('start_date', '<=', $date));
                    }),
            ])
            ->defaultSort('activity_name', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getTableQuery(): Builder
    {
        return ActivitySummary::query();
    }
}
