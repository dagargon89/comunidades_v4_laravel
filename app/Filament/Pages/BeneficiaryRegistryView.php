<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\BeneficiaryRegistry;
use App\Models\Activity;
use Filament\Tables\Actions;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;

class BeneficiaryRegistryView extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.beneficiary-registry-view';

    public ?int $activity_id = null;

    public function mount(): void
    {
        $this->activity_id = null;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('activity_id')
                ->label('Actividad')
                ->options(Activity::pluck('description', 'id'))
                ->searchable()
                ->required()
                ->live(),
        ]);
    }

    public function updatedActivityId($value): void
    {
        $this->activity_id = $value ? (int) $value : null;
        $this->resetTable();
    }

    protected function getTableQuery()
    {
        if (!$this->activity_id) {
            return BeneficiaryRegistry::query()->whereRaw('1=0');
        }
        return BeneficiaryRegistry::query()
            ->when($this->activity_id, fn ($q) => $q->where('activity_id', $this->activity_id));
    }

    protected function shouldRenderTable(): bool
    {
        return filled($this->activity_id);
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
        if (!filled($this->activity_id)) {
            return [];
        }
        return [
            Actions\Action::make('addSingle')
                ->label('Registrar beneficiario único')
                ->icon('heroicon-o-user-plus')
                ->form([
                    TextInput::make('last_name')->label('Apellido Paterno')->required(),
                    TextInput::make('mother_last_name')->label('Apellido Materno')->required(),
                    TextInput::make('first_names')->label('Nombres')->required(),
                    TextInput::make('birth_year')->label('Año de Nacimiento')->numeric()->minValue(1900)->maxValue(date('Y')),
                    Select::make('gender')->label('Género')->options([
                        'Masculino' => 'Masculino',
                        'Femenino' => 'Femenino',
                        'Otro' => 'Otro',
                    ])->required(),
                    TextInput::make('phone')->label('Teléfono')->tel(),
                ])
                ->action(function (array $data): void {
                    BeneficiaryRegistry::create(array_merge($data, [
                        'activity_id' => $this->activity_id,
                    ]));
                    Notification::make()
                        ->title('Beneficiario registrado correctamente')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('addMassive')
                ->label('Registrar beneficiarios masivos')
                ->icon('heroicon-o-users')
                ->form([
                    Repeater::make('beneficiaries')
                        ->label('Beneficiarios')
                        ->schema([
                            TextInput::make('last_name')->label('Apellido Paterno')->required(),
                            TextInput::make('mother_last_name')->label('Apellido Materno')->required(),
                            TextInput::make('first_names')->label('Nombres')->required(),
                            TextInput::make('birth_year')->label('Año de Nacimiento')->numeric()->minValue(1900)->maxValue(date('Y')),
                            Select::make('gender')->label('Género')->options([
                                'Masculino' => 'Masculino',
                                'Femenino' => 'Femenino',
                                'Otro' => 'Otro',
                            ])->required(),
                            TextInput::make('phone')->label('Teléfono')->tel(),
                        ])
                        ->minItems(1)
                        ->addActionLabel('Agregar otro beneficiario'),
                ])
                ->action(function (array $data): void {
                    foreach ($data['beneficiaries'] as $beneficiary) {
                        BeneficiaryRegistry::create(array_merge($beneficiary, [
                            'activity_id' => $this->activity_id,
                        ]));
                    }
                    Notification::make()
                        ->title('Beneficiarios registrados correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }
}
