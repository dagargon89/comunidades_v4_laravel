# Relación de Beneficiarios con Calendarización de Actividades

## Objetivo

El objetivo era que la tabla de beneficiarios (`beneficiary_registries`) estuviera relacionada directamente con la tabla de calendarización de actividades (`activity_calendars`). Esto permitiría que, aunque existan varias actividades con el mismo nombre, cada beneficiario quede asociado a una calendarización específica (es decir, a una fecha y hora concreta de la actividad).

## Razón

-   Una actividad puede repetirse en diferentes fechas (varias calendarizaciones).
-   Se necesita distinguir a los beneficiarios según la fecha/hora en la que participaron en la actividad.
-   Al registrar un beneficiario, se debe seleccionar la calendarización específica, no solo la actividad.

## Pasos intentados

1. **Modificar la vista de registro de beneficiarios:**

    - Cambiar el selector de actividad por un selector de calendarización (`activity_calendar_id`).
    - Mostrar un infolist con los datos de la calendarización seleccionada (fecha, hora, dirección, etc).
    - Al registrar un beneficiario, asociarlo a la calendarización seleccionada.

2. **Actualizar la lógica de filtrado y guardado:**

    - Filtrar la tabla de beneficiarios por `activity_calendar_id`.
    - Guardar el campo `activity_calendar_id` en el registro del beneficiario.

3. **Problema encontrado:**
    - No existía la columna `activity_calendar_id` en la tabla `beneficiary_registries`, lo que causó errores SQL.
    - Se requiere una migración para agregar este campo y definir la relación correctamente.

## Recomendación

-   Antes de implementar la lógica en Filament, crear una migración para agregar la columna `activity_calendar_id` (entero, nullable, foreign key) a la tabla de beneficiarios.
-   Luego, adaptar los formularios y la lógica de la página para trabajar con este campo.

---

**Este documento resume el objetivo y los pasos realizados para relacionar beneficiarios con la calendarización de actividades en el sistema.**
