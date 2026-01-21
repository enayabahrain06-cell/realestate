<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add deleted_at column to support soft deletes for audit logs.
     */
    public function up(): void
    {
        Schema::table('real_estate_audit_logs', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('created_at');
            
            // Add index for soft delete queries
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('real_estate_audit_logs', function (Blueprint $table) {
            $table->dropIndex(['deleted_at']);
            $table->dropColumn('deleted_at');
        });
    }
};

