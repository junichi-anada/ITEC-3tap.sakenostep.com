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
        Schema::create('auth_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_code', 64)->unique();
            $table->string('name', 32);
            $table->string('description', 255)->nullable();
            $table->boolean('is_enable')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('provider_code', 'idx_uq_auth_providers_provider_code');
            $table->index('name', 'idx_col_auth_providers_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_providers');
    }
};
