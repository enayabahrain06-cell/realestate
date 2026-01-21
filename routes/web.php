<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RealEstate\BuildingController;
use App\Http\Controllers\RealEstate\FloorController;
use App\Http\Controllers\RealEstate\UnitController;
use App\Http\Controllers\RealEstate\TenantController;
use App\Http\Controllers\RealEstate\LeaseController;
use App\Http\Controllers\RealEstate\BookingController;
use App\Http\Controllers\RealEstate\DashboardController;
use App\Http\Controllers\RealEstate\LeadController;
use App\Http\Controllers\RealEstate\AgentController;
use App\Http\Controllers\RealEstate\CommissionController;
use App\Http\Controllers\RealEstate\DocumentController;
use App\Http\Controllers\RealEstate\ReportController;
use App\Http\Controllers\RealEstate\AuditLogController;
use App\Http\Controllers\RealEstate\RoleController;
use App\Http\Controllers\RealEstate\EwaBillController;
use App\Http\Controllers\RealEstate\ExpenseController;
use App\Http\Controllers\RealEstate\UserController;

use Illuminate\Support\Facades\Route;

// Test route - remove after testing
Route::get('/test-dashboard', function () {
    $user = (object)[
        'full_name' => 'Test User',
        'gender' => 'm',
        'media_gallery' => [],
        'mobile' => '+1234567890',
        'email' => 'test@example.com',
        'age' => 35,
        'nationality' => 'USA',
        'horoscope' => 'Leo',
        'birthdate' => \Carbon\Carbon::now()->subYears(35)->subMonths(3),
        'created_at' => \Carbon\Carbon::now(),
    ];

    $dependents = collect([]);
    $familyInvoices = collect([]);

    return view('family.dashboard', compact('user', 'dependents', 'familyInvoices'));
});

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('real-estate.dashboard');
    }
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Note: Family and Invoice routes removed - controllers do not exist
// These routes were referencing FamilyController and InvoiceController which are not implemented
// Profile management is available via the standard ProfileController (profile.edit, profile.update)

