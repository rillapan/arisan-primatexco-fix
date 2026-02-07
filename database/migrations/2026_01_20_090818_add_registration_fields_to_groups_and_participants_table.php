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
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_registration_active')->default(false)->after('is_active');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('name');
            $table->string('registration_status')->default('approved')->after('is_active'); // approved by default for existing, pending for new registrants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('is_registration_active');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['photo', 'registration_status']);
        });
    }
};
