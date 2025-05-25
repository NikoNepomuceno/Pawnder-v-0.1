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
        // First, modify the column to be a string temporarily
        DB::statement("ALTER TABLE post_reports MODIFY COLUMN status VARCHAR(255)");

        // Then, update the enum values
        DB::statement("ALTER TABLE post_reports MODIFY COLUMN status ENUM('pending', 'reviewed', 'rejected', 'resolved', 'dismissed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, modify the column to be a string temporarily
        DB::statement("ALTER TABLE post_reports MODIFY COLUMN status VARCHAR(255)");

        // Then, update the enum values back to original
        DB::statement("ALTER TABLE post_reports MODIFY COLUMN status ENUM('pending', 'reviewed', 'rejected', 'resolved') DEFAULT 'pending'");
    }
};
