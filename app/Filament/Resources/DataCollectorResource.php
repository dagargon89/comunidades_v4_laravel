<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataCollectorResource\Pages;
use App\Filament\Resources\DataCollectorResource\RelationManagers;
use App\Models\DataCollector;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataCollectorResource extends Resource
{
    protected static ?string $model = DataCollector::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Capturistas';
    protected static ?string $pluralLabel = 'Capturistas';
    protected static ?string $label = 'Capturista';
    protected static ?string $navigationGroup = 'Configuración del Sistema';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del capturista')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Toggle::make('active')
                            ->label('Activo')
                            ->required(),
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
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),
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
            'index' => Pages\ListDataCollectors::route('/'),
            'create' => Pages\CreateDataCollector::route('/create'),
            'edit' => Pages\EditDataCollector::route('/{record}/edit'),
        ];
    }
}
