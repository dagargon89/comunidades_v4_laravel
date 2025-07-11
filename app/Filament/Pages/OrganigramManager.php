<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;

class OrganigramManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Gestión de Organigrama';
    protected static ?string $title = 'Gestión de Organigrama';
    protected static string $view = 'filament.pages.organigram-manager';

    protected function getTableQuery()
    {
        return User::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')->label('Nombre')->searchable(),
            TextColumn::make('email')->label('Correo')->searchable(),
            SelectColumn::make('parent_id')
                ->label('Jefe directo')
                ->options(fn () => User::pluck('name', 'id')->toArray())
                ->searchable()
                ->placeholder('Sin jefe')
                ->afterStateUpdated(function ($record, $state) {
                    if ($record->id == $state) return; // No permitir ser su propio jefe
                    $record->parent_id = $state ?: null;
                    $record->save();
                }),
        ];
    }
}
