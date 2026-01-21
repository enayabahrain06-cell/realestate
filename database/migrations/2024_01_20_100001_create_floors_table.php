<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('real_estate_buildings')->onDelete('cascade');
            $table->integer('floor_number');
            $table->integer('total_units');
            $table->text('description')->nullable();
            $table->json('floor_plan')->nullable(); // For visual layout
            $table->timestamps();
            
            $table->unique(['building_id', 'floor_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_floors');
    }
};

