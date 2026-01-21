<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Permission definitions for granular access control.
     */
    public function up(): void
    {
        Schema::create('real_estate_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., buildings.create, buildings.view
            $table->string('display_name');
            $table->string('module'); // buildings, units, tenants, leases, etc.
            $table->text('description')->nullable();
            $table->string('action'); // create, view, edit, delete, export
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('module');
            $table->index('action');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_permissions');
    }
};

