<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramIndicatorResource\Pages;
use App\Filament\Resources\ProgramIndicatorResource\RelationManagers;
use App\Models\ProgramIndicator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramIndicatorResource extends Resource
{
    protected static ?string $model = ProgramIndicator::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Estructura Organizacional';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('initial_value')
                    ->numeric(),
                Forms\Components\TextInput::make('final_value')
                    ->numeric(),
                Forms\Components\Select::make('program_id')
                    ->relationship('program', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('initial_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListProgramIndicators::route('/'),
            'create' => Pages\CreateProgramIndicator::route('/create'),
            'edit' => Pages\EditProgramIndicator::route('/{record}/edit'),
        ];
    }
}
