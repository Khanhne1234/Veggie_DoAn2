<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' =>'Rau củ quả', 'slug' => 'rau-cu-qua', 'description' => 'Rau củ quả tươi ngon', 'image' => 'uploads/categories/rau-cu.png'],
            ['name' =>'Trái cây', 'slug' => 'trai-cay', 'description' => 'Trái cây nhập khẩu', 'image' => 'uploads/categories/trai-cay.png'],
            ['name' =>'Thịt', 'slug' => 'thit', 'description' => 'Thịt chất lượng', 'image' => 'uploads/categories/thit.png'],
            ['name' =>'Cá', 'slug' => 'ca', 'description' => 'Cá tươi ngon', 'image' => 'uploads/categories/ca.png'],
            ['name' =>'Các loại khác', 'slug' => 'cac-loai-khac', 'description' => 'Các loại sản phẩm khác', 'image' => 'uploads/categories/thuc-pham-khac.png'],

        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
