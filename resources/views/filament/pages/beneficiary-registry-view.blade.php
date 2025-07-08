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
