<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Audit logging for tracking all changes in the system.
     */
    public function up(): void
    {
        Schema::create('real_estate_audit_logs', function (Blueprint $table) {
            $table->id();
            
            // User who performed the action
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            
            // Action details
            $table->string('action'); // created, updated, deleted, viewed, exported, login, logout
            $table->string('module'); // buildings, units, tenants, leases, etc.
            $table->string('entity_type'); // Model class name
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_name')->nullable(); // Display name of the entity
            
            // Before/After values (JSON for complex data)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changed_fields')->nullable(); // Array of field names that changed
            
            // Description
            $table->text('description')->nullable();
            
            // Metadata
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('location')->nullable(); // City/Country from IP
            $table->string('session_id')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for efficient querying
            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
            $table->index(['module', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_audit_logs');
    }
};

