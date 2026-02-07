<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_periods', function (Blueprint $table) {
            $table->enum('status', ['draft', 'active', 'bidding', 'drawing', 'completed'])->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('monthly_periods', function (Blueprint $table) {
            $table->enum('status', ['active', 'bidding', 'drawing', 'completed'])->default('active')->change();
        });
    }
};
