<?php

namespace App\Filament\Pages;

use App\Models\Activity;
use App\Models\BeneficiaryRegistry;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class BeneficiaryRegistryView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Registro de Beneficiarios';
    protected static ?string $pluralLabel = 'Registro de Beneficiarios';
    protected static ?string $label = 'Registro de Beneficiario';
    protected static ?string $navigationGroup = 'Ejecución y Seguimiento';
    protected static ?int $navigationSort = 8;

    protected static string $view = 'filament.pages.beneficiary-registry-view';

    public ?int $selectedActivityId = null;
    public $beneficiaryForm = [];

    public function mount()
    {
        // Inicializar la página
    }

    public function saveBeneficiary()
    {
        if (!$this->selectedActivityId) {
            Notification::make()
                ->title('Error')
                ->body('Debes seleccionar una actividad primero')
                ->danger()
                ->send();
            return;
        }

        $this->validate([
            'beneficiaryForm.last_name' => 'required|string|max:255',
            'beneficiaryForm.mother_last_name' => 'required|string|max:255',
            'beneficiaryForm.first_names' => 'required|string|max:255',
            'beneficiaryForm.birth_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'beneficiaryForm.gender' => 'required|string|in:Masculino,Femenino,Otro',
            'beneficiaryForm.phone' => 'nullable|string|max:20',
            'beneficiaryForm.address_backup' => 'nullable|string',
            'beneficiaryForm.signature' => 'nullable|string',
        ]);

        try {
            BeneficiaryRegistry::create([
                'activity_id' => $this->selectedActivityId,
                'last_name' => $this->beneficiaryForm['last_name'],
                'mother_last_name' => $this->beneficiaryForm['mother_last_name'],
                'first_names' => $this->beneficiaryForm['first_names'],
                'birth_year' => $this->beneficiaryForm['birth_year'],
                'gender' => $this->beneficiaryForm['gender'],
                'phone' => $this->beneficiaryForm['phone'],
                'address_backup' => $this->beneficiaryForm['address_backup'],
                'signature' => $this->beneficiaryForm['signature'],
            ]);

            // Limpiar el formulario
            $this->beneficiaryForm = [];

            Notification::make()
                ->title('Beneficiario guardado')
                ->body('El beneficiario se ha registrado correctamente')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('No se pudo guardar el beneficiario: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getBeneficiariesTable(): Table
    {
        return Table::make()
            ->query(
                BeneficiaryRegistry::query()
                    ->where('activity_id', $this->selectedActivityId)
                    ->with(['activity', 'location', 'dataCollector'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellido Paterno')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mother_last_name')
                    ->label('Apellido Materno')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_names')
                    ->label('Nombres')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birth_year')
                    ->label('Año Nacimiento')
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Género')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Masculino' => 'primary',
                        'Femenino' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Género')
                    ->options([
                        'Masculino' => 'Masculino',
                        'Femenino' => 'Femenino',
                        'Otro' => 'Otro',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        TextInput::make('last_name')
                            ->label('Apellido Paterno')
                            ->required(),
                        TextInput::make('mother_last_name')
                            ->label('Apellido Materno')
                            ->required(),
                        TextInput::make('first_names')
                            ->label('Nombres')
                            ->required(),
                        TextInput::make('birth_year')
                            ->label('Año de Nacimiento')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),
                        Select::make('gender')
                            ->label('Género')
                            ->options([
                                'Masculino' => 'Masculino',
                                'Femenino' => 'Femenino',
                                'Otro' => 'Otro',
                            ])
                            ->required(),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel(),
                        Textarea::make('address_backup')
                            ->label('Dirección')
                            ->rows(3),
                        Textarea::make('signature')
                            ->label('Firma')
                            ->rows(2),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    public function getSelectedActivity()
    {
        if (!$this->selectedActivityId) {
            return null;
        }

        return Activity::with(['specificObjective', 'responsible', 'goal'])->find($this->selectedActivityId);
    }

    public function getBeneficiariesCount()
    {
        if (!$this->selectedActivityId) {
            return 0;
        }

        return BeneficiaryRegistry::where('activity_id', $this->selectedActivityId)->count();
    }

    public function getActivities()
    {
        return Activity::pluck('description', 'id');
    }

    public function getBeneficiaries()
    {
        if (!$this->selectedActivityId) {
            return collect();
        }

        return BeneficiaryRegistry::where('activity_id', $this->selectedActivityId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function deleteBeneficiary($beneficiaryId)
    {
        try {
            $beneficiary = BeneficiaryRegistry::findOrFail($beneficiaryId);

            // Verificar que pertenece a la actividad seleccionada
            if ($beneficiary->activity_id != $this->selectedActivityId) {
                Notification::make()
                    ->title('Error')
                    ->body('No tienes permisos para eliminar este beneficiario')
                    ->danger()
                    ->send();
                return;
            }

            $beneficiary->delete();

            Notification::make()
                ->title('Beneficiario eliminado')
                ->body('El beneficiario se ha eliminado correctamente')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('No se pudo eliminar el beneficiario: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
