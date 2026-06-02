<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();
        $permissions = Permission::all();
        //admin has all permissions
        $adminRole->permissions()->sync($permissions);

        //staff have some permissions
        $staffPermissions = $permissions->whereIn('name', ['manage_products', 'manage_contacts']);
        $staffRole->permissions()->sync($staffPermissions);
    }
}
