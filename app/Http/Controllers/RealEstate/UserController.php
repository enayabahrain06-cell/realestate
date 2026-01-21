<?php

namespace App\Http\Controllers\RealEstate;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\RealEstate\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->whereHas('realEstateRoles', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        // Filter by status (has email verification)
        if ($request->has('status') && $request->status) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('full_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('mobile', 'like', "%{$request->search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $users = $query->with('realEstateRoles')
            ->withCount('realEstateRoles')
            ->paginate(20);

        return view('real-estate.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        return view('real-estate.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'nullable|string|in:m,f',
            'birthdate' => 'nullable|date',
            'blood_type' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:100',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:real_estate_roles,id',
            'send_welcome_email' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'birthdate' => $validated['birthdate'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
        ]);

        // Assign roles
        if (isset($validated['roles'])) {
            $user->realEstateRoles()->sync($validated['roles']);
        }

        // Log activity
        $this->auditService->log([
            'action' => 'user_created',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => 'User created: ' . $user->email,
            'metadata' => [
                'email' => $user->email,
                'roles_assigned' => $user->realEstateRoles->pluck('name')->toArray(),
            ]
        ]);

        return redirect()->route('real-estate.users.show', $user)
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['realEstateRoles', 'auditLogs' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(20);
        }]);

        return view('real-estate.users.show', compact('user'));
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        $user->load('realEstateRoles');
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        $userRoleIds = $user->realEstateRoles->pluck('id')->toArray();

        return view('real-estate.users.edit', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'gender' => 'nullable|string|in:m,f',
            'birthdate' => 'nullable|date',
            'blood_type' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:100',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:real_estate_roles,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'birthdate' => $validated['birthdate'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Update roles
        if (isset($validated['roles'])) {
            $user->realEstateRoles()->sync($validated['roles']);
        } else {
            $user->realEstateRoles()->detach();
        }

        // Log activity
        $this->auditService->log([
            'action' => 'user_updated',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => 'User updated: ' . $user->email,
            'metadata' => [
                'email' => $user->email,
                'roles_assigned' => $user->realEstateRoles->pluck('name')->toArray(),
            ]
        ]);

        return redirect()->route('real-estate.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->route('real-estate.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting super admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('real-estate.users.index')
                ->with('error', 'Cannot delete super admin account.');
        }

        $email = $user->email;

        // Log activity before deletion
        $this->auditService->log([
            'action' => 'user_deleted',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => 'User deleted: ' . $email,
            'metadata' => [
                'email' => $email,
            ]
        ]);

        // Detach roles
        $user->realEstateRoles()->detach();

        // Delete user
        $user->delete();

        return redirect()->route('real-estate.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user status (activate/deactivate).
     */
    public function toggleStatus(User $user)
    {
        // Prevent self-deactivation
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        // Prevent deactivating super admin
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot deactivate super admin account.');
        }

        $status = $user->email_verified_at ? 'deactivated' : 'activated';
        $user->email_verified_at = $user->email_verified_at ? null : now();
        $user->save();

        // Log activity
        $this->auditService->log([
            'action' => 'user_status_changed',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => 'User ' . $status . ': ' . $user->email,
            'metadata' => [
                'action' => $status,
                'email' => $user->email,
            ]
        ]);

        return back()->with('success', "User {$status} successfully.");
    }

    /**
     * Get user activity logs.
     */
    public function activity(Request $request, User $user)
    {
        $days = $request->get('days', 30);
        $activity = $this->auditService->getUserActivity($user->id, $days);

        return view('real-estate.users.activity', compact('user', 'activity', 'days'));
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Apply filters
        if ($request->has('role') && $request->role) {
            $query->whereHas('realEstateRoles', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        if ($request->has('status') && $request->status) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->with('realEstateRoles')->get();

        // Generate CSV
        $csv = \League\Csv\Writer::createFromString('');
        $csv->insertOne([
            'ID', 'Name', 'Full Name', 'Email', 'Mobile', 'Gender',
            'Birthdate', 'Nationality', 'Roles', 'Email Verified', 'Created At'
        ]);

        foreach ($users as $user) {
            $csv->insertOne([
                $user->id,
                $user->name,
                $user->full_name ?? '',
                $user->email,
                $user->mobile ?? '',
                $user->gender ?? '',
                $user->birthdate ? $user->birthdate->format('Y-m-d') : '',
                $user->nationality ?? '',
                $user->realEstateRoles->pluck('name')->implode(', '),
                $user->email_verified_at ? 'Yes' : 'No',
                $user->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        return response((string) $csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="users_' . date('Y-m-d') . '.csv"');
    }
}

