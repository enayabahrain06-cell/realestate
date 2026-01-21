<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agent unit assignments for tracking agent responsibilities.
     */
    public function up(): void
    {
        Schema::create('real_estate_agent_unit_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('real_estate_agents')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('real_estate_units')->onDelete('cascade');
            
            $table->string('assignment_type'); // primary, secondary, viewing_agent
            $table->date('assigned_from')->useCurrent();
            $table->date('assigned_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['agent_id', 'unit_id', 'assignment_type'], 'agent_unit_type_unique');
            $table->index('unit_id');
            $table->index(['agent_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_agent_unit_assignments');
    }
};

