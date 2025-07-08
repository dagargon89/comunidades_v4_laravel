<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActionLineResource\Pages;
use App\Filament\Resources\ActionLineResource\RelationManagers;
use App\Models\ActionLine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class ActionLineResource extends Resource
{
    protected static ?string $model = ActionLine::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Líneas de acción';
    protected static ?string $pluralLabel = 'Líneas de acción';
    protected static ?string $label = 'Línea de acción';

    protected static ?string $navigationGroup = 'Estructura Organizacional';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos de la línea de acción')
                    ->description('Información básica de la línea de acción')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                    ]),
                Section::make('Relaciones')
                    ->description('Vinculación con otros recursos')
                    ->schema([
                        Forms\Components\Select::make('program_id')
                            ->label('Programa')
                            ->relationship('program', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Programa')
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
            'index' => Pages\ListActionLines::route('/'),
            'create' => Pages\CreateActionLine::route('/create'),
            'edit' => Pages\EditActionLine::route('/{record}/edit'),
        ];
    }
}
