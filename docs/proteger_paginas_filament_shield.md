# Protección de Páginas Personalizadas en Filament con Shield

## ¿Por qué es importante?

Cuando creas páginas personalizadas (`Page`) en Filament, **no se protegen automáticamente** con los permisos de Filament Shield. Para que solo los usuarios autorizados puedan ver y acceder a estas páginas, debes agregar el trait especial de Shield y generar los permisos correspondientes.

---

## Pasos para proteger una página personalizada

### 1. Agregar el trait `HasPageShield`

En la clase de tu página personalizada (por ejemplo, `OrganigramManager.php`), importa y usa el trait así:

```php
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class OrganigramManager extends Page implements HasTable
{
    use InteractsWithTable;
    use HasPageShield;

    // ... el resto de tu código
}
```

> **Nota:** El trait puede ir junto con otros traits como `InteractsWithTable`.

---

### 2. Generar los permisos para las Pages

Ejecuta el siguiente comando en la terminal para que Filament Shield genere los permisos necesarios para todas las páginas de tu panel:

```bash
php artisan shield:generate --page
```

Cuando te pregunte el panel, presiona `Enter` para aceptar el valor por defecto (por ejemplo, `admin`).

---

### 3. Asignar el permiso al rol correspondiente

-   Ve al panel de administración de roles en Filament.
-   Busca el permiso generado para tu página (por ejemplo, `view_organigram_manager`).
-   Asígnalo a los roles que deban tener acceso.

---

### 4. Limpiar el caché de permisos (opcional pero recomendable)

Después de generar o asignar permisos, ejecuta:

```bash
php artisan permission:cache-reset
```

---

## ¿Qué hace el trait `HasPageShield`?

-   Controla automáticamente la visibilidad del menú y el acceso a la página según los permisos generados por Shield.
-   Si el usuario no tiene el permiso, **no verá la página en el menú** y **no podrá acceder aunque escriba la URL**.

---

## Referencia

-   [Documentación oficial de Filament Shield - Pages](https://filamentphp.com/plugins/bezhansalleh-shield#pages)

---

¡Con estos pasos, tus páginas personalizadas estarán protegidas y solo serán accesibles para los usuarios correctos!
