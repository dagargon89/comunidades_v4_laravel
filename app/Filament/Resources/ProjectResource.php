<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Proyectos';
    protected static ?string $pluralLabel = 'Proyectos';
    protected static ?string $label = 'Proyecto';

    protected static ?string $navigationGroup = 'Ejecución y Seguimiento';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información general')
                    ->description('Datos principales del proyecto')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        Forms\Components\Textarea::make('background')
                            ->label('Antecedentes')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('justification')
                            ->label('Justificación')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('general_objective')
                            ->label('Objetivo general')
                            ->columnSpanFull(),
                    ]),
                Section::make('Relaciones')
                    ->description('Vinculación con otros recursos')
                    ->schema([
                        Forms\Components\Select::make('financier_id')
                            ->label('Financiador')
                            ->relationship('financier', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload(),
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
                Tables\Columns\TextColumn::make('background')
                    ->label('Antecedentes')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('justification')
                    ->label('Justificación')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('general_objective')
                    ->label('Objetivo general')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('financier.name')
                    ->label('Financiador')
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
