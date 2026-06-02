<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addresses = [
            [
                'email' => 'customer1@example.com',
                'full_name' => 'Nguyễn Văn An',
                'phone' => '0901111111',
                'address' => '12 Nguyễn Trãi, Phường Bến Thành',
                'city' => 'TP. Hồ Chí Minh',
                'default' => true,
            ],
            [
                'email' => 'customer1@example.com',
                'full_name' => 'Nguyễn Văn An',
                'phone' => '0901111112',
                'address' => '45 Lê Lợi, Phường Phạm Ngũ Lão',
                'city' => 'TP. Hồ Chí Minh',
                'default' => false,
            ],
            [
                'email' => 'customer2@example.com',
                'full_name' => 'Trần Thị Bình',
                'phone' => '0902222221',
                'address' => '88 Trần Phú, Phường Hải Châu',
                'city' => 'Đà Nẵng',
                'default' => true,
            ],
            [
                'email' => 'customer2@example.com',
                'full_name' => 'Trần Thị Bình',
                'phone' => '0902222222',
                'address' => '16 Lý Thường Kiệt, Phường Thạch Thang',
                'city' => 'Đà Nẵng',
                'default' => false,
            ],
            [
                'email' => 'customer3@example.com',
                'full_name' => 'Lê Quốc Cường',
                'phone' => '0903333331',
                'address' => '27 Hai Bà Trưng, Phường Tân Định',
                'city' => 'TP. Hồ Chí Minh',
                'default' => true,
            ],
        ];

        foreach ($addresses as $addressData) {
            $userId = User::where('email', $addressData['email'])->value('id');

            if (!$userId) {
                continue;
            }

            DB::table('shipping_addresses')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'phone' => $addressData['phone'],
                ],
                [
                    'full_name' => $addressData['full_name'],
                    'address' => $addressData['address'],
                    'city' => $addressData['city'],
                    'default' => $addressData['default'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
