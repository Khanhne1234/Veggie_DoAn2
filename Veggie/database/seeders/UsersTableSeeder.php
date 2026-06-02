<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerRoleId = Role::where('name', 'customer')->value('id') ?? 3;

        User::updateOrCreate([
            'email' => 'customer1@example.com',
        ],[
            'name' => 'Customer User 1',
            'password' => bcrypt('password'),
            'phone_number' => '0900000001',
            'status' => 'active',
            'avatar' => '',
            'address' => '123 Customer Street',
            'role_id' => $customerRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::updateOrCreate([
            'email' => 'customer2@example.com',
        ],[
            'name' => 'Customer User 2',
            'password' => bcrypt('password'),
            'phone_number' => '0900000002',
            'status' => 'active',
            'avatar' => '',
            'address' => '234 Customer Street',
            'role_id' => $customerRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::updateOrCreate([
            'email' => 'customer3@example.com',
        ],[
            'name' => 'Customer User 3',
            'password' => bcrypt('password'),
            'phone_number' => '0900000003',
            'status' => 'active',
            'avatar' => '',
            'address' => '345 Customer Street',
            'role_id' => $customerRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
