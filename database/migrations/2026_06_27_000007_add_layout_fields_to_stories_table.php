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
        Schema::table('stories', function (Blueprint $table) {
            $table->enum('layout', ['standard', 'picture', 'title_only'])->default('standard')->after('content');
            $table->enum('source', ['admin', 'public'])->default('admin')->after('layout');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn(['layout', 'source', 'status']);
        });
    }
};
