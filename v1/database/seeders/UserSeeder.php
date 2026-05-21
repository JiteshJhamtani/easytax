<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::forceCreate([
            'name' => 'Super Admin',
            'email' => 'admin@easytax.test',
            'password' => Hash::make('password123'),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        // AGENT
        User::forceCreate([
            'name' => 'Test Agent',
            'email' => 'agent@easytax.test',
            'password' => Hash::make('password123'),
            'role' => 'AGENT',
            'is_active' => true,
        ]);
    }
}
