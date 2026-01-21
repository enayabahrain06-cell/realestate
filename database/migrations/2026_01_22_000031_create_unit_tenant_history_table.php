<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Track tenant move-in/move-out history for each unit.
     */
    public function up(): void
    {
        Schema::create('real_estate_unit_tenant_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('real_estate_units')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('real_estate_tenants')->onDelete('cascade');
            $table->foreignId('lease_id')->nullable()->constrained('real_estate_leases')->onDelete('set null');
            
            $table->date('move_in_date');
            $table->date('move_out_date')->nullable();
            $table->string('tenancy_type'); // initial_lease, renewal, transfer
            
            $table->decimal('rent_at_move_in', 10, 2)->nullable();
            $table->decimal('rent_at_move_out', 10, 2)->nullable();
            
            $table->string('termination_reason')->nullable(); // lease_end, early_termination, eviction, transfer
            $table->text('termination_notes')->nullable();
            
            $table->boolean('is_current')->default(false);
            $table->text('notes')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->index('unit_id');
            $table->index('tenant_id');
            $table->index(['unit_id', 'is_current']);
            $table->index('move_in_date');
            $table->index('move_out_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_unit_tenant_history');
    }
};

