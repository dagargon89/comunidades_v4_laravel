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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\View as ViewField;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Tables\Columns\ImageColumn;

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
            Section::make('Información de actividad')
                ->description('Datos de la actividad asociada')
                ->schema([
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
                ]),
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
        $query = \App\Models\BeneficiaryActivityCalendar::query()
            ->with(['beneficiaryRegistry', 'activity', 'activityCalendar']);

        if ($this->activity_id) {
            $query->where('activity_id', $this->activity_id);
        }
        if ($this->activity_calendar_date) {
            $calendarId = \App\Models\ActivityCalendar::where('activity_id', $this->activity_id)
            ->where('start_date', $this->activity_calendar_date)
                ->value('id');
            if ($calendarId) {
                $query->where('activity_calendar_id', $calendarId);
            }
        }

        return $query;
    }

    protected function shouldRenderTable(): bool
    {
        return $this->activity_id && $this->activity_id > 0;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('beneficiaryRegistry.last_name')->label('Apellido Paterno'),
            Tables\Columns\TextColumn::make('beneficiaryRegistry.mother_last_name')->label('Apellido Materno'),
            Tables\Columns\TextColumn::make('beneficiaryRegistry.first_names')->label('Nombres'),
            Tables\Columns\TextColumn::make('beneficiaryRegistry.birth_year')->label('Año Nacimiento'),
            Tables\Columns\TextColumn::make('beneficiaryRegistry.gender')->label('Género')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'Male' => 'Masculino',
                    'Female' => 'Femenino',
                    default => $state,
                }),
            Tables\Columns\TextColumn::make('beneficiaryRegistry.phone')->label('Teléfono'),
            Tables\Columns\TextColumn::make('beneficiaryRegistry.identifier')->label('Identificador'),
            Tables\Columns\TextColumn::make('activity.description')->label('Actividad')->limit(50),
            Tables\Columns\TextColumn::make('activityCalendar.start_date')->label('Fecha de la actividad')->date('d/m/Y'),
            // Columna para mostrar la firma
            ImageColumn::make('signature')
                ->label('Firma')
                ->height(60)
                ->width(180),
            Tables\Columns\TextColumn::make('created_at')->label('Registrado el')->dateTime('d/m/Y H:i'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('identifier')
                ->form([
                    TextInput::make('identifier')->label('Identificador'),
                ])
                ->query(function ($query, $data) {
                    if (!empty($data['identifier'])) {
                        $query->where('identifier', 'like', '%' . $data['identifier'] . '%');
                    }
                }),
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
                    // Campo de búsqueda por identificador
                    TextInput::make('search_identifier')
                        ->label('Buscar por identificador')
                        ->placeholder('Ej: PEREZ2025M')
                        ->helperText('Ingresa el identificador para buscar un beneficiario existente')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if (!empty($state)) {
                                $beneficiary = \App\Models\BeneficiaryRegistry::where('identifier', $state)->first();
                                if ($beneficiary) {
                                    $set('last_name', $beneficiary->last_name);
                                    $set('mother_last_name', $beneficiary->mother_last_name);
                                    $set('first_names', $beneficiary->first_names);
                                    $set('birth_year', $beneficiary->birth_year);
                                    $set('gender', $beneficiary->gender);
                                    $set('phone', $beneficiary->phone);
                                    $set('address_backup', $beneficiary->address_backup);
                                    $set('location_id', $beneficiary->location_id);
                                    $set('data_collector_id', $beneficiary->data_collector_id);

                                    // Mostrar notificación de beneficiario encontrado
                                    \Filament\Notifications\Notification::make()
                                        ->title('Beneficiario encontrado')
                                        ->body("Identificador: {$beneficiary->identifier}. Los datos han sido pre-llenados. Solo necesitas capturar la nueva firma.")
                                        ->success()
                                        ->send();
                                } else {
                                    // Limpiar campos si no se encuentra
                                    $set('last_name', '');
                                    $set('mother_last_name', '');
                                    $set('first_names', '');
                                    $set('birth_year', '');
                                    $set('gender', '');
                                    $set('phone', '');
                                    $set('address_backup', '');
                                    $set('location_id', '');
                                    $set('data_collector_id', '');

                                    \Filament\Notifications\Notification::make()
                                        ->title('Beneficiario no encontrado')
                                        ->body('No se encontró un beneficiario con ese identificador. Puedes proceder con el registro normal.')
                                        ->warning()
                                        ->send();
                                }
                            }
                        }),

                    TextInput::make('last_name')->label('Apellido Paterno')->required(),
                    TextInput::make('mother_last_name')->label('Apellido Materno')->required(),
                    TextInput::make('first_names')->label('Nombres')->required(),
                    TextInput::make('birth_year')->label('Año de Nacimiento')->numeric()->minValue(1900)->maxValue(date('Y')),
                    Select::make('gender')->label('Género')->options([
                        'Male' => 'Masculino',
                        'Female' => 'Femenino',
                    ])->required(),
                    TextInput::make('phone')->label('Teléfono')->tel(),
                    // Campo de firma digital con el nuevo plugin
                    SignaturePad::make('signature')
                        ->label('Firma del beneficiario')
                        ->required()
                        ->columnSpanFull(),
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

                    $identifier = \App\Models\BeneficiaryRegistry::generarIdentificador(
                        $data['first_names'] ?? '',
                        $data['last_name'] ?? '',
                        $data['mother_last_name'] ?? '',
                        $data['birth_year'] ?? '',
                        $data['gender'] ?? ''
                    );

                    // Buscar o crear beneficiario
                    $beneficiary = \App\Models\BeneficiaryRegistry::firstOrCreate(
                        ['identifier' => $identifier],
                        array_merge($data, [
                            'identifier' => $identifier,
                        ])
                    );

                    // Verificar si ya existe una participación para esta actividad y calendario
                    $existingParticipation = \App\Models\BeneficiaryActivityCalendar::where([
                        'beneficiary_registry_id' => $beneficiary->id,
                        'activity_id' => $this->activity_id,
                        'activity_calendar_id' => $calendarId,
                    ])->first();

                    if ($existingParticipation) {
                        \Filament\Notifications\Notification::make()
                            ->title('Participación ya registrada')
                            ->body('Este beneficiario ya está registrado para esta actividad en la fecha seleccionada.')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Crear registro en la tabla pivote
                    \App\Models\BeneficiaryActivityCalendar::create([
                        'beneficiary_registry_id' => $beneficiary->id,
                        'activity_id' => $this->activity_id,
                        'activity_calendar_id' => $calendarId,
                        'signature' => $data['signature'] ?? null,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Beneficiario registrado correctamente')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('addMassive')
                ->label('Registrar beneficiarios masivos')
                ->icon('heroicon-o-users')
                ->modalWidth('10xl')
                ->form([
                    Repeater::make('beneficiaries')
                        ->label('Beneficiarios')
                        ->schema([
                            // Campo de búsqueda por identificador
                            TextInput::make('search_identifier')
                                ->label('Buscar por identificador')
                                ->placeholder('Ej: PEREZ2025M')
                                ->helperText('Ingresa el identificador para buscar un beneficiario existente')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    if (!empty($state)) {
                                        $beneficiary = \App\Models\BeneficiaryRegistry::where('identifier', $state)->first();
                                        if ($beneficiary) {
                                            $set('last_name', $beneficiary->last_name);
                                            $set('mother_last_name', $beneficiary->mother_last_name);
                                            $set('first_names', $beneficiary->first_names);
                                            $set('birth_year', $beneficiary->birth_year);
                                            $set('gender', $beneficiary->gender);
                                            $set('phone', $beneficiary->phone);
                                            $set('address_backup', $beneficiary->address_backup);
                                            $set('location_id', $beneficiary->location_id);
                                            $set('data_collector_id', $beneficiary->data_collector_id);

                                            // Mostrar notificación de beneficiario encontrado
                                            \Filament\Notifications\Notification::make()
                                                ->title('Beneficiario encontrado')
                                                ->body("Identificador: {$beneficiary->identifier}. Los datos han sido pre-llenados. Solo necesitas capturar la nueva firma.")
                                                ->success()
                                                ->send();
                                        } else {
                                            // Limpiar campos si no se encuentra
                                            $set('last_name', '');
                                            $set('mother_last_name', '');
                                            $set('first_names', '');
                                            $set('birth_year', '');
                                            $set('gender', '');
                                            $set('phone', '');
                                            $set('address_backup', '');
                                            $set('location_id', '');
                                            $set('data_collector_id', '');

                                            \Filament\Notifications\Notification::make()
                                                ->title('Beneficiario no encontrado')
                                                ->body('No se encontró un beneficiario con ese identificador. Puedes proceder con el registro normal.')
                                                ->warning()
                                                ->send();
                                        }
                                    }
                                })
                                ->columnSpanFull(),

                            // Primera fila
                            Forms\Components\Grid::make(8)
                                ->schema([
                                    TextInput::make('last_name')->label('Apellido Paterno')->required()->columnSpan(2),
                                    TextInput::make('mother_last_name')->label('Apellido Materno')->required()->columnSpan(2),
                                    TextInput::make('first_names')->label('Nombres')->required()->columnSpan(4),
                                ]),
                            // Segunda fila
                            Forms\Components\Grid::make(8)
                                ->schema([
                                    TextInput::make('birth_year')->label('Año de Nacimiento')->numeric()->minValue(1900)->maxValue(date('Y'))->columnSpan(2),
                                    Select::make('gender')->label('Género')->options([
                                        'Male' => 'Masculino',
                                        'Female' => 'Femenino',
                                    ])->required()->columnSpan(2),
                                    TextInput::make('phone')->label('Teléfono')->tel()->columnSpan(2),
                                    Textarea::make('address_backup')->label('Dirección de respaldo')->rows(1)->columnSpan(2),
                                    Select::make('location_id')->label('Ubicación')
                                        ->options(Location::pluck('name', 'id')->toArray())
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->preload()->columnSpan(3),
                                    Select::make('data_collector_id')->label('Recolector de datos')
                                        ->options(DataCollector::pluck('name', 'id')->toArray())
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->preload()->columnSpan(3),
                                ]),
                            // Tercera fila: solo la firma
                            SignaturePad::make('signature')
                                ->label('Firma del beneficiario')
                                ->required()
                                ->columnSpanFull(),
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

                    $registeredCount = 0;
                    $skippedCount = 0;

                    foreach ($data['beneficiaries'] as $beneficiary) {
                        $identifier = \App\Models\BeneficiaryRegistry::generarIdentificador(
                            $beneficiary['first_names'] ?? '',
                            $beneficiary['last_name'] ?? '',
                            $beneficiary['mother_last_name'] ?? '',
                            $beneficiary['birth_year'] ?? '',
                            $beneficiary['gender'] ?? ''
                        );

                        // Buscar o crear beneficiario
                        $beneficiaryModel = \App\Models\BeneficiaryRegistry::firstOrCreate(
                            ['identifier' => $identifier],
                            array_merge($beneficiary, [
                                'identifier' => $identifier,
                            ])
                        );

                        // Verificar si ya existe una participación para esta actividad y calendario
                        $existingParticipation = \App\Models\BeneficiaryActivityCalendar::where([
                            'beneficiary_registry_id' => $beneficiaryModel->id,
                            'activity_id' => $this->activity_id,
                            'activity_calendar_id' => $calendarId,
                        ])->first();

                        if ($existingParticipation) {
                            $skippedCount++;
                            continue; // Saltar este beneficiario
                        }

                        // Crear registro en la tabla pivote
                        \App\Models\BeneficiaryActivityCalendar::create([
                            'beneficiary_registry_id' => $beneficiaryModel->id,
                            'activity_id' => $this->activity_id,
                            'activity_calendar_id' => $calendarId,
                            'signature' => $beneficiary['signature'] ?? null,
                        ]);

                        $registeredCount++;
                    }

                    $message = "Se registraron {$registeredCount} beneficiarios correctamente.";
                    if ($skippedCount > 0) {
                        $message .= " Se omitieron {$skippedCount} beneficiarios que ya estaban registrados.";
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Registro completado')
                        ->body($message)
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
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
                    TextInput::make('identifier')->label('Identificador')->disabled(),
                    SignaturePad::make('signature')
                        ->label('Firma del beneficiario')
                        ->required()
                        ->columnSpanFull(),
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
                ->mutateRecordDataUsing(function ($data, $record) {
                    // Cargar los datos desde la relación beneficiaryRegistry
                    if ($record && $record->beneficiaryRegistry) {
                        $data['last_name'] = $record->beneficiaryRegistry->last_name;
                        $data['mother_last_name'] = $record->beneficiaryRegistry->mother_last_name;
                        $data['first_names'] = $record->beneficiaryRegistry->first_names;
                        $data['birth_year'] = $record->beneficiaryRegistry->birth_year;
                        $data['gender'] = $record->beneficiaryRegistry->gender;
                        $data['phone'] = $record->beneficiaryRegistry->phone;
                        $data['identifier'] = $record->beneficiaryRegistry->identifier;
                        $data['signature'] = $record->signature; // La firma está en el pivote
                        $data['address_backup'] = $record->beneficiaryRegistry->address_backup;
                        $data['location_id'] = $record->beneficiaryRegistry->location_id;
                        $data['data_collector_id'] = $record->beneficiaryRegistry->data_collector_id;
                    }
                    return $data;
                })
                ->action(function ($record, $data) {
                    // Guardar los datos en beneficiaryRegistry
                    if ($record && $record->beneficiaryRegistry) {
                        $record->beneficiaryRegistry->update([
                            'last_name' => $data['last_name'],
                            'mother_last_name' => $data['mother_last_name'],
                            'first_names' => $data['first_names'],
                            'birth_year' => $data['birth_year'],
                            'gender' => $data['gender'],
                            'phone' => $data['phone'],
                            'address_backup' => $data['address_backup'],
                            'location_id' => $data['location_id'],
                            'data_collector_id' => $data['data_collector_id'],
                        ]);
                    }
                    // Guardar la firma en el pivote
                    $record->signature = $data['signature'];
                    $record->save();
                }),
            Tables\Actions\DeleteAction::make(),
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
