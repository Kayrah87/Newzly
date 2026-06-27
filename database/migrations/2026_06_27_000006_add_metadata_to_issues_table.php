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
        Schema::table('issues', function (Blueprint $table) {
            $table->unsignedInteger('issue_number')->nullable()->after('title');
            $table->string('coverage_label')->nullable()->after('issue_number');
            $table->date('release_date')->nullable()->after('coverage_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn(['issue_number', 'coverage_label', 'release_date']);
        });
    }
};
