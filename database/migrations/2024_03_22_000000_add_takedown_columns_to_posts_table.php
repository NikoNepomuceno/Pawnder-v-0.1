<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('posts', 'is_taken_down')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->boolean('is_taken_down')->default(false);
            });
        }
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('is_taken_down');
        });
    }
};
