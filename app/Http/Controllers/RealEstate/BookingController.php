<?php

namespace App\Http\Controllers\RealEstate;

use App\Models\Booking;
use App\Models\Unit;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['tenant', 'unit.building']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->booking_type) {
            $query->where('booking_type', $request->booking_type);
        }

        if ($request->unit_id) {
            $query->where('unit_id', $request->unit_id);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->paginate(15);

        return view('real-estate.bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $unitId = $request->unit_id;
        $unit = null;
        
        if ($unitId) {
            $unit = Unit::with(['building', 'floor'])->findOrFail($unitId);
            
            if (!$unit->isAvailable()) {
                return redirect()->route('real-estate.units.show', $unit)
                    ->with('error', 'This unit is not available for booking.');
            }
        }

        $tenants = Tenant::where('status', 'active')->get();
        $units = Unit::where('status', Unit::STATUS_AVAILABLE)->with(['building', 'floor'])->get();

        return view('real-estate.bookings.create', compact('tenants', 'units', 'unit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'unit_id' => 'required|exists:units,id',
            'booking_type' => 'required|in:inquiry,viewing,reservation,rental',
            'booking_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);

        // Check unit availability
        if (!$unit->isAvailable()) {
            return back()->withErrors(['unit_id' => 'This unit is not available for booking.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $request, $unit) {
            $booking = Booking::create([
                'tenant_id' => $validated['tenant_id'],
                'unit_id' => $validated['unit_id'],
                'booking_type' => $validated['booking_type'],
                'booking_date' => $validated['booking_date'],
                'notes' => $validated['notes'],
                'status' => Booking::STATUS_PENDING,
                'ip_address' => $request->ip(),
                'session_id' => session()->getId()
            ]);

            // If reservation, lock the unit temporarily
            if ($validated['booking_type'] === Booking::TYPE_RESERVATION) {
                $unit->lock(session()->getId() ?? Str::random(40), 1440); // 24 hours
            }
        });

        return redirect()->route('real-estate.bookings.index')
            ->with('success', 'Booking created successfully!');
    }

    public function show(Booking $booking)
    {
        $booking->load(['tenant', 'unit.building', 'unit.floor']);

        return view('real-estate.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $booking->load(['unit']);
        $tenants = Tenant::where('status', 'active')->get();
        $units = Unit::with(['building', 'floor'])->get();

        return view('real-estate.bookings.edit', compact('booking', 'tenants', 'units'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'unit_id' => 'required|exists:units,id',
            'booking_type' => 'required|in:inquiry,viewing,reservation,rental',
            'booking_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $oldUnitId = $booking->unit_id;
        $newUnit = Unit::findOrFail($validated['unit_id']);

        DB::transaction(function () use ($validated, $request, $booking, $oldUnitId, $newUnit) {
            // Unlock old unit if booking type was reservation
            if ($booking->booking_type === Booking::TYPE_RESERVATION && $oldUnitId != $newUnit->id) {
                $oldUnit = Unit::find($oldUnitId);
                if ($oldUnit) {
                    $oldUnit->unlock();
                }
            }

            $booking->update([
                'tenant_id' => $validated['tenant_id'],
                'unit_id' => $validated['unit_id'],
                'booking_type' => $validated['booking_type'],
                'booking_date' => $validated['booking_date'],
                'notes' => $validated['notes'],
                'status' => $validated['status']
            ]);

            // Lock new unit if booking type is reservation
            if ($validated['booking_type'] === Booking::TYPE_RESERVATION) {
                $newUnit->lock(session()->getId() ?? Str::random(40), 1440);
            }
        });

        return redirect()->route('real-estate.bookings.show', $booking)
            ->with('success', 'Booking updated successfully!');
    }

    public function destroy(Booking $booking)
    {
        // Cancel the booking first (which unlocks units if needed)
        $booking->cancel();

        $booking->delete();

        return redirect()->route('real-estate.bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }

    /**
     * Confirm a booking
     */
    public function confirm(Booking $booking)
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        $booking->update(['status' => Booking::STATUS_CONFIRMED]);

        // If rental, create lease
        if ($booking->booking_type === Booking::TYPE_RENTAL) {
            return redirect()->route('real-estate.leases.create', ['unit_id' => $booking->unit_id])
                ->with('info', 'Booking confirmed! Please create a lease for this rental.');
        }

        return back()->with('success', 'Booking confirmed successfully!');
    }

    /**
     * Complete a booking (e.g., after viewing)
     */
    public function complete(Booking $booking)
    {
        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])) {
            return back()->with('error', 'This booking cannot be completed.');
        }

        $booking->update(['status' => Booking::STATUS_COMPLETED]);

        return back()->with('success', 'Booking completed!');
    }

    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking)
    {
        if (!$booking->canCancel()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->cancel();

        return back()->with('success', 'Booking cancelled successfully!');
    }
}

