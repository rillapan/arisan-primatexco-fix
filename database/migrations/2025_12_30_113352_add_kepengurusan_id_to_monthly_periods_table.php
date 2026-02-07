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
        Schema::table('monthly_periods', function (Blueprint $table) {
            $table->foreignId('saksi_id')->nullable()->after('group_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_periods', function (Blueprint $table) {
            $table->dropForeign(['saksi_id']);
            $table->dropColumn('saksi_id');
        });
    }
};
