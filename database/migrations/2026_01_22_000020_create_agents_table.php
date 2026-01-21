<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agent profiles for the real estate system.
     */
    public function up(): void
    {
        Schema::create('real_estate_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Agent info
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            
            // Employment details
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('join_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->string('status')->default('active');
            
            // Commission settings
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('default_commission', 10, 2)->default(0);
            
            // Performance tracking
            $table->unsignedInteger('total_leads_handled')->default(0);
            $table->unsignedInteger('total_deals_closed')->default(0);
            $table->decimal('total_revenue_generated', 15, 2)->default(0);
            $table->decimal('total_commission_earned', 15, 2)->default(0);
            
            // Target settings
            $table->decimal('monthly_target', 15, 2)->nullable();
            $table->decimal('yearly_target', 15, 2)->nullable();
            
            // Metadata
            $table->text('bio')->nullable();
            $table->json('specializations')->nullable();
            $table->json('languages')->nullable();
            $table->json('assigned_buildings')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('employee_id');
            $table->index(['last_name', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_agents');
    }
};

