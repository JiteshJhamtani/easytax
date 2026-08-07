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

        User::forceCreate([
            'name' => 'Rahul Sharma',
            'email' => 'rahul@easytax.live',
            'password' => Hash::make('Rahul@12345'),
            'role' => 'TEAM',
            'is_active' => true,
        ]);

        // SUB-ADMIN
        $subadmin = User::forceCreate([
            'name' => 'Sub Admin',
            'email' => 'subadmin@easytax.test',
            'password' => Hash::make('password123'),
            'role' => 'SUB-ADMIN',
            'is_active' => true,
        ]);
        $subadmin->assignRole('sub-admin');

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
