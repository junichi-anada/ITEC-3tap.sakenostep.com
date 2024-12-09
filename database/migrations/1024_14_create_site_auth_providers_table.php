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
        Schema::create('site_auth_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('auth_provider_id')->constrained('auth_providers')->onDelete('cascade');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['site_id', 'auth_provider_id'], 'idx_uq_site_auth_providers');
            $table->index('site_id', 'idx_fk_site_auth_providers_site_id');
            $table->index('auth_provider_id', 'idx_fk_site_auth_providers_auth_provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_auth_providers');
    }
};
