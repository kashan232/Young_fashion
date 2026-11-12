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
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->date('mfg_date')->nullable()->after('line_total');
            $table->date('exp_date')->nullable()->after('mfg_date');
            $table->text('description')->nullable()->after('exp_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['mfg_date', 'exp_date', 'description']);
        });
    }
};
