<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('real_estate_leases')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('real_estate_tenants')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('real_estate_units')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_type', ['rent', 'deposit', 'late_fee', 'maintenance', 'other']);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'debit_card', 'check', 'online'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_payments');
    }
};

