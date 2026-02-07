<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('max_participants')->default(90);
            $table->decimal('monthly_installment', 15, 2)->default(175000);
            $table->decimal('main_prize', 15, 2)->default(17500000);
            $table->decimal('shu', 15, 2)->default(500000);
            $table->decimal('min_bid', 15, 2)->default(2250000);
            $table->decimal('max_bid', 15, 2)->default(6000000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
