<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\BeneficiaryRegistry;
use Filament\Tables\Actions;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;

class BeneficiaryRegistryView extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.beneficiary-registry-view';

    protected function getTableQuery()
    {
        return BeneficiaryRegistry::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('last_name')->label('Apellido Paterno'),
            Tables\Columns\TextColumn::make('mother_last_name')->label('Apellido Materno'),
            Tables\Columns\TextColumn::make('first_names')->label('Nombres'),
            Tables\Columns\TextColumn::make('birth_year')->label('Año Nacimiento'),
            Tables\Columns\TextColumn::make('gender')->label('Género'),
            Tables\Columns\TextColumn::make('phone')->label('Teléfono'),
            Tables\Columns\TextColumn::make('created_at')->label('Registrado el')->dateTime('d/m/Y H:i'),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Actions\Action::make('addSingle')
                ->label('Registrar beneficiario único')
                ->icon('heroicon-o-user-plus')
                ->form([
                    Forms\Components\TextInput::make('last_name')->label('Apellido Paterno')->required(),
                    Forms\Components\TextInput::make('mother_last_name')->label('Apellido Materno')->required(),
                    Forms\Components\TextInput::make('first_names')->label('Nombres')->required(),
                    Forms\Components\TextInput::make('birth_year')->label('Año de Nacimiento')->numeric()->minValue(1900)->maxValue(date('Y')),
                    Forms\Components\Select::make('gender')->label('Género')->options([
                        'Masculino' => 'Masculino',
                        'Femenino' => 'Femenino',
                        'Otro' => 'Otro',
                    ])->required(),
                    Forms\Components\TextInput::make('phone')->label('Teléfono')->tel(),
                ])
                ->action(function (array $data): void {
                    BeneficiaryRegistry::create($data);
                    Notification::make()
                        ->title('Beneficiario registrado correctamente')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('addMassive')
                ->label('Registrar beneficiarios masivos')
                ->icon('heroicon-o-users')
                ->form([
                    Forms\Components\Repeater::make('beneficiaries')
                        ->label('Beneficiarios')
                        ->schema([
                            Forms\Components\TextInput::make('last_name')->label('Apellido Paterno')->required(),
                            Forms\Components\TextInput::make('mother_last_name')->label('Apellido Materno')->required(),
                            Forms\Components\TextInput::make('first_names')->label('Nombres')->required(),
                            Forms\Components\TextInput::make('birth_year')->label('Año de Nacimiento')->numeric()->minValue(1900)->maxValue(date('Y')),
                            Forms\Components\Select::make('gender')->label('Género')->options([
                                'Masculino' => 'Masculino',
                                'Femenino' => 'Femenino',
                                'Otro' => 'Otro',
                            ])->required(),
                            Forms\Components\TextInput::make('phone')->label('Teléfono')->tel(),
                        ])
                        ->minItems(1)
                        ->addActionLabel('Agregar otro beneficiario'),
                ])
                ->action(function (array $data): void {
                    foreach ($data['beneficiaries'] as $beneficiary) {
                        BeneficiaryRegistry::create($beneficiary);
                    }
                    Notification::make()
                        ->title('Beneficiarios registrados correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }
}
