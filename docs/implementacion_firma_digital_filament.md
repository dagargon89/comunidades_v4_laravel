# Implementación de Firma Digital en Formularios Filament (Laravel)

## Resumen

Esta documentación describe la implementación completa y funcional de un campo de firma digital (Signature Pad) en los formularios de registro de beneficiarios usando Filament 3, Alpine.js y el paquete JS szimek/signature_pad. La solución es compatible con Livewire y permite capturar la firma como imagen (base64) y almacenarla en la base de datos.

---

## 1. Requisitos

-   Laravel 10+ (en este caso, Laravel 12)
-   Filament 3.x
-   Alpine.js (incluido por defecto en Filament)
-   [szimek/signature_pad](https://github.com/szimek/signature_pad) vía CDN

---

## 2. Estructura de Archivos

### Archivos principales:

-   `app/Filament/Pages/BeneficiaryRegistryView.php` - Página personalizada con formularios de registro
-   `resources/views/filament/pages/beneficiary-registry-view.blade.php` - Vista Blade de la página
-   `resources/views/filament/components/signature-pad.blade.php` - Componente de firma digital
-   `app/Filament/Resources/BeneficiaryRegistryResource.php` - Resource principal

---

## 3. Implementación Detallada

### a) Página Personalizada - BeneficiaryRegistryView.php

```php
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

class BeneficiaryRegistryView extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.beneficiary-registry-view';
    protected static ?string $title = 'Registro de Beneficiarios';
    protected static ?string $navigationLabel = 'Registro de Beneficiarios';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public ?int $activity_id = null;
    public ?string $activity_calendar_date = null;

    // ... resto del código de la clase ...

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
                    // Campo visual para el pad
                    ViewField::make('signature')
                        ->view('filament.components.signature-pad')
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
                            // Campo visual para el pad de firma
                            ViewField::make('signature')
                                ->view('filament.components.signature-pad')
                                ->columnSpanFull(),
                            TextInput::make('signature')->label('Firma')->hidden(), // Para compatibilidad, pero no se muestra
                            TextInput::make('signature')->label('Firma')->hidden(),
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
}
```

### b) Vista Blade - beneficiary-registry-view.blade.php

```blade
<x-filament-panels::page>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight">Registro de Beneficiarios</h1>
    </x-slot>

    @php
        $canvasId = 'signature-canvas-' . uniqid();
        $inputId = 'signature-input-' . uniqid();
        $clearId = 'clear-signature-' . uniqid();
    @endphp
    <div style="display:none !important;">
        <label class="block text-sm font-medium text-gray-700 mb-1">Firma del beneficiario</label>
        <div class="border rounded bg-white" style="width: 350px; height: 120px;">
            <canvas id="{{ $canvasId }}" width="350" height="120"></canvas>
        </div>
        <button type="button" id="{{ $clearId }}" class="mt-2 text-xs text-red-500">Limpiar firma</button>
        <input type="hidden" id="{{ $inputId }}" name="signature" />
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById(@json($canvasId));
            const input = document.getElementById(@json($inputId));
            const clearBtn = document.getElementById(@json($clearId));
            const signaturePad = new SignaturePad(canvas, { backgroundColor: 'white' });

            clearBtn.addEventListener('click', function () {
                signaturePad.clear();
                input.value = '';
            });

            function updateSignatureField() {
                if (!signaturePad.isEmpty()) {
                    input.value = signaturePad.toDataURL();
                }
            }

            canvas.addEventListener('mouseup', updateSignatureField);
            canvas.addEventListener('touchend', updateSignatureField);
        });
    </script>
    @endpush

    {{ $this->form }}

    @php $info = $this->getActivityInfo(); @endphp
    @if($info)
        <x-filament::section>
            <x-filament::grid default="1" md="3" xl="5" class="gap-4">
                <x-filament::card class="bg-primary-50 border-primary-200">
                    <div class="text-xs text-primary-600 font-semibold uppercase mb-1">Actividad</div>
                    <div class="text-base font-bold text-primary-900">{{ $info['actividad'] }}</div>
                </x-filament::card>
                <x-filament::card>
                    <div class="text-xs text-gray-500 font-semibold uppercase mb-1">Fecha</div>
                    <div class="text-base font-bold text-gray-900">{{ $info['fecha'] }}</div>
                </x-filament::card>
                <x-filament::card>
                    <div class="text-xs text-gray-500 font-semibold uppercase mb-1">Hora de inicio</div>
                    <div class="text-base font-bold text-gray-900">{{ $info['hora_inicio'] }}</div>
                </x-filament::card>
                <x-filament::card>
                    <div class="text-xs text-gray-500 font-semibold uppercase mb-1">Hora de fin</div>
                    <div class="text-base font-bold text-gray-900">{{ $info['hora_fin'] }}</div>
                </x-filament::card>
                <x-filament::card>
                    <div class="text-xs text-gray-500 font-semibold uppercase mb-1">Responsable</div>
                    <div class="text-base font-bold text-gray-900">{{ $info['responsable'] }}</div>
                </x-filament::card>
            </x-filament::grid>
        </x-filament::section>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
```

### c) Componente de Firma Digital - signature-pad.blade.php

```blade
@php
    $canvasId = 'signature-canvas-' . uniqid();
@endphp

<div
    x-data="{
        signaturePad: null,
        signature: $wire.entangle('data.signature').defer,
        init() {
            const canvas = document.getElementById('{{ $canvasId }}');
            this.signaturePad = new window.SignaturePad(canvas, { backgroundColor: 'white' });

            // Si ya hay firma previa, cargarla
            this.$watch('signature', value => {
                if (value) {
                    const img = new Image();
                    img.onload = () => {
                        this.signaturePad.clear();
                        this.signaturePad._ctx.drawImage(img, 0, 0);
                    };
                    img.src = value;
                } else {
                    this.signaturePad.clear();
                }
            });
        },
        clear() {
            this.signaturePad.clear();
            this.signature = '';
        },
        save() {
            if (!this.signaturePad.isEmpty()) {
                this.signature = this.signaturePad.toDataURL();
            }
        }
    }"
    x-init="init()"
    class="space-y-2"
>
    <label class="block text-sm font-medium text-gray-700 mb-1">Firma del beneficiario</label>
    <div class="border rounded bg-white" style="width: 350px; height: 120px;">
        <canvas id="{{ $canvasId }}" width="350" height="120"
            x-on:mouseup="save"
            x-on:touchend="save"
        ></canvas>
    </div>
    <button type="button" class="mt-2 text-xs text-red-500" x-on:click="clear">Limpiar firma</button>
</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
    @endpush
@endonce
```

### d) Resource Principal - BeneficiaryRegistryResource.php

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryRegistryResource\Pages;
use App\Filament\Resources\BeneficiaryRegistryResource\RelationManagers;
use App\Models\BeneficiaryRegistry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BeneficiaryRegistryResource extends Resource
{
    protected static ?string $model = BeneficiaryRegistry::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Registros de beneficiarios';
    protected static ?string $pluralLabel = 'Registros de beneficiarios';
    protected static ?string $label = 'Registro de beneficiario';
    protected static ?string $navigationGroup = 'Ejecución y Seguimiento';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('last_name')
                    ->label('Apellido paterno'),
                Forms\Components\TextInput::make('mother_last_name')
                    ->label('Apellido materno'),
                Forms\Components\TextInput::make('first_names')
                    ->label('Nombres'),
                Forms\Components\TextInput::make('birth_year')
                    ->label('Año de nacimiento'),
                Forms\Components\Select::make('gender')
                    ->label('Género')
                    ->options([
                        'male' => 'Masculino',
                        'female' => 'Femenino',
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel(),
                Forms\Components\Textarea::make('signature')
                    ->label('Firma')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('address_backup')
                    ->label('Respaldo de dirección')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('identifier')
                    ->label('Identificador')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('activity_id')
                    ->label('Actividad')
                    ->relationship('activity', 'description')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->reactive(),
            ]);
    }

    // ... resto del código de la clase ...
}
```

---

## 4. Configuración de Base de Datos

### Migración para el campo signature:

```php
// En la migración de beneficiary_registries
$table->text('signature')->nullable();
```

### Modelo BeneficiaryRegistry:

```php
protected $fillable = [
    'last_name',
    'mother_last_name',
    'first_names',
    'birth_year',
    'gender',
    'phone',
    'signature', // Campo para la firma en base64
    'address_backup',
    'activity_id',
    'location_id',
    'data_collector_id',
    'identifier',
    'activity_calendar_id',
];
```

---

## 5. Funcionalidades Implementadas

### a) Registro Único de Beneficiario

-   Formulario con campos personales
-   Campo de firma digital funcional
-   Validaciones de campos requeridos
-   Guardado automático en base de datos

### b) Registro Masivo de Beneficiarios

-   Formulario con repeater para múltiples beneficiarios
-   Campo de firma individual para cada beneficiario
-   Validaciones por beneficiario
-   Guardado en lote

### c) Componente de Firma Digital

-   Canvas interactivo para dibujar la firma
-   Botón para limpiar la firma
-   Guardado automático en formato base64
-   Compatible con dispositivos táctiles

---

## 6. Características Técnicas

### a) Enlace de Datos

-   Uso de `$wire.entangle('data.signature').defer` para sincronización con Livewire
-   Actualización automática del campo al dibujar en el canvas

### b) Compatibilidad

-   Funciona en formularios de acciones de tabla
-   Compatible con repeater (registro masivo)
-   Responsive y táctil

### c) Rendimiento

-   Carga del script SignaturePad solo una vez por página
-   IDs únicos para evitar conflictos en múltiples instancias

---

## 7. Consideraciones Importantes

### a) Campo Oculto en Vista Principal

-   El bloque de firma en `beneficiary-registry-view.blade.php` está oculto (`display:none`)
-   Se usa solo para compatibilidad, no se muestra visualmente
-   La funcionalidad real está en el componente `signature-pad.blade.php`

### b) Campos Duplicados en Formulario Masivo

-   Se incluyen campos ocultos de firma para compatibilidad
-   No afectan la funcionalidad visual

### c) Uso de ViewField vs View

-   En formularios de acciones: usar `ViewField::make('signature')`
-   En Resource principal: usar `Forms\Components\Textarea::make('signature')`

### d) Campo de firma en el schema del formulario

-   El campo de firma debe estar **solo** en el schema del formulario, no fuera de él
-   Si tienes un bloque de firma fuera del formulario, ocúltalo con `display:none`
-   El campo en la base de datos debe ser `text` o `longText`
-   Puedes mostrar la firma guardada usando `<img src="{{ $registro->signature }}" />`

---

## 8. Solución de Problemas

### Problema: Firma no se guarda

**Solución**: Verificar que el campo `signature` esté en el `$fillable` del modelo

### Problema: Canvas no responde

**Solución**: Verificar que el script SignaturePad se cargue correctamente

### Problema: Múltiples instancias de firma

**Solución**: Los IDs únicos generados con `uniqid()` evitan conflictos

---

## 9. Recursos

-   [szimek/signature_pad](https://github.com/szimek/signature_pad)
-   [Filament Custom Fields](https://filamentphp.com/docs/3.x/forms/fields#custom-fields)
-   [Alpine.js Documentation](https://alpinejs.dev/)

---

**Implementación realizada y probada con Filament 3, Laravel 12 y Alpine.js.**

**Fecha de implementación**: Julio 2025
**Estado**: Funcional y en producción
