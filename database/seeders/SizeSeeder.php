<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Size;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            // Standard Footwear Euro Sizes
            ['name' => '38', 'size_code' => 'EU-38'],
            ['name' => '39', 'size_code' => 'EU-39'],
            ['name' => '40', 'size_code' => 'EU-40'],
            ['name' => '41', 'size_code' => 'EU-41'],
            ['name' => '42', 'size_code' => 'EU-42'],
            ['name' => '43', 'size_code' => 'EU-43'],
            ['name' => '44', 'size_code' => 'EU-44'],
            ['name' => '45', 'size_code' => 'EU-45'],
            ['name' => '46', 'size_code' => 'EU-46'],

            // UK / US Sizes
            ['name' => '6 UK', 'size_code' => 'UK-6'],
            ['name' => '7 UK', 'size_code' => 'UK-7'],
            ['name' => '8 UK', 'size_code' => 'UK-8'],
            ['name' => '9 UK', 'size_code' => 'UK-9'],
            ['name' => '10 UK', 'size_code' => 'UK-10'],
            ['name' => '11 UK', 'size_code' => 'UK-11'],
            ['name' => '12 UK', 'size_code' => 'UK-12'],

            // Special / Standard Text Sizes
            ['name' => 'Small', 'size_code' => 'S'],
            ['name' => 'Medium', 'size_code' => 'M'],
            ['name' => 'Large', 'size_code' => 'L'],
            ['name' => 'Free Size', 'size_code' => 'FS'],
        ];

        foreach ($sizes as $sizeData) {
            Size::firstOrCreate(['name' => $sizeData['name']], $sizeData);
        }
    }
}
