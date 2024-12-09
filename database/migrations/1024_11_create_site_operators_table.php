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
        Schema::create('site_operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('operator_id')->constrained('operators')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('site_operator_roles')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['site_id', 'operator_id', 'role_id'], 'idx_uq_site_operators');
            $table->index(['site_id', 'operator_id', 'role_id'], 'idx_cp_site_operators');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_operators');
    }
};
