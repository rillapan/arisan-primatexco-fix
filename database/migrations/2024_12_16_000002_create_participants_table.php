<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->string('lottery_number')->unique();
            $table->string('name');
            $table->string('nik')->unique();
            $table->string('department');
            $table->string('shift');
            $table->decimal('monthly_installment', 15, 2)->default(175000);
            $table->boolean('has_won')->default(false);
            $table->timestamp('won_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
