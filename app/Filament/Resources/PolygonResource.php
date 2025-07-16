<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolygonResource\Pages;
use App\Filament\Resources\PolygonResource\RelationManagers;
use App\Models\Polygon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PolygonResource extends Resource
{
    protected static ?string $model = Polygon::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Polígonos';
    protected static ?string $pluralLabel = 'Polígonos';
    protected static ?string $label = 'Polígono';

    protected static ?string $navigationGroup = 'Gestión Geográfica';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del polígono')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre'),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
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
            'index' => Pages\ListPolygons::route('/'),
            'create' => Pages\CreatePolygon::route('/create'),
            'edit' => Pages\EditPolygon::route('/{record}/edit'),
        ];
    }
}
