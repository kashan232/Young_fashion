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
        Schema::table('stock_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfers', 'size')) {
                $table->string('size')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('stock_transfers', 'color')) {
                $table->string('color')->nullable()->after('size');
            }
            if (!Schema::hasColumn('stock_transfers', 'variant_data')) {
                $table->text('variant_data')->nullable()->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropColumn(['size', 'color', 'variant_data']);
        });
    }
};
