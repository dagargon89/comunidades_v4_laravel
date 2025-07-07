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
            $table->string('identifier')->unique()->nullable()->after('id');
            $table->date('activity_date')->nullable()->after('identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_registries', function (Blueprint $table) {
            $table->dropColumn(['identifier', 'activity_date']);
        });
    }
};
