<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documentations', function (Blueprint $table) {
            $table->string('type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Changing it back to an enum may not be straightforward if there are 'link' records.
        // So we keep it as a string for safety or recreate the enum constraint.
        Schema::table('documentations', function (Blueprint $table) {
            $table->enum('type', ['image', 'video', 'text'])->change();
        });
    }
};
