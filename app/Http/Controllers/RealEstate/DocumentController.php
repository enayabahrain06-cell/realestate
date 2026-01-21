<?php

namespace App\Http\Controllers\RealEstate;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Services\RealEstate\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
        $this->authorizeResource(Document::class, 'document');
    }

    /**
     * Display a listing of documents.
     */
    public function index(Request $request)
    {
        $query = Document::query();

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by entity type
        if ($request->has('entity_type') && $request->entity_type) {
            $query->where('model_type', 'like', "%{$request->entity_type}");
        }

        // Filter by expiry status
        if ($request->has('expiry_status') && $request->expiry_status) {
            switch ($request->expiry_status) {
                case 'expired':
                    $query->whereNotNull('expiry_date')
                          ->where('expiry_date', '<', now());
                    break;
                case 'expiring':
                    $query->whereNotNull('expiry_date')
                          ->whereBetween('expiry_date', [now(), now()->addDays(30)]);
                    break;
                case 'valid':
                    $query->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                    break;
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('original_name', 'like', "%{$request->search}%");
            });
        }

        $documents = $query->with('model')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = $this->documentService->getDocumentStats();

        return view('real-estate.documents.index', compact('documents', 'stats'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        return view('real-estate.documents.create');
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'type' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $document = $this->documentService->uploadDocument($request->file('file'), [
            'model_type' => $validated['model_type'],
            'model_id' => $validated['model_id'],
            'type' => $validated['type'] ?? 'general',
            'title' => $validated['title'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        $document->load(['versions', 'model']);
        return view('real-estate.documents.show', compact('document'));
    }

    /**
     * Download a document.
     */
    public function download(Document $document)
    {
        $this->authorize('view', $document);
        return $this->documentService->downloadDocument($document->id);
    }

    /**
     * Delete a document.
     */
    public function destroy(Document $document)
    {
        $this->documentService->deleteDocument($document->id);
        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Add a new version to a document.
     */
    public function addVersion(Request $request, Document $document)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'notes' => 'nullable|string',
        ]);

        $this->documentService->createVersion($document->id, $request->file('file'), $validated['notes'] ?? '');

        return back()->with('success', 'New version uploaded successfully.');
    }

    /**
     * Download a specific version.
     */
    public function downloadVersion(DocumentVersion $version)
    {
        if (!Storage::disk('documents')->exists($version->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('documents')->download(
            $version->file_path,
            $version->file_name
        );
    }

    /**
     * Get documents for a specific entity (API).
     */
    public function forEntity(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
        ]);

        $documents = $this->documentService->getDocumentsForEntity(
            $validated['entity_type'],
            $validated['entity_id']
        );

        return response()->json($documents);
    }

    /**
     * Get expiring documents.
     */
    public function expiring(Request $request)
    {
        $days = $request->get('days', 30);
        $documents = $this->documentService->getExpiringDocuments($days);
        return view('real-estate.documents.expiring', compact('documents', 'days'));
    }

    /**
     * Get expired documents.
     */
    public function expired()
    {
        $documents = $this->documentService->getExpiredDocuments();
        return view('real-estate.documents.expired', compact('documents'));
    }

    /**
     * Check and update expired status.
     */
    public function checkExpiry()
    {
        $count = $this->documentService->checkAndUpdateExpiredStatus();
        return back()->with('success', "Updated {$count} expired documents.");
    }

    /**
     * Send expiry alerts.
     */
    public function sendExpiryAlerts()
    {
        $count = $this->documentService->sendExpiryAlerts();
        return back()->with('success', "Sent {$count} expiry alerts.");
    }
}

