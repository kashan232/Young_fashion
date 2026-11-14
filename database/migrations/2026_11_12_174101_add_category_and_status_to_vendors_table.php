<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
            if (!Schema::hasColumn('vendors', 'status')) {
                $table->boolean('status')->default(true)->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('vendors', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
