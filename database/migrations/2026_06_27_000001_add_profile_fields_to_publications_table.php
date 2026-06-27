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
        Schema::table('publications', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('description');
            $table->string('website_url')->nullable()->after('logo_path');
            $table->json('social_links')->nullable()->after('website_url');
            $table->string('from_name')->nullable()->after('social_links');
            $table->string('from_email')->nullable()->after('from_name');
            $table->string('reply_to_email')->nullable()->after('from_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'website_url',
                'social_links',
                'from_name',
                'from_email',
                'reply_to_email',
            ]);
        });
    }
};
