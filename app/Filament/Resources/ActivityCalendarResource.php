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
                Forms\Components\Section::make('Fechas y horarios')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de inicio')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de fin')
                            ->required(),
                        Forms\Components\TimePicker::make('start_hour')
                            ->label('Hora de inicio'),
                        Forms\Components\TimePicker::make('end_hour')
                            ->label('Hora de fin'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Ubicación y responsables')
                    ->schema([
                        Forms\Components\Select::make('activity_id')
                            ->label('Actividad')
                            ->relationship('activity', 'description')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('location_id')
                            ->label('Ubicación')
                            ->relationship('location', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('data_collector_id')
                            ->label('Capturista')
                            ->relationship('dataCollector', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Detalles adicionales')
                    ->schema([
                        Forms\Components\Textarea::make('address_backup')
                            ->label('Dirección alternativa'),
                        Forms\Components\Toggle::make('cancelled')
                            ->label('Cancelado')
                            ->required(),
                        Forms\Components\Textarea::make('change_reason')
                            ->label('Motivo de cambio'),
                    ]),
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
