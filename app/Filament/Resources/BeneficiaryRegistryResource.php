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
use Filament\Forms\Components\Section;

class BeneficiaryRegistryResource extends Resource
{
    protected static ?string $model = BeneficiaryRegistry::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Beneficiarios';
    protected static ?string $pluralLabel = 'Beneficiarios';
    protected static ?string $label = 'Beneficiario';

    protected static ?string $navigationGroup = 'Ejecución y Seguimiento';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos personales')
                    ->description('Información básica del beneficiario')
                    ->schema([
                        Forms\Components\TextInput::make('last_name')->label('Apellido paterno')->required(),
                        Forms\Components\TextInput::make('mother_last_name')->label('Apellido materno')->required(),
                        Forms\Components\TextInput::make('first_names')->label('Nombres')->required(),
                        Forms\Components\TextInput::make('birth_year')->label('Año de nacimiento')->numeric()->minValue(1900)->maxValue(date('Y')),
                        Forms\Components\Select::make('gender')->label('Género')->options([
                            'Male' => 'Masculino',
                            'Female' => 'Femenino',
                        ])->required(),
                        Forms\Components\TextInput::make('phone')->label('Teléfono')->tel(),
                    ])->columns(3),
                Section::make('Información de actividad')
                    ->description('Datos de la actividad asociada')
                    ->schema([
                        Forms\Components\TextInput::make('identifier')->label('Identificador')->unique(ignoreRecord: true)->maxLength(255),
                    ])->columns(3),
                Section::make('Firma')
                    ->description('Firma digital del beneficiario')
                    ->schema([
                        Forms\Components\TextInput::make('signature')
                        ->label('Firma'),
                    ]),
                Section::make('Ubicación y capturista')
                    ->schema([
                        Forms\Components\Textarea::make('address_backup')->label('Respaldo de dirección')->columnSpanFull(),
                        Forms\Components\Select::make('location_id')->label('Ubicación')
                            ->relationship('location', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('data_collector_id')->label('Recolector de datos')
                            ->relationship('dataCollector', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
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
