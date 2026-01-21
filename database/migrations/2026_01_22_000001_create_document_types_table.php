<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Document type definitions for categorization.
     */
    public function up(): void
    {
        Schema::create('real_estate_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'EWA Bill', 'Lease Contract', 'Inspection Report'
            $table->string('slug')->unique(); // e.g., 'ewa-bill', 'lease-contract'
            $table->string('description')->nullable();
            $table->string('extension_allowed')->nullable(); // e.g., 'pdf,doc,docx'
            $table->integer('max_size_mb')->default(10);
            $table->boolean(' versioning_enabled')->default(true);
            $table->boolean('expiry_tracking_enabled')->default(false);
            $table->integer('default_expiry_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_document_types');
    }
};

