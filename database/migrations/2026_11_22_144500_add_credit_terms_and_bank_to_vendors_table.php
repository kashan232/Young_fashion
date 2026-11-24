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
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'credit_limit')) {
                $table->decimal('credit_limit', 15, 2)->nullable()->default(0)->after('opening_balance');
            }
            if (!Schema::hasColumn('vendors', 'payment_terms')) {
                $table->string('payment_terms', 150)->nullable()->after('payment_day');
            }
            if (!Schema::hasColumn('vendors', 'bank_name')) {
                $table->string('bank_name', 150)->nullable()->after('address');
            }
            if (!Schema::hasColumn('vendors', 'account_title')) {
                $table->string('account_title', 150)->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('vendors', 'account_number')) {
                $table->string('account_number', 100)->nullable()->after('account_title');
            }
            if (!Schema::hasColumn('vendors', 'bank_details')) {
                $table->text('bank_details')->nullable()->after('account_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $columns = ['credit_limit', 'payment_terms', 'bank_name', 'account_title', 'account_number', 'bank_details'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('vendors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
