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
        Schema::create('authenticate_oauths', function (Blueprint $table) {
            $table->id();
            $table->string('auth_code', 64)->unique();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('entity_type', 64);
            $table->unsignedBigInteger('entity_id');
            $table->foreignId('auth_provider_id')->constrained('auth_providers')->onDelete('cascade');
            $table->string('token', 255);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('auth_code', 'idx_uq_authenticate_oauths_auth_code');
            $table->index(['entity_type', 'entity_id'], 'idx_cp_authenticate_oauths');
            $table->index('site_id', 'idx_fk_authenticate_oauths_site_id');
            $table->index('auth_provider_id', 'idx_fk_auth_authenticate_oauths_provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authenticate_oauths');
    }
};
