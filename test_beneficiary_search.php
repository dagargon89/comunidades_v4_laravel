<?php

require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BeneficiaryRegistry;
use App\Models\BeneficiaryActivityCalendar;
use App\Models\Activity;
use App\Models\ActivityCalendar;

// Simular la búsqueda de un beneficiario existente
echo "=== PRUEBA DE BÚSQUEDA DE BENEFICIARIOS ===\n\n";

// 1. Crear un beneficiario de prueba si no existe
$testIdentifier = 'PEREZ2025M';
$existingBeneficiary = BeneficiaryRegistry::where('identifier', $testIdentifier)->first();

if (!$existingBeneficiary) {
    echo "Creando beneficiario de prueba...\n";
    $existingBeneficiary = BeneficiaryRegistry::create([
        'identifier' => $testIdentifier,
        'last_name' => 'Pérez',
        'mother_last_name' => 'García',
        'first_names' => 'Juan Carlos',
        'birth_year' => 1990,
        'gender' => 'Male',
        'phone' => '555-1234',
        'address_backup' => 'Calle Principal 123',
        'location_id' => 1,
        'data_collector_id' => 1,
    ]);
    echo "Beneficiario creado con ID: {$existingBeneficiary->id}\n";
} else {
    echo "Beneficiario existente encontrado con ID: {$existingBeneficiary->id}\n";
}

// 2. Probar la búsqueda por identificador
echo "\n=== PRUEBA DE BÚSQUEDA ===\n";
$foundBeneficiary = BeneficiaryRegistry::where('identifier', $testIdentifier)->first();

if ($foundBeneficiary) {
    echo "✅ Búsqueda exitosa:\n";
    echo "   Identificador: {$foundBeneficiary->identifier}\n";
    echo "   Nombre: {$foundBeneficiary->first_names} {$foundBeneficiary->last_name} {$foundBeneficiary->mother_last_name}\n";
    echo "   Año de nacimiento: {$foundBeneficiary->birth_year}\n";
    echo "   Género: {$foundBeneficiary->gender}\n";
    echo "   Teléfono: {$foundBeneficiary->phone}\n";
} else {
    echo "❌ Búsqueda fallida\n";
}

// 3. Probar la verificación de participación existente
echo "\n=== PRUEBA DE VERIFICACIÓN DE PARTICIPACIÓN ===\n";

// Obtener una actividad y calendario de prueba
$activity = Activity::first();
$calendar = ActivityCalendar::where('activity_id', $activity->id)->first();

if ($activity && $calendar) {
    echo "Actividad: {$activity->name}\n";
    echo "Fecha: {$calendar->start_date}\n";

    // Verificar si ya existe participación
    $existingParticipation = BeneficiaryActivityCalendar::where([
        'beneficiary_registry_id' => $foundBeneficiary->id,
        'activity_id' => $activity->id,
        'activity_calendar_id' => $calendar->id,
    ])->first();

    if ($existingParticipation) {
        echo "⚠️  El beneficiario ya participa en esta actividad\n";
    } else {
        echo "✅ El beneficiario puede registrarse en esta actividad\n";

        // Crear participación de prueba
        BeneficiaryActivityCalendar::create([
            'beneficiary_registry_id' => $foundBeneficiary->id,
            'activity_id' => $activity->id,
            'activity_calendar_id' => $calendar->id,
            'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
        ]);
        echo "✅ Participación creada exitosamente\n";
    }
} else {
    echo "❌ No se encontraron actividades o calendarios para la prueba\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
