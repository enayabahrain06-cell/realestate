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
        Schema::table('real_estate_buildings', function (Blueprint $table) {
            $table->string('ewa_account_number')->nullable()->after('status');
        });

        // Note: Status enum update not applied due to SQLite limitations.
        // The model will handle validation for 'active', 'inactive', 'maintenance'.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('real_estate_buildings', function (Blueprint $table) {
            $table->dropColumn('ewa_account_number');
        });

        // Note: Status enum revert not applied due to SQLite limitations.
    }
};
