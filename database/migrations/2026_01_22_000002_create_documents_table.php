<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Polymorphic document storage for buildings, units, and tenants.
     */
    public function up(): void
    {
        Schema::create('real_estate_documents', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationships
            $table->string('documentable_type'); // App\Models\Building, Unit, Tenant, etc.
            $table->unsignedBigInteger('documentable_id');
            
            // Document info
            $table->foreignId('document_type_id')->constrained('real_estate_document_types')->onDelete('restrict');
            
            // File info
            $table->string('original_name');
            $table->string('file_name'); // Stored filename
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size'); // in bytes
            $table->string('extension', 10);
            
            // Version tracking
            $table->unsignedBigInteger('version')->default(1);
            $table->foreignId('parent_document_id')->nullable()->constrained('real_estate_documents')->onDelete('set null');
            
            // Metadata
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional metadata
            
            // Expiry tracking
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('expiry_notification_sent')->default(false);
            
            // Access control
            $table->json('allowed_roles')->nullable(); // Roles that can access
            $table->boolean('is_private')->default(false);
            
            // Timestamps
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('document_type_id');
            $table->index('uploaded_by');
            $table->index(['expiry_date', 'expiry_notification_sent']);
            $table->index('is_private');
            
            // Unique constraint for versioning
            $table->unique(['documentable_type', 'documentable_id', 'document_type_id', 'version'], 'doc_version_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_documents');
    }
};

