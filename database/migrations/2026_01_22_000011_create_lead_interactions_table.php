<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Lead interaction logging for CRM.
     */
    public function up(): void
    {
        Schema::create('real_estate_lead_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('real_estate_leads')->onDelete('cascade');
            
            $table->string('type'); // call, email, meeting, site_visit, message
            $table->text('notes');
            $table->string('outcome')->nullable(); // positive, neutral, negative
            $table->string('duration')->nullable(); // For calls/meetings
            
            // Schedule follow-up
            $table->boolean('followup_required')->default(false);
            $table->date('followup_date')->nullable();
            $table->text('followup_notes')->nullable();
            
            // Units shown (if any)
            $table->json('units_shown')->nullable(); // Array of unit IDs
            
            // Metadata
            $table->string('ip_address')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('lead_id');
            $table->index('type');
            $table->index(['followup_required', 'followup_date']);
            $table->index('recorded_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_lead_interactions');
    }
};
