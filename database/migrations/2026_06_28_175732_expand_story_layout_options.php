<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expand the story `layout` enum with the "clear" variants (no filled accent
     * banner — clear header with an accent-coloured title).
     */
    public function up(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->enum('layout', [
                'standard', 'standard_clear', 'picture', 'picture_clear', 'title_only',
            ])->default('standard')->change();
        });
    }

    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->enum('layout', ['standard', 'picture', 'title_only'])
                ->default('standard')->change();
        });
    }
};
