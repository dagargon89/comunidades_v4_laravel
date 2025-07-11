<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('beneficiary_registries', function (Blueprint $table) {
            // Eliminar las foreign keys primero
            $table->dropForeign(['activity_id']);
            $table->dropForeign(['activity_calendar_id']);
            // Eliminar las columnas
            $table->dropColumn(['activity_id', 'activity_calendar_id', 'activity_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_registries', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('activity_calendar_id')->nullable();
            $table->date('activity_date')->nullable();

            // Restaurar las foreign keys
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('set null');
            $table->foreign('activity_calendar_id')->references('id')->on('activity_calendars')->onDelete('set null');
        });
    }
};
