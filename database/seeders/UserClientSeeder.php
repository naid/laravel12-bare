<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates test users with different roles and assigns them to clients
     */
    public function run(): void
    {
        // Create an admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        // Create a manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role' => 'manager'
            ]
        );

        // Create a regular user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'role' => 'user'
            ]
        );

        // Get some clients (assuming they exist from ClientSeeder)
        $clients = Client::limit(3)->get();

        if ($clients->count() > 0) {
            // Assign manager to first 2 clients with write access
            if (isset($clients[0])) {
                $manager->clients()->syncWithoutDetaching([
                    $clients[0]->id => ['access_level' => 'write']
                ]);
            }
            if (isset($clients[1])) {
                $manager->clients()->syncWithoutDetaching([
                    $clients[1]->id => ['access_level' => 'write']
                ]);
            }

            // Assign regular user to first client with read access only
            if (isset($clients[0])) {
                $user->clients()->syncWithoutDetaching([
                    $clients[0]->id => ['access_level' => 'read']
                ]);
            }
        }

        $this->command->info('Users created with roles:');
        $this->command->info('Admin: admin@example.com (can access all clients)');
        $this->command->info('Manager: manager@example.com (can access assigned clients with write access)');
        $this->command->info('User: user@example.com (can access assigned clients with read access)');
        $this->command->info('All passwords: password');
    }
}
