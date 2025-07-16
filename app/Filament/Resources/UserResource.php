<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $pluralLabel = 'Usuarios';
    protected static ?string $label = 'Usuario';
    protected static ?string $navigationGroup = 'Configuración del Sistema';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos de usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Acceso y roles')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Roles'),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? bcrypt($state) : null)
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state)),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Jerarquía')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Jefe directo')
                            ->options(fn () => \App\Models\User::where('id', '!=', request()->route('record'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Selecciona el jefe directo de este usuario (opcional)'),
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
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager.name')
                    ->label('Jefe directo')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Correo verificado en')
                    ->dateTime()
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
