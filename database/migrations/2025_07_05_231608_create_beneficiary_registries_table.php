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
        Schema::create('beneficiary_registries', function (Blueprint $table) {
            $table->id();
            $table->string('last_name', 100)->nullable();
            $table->string('mother_last_name', 100)->nullable();
            $table->string('first_names', 100)->nullable();
            $table->string('birth_year', 4)->nullable();
            $table->enum('gender', ['M', 'F', 'Male', 'Female'])->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('signature')->nullable();
            $table->text('address_backup')->nullable();
            $table->foreignId('activity_id');
            $table->foreignId('location_id');
            $table->foreignId('data_collector_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_registries');
    }
};
