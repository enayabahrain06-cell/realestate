<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Unit history tracking for rent changes and status updates.
     */
    public function up(): void
    {
        Schema::create('real_estate_unit_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('real_estate_units')->onDelete('cascade');
            
            $table->string('change_type'); // rent_change, status_change, feature_update, price_update
            $table->string('field_changed')->nullable(); // rent_amount, status, etc.
            
            // Before/After values
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            
            // For rent changes specifically
            $table->decimal('old_rent', 10, 2)->nullable();
            $table->decimal('new_rent', 10, 2)->nullable();
            $table->decimal('rent_increase_percentage', 8, 4)->nullable();
            
            // For status changes
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            
            // Reason
            $table->string('change_reason')->nullable();
            $table->text('change_notes')->nullable();
            
            // Who made the change
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            
            $table->timestamps();

            // Indexes
            $table->index('unit_id');
            $table->index('change_type');
            $table->index('changed_at');
            $table->index('changed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_unit_histories');
    }
};

