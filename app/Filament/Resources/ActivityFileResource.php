<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityFileResource\Pages;
use App\Filament\Resources\ActivityFileResource\RelationManagers;
use App\Models\ActivityFile;
use App\Models\ActivityCalendar;
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
                Forms\Components\TextInput::make('file_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('file_path')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('activity_id')
                    ->relationship('activity', 'description')
                    ->required(),
                Forms\Components\Select::make('activity_calendar_id')
                    ->label('Fecha de la actividad')
                    ->options(ActivityCalendar::all()->pluck('start_date', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('file_name')->label('Nombre del archivo')->searchable(),
                Tables\Columns\TextColumn::make('file_path')->label('Ruta')->limit(30),
                Tables\Columns\TextColumn::make('activity.description')->label('Actividad'),
                Tables\Columns\TextColumn::make('activityCalendar.start_date')->label('Fecha de la actividad'),
                Tables\Columns\TextColumn::make('created_at')->label('Subido el')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                // Puedes agregar filtros aquí si lo deseas
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
