<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('Admin user created/updated: ' . $user->email);

        // Assign Super Admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $user->realEstateRoles()->sync([$superAdminRole->id]);
            $this->command->info('Assigned Super Admin role to admin user');
        }

        // Create demo user
        $demoUser = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => bcrypt('demo123'),
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('Demo user created/updated: ' . $demoUser->email);
    }
}

