<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Ubicaciones';
    protected static ?string $pluralLabel = 'Ubicaciones';
    protected static ?string $label = 'Ubicación';

    protected static ?string $navigationGroup = 'Gestión Geográfica';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre'),
                        Forms\Components\TextInput::make('category')
                            ->label('Categoría'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Dirección')
                    ->schema([
                        Forms\Components\TextInput::make('street')
                            ->label('Calle'),
                        Forms\Components\TextInput::make('neighborhood')
                            ->label('Colonia'),
                        Forms\Components\TextInput::make('ext_number')
                            ->label('Número exterior'),
                        Forms\Components\TextInput::make('int_number')
                            ->label('Número interior'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Otros datos')
                    ->schema([
                        Forms\Components\TextInput::make('google_place_id')
                            ->label('Google Place ID'),
                        Forms\Components\Select::make('polygon_id')
                            ->label('Polígono')
                            ->relationship('polygon', 'name')
                            ->native(false)
                            ->searchable()
                            ->preload(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->searchable(),
                Tables\Columns\TextColumn::make('neighborhood')
                    ->label('Colonia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ext_number')
                    ->label('Número exterior')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('int_number')
                    ->label('Número interior')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('google_place_id')
                    ->label('ID de Google Place')
                    ->searchable(),
                Tables\Columns\TextColumn::make('polygon.name')
                    ->label('Polígono')
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
