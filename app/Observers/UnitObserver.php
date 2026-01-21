<?php

namespace App\Observers;

use App\Models\Unit;
use App\Models\UnitHistory;

class UnitObserver
{
    /**
     * Handle the Unit "created" event.
     */
    public function created(Unit $unit): void
    {
        // Log unit creation
        UnitHistory::create([
            'unit_id' => $unit->id,
            'action' => UnitHistory::ACTION_FEATURE_UPDATE,
            'previous_rent_amount' => null,
            'new_rent_amount' => $unit->rent_amount,
            'previous_status' => null,
            'new_status' => $unit->status,
            'change_reason' => 'Unit created',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    /**
     * Handle the Unit "updated" event.
     */
    public function updated(Unit $unit): void
    {
        $original = $unit->getOriginal();
        $changes = $unit->getChanges();

        // Track rent changes
        if (array_key_exists('rent_amount', $changes)) {
            UnitHistory::create([
                'unit_id' => $unit->id,
                'action' => UnitHistory::ACTION_RENT_CHANGE,
                'previous_rent_amount' => $original['rent_amount'],
                'new_rent_amount' => $changes['rent_amount'],
                'previous_status' => $original['status'] ?? null,
                'new_status' => $unit->status,
                'change_reason' => 'Rent amount updated',
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        }

        // Track status changes
        if (array_key_exists('status', $changes)) {
            $action = match ($changes['status']) {
                Unit::STATUS_MAINTENANCE => UnitHistory::ACTION_MAINTENANCE,
                default => UnitHistory::ACTION_STATUS_CHANGE,
            };

            UnitHistory::create([
                'unit_id' => $unit->id,
                'action' => $action,
                'previous_rent_amount' => $unit->rent_amount,
                'new_rent_amount' => $unit->rent_amount,
                'previous_status' => $original['status'] ?? null,
                'new_status' => $changes['status'],
                'change_reason' => 'Status updated',
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        }

        // Track feature/other updates
        if (empty(array_intersect(array_keys($changes), ['rent_amount', 'status']))) {
            UnitHistory::create([
                'unit_id' => $unit->id,
                'action' => UnitHistory::ACTION_FEATURE_UPDATE,
                'previous_rent_amount' => $unit->rent_amount,
                'new_rent_amount' => $unit->rent_amount,
                'previous_status' => $unit->status,
                'new_status' => $unit->status,
                'change_reason' => 'Unit details updated',
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Unit "deleted" event.
     */
    public function deleted(Unit $unit): void
    {
        //
    }

    /**
     * Handle the Unit "restored" event.
     */
    public function restored(Unit $unit): void
    {
        //
    }

    /**
     * Handle the Unit "force deleted" event.
     */
    public function forceDeleted(Unit $unit): void
    {
        //
    }

    /**
     * Handle bulk updates from controller methods.
     */
    public static function logBulkUpdate(int $unitId, string $action, array $changes, ?string $reason = null): void
    {
        $unit = Unit::find($unitId);
        if (!$unit) {
            return;
        }

        UnitHistory::create([
            'unit_id' => $unitId,
            'action' => $action,
            'previous_rent_amount' => $changes['previous_rent_amount'] ?? $unit->rent_amount,
            'new_rent_amount' => $changes['new_rent_amount'] ?? $unit->rent_amount,
            'previous_status' => $changes['previous_status'] ?? $unit->status,
            'new_status' => $changes['new_status'] ?? $unit->status,
            'change_reason' => $reason ?? 'Bulk update',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }
}

