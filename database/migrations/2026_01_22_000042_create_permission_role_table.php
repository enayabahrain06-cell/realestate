<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Role-Permission pivot table.
     */
    public function up(): void
    {
        Schema::create('real_estate_permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('real_estate_roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('real_estate_permissions')->onDelete('cascade');
            $table->timestamps();

            // Primary key
            $table->primary(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_permission_role');
    }
};

