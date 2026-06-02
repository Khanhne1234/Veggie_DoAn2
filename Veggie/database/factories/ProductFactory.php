<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement(
            ['Cà chua', 'Dưa leo', 'Cà rốt', 'Bắp cải', 'Khoai tây', 'Hành tây',
            'Ớt chuông', 'Cải bó xôi', 'Cải thảo', 'Rau muống',
            'Táo', 'Cam', 'Chuối', 'Nho', 'Dưa hấu', 'Dứa',
            'Thịt bò', 'Thịt gà', 'Thịt lợn',
            'Cá hồi', 'Cá thu', 'Cá trích', 'Cá ngừ', 'Cá basa',
            'Gạo', 'Mì ống', 'Đậu phụ', 'Trứng', 'Sữa',
        ]);
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(2),
            'category_id' => Category::inRandomOrder()->first()->id,
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'stock' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['in_stock', 'out_of_stock']),
            'unit' => $this->faker->randomElement(['kg', 'g', 'l', 'ml', 'pcs', 'bó', 'hộp', 'túi']),
        ];
    }
}
