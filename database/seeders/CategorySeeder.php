<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Formal Shoes' => [
                'Oxford Shoes',
                'Derby Shoes',
                'Formal Loafers',
                'Monk Strap',
                'Court Shoes'
            ],
            'Casual Shoes' => [
                'Sneakers',
                'Casual Loafers',
                'Slip-Ons',
                'Canvas Shoes',
                'Moccasins'
            ],
            'Sports & Active' => [
                'Joggers',
                'Running Shoes',
                'Training Shoes',
                'Gym Shoes'
            ],
            'Traditional & Cultural' => [
                'Peshawari Chappal',
                'Kaptaan Chappal',
                'Norzi Chappal',
                'Khussa'
            ],
            'Sandals & Slippers' => [
                'Leather Sandals',
                'Daily Slippers',
                'Slides',
                'Flip Flops'
            ],
            'Boots' => [
                'Chelsea Boots',
                'Ankle Boots',
                'Leather Boots'
            ],
            'Accessories' => [
                'Socks',
                'Shoe Polish & Wax',
                'Shoe Brushes',
                'Shoe Horns',
                'Insoles'
            ]
        ];

        foreach ($data as $categoryName => $subcategories) {
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['show_on_website' => 1]
            );

            foreach ($subcategories as $sub) {
                Subcategory::firstOrCreate([
                    'category_id' => $category->id,
                    'name' => $sub,
                ]);
            }
        }
    }
}
