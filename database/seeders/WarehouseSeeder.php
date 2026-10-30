<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\Branch;
use App\Models\User;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId = $adminUser ? $adminUser->id : 1;

        // Ensure default branch exists
        $branch = Branch::firstOrCreate(
            ['name' => 'Shaibazar HYD Branch'],
            [
                'address' => 'Shaibazar, Hyderabad',
                'number' => '03000000000',
                'user_id' => $adminId,
            ]
        );

        $warehouses = [
            [
                'branch_id' => $branch->id,
                'warehouse_name' => 'Main Showroom (Shaibazar HYD)',
                'creater_id' => $adminId,
                'location' => 'Shaibazar, Hyderabad',
                'remarks' => 'Primary retail shop and display counter'
            ],
            [
                'branch_id' => $branch->id,
                'warehouse_name' => 'Main Warehouse / Godown',
                'creater_id' => $adminId,
                'location' => 'Hyderabad Central Godown',
                'remarks' => 'Bulk footwear storage and inventory reserve'
            ],
        ];

        foreach ($warehouses as $data) {
            Warehouse::firstOrCreate(['warehouse_name' => $data['warehouse_name']], $data);
        }
    }
}
