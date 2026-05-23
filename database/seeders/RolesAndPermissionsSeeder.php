<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define exact system permissions
        $permissions = [
            // Applications
            'view applications', 'manage applications', 'assign applications',
            // Services
            'view services', 'manage services', 'manage service pricing',
            // CRM & Marketing
            'view leads', 'manage leads',
            'view coupons', 'manage coupons',
            'view gifts', 'manage gifts',
            // Financials
            'view payouts', 'manage payouts',
            // Team & Roles
            'view team', 'manage team', 'manage team rates',
            'view agents', 'manage agents',
            'view marketers', 'manage marketers',
            // Content
            'view pages', 'manage pages',
        ];

        // 3. Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 4. Create roles and assign specific permissions
        $roleSubAdmin = Role::firstOrCreate(['name' => 'sub-admin']);
        $roleSubAdmin->syncPermissions([
            'view applications',
            'manage applications',
            'assign applications',
            'view leads',
            'manage leads',
            'view team',
            'view services',
            'view pages',
            'manage pages',
        ]);

        // Create remaining roles (no permissions attached directly)
        Role::firstOrCreate(['name' => 'admin']); // Gets everything via Gate::before
        Role::firstOrCreate(['name' => 'agent']);
        Role::firstOrCreate(['name' => 'marketer']);
        Role::firstOrCreate(['name' => 'operator']);
    }
}
