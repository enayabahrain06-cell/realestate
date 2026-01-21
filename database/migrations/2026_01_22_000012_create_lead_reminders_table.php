<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Lead follow-up reminders.
     */
    public function up(): void
    {
        Schema::create('real_estate_lead_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('real_estate_leads')->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('reminder_date');
            $table->time('reminder_time')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            
            // Assignment
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->index('lead_id');
            $table->index(['reminder_date', 'is_completed']);
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_lead_reminders');
    }
};

