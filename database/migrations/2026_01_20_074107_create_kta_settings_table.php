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
        Schema::create('kta_settings', function (Blueprint $table) {
            $table->id();
            $table->string('header_title')->nullable();
            $table->string('logo')->nullable();
            $table->string('signature_name')->nullable();
            $table->string('signature_image')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kta_settings');
    }
};
