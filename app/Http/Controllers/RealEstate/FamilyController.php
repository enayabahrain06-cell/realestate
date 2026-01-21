<?php

namespace App\Http\Controllers\RealEstate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Support\Facades\Auth;

class FamilyController extends Controller
{
    /**
     * Display the family dashboard.
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        
        // Get all dependents (relationships where user is guardian)
        $relationships = $user->dependents()->with('dependent')->get();
        $dependents = $relationships->map(function ($relationship) {
            return $relationship->dependent;
        });
        
        // Get invoices for the user and their dependents
        $userInvoices = $user->payerInvoices()->with('student')->get();
        $familyInvoices = $userInvoices->merge(
            $user->dependents()->with('dependent.payerInvoices')->get()
                ->pluck('dependent')->filter()
                ->flatMap(function ($dependent) {
                    return $dependent->payerInvoices ?? collect([]);
                })
        )->unique('id');

        return view('real-estate.family.dashboard', compact('user', 'dependents', 'familyInvoices'));
    }

    /**
     * Display the user's profile.
     */
    public function profile(Request $request): View
    {
        $user = $request->user();
        
        // Get the relationship for the user
        $relationship = $user->guardians()->with('guardian')->first();
        
        return view('real-estate.family.profile', compact('user', 'relationship'));
    }

    /**
     * Show the form for creating a new family member.
     */
    public function create(Request $request): View
    {
        return view('real-estate.family.create', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Store a newly created family member.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'relationship_type' => ['required', 'string'],
            'is_billing_contact' => ['boolean'],
        ]);

        // Find the dependent user by email
        $dependent = User::where('email', $validated['email'])->firstOrFail();

        // Create the relationship
        UserRelationship::create([
            'guardian_user_id' => $request->user()->id,
            'dependent_user_id' => $dependent->id,
            'relationship_type' => $validated['relationship_type'],
            'is_billing_contact' => $validated['is_billing_contact'] ?? false,
        ]);

        return redirect()->route('family.dashboard')
            ->with('status', 'family-member-added');
    }

    /**
     * Display the specified family member.
     */
    public function show(Request $request, string $id): View
    {
        $user = $request->user();
        $relationship = UserRelationship::where('guardian_user_id', $user->id)
            ->where('id', $id)
            ->with('dependent')
            ->firstOrFail();

        return view('real-estate.family.show', [
            'user' => $user,
            'relationship' => $relationship,
            'dependent' => $relationship->dependent,
        ]);
    }

    /**
     * Show the form for editing the specified family member.
     */
    public function edit(Request $request, string $id): View
    {
        $user = $request->user();
        $relationship = UserRelationship::where('guardian_user_id', $user->id)
            ->where('id', $id)
            ->with('dependent')
            ->firstOrFail();

        return view('real-estate.family.edit', [
            'user' => $user,
            'relationship' => $relationship,
            'dependent' => $relationship->dependent,
        ]);
    }

    /**
     * Update the specified family member.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'relationship_type' => ['required', 'string'],
            'is_billing_contact' => ['boolean'],
        ]);

        $user = $request->user();
        $relationship = UserRelationship::where('guardian_user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $relationship->update([
            'relationship_type' => $validated['relationship_type'],
            'is_billing_contact' => $validated['is_billing_contact'] ?? false,
        ]);

        return redirect()->route('family.show', $id)
            ->with('status', 'family-member-updated');
    }

    /**
     * Remove the specified family member.
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();
        $relationship = UserRelationship::where('guardian_user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $relationship->delete();

        return redirect()->route('family.dashboard')
            ->with('status', 'family-member-deleted');
    }
}

