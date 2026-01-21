<?php

namespace App\Services\RealEstate;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\CommissionRule;
use App\Models\Lease;
use App\Models\AgentUnitAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Commission types
     */
    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_TIERED = 'tiered';

    /**
     * Commission status
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Create a commission rule
     */
    public function createCommissionRule(array $data): CommissionRule
    {
        return CommissionRule::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'value' => $data['value'],
            'min_deal_amount' => $data['min_deal_amount'] ?? null,
            'max_deal_amount' => $data['max_deal_amount'] ?? null,
            'property_type' => $data['property_type'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Calculate commission for a deal
     */
    public function calculateCommission(int $agentId, float $dealAmount, string $propertyType = null): float
    {
        $rule = $this->getApplicableRule($dealAmount, $propertyType);

        if (!$rule) {
            return 0;
        }

        return $this->calculateByRule($rule, $dealAmount);
    }

    /**
     * Get applicable commission rule for a deal
     */
    public function getApplicableRule(float $dealAmount, string $propertyType = null): ?CommissionRule
    {
        return CommissionRule::where('is_active', true)
            ->where(function ($query) use ($dealAmount) {
                $query->whereNull('min_deal_amount')
                      ->orWhere('min_deal_amount', '<=', $dealAmount);
            })
            ->where(function ($query) use ($dealAmount) {
                $query->whereNull('max_deal_amount')
                      ->orWhere('max_deal_amount', '>=', $dealAmount);
            })
            ->when($propertyType, function ($query) use ($propertyType) {
                return $query->where(function ($q) use ($propertyType) {
                    $q->whereNull('property_type')
                      ->orWhere('property_type', $propertyType);
                });
            })
            ->orderBy('min_deal_amount', 'desc')
            ->first();
    }

    /**
     * Calculate commission value based on rule
     */
    protected function calculateByRule(CommissionRule $rule, float $dealAmount): float
    {
        switch ($rule->type) {
            case self::TYPE_FIXED:
                return (float) $rule->value;
            
            case self::TYPE_PERCENTAGE:
                return ($dealAmount * $rule->value) / 100;
            
            case self::TYPE_TIERED:
                return $this->calculateTieredCommission($dealAmount, $rule);
            
            default:
                return 0;
        }
    }

    /**
     * Calculate tiered commission
     */
    protected function calculateTieredCommission(float $dealAmount, CommissionRule $rule): float
    {
        // For tiered, we use the value as a percentage
        // In a more complex implementation, you'd have tiered rules
        return ($dealAmount * $rule->value) / 100;
    }

    /**
     * Create a commission record
     */
    public function createCommission(int $agentId, float $dealAmount, int $leaseId, array $data = []): Commission
    {
        $calculatedAmount = $this->calculateCommission(
            $agentId, 
            $dealAmount, 
            $data['property_type'] ?? null
        );

        return DB::transaction(function () use ($agentId, $dealAmount, $leaseId, $calculatedAmount, $data) {
            $lease = Lease::with('unit.building')->findOrFail($leaseId);

            $commission = Commission::create([
                'agent_id' => $agentId,
                'lease_id' => $leaseId,
                'deal_amount' => $dealAmount,
                'commission_amount' => $calculatedAmount,
                'type' => $data['type'] ?? 'rental',
                'property_type' => $data['property_type'] ?? $lease->unit->unit_type ?? null,
                'building_id' => $lease->unit->building->id ?? null,
                'rule_id' => $data['rule_id'] ?? null,
                'status' => self::STATUS_PENDING,
                'due_date' => $data['due_date'] ?? now()->addDays(30),
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id()
            ]);

            return $commission;
        });
    }

    /**
     * Approve a commission
     */
    public function approveCommission(int $commissionId): Commission
    {
        $commission = Commission::findOrFail($commissionId);
        
        $commission->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return $commission;
    }

    /**
     * Pay a commission
     */
    public function payCommission(int $commissionId, string $reference = null): Commission
    {
        return DB::transaction(function () use ($commissionId, $reference) {
            $commission = Commission::findOrFail($commissionId);
            
            $commission->update([
                'status' => self::STATUS_PAID,
                'paid_at' => now(),
                'payment_reference' => $reference,
                'paid_by' => auth()->id()
            ]);

            // Update agent's total earnings
            $commission->agent->update([
                'total_commissions_earned' => $commission->agent->total_commissions_earned + $commission->commission_amount,
                'pending_commissions' => $commission->agent->pending_commissions - $commission->commission_amount
            ]);

            return $commission;
        });
    }

    /**
     * Cancel a commission
     */
    public function cancelCommission(int $commissionId, string $reason): Commission
    {
        $commission = Commission::findOrFail($commissionId);
        
        $commission->update([
            'status' => self::STATUS_CANCELLED,
            'cancellation_reason' => $reason,
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now()
        ]);

        return $commission;
    }

    /**
     * Get agent performance report
     */
    public function getAgentPerformanceReport(int $agentId, $period = 'month'): array
    {
        $agent = Agent::findOrFail($agentId);
        
        $startDate = $this->getPeriodStartDate($period);

        $commissions = Commission::where('agent_id', $agentId)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where('created_at', '>=', $startDate)
            ->get();

        return [
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'email' => $agent->email,
                'phone' => $agent->phone
            ],
            'period' => $period,
            'from_date' => $startDate,
            'to_date' => now(),
            'summary' => [
                'total_deals' => $commissions->count(),
                'total_deal_value' => $commissions->sum('deal_amount'),
                'total_commissions_earned' => $commissions->where('status', self::STATUS_PAID)->sum('commission_amount'),
                'pending_commissions' => $commissions->where('status', self::STATUS_PENDING)->sum('commission_amount'),
                'approved_commissions' => $commissions->where('status', self::STATUS_APPROVED)->sum('commission_amount'),
                'average_commission_per_deal' => $commissions->avg('commission_amount')
            ],
            'by_status' => [
                'pending' => $commissions->where('status', self::STATUS_PENDING)->count(),
                'approved' => $commissions->where('status', self::STATUS_APPROVED)->count(),
                'paid' => $commissions->where('status', self::STATUS_PAID)->count(),
                'cancelled' => $commissions->where('status', self::STATUS_CANCELLED)->count()
            ],
            'by_property_type' => $commissions->groupBy('property_type')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'value' => $items->sum('deal_amount'),
                        'commission' => $items->sum('commission_amount')
                    ];
                })
                ->toArray(),
            'monthly_breakdown' => $this->getMonthlyBreakdown($agentId, $startDate)
        ];
    }

    /**
     * Get top performing agents
     */
    public function getTopPerformers(int $limit = 10, $period = 'month'): array
    {
        $startDate = $this->getPeriodStartDate($period);

        return Agent::where('is_active', true)
            ->withSum(['commissions' => function ($query) use ($startDate) {
                $query->where('status', self::STATUS_PAID)
                      ->where('created_at', '>=', $startDate);
            }], 'commission_amount')
            ->orderByDesc('commissions_sum_commission_amount')
            ->limit($limit)
            ->get()
            ->map(function ($agent) {
                return [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'email' => $agent->email,
                    'phone' => $agent->phone,
                    'total_earned' => $agent->commissions_sum_commission_amount ?? 0,
                    'deals_closed' => $agent->commissions()->where('status', self::STATUS_PAID)->count()
                ];
            })
            ->toArray();
    }

    /**
     * Get agent's pending commissions
     */
    public function getAgentPendingCommissions(int $agentId)
    {
        return Commission::where('agent_id', $agentId)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED])
            ->with('lease.unit.building')
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Get all pending commissions for approval
     */
    public function getPendingApprovals()
    {
        return Commission::where('status', self::STATUS_PENDING)
            ->with('agent', 'lease.unit.building')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Assign a unit to an agent
     */
    public function assignUnit(int $agentId, int $unitId, string $type = 'exclusive'): AgentUnitAssignment
    {
        return AgentUnitAssignment::create([
            'agent_id' => $agentId,
            'unit_id' => $unitId,
            'type' => $type,
            'assigned_at' => now(),
            'assigned_by' => auth()->id()
        ]);
    }

    /**
     * Remove unit assignment from agent
     */
    public function removeUnitAssignment(int $assignmentId): bool
    {
        return AgentUnitAssignment::findOrFail($assignmentId)->delete();
    }

    /**
     * Get units assigned to an agent
     */
    public function getAgentUnits(int $agentId)
    {
        return AgentUnitAssignment::where('agent_id', $agentId)
            ->with('unit.building')
            ->get();
    }

    /**
     * Calculate commission summary for an agent
     */
    public function getAgentCommissionSummary(int $agentId, $period = null)
    {
        $query = Commission::where('agent_id', $agentId)
            ->where('status', '!=', self::STATUS_CANCELLED);

        if ($period) {
            $query->where('created_at', '>=', $this->getPeriodStartDate($period));
        }

        $commissions = $query->get();

        return [
            'total_commissions' => $commissions->count(),
            'total_earned' => $commissions->where('status', self::STATUS_PAID)->sum('commission_amount'),
            'pending' => $commissions->where('status', self::STATUS_PENDING)->sum('commission_amount'),
            'approved' => $commissions->where('status', self::STATUS_APPROVED)->sum('commission_amount'),
            'average' => $commissions->avg('commission_amount')
        ];
    }

    /**
     * Generate monthly breakdown for agent
     */
    protected function getMonthlyBreakdown(int $agentId, $startDate)
    {
        $data = [];

        $commissions = Commission::where('agent_id', $agentId)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($commission) {
                return $commission->created_at->format('Y-m');
            });

        foreach ($commissions as $month => $monthlyCommissions) {
            $data[$month] = [
                'count' => $monthlyCommissions->count(),
                'deal_value' => $monthlyCommissions->sum('deal_amount'),
                'commission' => $monthlyCommissions->sum('commission_amount')
            ];
        }

        return $data;
    }

    /**
     * Get period start date
     */
    protected function getPeriodStartDate($period): Carbon
    {
        switch ($period) {
            case 'week':
                return now()->startOfWeek();
            case 'month':
                return now()->startOfMonth();
            case 'quarter':
                return now()->startOfQuarter();
            case 'year':
                return now()->startOfYear();
            default:
                return now()->subMonth();
        }
    }

    /**
     * Auto-calculate and create commission for a new lease
     */
    public function autoCalculateForLease(int $leaseId): ?Commission
    {
        $lease = Lease::with('unit.building')->findOrFail($leaseId);
        
        // Check if the unit has an assigned agent
        $assignment = AgentUnitAssignment::where('unit_id', $lease->unit_id)
            ->where('type', '!=', 'none')
            ->first();

        if (!$assignment) {
            return null;
        }

        return $this->createCommission(
            $assignment->agent_id,
            $lease->rent_amount,
            $leaseId,
            [
                'property_type' => $lease->unit->unit_type,
                'type' => 'rental'
            ]
        );
    }

    /**
     * Get commission statistics
     */
    public function getCommissionStats($startDate, $endDate): array
    {
        $commissions = Commission::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->get();

        return [
            'total_commissions' => $commissions->count(),
            'total_value' => $commissions->sum('commission_amount'),
            'by_status' => [
                'pending' => $commissions->where('status', self::STATUS_PENDING)->sum('commission_amount'),
                'approved' => $commissions->where('status', self::STATUS_APPROVED)->sum('commission_amount'),
                'paid' => $commissions->where('status', self::STATUS_PAID)->sum('commission_amount'),
            ],
            'by_agent' => $commissions->groupBy('agent_id')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'amount' => $items->sum('commission_amount')
                    ];
                })
                ->toArray()
        ];
    }
}

