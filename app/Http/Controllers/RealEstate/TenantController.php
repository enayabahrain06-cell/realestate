<?php

namespace App\Http\Controllers\RealEstate;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['leases.unit.building']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $tenants = $query->paginate(15);

        return view('real-estate.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('real-estate.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'required|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'id_type' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'employer' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blacklisted'
        ]);

        $tenant = Tenant::create($validated);

        return redirect()->route('real-estate.tenants.show', $tenant)
            ->with('success', 'Tenant created successfully!');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['leases.unit.building', 'payments', 'bookings']);

        $activeLeases = $tenant->leases()->where('status', 'active')->get();
        $totalPaid = $tenant->payments()->where('status', 'completed')->sum('amount');

        return view('real-estate.tenants.show', compact('tenant', 'activeLeases', 'totalPaid'));
    }

    public function edit(Tenant $tenant)
    {
        return view('real-estate.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'required|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'id_type' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'employer' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blacklisted'
        ]);

        $tenant->update($validated);

        return redirect()->route('real-estate.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->leases()->where('status', 'active')->exists()) {
            return redirect()->route('real-estate.tenants.index')
                ->with('error', 'Cannot delete tenant with active leases.');
        }

        $tenant->delete();

        return redirect()->route('real-estate.tenants.index')
            ->with('success', 'Tenant deleted successfully!');
    }
}

