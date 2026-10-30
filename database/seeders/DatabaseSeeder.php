<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ModulesTableSeeder::class,
            AllModulesPermissionsSeeder::class,
            HrPermissionsSeeder::class,
            SuperadminSeeder::class,
            AccountsAndHeadsSeeder::class,
            UnitSeeder::class,
            BrandSeeder::class,
            SizeSeeder::class,
            CategorySeeder::class,
            WarehouseSeeder::class,
        ]);
    }
}
