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
        if (!Schema::hasColumn('monthly_periods', 'bid_deadline')) {
            Schema::table('monthly_periods', function (Blueprint $table) {
                $table->timestamp('bid_deadline')->nullable()->after('period_end');
            });
        }
    }

    public function down(): void
    {
        Schema::table('monthly_periods', function (Blueprint $table) {
            $table->dropColumn('bid_deadline');
        });
    }
};
