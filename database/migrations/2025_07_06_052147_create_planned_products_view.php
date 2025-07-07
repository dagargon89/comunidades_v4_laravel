<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero eliminamos la vista si existe (compatible con SQLite)
        DB::statement('DROP VIEW IF EXISTS vw_planned_products');

        DB::statement("
            CREATE VIEW vw_planned_products AS
            SELECT
                pm.id,
                a.specific_objective_id,
                pm.activity_id,
                a.responsible_id,
                pm.is_product,
                pm.is_population,
                pm.description,
                pm.unit,
                pm.year,
                pm.month,
                pm.product_target_value as target_value,
                pm.product_real_value as real_value,
                pm.data_collector_id,
                pm.created_at,
                pm.updated_at,
                a.description AS activity_name,
                r.name AS responsible_name
            FROM
                planned_metrics pm
            JOIN activities a ON pm.activity_id = a.id
            JOIN responsibles r ON a.responsible_id = r.id
            WHERE
                pm.is_product = 1
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_planned_products');
    }
};