Route::middleware(['auth', 'verified'])->group(function () {
    // Family Routes (using RealEstate\FamilyController)
    Route::get('/family/dashboard', [App\Http\Controllers\RealEstate\FamilyController::class, 'dashboard'])->name('family.dashboard');
    Route::get('/family/create', [App\Http\Controllers\RealEstate\FamilyController::class, 'create'])->name('family.create');
    Route::post('/family', [App\Http\Controllers\RealEstate\FamilyController::class, 'store'])->name('family.store');
    Route::get('/family/{id}', [App\Http\Controllers\RealEstate\FamilyController::class, 'show'])->name('family.show');
    Route::get('/family/{id}/edit', [App\Http\Controllers\RealEstate\FamilyController::class, 'edit'])->name('family.edit');
    Route::put('/family/{id}', [App\Http\Controllers\RealEstate\FamilyController::class, 'update'])->name('family.update');
    Route::delete('/family/{id}', [App\Http\Controllers\RealEstate\FamilyController::class, 'destroy'])->name('family.destroy');
    Route::get('/profile/show', [App\Http\Controllers\RealEstate\FamilyController::class, 'profile'])->name('profile.show');

    // Invoice Routes (using RealEstate\InvoiceController)
    Route::get('/invoices', [App\Http\Controllers\RealEstate\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{id}', [App\Http\Controllers\RealEstate\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{id}/pay', [App\Http\Controllers\RealEstate\InvoiceController::class, 'pay'])->name('invoices.pay');
    Route::get('/invoices/pay-all', [App\Http\Controllers\RealEstate\InvoiceController::class, 'payAll'])->name('invoices.pay-all');
});

// Real Estate Management System Routes
Route::prefix('real-estate')->name('real-estate.')->middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/available-units', [DashboardController::class, 'availableUnits'])->name('available-units');

    // Buildings
    Route::resource('buildings', BuildingController::class);

    // Floors (managed within buildings - no separate index)
    Route::resource('floors', FloorController::class)->except(['index']);
    Route::get('/buildings/{building}/floors/bulk-create', [FloorController::class, 'bulkCreate'])->name('floors.bulk-create');
    Route::post('/buildings/{building}/floors/bulk-store', [FloorController::class, 'bulkStore'])->name('floors.bulk-store');
    Route::post('/floors/{floor}/bulk-units', [FloorController::class, 'bulkCreateUnits'])->name('floors.bulk-units');

    // Units
    Route::resource('units', UnitController::class);
    Route::get('/buildings/{building}/floor/{floorNumber}', [UnitController::class, 'floorPlan'])->name('units.floor-plan');
    Route::post('/units/{unit}/lock', [UnitController::class, 'lock'])->name('units.lock');
    Route::post('/units/{unit}/unlock', [UnitController::class, 'unlock'])->name('units.unlock');
    Route::post('/units/bulk-create', [UnitController::class, 'bulkCreate'])->name('units.bulk-create');
    Route::post('/units/bulk-status', [UnitController::class, 'bulkStatusUpdate'])->name('units.bulk-status');
    Route::post('/units/bulk-rent', [UnitController::class, 'bulkRentIncrease'])->name('units.bulk-rent');

    // Tenants
    Route::resource('tenants', TenantController::class);

    // Leases
    Route::resource('leases', LeaseController::class);
    Route::post('/leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('leases.terminate');
    Route::post('/leases/bulk-rent', [LeaseController::class, 'bulkRent'])->name('leases.bulk-rent');

    // Bookings
    Route::resource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Leads (CRM)
    Route::resource('leads', LeadController::class);
    Route::post('/leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
    Route::post('/leads/{lead}/assign-agent', [LeadController::class, 'assignAgent'])->name('leads.assign-agent');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::post('/leads/{lead}/add-interaction', [LeadController::class, 'addInteraction'])->name('leads.add-interaction');
    Route::post('/leads/{lead}/create-reminder', [LeadController::class, 'createReminder'])->name('leads.create-reminder');
    Route::post('/leads/complete-reminder/{reminder}', [LeadController::class, 'completeReminder'])->name('leads.complete-reminder');

    // Agents
    Route::resource('agents', AgentController::class);
    Route::post('/agents/{agent}/assign-unit', [AgentController::class, 'assignUnit'])->name('agents.assign-unit');
    Route::delete('/agents/{agent}/remove-unit-assignment/{assignment}', [AgentController::class, 'removeUnitAssignment'])->name('agents.remove-unit-assignment');

    // Commissions
    Route::resource('commissions', CommissionController::class);
    Route::post('/commissions/{commission}/approve', [CommissionController::class, 'approve'])->name('commissions.approve');
    Route::post('/commissions/{commission}/pay', [CommissionController::class, 'pay'])->name('commissions.pay');
    Route::post('/commissions/{commission}/cancel', [CommissionController::class, 'cancel'])->name('commissions.cancel');

    // Documents
    Route::resource('documents', DocumentController::class);
    Route::post('/documents/{document}/version', [DocumentController::class, 'addVersion'])->name('documents.version');
    Route::get('/documents/download/{document}', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/expiring', [DocumentController::class, 'expiring'])->name('documents.expiring');
    Route::get('/documents/expired', [DocumentController::class, 'expired'])->name('documents.expired');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/agent-performance', [ReportController::class, 'agentPerformance'])->name('agent-performance');
        Route::get('/occupancy', [ReportController::class, 'occupancy'])->name('occupancy');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('/ewa', [ReportController::class, 'ewa'])->name('ewa');
        Route::get('/commissions', [ReportController::class, 'commissions'])->name('commissions');
        Route::get('/export-financial', [ReportController::class, 'exportFinancial'])->name('export-financial');
    });

    // EWA Bills
    Route::resource('ewa-bills', EwaBillController::class);

    // Expenses
    Route::resource('expenses', ExpenseController::class);

    // Admin Routes (Audit Logs & Roles - Admin only)
    Route::middleware(['auth', 'permission:audit-logs.view'])->group(function () {
        Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show']);
        Route::get('/audit-logs/user-activity/{user}', [AuditLogController::class, 'userActivity'])->name('audit-logs.user-activity');
        Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    });

    // User Management Routes
    Route::middleware(['auth', 'permission:users.view'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('users.activity');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    });

    // Admin Routes (Roles - Super Admin only)
    Route::middleware(['auth', 'role:super_admin'])->group(function () {
        Route::resource('roles', RoleController::class);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    });
});

require __DIR__.'/auth.php';
