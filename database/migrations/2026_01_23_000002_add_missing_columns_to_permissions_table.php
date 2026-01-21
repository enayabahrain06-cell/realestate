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
        // Add missing columns to real_estate_permissions table
        Schema::table('real_estate_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('real_estate_permissions', 'display_name')) {
                $table->string('display_name')->after('name')->nullable();
            }
            if (!Schema::hasColumn('real_estate_permissions', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('action');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('real_estate_permissions', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'is_system']);
        });
    }
};

