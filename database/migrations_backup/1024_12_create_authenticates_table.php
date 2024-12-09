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
        Schema::create('authenticates', function (Blueprint $table) {
            $table->id();
            $table->string('auth_code', 64)->unique();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('entity_type', 64);
            $table->unsignedBigInteger('entity_id');
            $table->string('login_code', 50);
            $table->string('password', 255);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('auth_code', 'idx_uq_authenticates_auth_code');
            $table->index(['entity_type', 'entity_id'], 'idx_cp_authenticates');
            $table->index('site_id', 'idx_fk_authenticates_site_id');
            $table->index('login_code', 'idx_col_authenticates_login_code');
            $table->index('password', 'idx_col_authenticates_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authenticates');
    }
};
