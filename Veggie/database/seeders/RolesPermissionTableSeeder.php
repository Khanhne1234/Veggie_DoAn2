<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin','staff','customer'];
        foreach ($roles as $value) {
            //chay
          Role::firstOrCreate([
            'name' => $value
          ]);
        }
    }
}
