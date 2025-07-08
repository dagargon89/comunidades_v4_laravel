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
