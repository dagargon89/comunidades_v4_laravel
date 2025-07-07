<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlannedMetricResource\Pages;
use App\Filament\Resources\PlannedMetricResource\RelationManagers;
use App\Models\PlannedMetric;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlannedMetricResource extends Resource
{
    protected static ?string $model = PlannedMetric::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Métricas planificadas';
    protected static ?string $pluralLabel = 'Métricas planificadas';
    protected static ?string $label = 'Métrica planificada';

    protected static ?string $navigationGroup = 'Ejecución y Seguimiento';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('unit')
                    ->label('Unidad'),
                Forms\Components\TextInput::make('year')
                    ->label('Año')
                    ->numeric(),
                Forms\Components\TextInput::make('month')
                    ->label('Mes')
                    ->numeric(),
                Forms\Components\Toggle::make('is_product')
                    ->label('¿Es producto?')
                    ->required(),
                Forms\Components\Toggle::make('is_population')
                    ->label('¿Es población?')
                    ->required(),
                Forms\Components\TextInput::make('population_target_value')
                    ->label('Meta de población')
                    ->numeric(),
                Forms\Components\TextInput::make('population_real_value')
                    ->label('Valor real de población')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('product_target_value')
                    ->label('Meta de producto')
                    ->numeric(),
                Forms\Components\TextInput::make('product_real_value')
                    ->label('Valor real de producto')
                    ->numeric(),
                Forms\Components\Select::make('data_collector_id')
                    ->label('Capturista')
                    ->relationship('dataCollector', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('activity_id')
                    ->label('Actividad')
                    ->relationship('activity', 'description')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                    ->label('Mes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_product')
                    ->label('¿Es producto?')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_population')
                    ->label('¿Es población?')
                    ->boolean(),
                Tables\Columns\TextColumn::make('population_target_value')
                    ->label('Meta de población')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('population_real_value')
                    ->label('Valor real de población')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_target_value')
                    ->label('Meta de producto')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_real_value')
                    ->label('Valor real de producto')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dataCollector.name')
                    ->label('Capturista')
                    ->sortable(),
                Tables\Columns\TextColumn::make('activity.id')
                    ->label('Actividad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlannedMetrics::route('/'),
            'create' => Pages\CreatePlannedMetric::route('/create'),
            'edit' => Pages\EditPlannedMetric::route('/{record}/edit'),
        ];
    }
}
