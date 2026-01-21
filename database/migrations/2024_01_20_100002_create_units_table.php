<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('real_estate_buildings')->onDelete('cascade');
            $table->foreignId('floor_id')->constrained('real_estate_floors')->onDelete('cascade');
            $table->string('unit_number');
            $table->string('unit_type'); // flat, office, commercial, warehouse, parking
            $table->decimal('size_sqft', 10, 2);
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->enum('status', ['available', 'reserved', 'rented', 'maintenance'])->default('available');
            $table->json('features')->nullable(); // air_conditioning, parking, security, etc.
            $table->json('images')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('locked_until')->nullable(); // For temporary lock during selection
            $table->string('locked_by')->nullable(); // Session/user ID
            $table->timestamps();
            
            $table->unique(['building_id', 'unit_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_units');
    }
};

