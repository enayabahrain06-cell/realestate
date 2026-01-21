<?php

namespace App\Services\RealEstate;

use App\Models\Lead;
use App\Models\LeadInteraction;
use App\Models\LeadReminder;
use App\Models\Agent;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class LeadService
{
    /**
     * Pipeline stages
     */
    public const STAGE_NEW = 'new';
    public const STAGE_CONTACTED = 'contacted';
    public const STAGE_VIEWING = 'viewing';
    public const STAGE_NEGOTIATION = 'negotiation';
    public const STAGE_CLOSED = 'closed';
    public const STAGE_LOST = 'lost';

    /**
     * Lead sources
     */
    public const SOURCE_WEBSITE = 'website';
    public const SOURCE_REFERRAL = 'referral';
    public const SOURCE_SOCIAL = 'social';
    public const SOURCE_ADS = 'ads';
    public const SOURCE_WALKIN = 'walkin';
    public const SOURCE_OTHER = 'other';

    /**
     * Create a new lead
     */
    public function createLead(array $data): Lead
    {
        return DB::transaction(function () use ($data) {
            $lead = Lead::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'source' => $data['source'] ?? self::SOURCE_OTHER,
                'status' => self::STAGE_NEW,
                'notes' => $data['notes'] ?? null,
                'budget_min' => $data['budget_min'] ?? null,
                'budget_max' => $data['budget_max'] ?? null,
                'preferred_location' => $data['preferred_location'] ?? null,
                'preferred_unit_type' => $data['preferred_unit_type'] ?? null,
                'bedrooms' => $data['bedrooms'] ?? null,
                'assigned_agent_id' => $data['assigned_agent_id'] ?? null,
                'interested_unit_id' => $data['interested_unit_id'] ?? null,
                'created_by' => auth()->id()
            ]);

            // Log initial interaction
            $this->logInteraction($lead->id, 'created', 'Lead created from ' . ($data['source'] ?? 'unknown source'));

            return $lead;
        });
    }

    /**
     * Update lead status
     */
    public function updateLeadStatus(int $leadId, string $status, string $notes = ''): Lead
    {
        $lead = Lead::findOrFail($leadId);
        
        $oldStatus = $lead->status;
        $lead->update(['status' => $status]);

        $this->logInteraction($leadId, 'status_changed', 
            "Status changed from '{$oldStatus}' to '{$status}'. {$notes}");

        // Check if lead was closed
        if ($status === self::STAGE_CLOSED) {
            $this->logInteraction($leadId, 'closed', 'Lead successfully closed');
        } elseif ($status === self::STAGE_LOST) {
            $this->logInteraction($leadId, 'lost', 'Lead marked as lost. Reason: ' . $notes);
        }

        return $lead->fresh();
    }

    /**
     * Assign an agent to a lead
     */
    public function assignAgent(int $leadId, int $agentId): Lead
    {
        $lead = Lead::findOrFail($leadId);
        $agent = Agent::findOrFail($agentId);

        $oldAgentId = $lead->assigned_agent_id;
        $lead->update(['assigned_agent_id' => $agentId]);

        if ($oldAgentId) {
            $this->logInteraction($leadId, 'agent_reassigned', 
                "Reassigned from agent ID {$oldAgentId} to {$agent->name}");
        } else {
            $this->logInteraction($leadId, 'agent_assigned', 
                "Assigned to agent: {$agent->name}");
        }

        // TODO: Send notification to agent

        return $lead->fresh();
    }

    /**
     * Move lead to next stage in pipeline
     */
    public function moveToNextStage(int $leadId): Lead
    {
        $lead = Lead::findOrFail($leadId);
        
        $stages = [
            self::STAGE_NEW => self::STAGE_CONTACTED,
            self::STAGE_CONTACTED => self::STAGE_VIEWING,
            self::STAGE_VIEWING => self::STAGE_NEGOTIATION,
            self::STAGE_NEGOTIATION => self::STAGE_CLOSED,
        ];

        if (!isset($stages[$lead->status])) {
            return $lead;
        }

        return $this->updateLeadStatus($leadId, $stages[$lead->status]);
    }

    /**
     * Convert lead to tenant (when they sign a lease)
     */
    public function convertToTenant(int $leadId, array $tenantData, array $leaseData): Tenant
    {
        return DB::transaction(function () use ($leadId, $tenantData, $leaseData) {
            $lead = Lead::findOrFail($leadId);

            // Create tenant
            $tenant = Tenant::create([
                'full_name' => $tenantData['full_name'] ?? $lead->name,
                'email' => $tenantData['email'] ?? $lead->email,
                'phone' => $tenantData['phone'] ?? $lead->phone,
                'id_number' => $tenantData['id_number'] ?? null,
                'id_type' => $tenantData['id_type'] ?? null,
                'nationality' => $tenantData['nationality'] ?? null,
                'emirate' => $tenantData['emirate'] ?? null,
                'emergency_contact' => $tenantData['emergency_contact'] ?? null,
                'created_by' => auth()->id()
            ]);

            // Create lease if unit is provided
            if (isset($leaseData['unit_id'])) {
                $unit = Unit::findOrFail($leaseData['unit_id']);
                
                $lease = Lease::create([
                    'tenant_id' => $tenant->id,
                    'unit_id' => $leaseData['unit_id'],
                    'start_date' => $leaseData['start_date'],
                    'end_date' => $leaseData['end_date'],
                    'rent_amount' => $leaseData['rent_amount'] ?? $unit->rent_amount,
                    'deposit_amount' => $leaseData['deposit_amount'] ?? 0,
                    'status' => 'active',
                    'contract_document' => $leaseData['contract_document'] ?? null,
                    'notes' => $leaseData['notes'] ?? null
                ]);

                // Update unit status
                $unit->update(['status' => 'rented']);
            }

            // Update lead status
            $this->updateLeadStatus($leadId, self::STAGE_CLOSED, 
                "Converted to tenant ID: {$tenant->id}");

            $this->logInteraction($leadId, 'converted', 
                "Lead converted to tenant: {$tenant->full_name}");

            return $tenant;
        });
    }

    /**
     * Mark lead as lost
     */
    public function lostLead(int $leadId, string $reason): Lead
    {
        return $this->updateLeadStatus($leadId, self::STAGE_LOST, $reason);
    }

    /**
     * Get pipeline statistics
     */
    public function getPipelineStats(): array
    {
        $leads = Lead::query();

        return [
            'total' => $leads->count(),
            'by_stage' => Lead::groupBy('status')
                ->select('status', \DB::raw('count(*) as count'))
                ->pluck('count', 'status')
                ->toArray(),
            'by_source' => Lead::groupBy('source')
                ->select('source', \DB::raw('count(*) as count'))
                ->pluck('count', 'source')
                ->toArray(),
            'conversion_rate' => $this->calculateConversionRate(),
            'average_time_to_close' => $this->calculateAverageTimeToClose(),
            'total_value' => $this->calculateTotalPipelineValue(),
        ];
    }

    /**
     * Calculate lead conversion rate
     */
    public function calculateConversionRate(): float
    {
        $total = Lead::whereIn('status', [self::STAGE_CLOSED, self::STAGE_LOST])->count();
        
        if ($total === 0) {
            return 0;
        }

        $closed = Lead::where('status', self::STAGE_CLOSED)->count();
        
        return ($closed / $total) * 100;
    }

    /**
     * Calculate average time to close (in days)
     */
    public function calculateAverageTimeToClose(): int
    {
        $closedLeads = Lead::where('status', self::STAGE_CLOSED)
            ->whereNotNull('created_at')
            ->get();

        if ($closedLeads->isEmpty()) {
            return 0;
        }

        $totalDays = $closedLeads->sum(function ($lead) {
            return $lead->created_at->diffInDays($lead->updated_at);
        });

        return (int) ($totalDays / $closedLeads->count());
    }

    /**
     * Calculate total value of pipeline
     */
    public function calculateTotalPipelineValue(): float
    {
        return Lead::whereNotNull('budget_max')
            ->whereNotIn('status', [self::STAGE_CLOSED, self::STAGE_LOST])
            ->avg('budget_max') * Lead::whereNotIn('status', [self::STAGE_CLOSED, self::STAGE_LOST])->count();
    }

    /**
     * Get leads for a specific agent
     */
    public function getAgentLeads(int $agentId, string $status = null)
    {
        $query = Lead::where('assigned_agent_id', $agentId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Create a reminder for a lead
     */
    public function createReminder(int $leadId, array $data): LeadReminder
    {
        return LeadReminder::create([
            'lead_id' => $leadId,
            'title' => $data['title'],
            'due_date' => $data['due_date'],
            'notes' => $data['notes'] ?? null,
            'is_completed' => false,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Get overdue reminders
     */
    public function getOverdueReminders()
    {
        return LeadReminder::where('is_completed', false)
            ->where('due_date', '<', now())
            ->with('lead')
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Complete a reminder
     */
    public function completeReminder(int $reminderId): LeadReminder
    {
        $reminder = LeadReminder::findOrFail($reminderId);
        $reminder->update(['is_completed' => true, 'completed_at' => now()]);

        $this->logInteraction($reminder->lead_id, 'reminder_completed', 
            "Reminder completed: {$reminder->title}");

        return $reminder;
    }

    /**
     * Log a lead interaction
     */
    public function logInteraction(int $leadId, string $type, string $description, string $notes = ''): LeadInteraction
    {
        return LeadInteraction::create([
            'lead_id' => $leadId,
            'type' => $type,
            'description' => $description,
            'notes' => $notes,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Get lead interactions
     */
    public function getInteractions(int $leadId)
    {
        return LeadInteraction::where('lead_id', $leadId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get leads with upcoming reminders
     */
    public function getLeadsWithUpcomingReminders($days = 7)
    {
        return Lead::whereHas('reminders', function ($query) use ($days) {
            $query->where('is_completed', false)
                  ->whereBetween('due_date', [now(), now()->addDays($days)]);
        })->with(['reminders' => function ($query) use ($days) {
            $query->where('is_completed', false)
                  ->whereBetween('due_date', [now(), now()->addDays($days)]);
        }])->get();
    }

    /**
     * Get available agents for assignment
     */
    public function getAvailableAgents()
    {
        return Agent::where('is_active', true)->get();
    }

    /**
     * Search leads
     */
    public function searchLeads(string $query, array $filters = [])
    {
        $leads = Lead::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%");
        });

        if (isset($filters['status'])) {
            $leads->where('status', $filters['status']);
        }

        if (isset($filters['source'])) {
            $leads->where('source', $filters['source']);
        }

        if (isset($filters['assigned_agent_id'])) {
            $leads->where('assigned_agent_id', $filters['assigned_agent_id']);
        }

        if (isset($filters['date_from'])) {
            $leads->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $leads->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $leads->orderBy('created_at', 'desc')->paginate(20);
    }
}

