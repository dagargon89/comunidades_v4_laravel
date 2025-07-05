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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('unit'),
                Forms\Components\TextInput::make('year')
                    ->numeric(),
                Forms\Components\TextInput::make('month')
                    ->numeric(),
                Forms\Components\Toggle::make('is_product')
                    ->required(),
                Forms\Components\Toggle::make('is_population')
                    ->required(),
                Forms\Components\TextInput::make('population_target_value')
                    ->numeric(),
                Forms\Components\TextInput::make('population_real_value')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('product_target_value')
                    ->numeric(),
                Forms\Components\TextInput::make('product_real_value')
                    ->numeric(),
                Forms\Components\Select::make('data_collector_id')
                    ->relationship('dataCollector', 'name'),
                Forms\Components\Select::make('activity_id')
                    ->relationship('activity', 'id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_product')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_population')
                    ->boolean(),
                Tables\Columns\TextColumn::make('population_target_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('population_real_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_target_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_real_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dataCollector.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('activity.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
