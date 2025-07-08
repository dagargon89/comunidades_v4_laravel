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
            $table->unsignedBigInteger('activity_calendar_id')->nullable()->after('id');
            $table->foreign('activity_calendar_id')->references('id')->on('activity_calendars')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_registries', function (Blueprint $table) {
            $table->dropForeign(['activity_calendar_id']);
            $table->dropColumn('activity_calendar_id');
        });
    }
};
