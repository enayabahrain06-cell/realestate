<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Buildings
            ['name' => 'buildings.view', 'module' => 'Buildings', 'description' => 'View buildings'],
            ['name' => 'buildings.create', 'module' => 'Buildings', 'description' => 'Create buildings'],
            ['name' => 'buildings.edit', 'module' => 'Buildings', 'description' => 'Edit buildings'],
            ['name' => 'buildings.delete', 'module' => 'Buildings', 'description' => 'Delete buildings'],
            
            // Floors
            ['name' => 'floors.view', 'module' => 'Floors', 'description' => 'View floors'],
            ['name' => 'floors.create', 'module' => 'Floors', 'description' => 'Create floors'],
            ['name' => 'floors.edit', 'module' => 'Floors', 'description' => 'Edit floors'],
            ['name' => 'floors.delete', 'module' => 'Floors', 'description' => 'Delete floors'],
            
            // Units
            ['name' => 'units.view', 'module' => 'Units', 'description' => 'View units'],
            ['name' => 'units.create', 'module' => 'Units', 'description' => 'Create units'],
            ['name' => 'units.edit', 'module' => 'Units', 'description' => 'Edit units'],
            ['name' => 'units.delete', 'module' => 'Units', 'description' => 'Delete units'],
            ['name' => 'units.bulk_actions', 'module' => 'Units', 'description' => 'Perform bulk actions on units'],
            
            // Tenants
            ['name' => 'tenants.view', 'module' => 'Tenants', 'description' => 'View tenants'],
            ['name' => 'tenants.create', 'module' => 'Tenants', 'description' => 'Create tenants'],
            ['name' => 'tenants.edit', 'module' => 'Tenants', 'description' => 'Edit tenants'],
            ['name' => 'tenants.delete', 'module' => 'Tenants', 'description' => 'Delete tenants'],
            
            // Leases
            ['name' => 'leases.view', 'module' => 'Leases', 'description' => 'View leases'],
            ['name' => 'leases.create', 'module' => 'Leases', 'description' => 'Create leases'],
            ['name' => 'leases.edit', 'module' => 'Leases', 'description' => 'Edit leases'],
            ['name' => 'leases.delete', 'module' => 'Leases', 'description' => 'Delete leases'],
            ['name' => 'leases.renew', 'module' => 'Leases', 'description' => 'Renew leases'],
            ['name' => 'leases.terminate', 'module' => 'Leases', 'description' => 'Terminate leases'],
            
            // Payments
            ['name' => 'payments.view', 'module' => 'Payments', 'description' => 'View payments'],
            ['name' => 'payments.create', 'module' => 'Payments', 'description' => 'Create payments'],
            ['name' => 'payments.edit', 'module' => 'Payments', 'description' => 'Edit payments'],
            ['name' => 'payments.approve', 'module' => 'Payments', 'description' => 'Approve payments'],
            ['name' => 'payments.export', 'module' => 'Payments', 'description' => 'Export payments'],
            
            // EWA Bills
            ['name' => 'ewa.view', 'module' => 'EWA', 'description' => 'View EWA bills'],
            ['name' => 'ewa.create', 'module' => 'EWA', 'description' => 'Create EWA bills'],
            ['name' => 'ewa.edit', 'module' => 'EWA', 'description' => 'Edit EWA bills'],
            ['name' => 'ewa.delete', 'module' => 'EWA', 'description' => 'Delete EWA bills'],
            
            // Expenses
            ['name' => 'expenses.view', 'module' => 'Expenses', 'description' => 'View expenses'],
            ['name' => 'expenses.create', 'module' => 'Expenses', 'description' => 'Create expenses'],
            ['name' => 'expenses.edit', 'module' => 'Expenses', 'description' => 'Edit expenses'],
            ['name' => 'expenses.delete', 'module' => 'Expenses', 'description' => 'Delete expenses'],
            
            // Bookings
            ['name' => 'bookings.view', 'module' => 'Bookings', 'description' => 'View bookings'],
            ['name' => 'bookings.create', 'module' => 'Bookings', 'description' => 'Create bookings'],
            ['name' => 'bookings.edit', 'module' => 'Bookings', 'description' => 'Edit bookings'],
            ['name' => 'bookings.delete', 'module' => 'Bookings', 'description' => 'Delete bookings'],
            
            // Leads
            ['name' => 'leads.view', 'module' => 'Leads', 'description' => 'View leads'],
            ['name' => 'leads.create', 'module' => 'Leads', 'description' => 'Create leads'],
            ['name' => 'leads.edit', 'module' => 'Leads', 'description' => 'Edit leads'],
            ['name' => 'leads.delete', 'module' => 'Leads', 'description' => 'Delete leads'],
            ['name' => 'leads.convert', 'module' => 'Leads', 'description' => 'Convert leads to tenants'],
            ['name' => 'leads.assign', 'module' => 'Leads', 'description' => 'Assign leads to agents'],
            
            // Agents
            ['name' => 'agents.view', 'module' => 'Agents', 'description' => 'View agents'],
            ['name' => 'agents.create', 'module' => 'Agents', 'description' => 'Create agents'],
            ['name' => 'agents.edit', 'module' => 'Agents', 'description' => 'Edit agents'],
            ['name' => 'agents.delete', 'module' => 'Agents', 'description' => 'Delete agents'],
            
            // Commissions
            ['name' => 'commissions.view', 'module' => 'Commissions', 'description' => 'View commissions'],
            ['name' => 'commissions.create', 'module' => 'Commissions', 'description' => 'Create commissions'],
            ['name' => 'commissions.approve', 'module' => 'Commissions', 'description' => 'Approve commissions'],
            ['name' => 'commissions.pay', 'module' => 'Commissions', 'description' => 'Pay commissions'],
            ['name' => 'commissions.export', 'module' => 'Commissions', 'description' => 'Export commissions'],
            
            // Documents
            ['name' => 'documents.view', 'module' => 'Documents', 'description' => 'View documents'],
            ['name' => 'documents.create', 'module' => 'Documents', 'description' => 'Upload documents'],
            ['name' => 'documents.edit', 'module' => 'Documents', 'description' => 'Edit documents'],
            ['name' => 'documents.delete', 'module' => 'Documents', 'description' => 'Delete documents'],
            ['name' => 'documents.download', 'module' => 'Documents', 'description' => 'Download documents'],
            
            // Reports
            ['name' => 'reports.view', 'module' => 'Reports', 'description' => 'View reports'],
            ['name' => 'reports.export', 'module' => 'Reports', 'description' => 'Export reports'],
            ['name' => 'reports.financial', 'module' => 'Reports', 'description' => 'View financial reports'],
            
            // Audit Logs
            ['name' => 'audit-logs.view', 'module' => 'Audit Logs', 'description' => 'View audit logs'],
            ['name' => 'audit-logs.export', 'module' => 'Audit Logs', 'description' => 'Export audit logs'],
            ['name' => 'audit-logs.cleanup', 'module' => 'Audit Logs', 'description' => 'Cleanup old logs'],
            
            // Roles & Permissions
            ['name' => 'roles.view', 'module' => 'Roles', 'description' => 'View roles'],
            ['name' => 'roles.create', 'module' => 'Roles', 'description' => 'Create roles'],
            ['name' => 'roles.edit', 'module' => 'Roles', 'description' => 'Edit roles'],
            ['name' => 'roles.delete', 'module' => 'Roles', 'description' => 'Delete roles'],
            ['name' => 'roles.assign', 'module' => 'Roles', 'description' => 'Assign roles to users'],
            
            // Users
            ['name' => 'users.view', 'module' => 'Users', 'description' => 'View users'],
            ['name' => 'users.create', 'module' => 'Users', 'description' => 'Create users'],
            ['name' => 'users.edit', 'module' => 'Users', 'description' => 'Edit users'],
            ['name' => 'users.delete', 'module' => 'Users', 'description' => 'Delete users'],
            
            // Settings
            ['name' => 'settings.view', 'module' => 'Settings', 'description' => 'View settings'],
            ['name' => 'settings.edit', 'module' => 'Settings', 'description' => 'Edit settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                [
                    'name' => $permission['name'],
                    'display_name' => ucfirst(str_replace('.', ' ', $permission['name'])),
                    'module' => $permission['module'],
                    'description' => $permission['description'],
                    'action' => explode('.', $permission['name'])[1] ?? 'view',
                    'is_active' => true
                ]
            );
        }

        $this->command->info('Permissions seeded successfully.');
    }
}

