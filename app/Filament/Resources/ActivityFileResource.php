<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityFileResource\Pages;
use App\Filament\Resources\ActivityFileResource\RelationManagers;
use App\Models\ActivityFile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityFileResource extends Resource
{
    protected static ?string $model = ActivityFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Ejecución y Seguimiento';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Archivos de actividades';
    protected static ?string $pluralLabel = 'Archivos de actividades';
    protected static ?string $label = 'Archivo de actividad';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('month')
                    ->label('Mes'),
                Forms\Components\TextInput::make('type')
                    ->label('Tipo'),
                Forms\Components\Textarea::make('file_path')
                    ->label('Ruta del archivo')
                    ->columnSpanFull(),
                Forms\Components\Select::make('activity_id')
                    ->label('Actividad')
                    ->relationship('activity', 'id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Mes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable(),
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
            'index' => Pages\ListActivityFiles::route('/'),
            'create' => Pages\CreateActivityFile::route('/create'),
            'edit' => Pages\EditActivityFile::route('/{record}/edit'),
        ];
    }
}
