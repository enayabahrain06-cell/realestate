<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Document version history for tracking changes.
     */
    public function up(): void
    {
        Schema::create('real_estate_document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('real_estate_documents')->onDelete('cascade');
            
            $table->unsignedInteger('version_number');
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size'); // in bytes
            $table->string('changelog')->nullable(); // Description of changes
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->text('notes')->nullable(); // Additional notes for this version

            // Indexes
            $table->index(['document_id', 'version_number']);
            $table->index('created_by');
            
            // Unique constraint
            $table->unique(['document_id', 'version_number'], 'doc_ver_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_document_versions');
    }
};

