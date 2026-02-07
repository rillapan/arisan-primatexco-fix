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
        Schema::create('monthly_period_saksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('saksi_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['monthly_period_id', 'saksi_id'], 'mpk_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_period_saksi');
    }
};
