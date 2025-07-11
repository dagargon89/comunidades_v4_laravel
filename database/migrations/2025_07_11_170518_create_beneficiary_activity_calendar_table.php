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
        Schema::create('beneficiary_activity_calendar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiary_registry_id');
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('activity_calendar_id');
            $table->text('signature')->nullable();
            $table->timestamps();

            $table->foreign('beneficiary_registry_id')->references('id')->on('beneficiary_registries')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('activity_calendar_id')->references('id')->on('activity_calendars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_activity_calendar');
    }
};
