<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Commission payouts tracking.
     */
    public function up(): void
    {
        Schema::create('real_estate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('real_estate_agents')->onDelete('cascade');
            $table->foreignId('commission_rule_id')->nullable()->constrained('real_estate_commission_rules')->onDelete('set null');
            
            // Deal info
            $table->foreignId('lease_id')->nullable()->constrained('real_estate_leases')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('real_estate_units')->onDelete('set null');
            $table->foreignId('tenant_id')->nullable()->constrained('real_estate_tenants')->onDelete('set null');
            
            // Amount details
            $table->decimal('deal_value', 15, 2); // Rent amount or deal value
            $table->decimal('commission_rate', 10, 4); // Percentage or fixed rate used
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('commission_amount', 15, 2); // Calculated commission
            $table->decimal('agent_share', 15, 2); // Agent's portion if shared
            
            // Payment status
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled', 'disputed'])->default('pending');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('payment_reference')->nullable();
            
            // Calculation breakdown
            $table->json('calculation_details')->nullable(); // Breakdown of calculation
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('dispute_reason')->nullable();
            
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('agent_id');
            $table->index('status');
            $table->index(['status', 'due_date']);
            $table->index('lease_id');
            $table->index('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_commissions');
    }
};

