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
        Schema::create('site_operator_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->timestamps();
            $table->softDeletes();

            $table->index('name', 'idx_col_site_operator_roles_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_operator_roles');
    }
};
