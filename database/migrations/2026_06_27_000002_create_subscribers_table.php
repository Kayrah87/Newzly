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
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('name')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'unsubscribed'])->default('pending');

            // GDPR consent record.
            $table->timestamp('consent_at')->nullable();
            $table->string('consent_ip', 45)->nullable();
            $table->string('consent_source')->nullable();

            // Double opt-in confirmation (used by the public subscribe flow).
            $table->string('confirmation_token')->nullable()->index();
            $table->timestamp('confirmed_at')->nullable();

            // Unsubscribe handling.
            $table->string('unsubscribe_token')->unique();
            $table->timestamp('unsubscribed_at')->nullable();

            $table->timestamps();

            $table->unique(['publication_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
