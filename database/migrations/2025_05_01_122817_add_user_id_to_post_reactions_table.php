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
        Schema::table('post_reactions', function (Blueprint $table) {
            if (!Schema::hasColumn('post_reactions', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('post_reactions', 'post_id')) {
                $table->foreignId('post_id')->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('post_reactions', 'reaction_type')) {
                $table->string('reaction_type')->default('like');
            }
            
            // Check if the unique constraint exists
            $indexExists = DB::select("SHOW INDEX FROM post_reactions WHERE Key_name = 'post_reactions_user_id_post_id_unique'");
            if (empty($indexExists)) {
                $table->unique(['user_id', 'post_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_reactions', function (Blueprint $table) {
            // Check if the unique constraint exists
            $indexExists = DB::select("SHOW INDEX FROM post_reactions WHERE Key_name = 'post_reactions_user_id_post_id_unique'");
            if (!empty($indexExists)) {
                $table->dropUnique(['user_id', 'post_id']);
            }
            
            if (Schema::hasColumn('post_reactions', 'reaction_type')) {
                $table->dropColumn('reaction_type');
            }
            
            if (Schema::hasColumn('post_reactions', 'post_id')) {
                $table->dropForeign(['post_id']);
                $table->dropColumn('post_id');
            }
            
            if (Schema::hasColumn('post_reactions', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
