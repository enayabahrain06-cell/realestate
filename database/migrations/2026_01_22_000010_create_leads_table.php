<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Lead management for CRM functionality.
     */
    public function up(): void
    {
        Schema::create('real_estate_leads', function (Blueprint $table) {
            $table->id();
            
            // Basic info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->nullable(); // Computed full name
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            
            // Lead details
            $table->string('source')->nullable(); // website, referral, advertisement, etc.
            $table->string('interest_type')->nullable(); // flat, office, commercial
            $table->string('budget_min')->nullable();
            $table->string('budget_max')->nullable();
            $table->string('preferred_location')->nullable();
            $table->text('requirements')->nullable();
            
            // Assignment
            $table->foreignId('assigned_agent_id')->nullable()->constrained('real_estate_agents')->onDelete('set null');
            $table->unsignedBigInteger('assigned_by')->nullable();
            
            // Pipeline status
            $table->string('status')->default('new'); // new, contacted, viewing, negotiation, closed, lost
            $table->date('next_followup_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('lost_reason')->nullable();
            
            // Conversion tracking
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('converted_tenant_id')->nullable()->constrained('real_estate_tenants')->onDelete('set null');
            
            // Timestamps
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index(['status', 'assigned_agent_id']);
            $table->index('assigned_agent_id');
            $table->index('next_followup_date');
            $table->index(['budget_min', 'budget_max']);
            $table->index('source');
            $table->index(['last_name', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_leads');
    }
};

