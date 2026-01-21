<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the permissions and roles seeders first
        $this->call([
            PermissionsSeeder::class,
            RolesSeeder::class,
        ]);

        // You can add more seeders here as needed
        // Example:
        // $this->call([
        //     UsersSeeder::class,
        //     ExpenseCategoriesSeeder::class,
        //     DocumentTypesSeeder::class,
        // ]);
    }
}

