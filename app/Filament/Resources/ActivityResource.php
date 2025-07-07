<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';

    protected static ?string $navigationLabel = 'Actividades';
    protected static ?string $pluralLabel = 'Actividades';
    protected static ?string $label = 'Actividad';

    protected static ?string $navigationGroup = 'Ejecución y Seguimiento';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                Forms\Components\Select::make('specific_objective_id')
                    ->label('Objetivo específico')
                    ->relationship('specificObjective', 'description')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('responsible_id')
                    ->label('Responsable')
                    ->relationship('responsible', 'name')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('goal_id')
                    ->label('Meta')
                    ->relationship('goal', 'description')
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
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('specificObjective.description')
                    ->label('Objetivo específico')
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsible.name')
                    ->label('Responsable')
                    ->sortable(),
                Tables\Columns\TextColumn::make('goal.description')
                    ->label('Meta')
                    ->limit(50)
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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
