<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('vendor_categories')) {
            Schema::create('vendor_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150)->unique();
                $table->text('description')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });

            // Seed initial categories including user requested examples
            $initial = [
                ['name' => 'Loafer / Manufacturer', 'description' => 'Vendors manufacturing loafing footwear', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Sneakers / Supplier', 'description' => 'Suppliers providing sneakers and casual footwear', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Manufacturer', 'description' => 'General goods & footwear manufacturers', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Supplier', 'description' => 'General suppliers and material providers', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Distributor', 'description' => 'Wholesale and regional distributors', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Wholesaler', 'description' => 'Bulk wholesalers', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ];

            DB::table('vendor_categories')->insert($initial);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_categories');
    }
};
