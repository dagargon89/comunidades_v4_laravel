<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AxeResource\Pages;
use App\Filament\Resources\AxeResource\RelationManagers;
use App\Models\Axe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class AxeResource extends Resource
{
    protected static ?string $model = Axe::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Ejes';
    protected static ?string $pluralLabel = 'Ejes';
    protected static ?string $label = 'Eje';

    protected static ?string $navigationGroup = 'Estructura Organizacional';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos del eje')
                    ->description('Información básica del eje')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre'),
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
            'index' => Pages\ListAxes::route('/'),
            'create' => Pages\CreateAxe::route('/create'),
            'edit' => Pages\EditAxe::route('/{record}/edit'),
        ];
    }
}
