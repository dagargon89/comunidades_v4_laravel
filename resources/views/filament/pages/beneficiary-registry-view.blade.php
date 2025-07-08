<x-filament-panels::page>
    {{ $this->form }}

    @php $info = $this->getActivityInfo(); @endphp
    @if($info)
        <div style="margin-bottom: 1.5rem;">
            <x-filament::card>
                <dl style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <div>
                        <dt><strong>Actividad</strong></dt>
                        <dd>{{ $info['actividad'] }}</dd>
                    </div>
                    <div>
                        <dt><strong>Fecha</strong></dt>
                        <dd>{{ $info['fecha'] }}</dd>
                    </div>
                    <div>
                        <dt><strong>Hora de inicio</strong></dt>
                        <dd>{{ $info['hora_inicio'] }}</dd>
                    </div>
                    <div>
                        <dt><strong>Hora de fin</strong></dt>
                        <dd>{{ $info['hora_fin'] }}</dd>
                    </div>
                    <div>
                        <dt><strong>Responsable</strong></dt>
                        <dd>{{ $info['responsable'] }}</dd>
                    </div>
                </dl>
            </x-filament::card>
        </div>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
