<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Get permission IDs from permission names, filtering out invalid ones.
     */
    private function getPermissionIds(array $permissionNames, \Illuminate\Database\Eloquent\Collection $allPermissions): array
    {
        $permissionMap = $allPermissions->pluck('id', 'name')->toArray();
        return array_filter(array_map(function ($name) use ($permissionMap) {
            return $permissionMap[$name] ?? null;
        }, $permissionNames));
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all permissions
        $allPermissions = Permission::all();

        // Super Admin role
        $superAdmin = Role::updateOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Admin',
                'description' => 'Full system access with all permissions'
            ]
        );
        $superAdmin->permissions()->sync($allPermissions);

        // Property Manager role
        $propertyManager = Role::updateOrCreate(
            ['name' => 'property_manager'],
            [
                'display_name' => 'Property Manager',
                'description' => 'Can manage properties, units, tenants, leases, and view reports'
            ]
        );
        $propertyManager->permissions()->sync(
            $this->getPermissionIds([
                'buildings.view', 'buildings.create', 'buildings.edit',
                'floors.view', 'floors.create', 'floors.edit',
                'units.view', 'units.create', 'units.edit', 'units.bulk_actions',
                'tenants.view', 'tenants.create', 'tenants.edit',
                'leases.view', 'leases.create', 'leases.edit',
                'payments.view', 'payments.create',
                'ewa.view', 'ewa.create', 'ewa.edit',
                'expenses.view', 'expenses.create', 'expenses.edit',
                'bookings.view', 'bookings.create', 'bookings.edit',
                'documents.view', 'documents.create', 'documents.edit',
                'reports.view', 'reports.financial',
                'audit-logs.view',
            ], $allPermissions)
        );

        // Accountant role
        $accountant = Role::updateOrCreate(
            ['name' => 'accountant'],
            [
                'display_name' => 'Accountant',
                'description' => 'Can manage payments, expenses, and view financial reports'
            ]
        );
        $accountant->permissions()->sync(
            $this->getPermissionIds([
                'payments.view', 'payments.create', 'payments.edit', 'payments.approve', 'payments.export',
                'ewa.view', 'ewa.create', 'ewa.edit',
                'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete',
                'commissions.view', 'commissions.approve', 'commissions.pay',
                'reports.view', 'reports.export', 'reports.financial',
            ], $allPermissions)
        );

        // Agent role
        $agent = Role::updateOrCreate(
            ['name' => 'agent'],
            [
                'display_name' => 'Agent',
                'description' => 'Can manage leads, view units, and track commissions'
            ]
        );
        $agent->permissions()->sync(
            $this->getPermissionIds([
                'buildings.view', 'units.view',
                'leads.view', 'leads.create', 'leads.edit', 'leads.assign',
                'tenants.view', 'commissions.view',
                'documents.view', 'documents.create',
            ], $allPermissions)
        );

        // Viewer role (read-only)
        $viewer = Role::updateOrCreate(
            ['name' => 'viewer'],
            [
                'display_name' => 'Viewer',
                'description' => 'Read-only access to view data'
            ]
        );
        $viewer->permissions()->sync(
            $this->getPermissionIds([
                'buildings.view', 'floors.view', 'units.view',
                'tenants.view', 'leases.view',
                'payments.view', 'ewa.view', 'expenses.view',
                'bookings.view', 'leads.view',
                'documents.view', 'documents.download',
            ], $allPermissions)
        );

        // Tenant role (limited access)
        $tenant = Role::updateOrCreate(
            ['name' => 'tenant'],
            [
                'display_name' => 'Tenant',
                'description' => 'Can view their own lease and payment information'
            ]
        );
        $tenant->permissions()->sync(
            $this->getPermissionIds([
                'leases.view', 'payments.view',
                'documents.view', 'documents.download',
            ], $allPermissions)
        );

        $this->command->info('Roles seeded successfully:');
        $this->command->info('- Super Admin (all permissions)');
        $this->command->info('- Property Manager');
        $this->command->info('- Accountant');
        $this->command->info('- Agent');
        $this->command->info('- Viewer');
        $this->command->info('- Tenant');
    }
}

