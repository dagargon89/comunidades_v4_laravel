# Implementación de Firma Digital en Formularios Filament (Laravel)

## Resumen

Esta documentación describe cómo se integró un campo de firma digital (Signature Pad) en los formularios de registro de beneficiarios (único y masivo) usando Filament 3, Alpine.js y el paquete JS szimek/signature_pad. La solución es compatible con Livewire y permite capturar la firma como imagen (base64) y almacenarla en la base de datos.

---

## 1. Requisitos

-   Laravel 10+ (en este caso, Laravel 12)
-   Filament 3.x
-   Alpine.js (incluido por defecto en Filament)
-   [szimek/signature_pad](https://github.com/szimek/signature_pad) vía CDN

---

## 2. Estructura de la Solución

### a) Campo de firma en el schema del formulario

Se utiliza un campo custom de Filament:

```php
use Filament\Forms\Components\View as ViewField;

ViewField::make('signature')
    ->view('filament.components.signature-pad')
    ->columnSpanFull(),
```

Esto se agrega tanto en el formulario de registro único como en el masivo (dentro del repeater).

### b) Componente Blade para el Signature Pad

Archivo: `resources/views/filament/components/signature-pad.blade.php`

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

-   El valor de la firma se sincroniza con el campo `signature` del formulario usando `$wire.entangle`.
-   El botón "Limpiar firma" borra el canvas y el valor.
-   El canvas es responsivo y se puede personalizar el tamaño.

---

## 3. Guardado de la firma

-   El valor de la firma se guarda como base64 en el campo `signature` de la base de datos.
-   No es necesario un campo oculto ni input extra.
-   En el registro masivo, cada beneficiario tiene su propio pad de firma.

---

## 4. Ejemplo de uso en el schema del formulario masivo

```php
Repeater::make('beneficiaries')
    ->label('Beneficiarios')
    ->schema([
        // ...otros campos...
        ViewField::make('signature')
            ->view('filament.components.signature-pad')
            ->columnSpanFull(),
        // ...otros campos...
    ])
    ->minItems(1)
    ->addActionLabel('Agregar otro beneficiario')
    ->columns(1)
```

---

## 5. Consideraciones

-   El campo de firma debe estar **solo** en el schema del formulario, no fuera de él.
-   Si tienes un bloque de firma fuera del formulario, ocúltalo con `display:none`.
-   El campo en la base de datos debe ser `text` o `longText`.
-   Puedes mostrar la firma guardada usando `<img src="{{ $registro->signature }}" />`.

---

## 6. Recursos

-   [szimek/signature_pad](https://github.com/szimek/signature_pad)
-   [Filament Custom Fields](https://filamentphp.com/docs/3.x/forms/fields#custom-fields)

---

**Implementación realizada por IA con Filament 3, Laravel 12 y Alpine.js.**
