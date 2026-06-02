<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ProductImage::query()->delete();
        Product::query()->delete();
        Schema::enableForeignKeyConstraints();

        $categoryIds = Category::pluck('id', 'slug');

        $items = [
            ['name' => 'Cà chua hữu cơ', 'category' => 'rau-cu-qua', 'price' => 18000, 'stock' => 120, 'unit' => 'kg'],
            ['name' => 'Dưa leo baby', 'category' => 'rau-cu-qua', 'price' => 16000, 'stock' => 90, 'unit' => 'kg'],
            ['name' => 'Cà rốt Đà Lạt', 'category' => 'rau-cu-qua', 'price' => 22000, 'stock' => 110, 'unit' => 'kg'],
            ['name' => 'Bắp cải tím', 'category' => 'rau-cu-qua', 'price' => 25000, 'stock' => 75, 'unit' => 'kg'],
            ['name' => 'Khoai tây sạch', 'category' => 'rau-cu-qua', 'price' => 30000, 'stock' => 150, 'unit' => 'kg'],
            ['name' => 'Hành tây Mỹ', 'category' => 'rau-cu-qua', 'price' => 28000, 'stock' => 80, 'unit' => 'kg'],
            ['name' => 'Ớt chuông đỏ', 'category' => 'rau-cu-qua', 'price' => 42000, 'stock' => 60, 'unit' => 'kg'],
            ['name' => 'Cải bó xôi', 'category' => 'rau-cu-qua', 'price' => 24000, 'stock' => 95, 'unit' => 'bó'],
            ['name' => 'Cải thảo tươi', 'category' => 'rau-cu-qua', 'price' => 19000, 'stock' => 100, 'unit' => 'kg'],
            ['name' => 'Rau muống sạch', 'category' => 'rau-cu-qua', 'price' => 12000, 'stock' => 130, 'unit' => 'bó'],

            ['name' => 'Táo đỏ', 'category' => 'trai-cay', 'price' => 55000, 'stock' => 70, 'unit' => 'kg'],
            ['name' => 'Cam sành', 'category' => 'trai-cay', 'price' => 38000, 'stock' => 85, 'unit' => 'kg'],
            ['name' => 'Chuối già', 'category' => 'trai-cay', 'price' => 17000, 'stock' => 140, 'unit' => 'nải'],
            ['name' => 'Nho xanh', 'category' => 'trai-cay', 'price' => 89000, 'stock' => 55, 'unit' => 'kg'],
            ['name' => 'Dưa hấu ruột đỏ', 'category' => 'trai-cay', 'price' => 21000, 'stock' => 65, 'unit' => 'kg'],
            ['name' => 'Dứa mật', 'category' => 'trai-cay', 'price' => 26000, 'stock' => 60, 'unit' => 'quả'],
            ['name' => 'Xoài cát', 'category' => 'trai-cay', 'price' => 45000, 'stock' => 50, 'unit' => 'kg'],
            ['name' => 'Lê Hàn Quốc', 'category' => 'trai-cay', 'price' => 98000, 'stock' => 40, 'unit' => 'kg'],
            ['name' => 'Thanh long ruột đỏ', 'category' => 'trai-cay', 'price' => 32000, 'stock' => 90, 'unit' => 'kg'],
            ['name' => 'Kiwi xanh', 'category' => 'trai-cay', 'price' => 120000, 'stock' => 35, 'unit' => 'hộp'],

            ['name' => 'Thịt bò tươi', 'category' => 'thit', 'price' => 220000, 'stock' => 45, 'unit' => 'kg'],
            ['name' => 'Thịt gà ta', 'category' => 'thit', 'price' => 95000, 'stock' => 80, 'unit' => 'kg'],
            ['name' => 'Thịt heo nạc', 'category' => 'thit', 'price' => 125000, 'stock' => 60, 'unit' => 'kg'],
            ['name' => 'Sườn non', 'category' => 'thit', 'price' => 145000, 'stock' => 55, 'unit' => 'kg'],
            ['name' => 'Đùi gà', 'category' => 'thit', 'price' => 88000, 'stock' => 75, 'unit' => 'kg'],
            ['name' => 'Ba chỉ bò', 'category' => 'thit', 'price' => 275000, 'stock' => 30, 'unit' => 'kg'],
            ['name' => 'Nạc vai heo', 'category' => 'thit', 'price' => 118000, 'stock' => 70, 'unit' => 'kg'],
            ['name' => 'Thăn bò', 'category' => 'thit', 'price' => 310000, 'stock' => 25, 'unit' => 'kg'],
            ['name' => 'Cánh gà', 'category' => 'thit', 'price' => 76000, 'stock' => 90, 'unit' => 'kg'],
            ['name' => 'Lườn heo', 'category' => 'thit', 'price' => 132000, 'stock' => 50, 'unit' => 'kg'],

            ['name' => 'Cá hồi Na Uy', 'category' => 'ca', 'price' => 340000, 'stock' => 20, 'unit' => 'kg'],
            ['name' => 'Cá thu tươi', 'category' => 'ca', 'price' => 115000, 'stock' => 45, 'unit' => 'kg'],
            ['name' => 'Cá trích', 'category' => 'ca', 'price' => 98000, 'stock' => 60, 'unit' => 'kg'],
            ['name' => 'Cá ngừ đại dương', 'category' => 'ca', 'price' => 155000, 'stock' => 35, 'unit' => 'kg'],
            ['name' => 'Cá basa phi lê', 'category' => 'ca', 'price' => 88000, 'stock' => 80, 'unit' => 'kg'],
            ['name' => 'Cá lóc đồng', 'category' => 'ca', 'price' => 110000, 'stock' => 50, 'unit' => 'kg'],
            ['name' => 'Cá diêu hồng', 'category' => 'ca', 'price' => 92000, 'stock' => 70, 'unit' => 'kg'],
            ['name' => 'Cá chim', 'category' => 'ca', 'price' => 128000, 'stock' => 40, 'unit' => 'kg'],
            ['name' => 'Cá rô phi', 'category' => 'ca', 'price' => 68000, 'stock' => 95, 'unit' => 'kg'],
            ['name' => 'Tôm khô', 'category' => 'ca', 'price' => 220000, 'stock' => 25, 'unit' => 'g'],

            ['name' => 'Gạo thơm', 'category' => 'cac-loai-khac', 'price' => 75000, 'stock' => 150, 'unit' => 'kg'],
            ['name' => 'Mì ống Ý', 'category' => 'cac-loai-khac', 'price' => 35000, 'stock' => 100, 'unit' => 'gói'],
            ['name' => 'Đậu phụ non', 'category' => 'cac-loai-khac', 'price' => 12000, 'stock' => 130, 'unit' => 'bìa'],
            ['name' => 'Trứng gà', 'category' => 'cac-loai-khac', 'price' => 45000, 'stock' => 200, 'unit' => 'vỉ'],
            ['name' => 'Sữa tươi', 'category' => 'cac-loai-khac', 'price' => 32000, 'stock' => 120, 'unit' => 'hộp'],
            ['name' => 'Muối biển', 'category' => 'cac-loai-khac', 'price' => 9000, 'stock' => 300, 'unit' => 'gói'],
            ['name' => 'Đường nâu', 'category' => 'cac-loai-khac', 'price' => 27000, 'stock' => 140, 'unit' => 'kg'],
            ['name' => 'Bơ lạt', 'category' => 'cac-loai-khac', 'price' => 48000, 'stock' => 75, 'unit' => 'hộp'],
            ['name' => 'Nấm tươi', 'category' => 'cac-loai-khac', 'price' => 39000, 'stock' => 90, 'unit' => 'kg'],
            ['name' => 'Dầu ăn', 'category' => 'cac-loai-khac', 'price' => 52000, 'stock' => 110, 'unit' => 'chai'],
        ];

        foreach ($items as $index => $item) {
            $categoryId = $categoryIds[$item['category']] ?? Category::firstOrFail()->id;
            $product = Product::create([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . ($index + 1),
                'category_id' => $categoryId,
                'description' => 'Sản phẩm ' . $item['name'] . ' chất lượng cao, được cập nhật lại trong bộ dữ liệu mẫu.',
                'price' => $item['price'],
                'stock' => $item['stock'],
                'status' => 'in_stock',
                'unit' => $item['unit'],
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'image' => 'default_product.webp',
            ]);
        }
    }
}
