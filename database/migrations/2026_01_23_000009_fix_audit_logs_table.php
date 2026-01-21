<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add missing columns and make nullable fields for audit logs.
     */
    public function up(): void
    {
        Schema::table('real_estate_audit_logs', function (Blueprint $table) {
            // Make columns nullable that may not always have values
            $table->string('module')->nullable()->change();
            $table->string('entity_type')->nullable()->change();
            
            // Add missing columns
            $table->json('metadata')->nullable()->after('description');
            
            // Add updated_at column (for consistency, even though model uses $timestamps = false)
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('real_estate_audit_logs', function (Blueprint $table) {
            $table->string('module')->change();
            $table->string('entity_type')->change();
            $table->dropColumn(['metadata', 'updated_at']);
        });
    }
};

