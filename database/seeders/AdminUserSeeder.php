<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@arsipkontrak.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // Create sample regular user (verified)
        User::create([
            'name' => 'User Demo',
            'email' => 'user@arsipkontrak.local',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }
}

