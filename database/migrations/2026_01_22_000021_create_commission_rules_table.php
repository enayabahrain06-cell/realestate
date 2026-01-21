<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Commission rules and structures.
     */
    public function up(): void
    {
        Schema::create('real_estate_commission_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Rule type
            $table->string('rule_type'); // standard, tiered, building_specific, unit_type_specific
            
            // Commission calculation basis
            $table->enum('calculation_basis', ['rent_amount', 'deal_value', 'first_month_rent'])->default('rent_amount');
            
            // Rate settings
            $table->enum('rate_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('rate_value', 10, 4)->default(0);
            
            // Tiered structure (JSON for complex tiers)
            $table->json('tiers')->nullable(); // For tiered commissions
            
            // Building/Unit type filters
            $table->foreignId('building_id')->nullable()->constrained('real_estate_buildings')->onDelete('cascade');
            $table->string('unit_type')->nullable(); // flat, office, commercial
            
            // Validity
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();

            // Indexes
            $table->index('rule_type');
            $table->index('building_id');
            $table->index('is_active');
            $table->index(['effective_from', 'effective_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_commission_rules');
    }
};

