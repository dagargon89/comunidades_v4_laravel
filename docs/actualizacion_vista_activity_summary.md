# Actualización de la vista `vw_activity_summary`

## Motivo

Se actualizó la estructura de la tabla `beneficiary_registries` y ahora la relación entre beneficiarios y actividades se realiza a través de la tabla pivote `beneficiary_activity_calendar`. Por ello, es necesario modificar la vista `vw_activity_summary` para reflejar correctamente esta relación.

---

## Pasos para actualizar la vista

### 1. Eliminar la vista anterior (si existe)

```sql
DROP VIEW IF EXISTS vw_activity_summary;
```

### 2. Crear la nueva vista con la relación correcta

```sql
CREATE VIEW vw_activity_summary AS
SELECT
    a.id AS activity_id,
    a.description AS activity_name,
    so.id AS specific_objective_id,
    so.description AS objective_description,
    p.id AS project_id,
    p.name AS project_name,
    ax.id AS axis_id,
    ax.name AS axis_name,
    r.id AS responsible_id,
    r.name AS responsible_name,
    org.name AS organization,
    ac.start_date AS start_date,
    ac.end_date AS end_date,
    pol.name AS polygon_name,
    loc.name AS location_name,
    dc.name AS data_collector_name,
    (SELECT COUNT(*) FROM planned_metrics pm_prod WHERE pm_prod.activity_id = a.id AND pm_prod.is_product = 1) AS products_count,
    (SELECT COUNT(*) FROM planned_metrics pm_pop WHERE pm_pop.activity_id = a.id AND pm_pop.is_population = 1) AS population_count,
    (SELECT COUNT(*) FROM beneficiary_activity_calendar bac WHERE bac.activity_id = a.id) AS beneficiaries_count
FROM
    activities a
JOIN responsibles r ON a.responsible_id = r.id
JOIN specific_objectives so ON a.specific_objective_id = so.id
JOIN projects p ON so.project_id = p.id
JOIN goals g ON a.goal_id = g.id
JOIN organizations org ON g.organization_id = org.id
JOIN components c ON g.component_id = c.id
JOIN action_lines al ON c.action_line_id = al.id
JOIN programs prog ON al.program_id = prog.id
JOIN axes ax ON prog.axe_id = ax.id
LEFT JOIN activity_calendars ac ON a.id = ac.activity_id AND ac.cancelled = 0
LEFT JOIN locations loc ON ac.location_id = loc.id
LEFT JOIN polygons pol ON loc.polygon_id = pol.id
LEFT JOIN data_collectors dc ON ac.data_collector_id = dc.id
GROUP BY
    a.id, r.id, so.id, p.id, ax.id, org.name, ac.start_date, ac.end_date, pol.name, loc.name, dc.name;
```

---

## Notas

-   Asegúrate de ejecutar ambos comandos en tu cliente MySQL o herramienta de administración de base de datos.
-   Esta actualización es necesaria para que los reportes y consultas que dependen de la vista funcionen correctamente con la nueva estructura de datos.
