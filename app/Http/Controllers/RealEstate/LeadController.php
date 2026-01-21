<?php

namespace App\Http\Controllers\RealEstate;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Agent;
use App\Services\RealEstate\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
        $this->authorizeResource(Lead::class, 'lead');
    }

    /**
     * Display a listing of leads.
     */
    public function index(Request $request)
    {
        $query = Lead::query();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by source
        if ($request->has('source') && $request->source) {
            $query->where('source', $request->source);
        }

        // Filter by assigned agent
        if ($request->has('agent_id') && $request->agent_id) {
            $query->where('assigned_agent_id', $request->agent_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $leads = $query->with('assignedAgent')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $pipelineStats = $this->leadService->getPipelineStats();
        $agents = Agent::where('is_active', true)->get();

        return view('real-estate.leads.index', compact('leads', 'pipelineStats', 'agents'));
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create()
    {
        $agents = Agent::where('is_active', true)->get();
        return view('real-estate.leads.create', compact('agents'));
    }

    /**
     * Store a newly created lead.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
            'budget_min' => 'nullable|numeric',
            'budget_max' => 'nullable|numeric',
            'preferred_location' => 'nullable|string',
            'preferred_unit_type' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'assigned_agent_id' => 'nullable|exists:real_estate_agents,id',
        ]);

        $lead = $this->leadService->createLead($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        $lead->load(['assignedAgent', 'interactions.user', 'reminders']);
        $interactions = $this->leadService->getInteractions($lead->id);
        $agents = Agent::where('is_active', true)->get();

        return view('real-estate.leads.show', compact('lead', 'interactions', 'agents'));
    }

    /**
     * Show the form for editing the lead.
     */
    public function edit(Lead $lead)
    {
        $agents = Agent::where('is_active', true)->get();
        return view('real-estate.leads.edit', compact('lead', 'agents'));
    }

    /**
     * Update the specified lead.
     */
    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
            'budget_min' => 'nullable|numeric',
            'budget_max' => 'nullable|numeric',
            'preferred_location' => 'nullable|string',
            'preferred_unit_type' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'assigned_agent_id' => 'nullable|exists:real_estate_agents,id',
        ]);

        $lead->update($validated);

        return redirect()->route('leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified lead.
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    /**
     * Update lead status.
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,contacted,viewing,negotiation,closed,lost',
            'notes' => 'nullable|string',
        ]);

        $this->leadService->updateLeadStatus($lead->id, $validated['status'], $validated['notes'] ?? '');

        return back()->with('success', 'Lead status updated successfully.');
    }

    /**
     * Assign agent to lead.
     */
    public function assignAgent(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:real_estate_agents,id',
        ]);

        $this->leadService->assignAgent($lead->id, $validated['agent_id']);

        return back()->with('success', 'Agent assigned successfully.');
    }

    /**
     * Convert lead to tenant.
     */
    public function convert(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'unit_id' => 'nullable|exists:real_estate_units,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'rent_amount' => 'nullable|numeric',
        ]);

        $tenantData = [
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? $lead->email,
            'phone' => $validated['phone'] ?? $lead->phone,
            'id_number' => $validated['id_number'] ?? null,
        ];

        $leaseData = [];
        if (isset($validated['unit_id'])) {
            $leaseData = [
                'unit_id' => $validated['unit_id'],
                'start_date' => $validated['start_date'] ?? now(),
                'end_date' => $validated['end_date'] ?? now()->addYear(),
                'rent_amount' => $validated['rent_amount'] ?? null,
            ];
        }

        $tenant = $this->leadService->convertToTenant($lead->id, $tenantData, $leaseData);

        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Lead converted to tenant successfully.');
    }

    /**
     * Log interaction for a lead.
     */
    public function addInteraction(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $this->leadService->logInteraction($lead->id, $validated['type'], $validated['description'], $validated['notes'] ?? '');

        return back()->with('success', 'Interaction logged successfully.');
    }

    /**
     * Create reminder for a lead.
     */
    public function createReminder(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $this->leadService->createReminder($lead->id, $validated);

        return back()->with('success', 'Reminder created successfully.');
    }

    /**
     * Move lead to next stage.
     */
    public function moveToNextStage(Lead $lead)
    {
        $this->leadService->moveToNextStage($lead->id);
        return back()->with('success', 'Lead moved to next stage.');
    }

    /**
     * Mark lead as lost.
     */
    public function markLost(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $this->leadService->lostLead($lead->id, $validated['reason']);
        return back()->with('success', 'Lead marked as lost.');
    }

    /**
     * Get pipeline data for API.
     */
    public function pipelineData()
    {
        $stats = $this->leadService->getPipelineStats();
        return response()->json($stats);
    }
}

