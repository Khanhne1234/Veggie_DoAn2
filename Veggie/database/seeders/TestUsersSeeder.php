<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Nguyễn Văn An',    'email' => 'user1@test.com',  'phone_number' => '0901234567', 'address' => 'Hà Nội',          'status' => 'active'],
            ['name' => 'Trần Thị Bình',    'email' => 'user2@test.com',  'phone_number' => '0912345678', 'address' => 'TP Hồ Chí Minh',  'status' => 'active'],
            ['name' => 'Lê Hoàng Cường',   'email' => 'user3@test.com',  'phone_number' => '0923456789', 'address' => 'Đà Nẵng',         'status' => 'active'],
            ['name' => 'Phạm Minh Đức',    'email' => 'user4@test.com',  'phone_number' => '0934567890', 'address' => 'Hải Phòng',       'status' => 'active'],
            ['name' => 'Hoàng Thị Em',     'email' => 'user5@test.com',  'phone_number' => '0945678901', 'address' => 'Cần Thơ',         'status' => 'pending'],
            ['name' => 'Vũ Quốc Phong',    'email' => 'user6@test.com',  'phone_number' => '0956789012', 'address' => 'Huế',             'status' => 'active'],
            ['name' => 'Đặng Thị Giang',   'email' => 'user7@test.com',  'phone_number' => '0967890123', 'address' => 'Nha Trang',       'status' => 'banned'],
            ['name' => 'Bùi Văn Hải',      'email' => 'user8@test.com',  'phone_number' => '0978901234', 'address' => 'Vũng Tàu',        'status' => 'active'],
            ['name' => 'Ngô Thị Lan',      'email' => 'user9@test.com',  'phone_number' => '0989012345', 'address' => 'Đà Lạt',          'status' => 'active'],
            ['name' => 'Dương Minh Khôi',  'email' => 'user10@test.com', 'phone_number' => '0990123456', 'address' => 'Quy Nhơn',        'status' => 'active'],
        ];

        foreach ($data as $item) {
            User::create([
                'name'         => $item['name'],
                'email'        => $item['email'],
                'password'     => Hash::make('12345678'),
                'status'       => $item['status'],
                'phone_number' => $item['phone_number'],
                'address'      => $item['address'],
                'role_id'      => 3,
            ]);
        }
    }
}
