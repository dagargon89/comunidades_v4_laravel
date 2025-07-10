<?php

namespace App\Filament\Pages;

use App\Models\Activity;
use App\Models\ActivityFile;
use App\Models\ActivityCalendar;
use App\Models\Responsible;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ActivityFileManager extends Page implements Tables\Contracts\HasTable
{
    use HasPageShield;
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Gestión de Archivos';
    protected static ?string $title = 'Gestión de Archivos de Actividades';
    protected static ?string $slug = 'activity-file-manager';
    protected static string $view = 'filament.pages.activity-file-manager';

    public ?int $activity_id = null;
    public ?string $activity_calendar_date = null;

    public function mount(): void
    {
        $this->activity_id = Activity::query()->min('id') ?? null;
        $firstCalendar = null;
        if ($this->activity_id) {
            $firstCalendar = ActivityCalendar::where('activity_id', $this->activity_id)
                ->orderBy('start_date')
                ->first();
        }
        $this->activity_calendar_date = $firstCalendar ? $firstCalendar->start_date : null;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información de actividad')
                ->description('Selecciona la actividad y la fecha para gestionar archivos')
                ->schema([
                    Forms\Components\Select::make('activity_id')
                        ->label('Actividad')
                        ->options(Activity::pluck('description', 'id')->toArray())
                        ->searchable()
                        ->required()
                        ->live()
                        ->default(Activity::query()->min('id')),
                    Forms\Components\Select::make('activity_calendar_date')
                        ->label('Fecha de la actividad')
                        ->options(function () {
                            $activityId = $this->activity_id;
                            if (!$activityId) {
                                return [];
                            }
                            return ActivityCalendar::where('activity_id', $activityId)
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
                            $firstCalendar = ActivityCalendar::where('activity_id', $activityId)
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
        $firstCalendar = null;
        if ($this->activity_id) {
            $firstCalendar = ActivityCalendar::where('activity_id', $this->activity_id)
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

    public function getActivityInfo(): ?array
    {
        if (!$this->activity_id || !$this->activity_calendar_date) {
            return null;
        }
        $activity = Activity::find($this->activity_id);
        $calendar = ActivityCalendar::where('activity_id', $this->activity_id)
            ->where('start_date', $this->activity_calendar_date)
            ->first();
        if (!$activity || !$calendar) {
            return null;
        }
        $responsable = Responsible::find($activity->responsible_id);
        return [
            'actividad' => $activity->description,
            'fecha' => $calendar->start_date,
            'responsable' => $responsable?->name ?? '',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                if (!$this->activity_id || !$this->activity_calendar_date) {
                    return ActivityFile::query()->whereRaw('1=0');
                }
                $calendar = ActivityCalendar::where('activity_id', $this->activity_id)
                    ->where('start_date', $this->activity_calendar_date)
                    ->first();
                if (!$calendar) {
                    return ActivityFile::query()->whereRaw('1=0');
                }
                return ActivityFile::query()
                    ->where('activity_id', $this->activity_id)
                    ->where('activity_calendar_id', $calendar->id);
            })
            ->columns([
                Tables\Columns\TextColumn::make('file_name')->label('Nombre del archivo')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de subida')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->actions([
                TableAction::make('download')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(ActivityFile $record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),
                DeleteAction::make(),
            ])
            ->headerActions([
                TableAction::make('add_files')
                    ->label('Agregar archivos')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Repeater::make('files')
                            ->label('Archivos')
                            ->schema([
                                FileUpload::make('file_upload')
                                    ->label('Archivo')
                                    ->required()
                                    ->directory('activity-files')
                                    ->preserveFilenames(),
                            ])
                            ->addActionLabel('Agregar otro archivo')
                            ->minItems(1),
                    ])
                    ->action(function (array $data) {
                        $activityId = $this->activity_id;
                        $calendar = ActivityCalendar::where('activity_id', $activityId)
                            ->where('start_date', $this->activity_calendar_date)
                            ->first();
                        if (!$activityId || !$calendar) {
                            Notification::make()->title('Selecciona una actividad y fecha')->danger()->send();
                            return;
                        }
                        foreach ($data['files'] as $fileData) {
                            if (!empty($fileData['file_upload'])) {
                                $uploaded = $fileData['file_upload'];
                                $path = $uploaded->store('activity-files', 'public');
                                ActivityFile::create([
                                    'file_name' => $uploaded->getClientOriginalName(),
                                    'file_path' => $path,
                                    'activity_id' => $activityId,
                                    'activity_calendar_id' => $calendar->id,
                                ]);
                            }
                        }
                        Notification::make()->title('Archivos subidos correctamente')->success()->send();
                        $this->resetTable();
                    })
                    ->color('success'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
