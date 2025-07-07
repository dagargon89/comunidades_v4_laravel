<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryRegistryResource\Pages;
use App\Filament\Resources\BeneficiaryRegistryResource\RelationManagers;
use App\Models\BeneficiaryRegistry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BeneficiaryRegistryResource extends Resource
{
    protected static ?string $model = BeneficiaryRegistry::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Registros de beneficiarios';
    protected static ?string $pluralLabel = 'Registros de beneficiarios';
    protected static ?string $label = 'Registro de beneficiario';

    protected static ?string $navigationGroup = 'Ejecución y Seguimiento';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('last_name')
                    ->label('Apellido paterno'),
                Forms\Components\TextInput::make('mother_last_name')
                    ->label('Apellido materno'),
                Forms\Components\TextInput::make('first_names')
                    ->label('Nombres'),
                Forms\Components\TextInput::make('birth_year')
                    ->label('Año de nacimiento'),
                Forms\Components\Select::make('gender')
                    ->label('Género')
                    ->options([
                        'male' => 'Masculino',
                        'female' => 'Femenino',
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel(),
                Forms\Components\Textarea::make('signature')
                    ->label('Firma')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('address_backup')
                    ->label('Respaldo de dirección')
                    ->columnSpanFull(),
                    Forms\Components\TextInput::make('identifier')
                    ->label('Identificador')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('activity_id')
                    ->label('Actividad')
                    ->relationship('activity', 'description')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('activity_date')
                    ->label('Fecha de la actividad'),
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
                    ->native(false)
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellido paterno')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mother_last_name')
                    ->label('Apellido materno')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_names')
                    ->label('Nombres')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_year')
                    ->label('Año de nacimiento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Género')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label('Identificador')
                    ->searchable(),
                Tables\Columns\TextColumn::make('activity.description')
                    ->label('Actividad')
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('activity_date')
                    ->label('Fecha de la actividad')
                    ->date(),
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
            'index' => Pages\ListBeneficiaryRegistries::route('/'),
            'create' => Pages\CreateBeneficiaryRegistry::route('/create'),
            'edit' => Pages\EditBeneficiaryRegistry::route('/{record}/edit'),
        ];
    }
}
