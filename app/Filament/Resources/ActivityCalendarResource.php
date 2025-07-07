<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityCalendarResource\Pages;
use App\Filament\Resources\ActivityCalendarResource\RelationManagers;
use App\Models\ActivityCalendar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityCalendarResource extends Resource
{
    protected static ?string $model = ActivityCalendar::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Calendarios de actividades';
    protected static ?string $pluralLabel = 'Calendarios de actividades';
    protected static ?string $label = 'Calendario de actividades';

    protected static ?string $navigationGroup = 'Ejecución y Seguimiento';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('start_date')
                    ->label('Fecha de inicio'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Fecha de fin'),
                Forms\Components\TextInput::make('start_hour')
                    ->label('Hora de inicio'),
                Forms\Components\TextInput::make('end_hour')
                    ->label('Hora de fin'),
                Forms\Components\Textarea::make('address_backup')
                    ->label('Respaldo de dirección')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('cancelled')
                    ->label('Cancelado')
                    ->required(),
                Forms\Components\Textarea::make('change_reason')
                    ->label('Motivo de cambio')
                    ->columnSpanFull(),
                Forms\Components\Select::make('activity_id')
                    ->label('Actividad')
                    ->relationship('activity', 'description')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('location_id')
                    ->label('Ubicación')
                    ->relationship('location', 'name')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('data_collector_id')
                    ->label('Capturista')
                    ->relationship('dataCollector', 'name')
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
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Fecha de inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fecha de fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_hour')
                    ->label('Hora de inicio'),
                Tables\Columns\TextColumn::make('end_hour')
                    ->label('Hora de fin'),
                Tables\Columns\IconColumn::make('cancelled')
                    ->label('Cancelado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('activity.id')
                    ->label('Actividad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Ubicación')
                    ->sortable(),
                Tables\Columns\TextColumn::make('dataCollector.name')
                    ->label('Capturista')
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
            'index' => Pages\ListActivityCalendars::route('/'),
            'create' => Pages\CreateActivityCalendar::route('/create'),
            'edit' => Pages\EditActivityCalendar::route('/{record}/edit'),
        ];
    }
}
