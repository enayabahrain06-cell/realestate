<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('real_estate_tenants')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('real_estate_units')->cascadeOnDelete();
            $table->enum('booking_type', ['inquiry', 'viewing', 'reservation', 'rental']);
            $table->dateTime('booking_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->string('ip_address', 45)->nullable();
            $table->string('session_id', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_bookings');
    }
};

