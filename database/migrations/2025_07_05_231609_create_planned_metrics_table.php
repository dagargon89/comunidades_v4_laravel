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
        Schema::disableForeignKeyConstraints();

        Schema::create('planned_metrics', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('unit', 100)->nullable();
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->boolean('is_product')->default(false);
            $table->boolean('is_population')->default(false);
            $table->decimal('population_target_value', 10, 2)->nullable();
            $table->decimal('population_real_value', 10, 2)->default(0.00);
            $table->decimal('product_target_value', 10, 2)->nullable();
            $table->decimal('product_real_value', 10, 2)->nullable();
            $table->foreignId('data_collector_id')->nullable()->constrained('data_collectors');
            $table->foreignId('activity_id');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planned_metrics');
    }
};
