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
        Schema::create('user_external_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('external_code', 255);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'external_code'], 'idx_uq_user_external_codes');
            $table->index('user_id', 'idx_fk_user_external_codes_user_id');
            $table->index('external_code', 'idx_col_user_external_codes_external_code');
            $table->index('created_at', 'idx_col_user_external_codes_created_at');
            $table->index('deleted_at', 'idx_col_user_external_codes_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_external_codes');
    }
};
