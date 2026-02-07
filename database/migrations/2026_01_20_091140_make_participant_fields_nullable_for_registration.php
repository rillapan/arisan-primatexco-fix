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
        Schema::table('participants', function (Blueprint $table) {
            $table->string('lottery_number')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->string('department')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('lottery_number')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
            $table->string('department')->nullable(false)->change();
        });
    }
};
