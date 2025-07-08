<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\BeneficiaryRegistry;
use App\Models\Activity;
use App\Models\Location;
use App\Models\DataCollector;
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

    protected static ?string $title = 'Registro de Beneficiarios';
    protected static ?string $navigationLabel = 'Registro de Beneficiarios';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public ?int $activity_id = null;
    public ?string $activity_calendar_date = null;

    public function mount(): void
    {
        $this->activity_id = Activity::query()->min('id') ?? null;
        // Selecciona la primera fecha disponible para la actividad predeterminada
        $firstCalendar = null;
        if ($this->activity_id) {
            $firstCalendar = \App\Models\ActivityCalendar::where('activity_id', $this->activity_id)
                ->orderBy('start_date')
                ->first();
        }
        $this->activity_calendar_date = $firstCalendar ? $firstCalendar->start_date : null;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('activity_id')
                ->label('Actividad')
                ->options(Activity::pluck('description', 'id')->toArray())
                ->searchable()
                ->required()
                ->live()
                ->default(Activity::query()->min('id')),
            Select::make('activity_calendar_date')
                ->label('Fecha de la actividad')
                ->options(function () {
                    $activityId = $this->activity_id;
                    if (!$activityId) {
                        return [];
                    }
                    return \App\Models\ActivityCalendar::where('activity_id', $activityId)
                        ->orderBy('start_date')
                        ->get()
                        ->pluck('start_date', 'start_date')
                        ->unique()
                        ->toArray();
                })
                ->required()
                ->live()
                ->default(function () {
                    $activityId = $this->activity_id;
                    if (!$activityId) {
                        return null;
                    }
                    $firstCalendar = \App\Models\ActivityCalendar::where('activity_id', $activityId)
                        ->orderBy('start_date')
                        ->first();
                    return $firstCalendar ? $firstCalendar->start_date : null;
                })
                ->native(false)
                ->searchable()
                ->preload()
                ->afterStateUpdated(function ($state, $set) {
                    $set('activity_calendar_date', $state);
                }),
        ]);
    }

    public function updatedActivityId($value): void
    {
        $this->activity_id = $value ? (int) $value : null;
        // Buscar la primera fecha disponible para la nueva actividad
        $firstCalendar = null;
        if ($this->activity_id) {
            $firstCalendar = \App\Models\ActivityCalendar::where('activity_id', $this->activity_id)
                ->orderBy('start_date')
                ->first();
        }
        $this->activity_calendar_date = $firstCalendar ? $firstCalendar->start_date : null;
        $this->resetTable();
    }

    public function updatedActivityCalendarDate($value): void
    {
        $this->activity_calendar_date = $value;
        $this->resetTable();
    }

    protected function getTableQuery()
    {
        if (
            !$this->activity_id || $this->activity_id <= 0 ||
            !$this->activity_calendar_date
        ) {
            return BeneficiaryRegistry::query()->whereRaw('1=0');
        }
        // Buscar los IDs de calendarios con esa fecha y actividad
        $calendarIds = \App\Models\ActivityCalendar::where('activity_id', $this->activity_id)
            ->where('start_date', $this->activity_calendar_date)
            ->pluck('id');

        return BeneficiaryRegistry::query()
            ->where('activity_id', $this->activity_id)
            ->whereIn('activity_calendar_id', $calendarIds);
    }

    protected function shouldRenderTable(): bool
    {
        return $this->activity_id && $this->activity_id > 0;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('last_name')->label('Apellido Paterno'),
            Tables\Columns\TextColumn::make('mother_last_name')->label('Apellido Materno'),
            Tables\Columns\TextColumn::make('first_names')->label('Nombres'),
            Tables\Columns\TextColumn::make('birth_year')->label('Año Nacimiento'),
            Tables\Columns\TextColumn::make('gender')->label('Género')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'Male' => 'Masculino',
                    'Female' => 'Femenino',
                    default => $state,
                }),
            Tables\Columns\TextColumn::make('phone')->label('Teléfono'),
            Tables\Columns\TextColumn::make('activity.description')->label('Actividad')->limit(50),
            Tables\Columns\TextColumn::make('activityCalendar.start_date')->label('Fecha de la actividad')->date('d/m/Y'),
            Tables\Columns\TextColumn::make('created_at')->label('Registrado el')->dateTime('d/m/Y H:i'),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        if (!$this->activity_id || $this->activity_id <= 0 || !$this->activity_calendar_date) {
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
                        'Male' => 'Masculino',
                        'Female' => 'Femenino',
                    ])->required(),
                    TextInput::make('phone')->label('Teléfono')->tel(),
                    TextInput::make('signature')->label('Firma'),
                    Forms\Components\Textarea::make('address_backup')
                        ->label('Dirección de respaldo')
                        ->rows(3),
                    Select::make('location_id')->label('Ubicación')
                        ->options(Location::pluck('name', 'id')->toArray())
                        ->required()
                        ->native(false)
                        ->searchable()
                        ->preload(),
                    Select::make('data_collector_id')->label('Recolector de datos')
                        ->options(DataCollector::pluck('name', 'id')->toArray())
                        ->required()
                        ->native(false)
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data): void {
                    $calendarId = \App\Models\ActivityCalendar::where('activity_id', $this->activity_id)
                        ->where('start_date', $this->activity_calendar_date)
                        ->orderBy('id')
                        ->value('id');
                    BeneficiaryRegistry::create(array_merge($data, [
                        'activity_id' => $this->activity_id,
                        'activity_calendar_id' => $calendarId,
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
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    TextInput::make('last_name')->label('Apellido Paterno')->required(),
                                    TextInput::make('mother_last_name')->label('Apellido Materno')->required(),
                                ]),
                            TextInput::make('first_names')->label('Nombres')->required()->columnSpanFull(),
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    TextInput::make('birth_year')->label('Año de Nacimiento')->numeric()->minValue(1900)->maxValue(date('Y')),
                                    Select::make('gender')->label('Género')->options([
                                        'Male' => 'Masculino',
                                        'Female' => 'Femenino',
                                    ])->required(),
                                    TextInput::make('phone')->label('Teléfono')->tel(),
                                ]),
                            TextInput::make('signature')->label('Firma')->columnSpanFull(),
                            Forms\Components\Textarea::make('address_backup')
                                ->label('Dirección de respaldo')
                                ->rows(2)
                                ->columnSpanFull(),
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Select::make('location_id')->label('Ubicación')
                                        ->options(Location::pluck('name', 'id')->toArray())
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->preload(),
                                    Select::make('data_collector_id')->label('Recolector de datos')
                                        ->options(DataCollector::pluck('name', 'id')->toArray())
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->preload(),
                                ]),
                        ])
                        ->minItems(1)
                        ->addActionLabel('Agregar otro beneficiario')
                        ->columns(1),
                ])
                ->action(function (array $data): void {
                    $calendarId = \App\Models\ActivityCalendar::where('activity_id', $this->activity_id)
                        ->where('start_date', $this->activity_calendar_date)
                        ->orderBy('id')
                        ->value('id');
                    foreach ($data['beneficiaries'] as $beneficiary) {
                        BeneficiaryRegistry::create(array_merge($beneficiary, [
                            'activity_id' => $this->activity_id,
                            'activity_calendar_id' => $calendarId,
                        ]));
                    }
                    Notification::make()
                        ->title('Beneficiarios registrados correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getActivityInfo(): ?array
    {
        if (!$this->activity_id || !$this->activity_calendar_date) {
            return null;
        }
        $calendar = \App\Models\ActivityCalendar::where('activity_id', $this->activity_id)
            ->where('start_date', $this->activity_calendar_date)
            ->orderBy('id')
            ->first();
        if (!$calendar) {
            return null;
        }
        $activity = $calendar->activity;
        $responsible = $activity?->responsible?->name ?? '-';
        return [
            'actividad' => $activity?->description ?? '-',
            'fecha' => $calendar->start_date ?? '-',
            'hora_inicio' => $calendar->start_hour ?? '-',
            'hora_fin' => $calendar->end_hour ?? '-',
            'responsable' => $responsible,
        ];
    }
}
