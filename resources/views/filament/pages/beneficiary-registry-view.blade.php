<x-filament-panels::page>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight">Registro de Beneficiarios</h1>
    </x-slot>

    {{-- Se elimina el bloque de firma digital, ahora gestionado por el campo custom en el formulario Filament --}}

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
