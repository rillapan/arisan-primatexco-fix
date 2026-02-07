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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('management_monthly_period');
        Schema::dropIfExists('managements');
        Schema::enableForeignKeyConstraints();
        
        Schema::create('managements', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('foto_profil')->nullable();
            $table->string('ttd')->nullable();
            $table->enum('jabatan', ['manager', 'ketua']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managements');
    }
};
