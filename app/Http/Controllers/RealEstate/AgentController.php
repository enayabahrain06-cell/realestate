<?php

namespace App\Http\Controllers\RealEstate;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Commission;
use App\Models\AgentUnitAssignment;
use App\Services\RealEstate\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
        $this->authorizeResource(Agent::class, 'agent');
    }

    /**
     * Display a listing of agents.
     */
    public function index(Request $request)
    {
        $query = Agent::query();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $agents = $query->withCount(['commissions'])
            ->orderBy('name')
            ->paginate(20);

        return view('real-estate.agents.index', compact('agents'));
    }

    /**
     * Show the form for creating a new agent.
     */
    public function create()
    {
        return view('real-estate.agents.create');
    }

    /**
     * Store a newly created agent.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Agent::create($validated);

        return redirect()->route('agents.index')
            ->with('success', 'Agent created successfully.');
    }

    /**
     * Display the specified agent.
     */
    public function show(Agent $agent)
    {
        $agent->load(['commissions.lease.unit.building', 'unitAssignments.unit.building']);
        
        $period = request('period', 'month');
        $performance = $this->commissionService->getAgentPerformanceReport($agent->id, $period);
        $pendingCommissions = $this->commissionService->getAgentPendingCommissions($agent->id);
        $assignedUnits = $this->commissionService->getAgentUnits($agent->id);

        return view('real-estate.agents.show', compact('agent', 'performance', 'pendingCommissions', 'assignedUnits'));
    }

    /**
     * Show the form for editing the agent.
     */
    public function edit(Agent $agent)
    {
        return view('real-estate.agents.edit', compact('agent'));
    }

    /**
     * Update the specified agent.
     */
    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $agent->update($validated);

        return redirect()->route('agents.show', $agent)
            ->with('success', 'Agent updated successfully.');
    }

    /**
     * Remove the specified agent.
     */
    public function destroy(Agent $agent)
    {
        // Soft delete - just deactivate
        $agent->update(['is_active' => false]);
        
        return redirect()->route('agents.index')
            ->with('success', 'Agent deactivated successfully.');
    }

    /**
     * Add commission to agent manually.
     */
    public function addCommission(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'lease_id' => 'required|exists:real_estate_leases,id',
            'deal_amount' => 'required|numeric|min:0',
            'commission_amount' => 'required|numeric|min:0',
            'type' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Commission::create([
            'agent_id' => $agent->id,
            'lease_id' => $validated['lease_id'],
            'deal_amount' => $validated['deal_amount'],
            'commission_amount' => $validated['commission_amount'],
            'type' => $validated['type'] ?? 'rental',
            'status' => 'pending',
            'due_date' => now()->addDays(30),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Commission added successfully.');
    }

    /**
     * Get agent's commission summary.
     */
    public function commissionSummary(Agent $agent, $period = 'month')
    {
        $summary = $this->commissionService->getAgentCommissionSummary($agent->id, $period);
        return response()->json($summary);
    }

    /**
     * Assign unit to agent.
     */
    public function assignUnit(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:real_estate_units,id',
            'type' => 'nullable|string|in:exclusive,shared,none',
        ]);

        $this->commissionService->assignUnit($agent->id, $validated['unit_id'], $validated['type'] ?? 'exclusive');

        return back()->with('success', 'Unit assigned successfully.');
    }

    /**
     * Remove unit assignment.
     */
    public function removeUnitAssignment(Agent $agent, $assignmentId)
    {
        $this->commissionService->removeUnitAssignment($assignmentId);
        return back()->with('success', 'Unit assignment removed.');
    }

    /**
     * Get top performing agents.
     */
    public function topPerformers()
    {
        $limit = request('limit', 10);
        $period = request('period', 'month');
        
        $performers = $this->commissionService->getTopPerformers($limit, $period);
        
        return view('real-estate.agents.top-performers', compact('performers', 'period'));
    }

    /**
     * Get pending commission approvals.
     */
    public function pendingApprovals()
    {
        $this->authorize('viewAny', Commission::class);
        
        $commissions = $this->commissionService->getPendingApprovals();
        
        return view('real-estate.commissions.pending', compact('commissions'));
    }

    /**
     * Get all agents as JSON (for dropdowns).
     */
    public function json()
    {
        $agents = Agent::where('is_active', true)
            ->select('id', 'name', 'email', 'phone')
            ->get();
        
        return response()->json($agents);
    }
}

