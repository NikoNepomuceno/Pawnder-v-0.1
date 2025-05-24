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
        // Drop the existing enum constraint and recreate it with new values
        DB::statement("ALTER TABLE posts MODIFY status ENUM('found', 'not_found', 'taken_down')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Roll back to previous enum values
        DB::statement("ALTER TABLE posts MODIFY status ENUM('found', 'not_found')");
    }
};
