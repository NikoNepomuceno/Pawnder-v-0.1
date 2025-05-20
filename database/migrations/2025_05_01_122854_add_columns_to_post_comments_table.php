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
        try {
            Schema::table('post_comments', function (Blueprint $table) {
                if (!Schema::hasColumn('post_comments', 'user_id')) {
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                }
                
                if (!Schema::hasColumn('post_comments', 'post_id')) {
                    $table->foreignId('post_id')->constrained()->onDelete('cascade');
                }
                
                if (!Schema::hasColumn('post_comments', 'content')) {
                    $table->text('content');
                }
                
                if (!Schema::hasColumn('post_comments', 'parent_id')) {
                    $table->unsignedBigInteger('parent_id')->nullable();
                    
                    // Check if the foreign key already exists
                    $constraintExists = DB::select("SELECT CONSTRAINT_NAME
                        FROM information_schema.TABLE_CONSTRAINTS 
                        WHERE TABLE_NAME = 'post_comments'
                        AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                        AND CONSTRAINT_NAME = 'post_comments_parent_id_foreign'");
                    
                    if (empty($constraintExists)) {
                        $table->foreign('parent_id')->references('id')->on('post_comments')->onDelete('cascade');
                    }
                }
            });
        } catch (\Exception $e) {
            echo "Migration error: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_comments', function (Blueprint $table) {
            if (Schema::hasColumn('post_comments', 'parent_id')) {
                // Check if the foreign key exists
                $constraintExists = DB::select("SELECT CONSTRAINT_NAME
                    FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE TABLE_NAME = 'post_comments'
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                    AND CONSTRAINT_NAME = 'post_comments_parent_id_foreign'");
                
                if (!empty($constraintExists)) {
                    $table->dropForeign(['parent_id']);
                }
                $table->dropColumn('parent_id');
            }
            
            if (Schema::hasColumn('post_comments', 'content')) {
                $table->dropColumn('content');
            }
            
            if (Schema::hasColumn('post_comments', 'post_id')) {
                $table->dropForeign(['post_id']);
                $table->dropColumn('post_id');
            }
            
            if (Schema::hasColumn('post_comments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
