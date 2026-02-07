<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL, we need to modify the auto_increment behavior
        // The 'id' will still be the primary key but we'll allow manual insertion
        // This is done by setting incrementing = false in the model
        
        // Note: The actual change is in the Group model where we set:
        // public $incrementing = false;
        // This allows us to manually set IDs when creating groups
    }

    public function down(): void
    {
        // Nothing to revert at database level
    }
};
