<?php

namespace App\Services\RealEstate;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Storage disk to use
     */
    protected string $disk = 'documents';

    /**
     * Upload a document
     */
    public function uploadDocument(UploadedFile $file, array $metadata): Document
    {
        // Validate file
        $this->validateFile($file);

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = $this->generateFilename($metadata['model_type'], $metadata['model_id'], $extension);

        // Determine storage path based on entity type
        $path = $this->getStoragePath($metadata['model_type'], $metadata['model_id'], $metadata['type'] ?? 'general');

        // Store the file
        $storedPath = $file->storeAs($path, $filename, $this->disk);

        // Create document record
        $document = Document::create([
            'model_type' => $metadata['model_type'],
            'model_id' => $metadata['model_id'],
            'type' => $metadata['type'] ?? 'general',
            'title' => $metadata['title'] ?? $originalName,
            'file_path' => $storedPath,
            'file_name' => $filename,
            'original_name' => $originalName,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $metadata['user_id'] ?? null,
            'expiry_date' => $metadata['expiry_date'] ?? null,
            'notes' => $metadata['notes'] ?? null,
            'version' => 1,
            'is_active' => true
        ]);

        // Log activity
        $this->logActivity($document, 'upload', 'Document uploaded');

        return $document;
    }

    /**
     * Create a new version of an existing document
     */
    public function createVersion(int $documentId, UploadedFile $file, string $notes = ''): DocumentVersion
    {
        $document = Document::findOrFail($documentId);

        // Validate file
        $this->validateFile($file);

        // Generate new version filename
        $newVersion = $document->version + 1;
        $extension = $file->getClientOriginalExtension();
        $filename = $document->file_name . '_v' . $newVersion;

        // Get storage path
        $path = dirname($document->file_path);

        // Store the file
        $storedPath = $file->storeAs($path, $filename, $this->disk);

        // Create version record
        $version = DocumentVersion::create([
            'document_id' => $documentId,
            'version' => $newVersion,
            'file_path' => $storedPath,
            'file_name' => $filename,
            'file_size' => $file->getSize(),
            'notes' => $notes,
            'created_by' => auth()->id()
        ]);

        // Update document with new version info
        $document->update([
            'version' => $newVersion,
            'file_path' => $storedPath,
            'file_name' => $filename,
            'file_size' => $file->getSize(),
            'updated_by' => auth()->id()
        ]);

        // Log activity
        $this->logActivity($document, 'version_created', "Version {$newVersion} created");

        return $version;
    }

    /**
     * Delete a document (soft delete)
     */
    public function deleteDocument(int $documentId): bool
    {
        $document = Document::findOrFail($documentId);

        // Log before deletion
        $this->logActivity($document, 'delete', 'Document deleted');

        // Soft delete the document
        $document->update(['is_active' => false]);

        // Optionally delete the file from storage
        // Storage::disk($this->disk)->delete($document->file_path);

        return true;
    }

    /**
     * Hard delete a document (permanent)
     */
    public function hardDeleteDocument(int $documentId): bool
    {
        $document = Document::findOrFail($documentId);

        // Delete file from storage
        Storage::disk($this->disk)->delete($document->file_path);

        // Delete all versions
        DocumentVersion::where('document_id', $documentId)->delete();

        // Delete document record
        $document->delete();

        return true;
    }

    /**
     * Get documents for a specific entity
     */
    public function getDocumentsForEntity(string $entityType, int $entityId)
    {
        return Document::where('model_type', $entityType)
            ->where('model_id', $entityId)
            ->where('is_active', true)
            ->with('versions')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get documents expiring within specified days
     */
    public function getExpiringDocuments(int $days = 30)
    {
        return Document::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays($days)])
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * Get expired documents
     */
    public function getExpiredDocuments()
    {
        return Document::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * Send expiry alerts for documents
     */
    public function sendExpiryAlerts(): int
    {
        $expiringDocuments = $this->getExpiringDocuments(30);
        $alertCount = 0;

        foreach ($expiringDocuments as $document) {
            // TODO: Send notification
            // Notification::route('mail', $this->getEntityEmail($document))
            //     ->notify(new DocumentExpiryNotification($document));
            
            $document->update(['expiry_alert_sent' => true]);
            $alertCount++;
        }

        return $alertCount;
    }

    /**
     * Check and update expired status
     */
    public function checkAndUpdateExpiredStatus(): int
    {
        $expiredCount = Document::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->update(['is_expired' => true]);

        // Update documents that were expired but are now valid
        Document::where('is_expired', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->update(['is_expired' => false]);

        return $expiredCount;
    }

    /**
     * Download a document
     */
    public function downloadDocument(int $documentId)
    {
        $document = Document::findOrFail($documentId);

        if (!Storage::disk($this->disk)->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk($this->disk)->download(
            $document->file_path,
            $document->original_name
        );
    }

    /**
     * Get document types count by entity
     */
    public function getDocumentStats(): array
    {
        return [
            'total' => Document::where('is_active', true)->count(),
            'by_type' => Document::where('is_active', true)
                ->groupBy('type')
                ->select('type', \DB::raw('count(*) as count'))
                ->pluck('count', 'type')
                ->toArray(),
            'expiring' => $this->getExpiringDocuments(30)->count(),
            'expired' => $this->getExpiredDocuments()->count(),
        ];
    }

    /**
     * Get storage path for document
     */
    protected function getStoragePath(string $modelType, int $modelId, string $type): string
    {
        // Extract the short name from model type (e.g., "App\Models\Building" -> "buildings")
        $shortName = strtolower(class_basename($modelType));
        
        // Pluralize the short name
        $pluralName = Str::plural($shortName);

        return "{$pluralName}/{$modelId}/{$type}";
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(string $modelType, int $modelId, string $extension): string
    {
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);
        return "{$modelId}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file): void
    {
        $allowedMimeTypes = config('documents.allowed_types', [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'jpg', 'jpeg', 'png', 'gif', 'webp',
            'txt', 'csv', 'zip', 'rar'
        ]);

        $maxSize = config('documents.max_size', 10240); // 10MB in KB

        $this->validateFileType($file, $allowedMimeTypes);
        $this->validateFileSize($file, $maxSize);
    }

    /**
     * Validate file type
     */
    protected function validateFileType(UploadedFile $file, array $allowedTypes): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedTypes)) {
            throw new \InvalidArgumentException(
                "File type '{$extension}' is not allowed. Allowed types: " . implode(', ', $allowedTypes)
            );
        }
    }

    /**
     * Validate file size
     */
    protected function validateFileSize(UploadedFile $file, int $maxSizeKB): void
    {
        $fileSizeKB = $file->getSize() / 1024;
        
        if ($fileSizeKB > $maxSizeKB) {
            throw new \InvalidArgumentException(
                "File size exceeds maximum allowed size of {$maxSizeKB}KB"
            );
        }
    }

    /**
     * Log activity for document
     */
    protected function logActivity(Document $document, string $action, string $description): void
    {
        // This will be handled by the AuditService
        // For now, we'll just log to the document's interactions if available
        if (method_exists($document, 'interactions')) {
            $document->interactions()->create([
                'type' => $action,
                'description' => $description,
                'user_id' => auth()->id()
            ]);
        }
    }

    /**
     * Get email for entity owner (for notifications)
     */
    protected function getEntityEmail(Document $document): ?string
    {
        $model = $document->model;
        
        if (!$model) {
            return null;
        }

        if (method_exists($model, 'email')) {
            return $model->email;
        }

        if (property_exists($model, 'email')) {
            return $model->email;
        }

        return null;
    }
}

