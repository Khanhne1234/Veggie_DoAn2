<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminStaffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $adminRoleId = Role::where('name', 'admin')->value('id') ?? 1;
          $staffRoleId = Role::where('name', 'staff')->value('id') ?? 2;

          User::updateOrCreate([
            'email' => 'admin@example.com',
        ],[
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'phone_number' => '1234567890',
            'status' => 'pending',
            'avatar' => '',
            'address' => '123 Admin Street',
            'role_id' => $adminRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
          User::updateOrCreate([
            'email' => 'staff@example.com',
        ],[
            'name' => 'Staff User',
            'password' => bcrypt('password'),
            'phone_number' => '0987654321',
            'status' => 'pending',
            'avatar' => '',
            'address' => '123 Staff Street',
            'role_id' => $staffRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
