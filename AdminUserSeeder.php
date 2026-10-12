<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin_bootstrap.email');
        $password = config('admin_bootstrap.password');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)
            || ! is_string($password) || strlen($password) < 12) {
            throw new InvalidArgumentException('Set BOOTSTRAP_ADMIN_EMAIL and a BOOTSTRAP_ADMIN_PASSWORD of at least 12 characters before seeding.');
        }

        // Never overwrite an existing account or promote a registered user.
        if (User::where('email', $email)->exists()) {
            return;
        }

        User::create([
            'name' => 'Administrator',
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }
}
