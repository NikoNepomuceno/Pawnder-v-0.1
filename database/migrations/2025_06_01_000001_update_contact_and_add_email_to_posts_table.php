<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->renameColumn('contact', 'mobile_number');
            $table->string('email')->nullable()->after('mobile_number');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->renameColumn('mobile_number', 'contact');
            $table->dropColumn('email');
        });
    }
};
