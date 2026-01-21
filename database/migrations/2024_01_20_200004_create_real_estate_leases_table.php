<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('real_estate_tenants')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('real_estate_units')->cascadeOnDelete();
            $table->enum('lease_type', ['single_unit', 'full_floor', 'full_building']);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2);
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'annually'])->default('monthly');
            $table->decimal('late_payment_fee', 10, 2)->nullable();
            $table->text('terms')->nullable();
            $table->enum('status', ['active', 'expired', 'terminated', 'pending'])->default('pending');
            $table->text('cancellation_notes')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_leases');
    }
};

