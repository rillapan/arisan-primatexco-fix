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
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('monthly_period_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('month_key', 7); // Format: YYYY-MM
            $table->string('month_name');
            $table->decimal('previous_balance', 15, 2)->default(0);
            $table->decimal('monthly_installments', 15, 2)->default(0);
            $table->decimal('total_bids', 15, 2)->default(0);
            $table->decimal('admin_fees', 15, 2)->default(0);
            $table->decimal('prizes_given', 15, 2)->default(0);
            $table->decimal('remaining_cash', 15, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'locked'])->default('active');
            $table->timestamps();
            
            $table->index(['group_id', 'month_key']);
            $table->unique(['group_id', 'month_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};
