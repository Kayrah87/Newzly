<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Publication-wide layout: the order of the header/content/footer
     * sections (static across all of the publication's issues) and the
     * colour palette used to render the header/footer.
     */
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->json('structure')->nullable()->after('settings');
            $table->json('palette')->nullable()->after('structure');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn(['structure', 'palette']);
        });
    }
};
