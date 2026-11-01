<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_discounts', function (Blueprint $table) {
            $table->string('variant_row_id')->nullable()->after('product_id');
            $table->string('variant_name')->nullable()->after('variant_row_id');
            $table->string('variant_size')->nullable()->after('variant_name');
            $table->string('variant_color')->nullable()->after('variant_size');
            $table->string('variant_barcode')->nullable()->after('variant_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_discounts', function (Blueprint $table) {
            $table->dropColumn([
                'variant_row_id',
                'variant_name',
                'variant_size',
                'variant_color',
                'variant_barcode'
            ]);
        });
    }
};
