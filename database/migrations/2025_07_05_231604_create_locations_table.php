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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('category', 50)->nullable();
            $table->text('street')->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->integer('ext_number')->nullable();
            $table->integer('int_number')->nullable();
            $table->string('google_place_id', 500)->nullable();
            $table->timestamp('created_at');
            $table->foreignId('polygon_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
