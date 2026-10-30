<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Young Fashion',
            'Nike',
            'Adidas',
            'Puma',
            'Servis',
            'Ndure',
            'Bata',
            'Borjan',
            'Hush Puppies',
            'Urban Sole',
            'Sputnik',
            'Local'
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand]);
        }
    }
}
