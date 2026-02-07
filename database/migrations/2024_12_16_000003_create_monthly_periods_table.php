<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->string('period_name'); // e.g., "Desember 2024"
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('previous_cash_balance', 15, 2)->default(0);
            $table->decimal('total_installments', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('shu_amount', 15, 2)->default(0);
            $table->decimal('available_funds', 15, 2)->default(0);
            $table->decimal('remaining_cash', 15, 2)->default(0);
            $table->string('access_code')->nullable();
            $table->enum('status', ['active', 'bidding', 'drawing', 'completed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_periods');
    }
};
