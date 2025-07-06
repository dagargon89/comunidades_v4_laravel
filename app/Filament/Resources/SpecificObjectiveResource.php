<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecificObjectiveResource\Pages;
use App\Filament\Resources\SpecificObjectiveResource\RelationManagers;
use App\Models\SpecificObjective;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpecificObjectiveResource extends Resource
{
    protected static ?string $model = SpecificObjective::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Objetivos específicos';
    protected static ?string $pluralLabel = 'Objetivos específicos';
    protected static ?string $label = 'Objetivo específico';

    protected static ?string $navigationGroup = 'Gestión de Proyectos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                Forms\Components\Select::make('project_id')
                    ->label('Proyecto')
                    ->relationship('project', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Proyecto')
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
            'index' => Pages\ListSpecificObjectives::route('/'),
            'create' => Pages\CreateSpecificObjective::route('/create'),
            'edit' => Pages\EditSpecificObjective::route('/{record}/edit'),
        ];
    }
}
