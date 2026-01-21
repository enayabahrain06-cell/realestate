<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Role definitions for access control.
     */
    public function up(): void
    {
        Schema::create('real_estate_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // super_admin, property_manager, accountant, agent, viewer
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false); // System roles cannot be deleted
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_roles');
    }
};

