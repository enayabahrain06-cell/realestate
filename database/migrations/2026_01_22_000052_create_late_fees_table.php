<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Late fee tracking for rent payments.
     */
    public function up(): void
    {
        Schema::create('real_estate_late_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('real_estate_buildings')->onDelete('cascade');
            $table->foreignId('lease_id')->nullable()->constrained('real_estate_leases')->onDelete('set null');
            $table->foreignId('tenant_id')->nullable()->constrained('real_estate_tenants')->onDelete('set null');
            $table->foreignId('payment_id')->nullable()->constrained('real_estate_payments')->onDelete('set null');
            
            $table->decimal('rent_amount_due', 10, 2);
            $table->integer('days_overdue');
            $table->decimal('late_fee_rate', 5, 4)->nullable(); // Percentage
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->decimal('grace_period_days')->default(0);
            $table->decimal('maximum_late_fee', 10, 2)->nullable();
            
            $table->date('due_date');
            $table->date('calculated_at')->useCurrent();
            
            $table->enum('status', ['pending', 'waived', 'paid', 'disputed'])->default('pending');
            $table->text('waiver_reason')->nullable();
            $table->unsignedBigInteger('waived_by')->nullable();
            $table->timestamp('waived_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('building_id');
            $table->index('lease_id');
            $table->index('tenant_id');
            $table->index('status');
            $table->index(['building_id', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_late_fees');
    }
};

