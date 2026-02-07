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
        // Check if index exists before dropping
        $indexExists = false;
        if (DB::getDriverName() === 'sqlite') {
            $indexExists = collect(DB::select("PRAGMA index_list(cash_flows)"))
                ->where('name', 'cash_flows_group_id_month_key_unique')
                ->count() > 0;
        } else {
            $indexExists = collect(DB::select("SHOW INDEX FROM cash_flows"))
                ->where('Key_name', 'cash_flows_group_id_month_key_unique')
                ->count() > 0;
        }

        Schema::table('cash_flows', function (Blueprint $table) use ($indexExists) {
            if ($indexExists) {
                $table->dropUnique('cash_flows_group_id_month_key_unique');
            }
            
            // Allow multiple CashFlows per period for now or just unique?
            // Actually, we want it to be unique per period.
            // Check if there are already duplicates in monthly_period_id
            // monthly_period_id can be null. Unique constraint on nullable column is fine in MySQL (multiple nulls allowed).
            $table->unique('monthly_period_id', 'cash_flows_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropUnique('cash_flows_period_unique');
            $table->unique(['group_id', 'month_key'], 'cash_flows_group_id_month_key_unique');
        });
    }
};
