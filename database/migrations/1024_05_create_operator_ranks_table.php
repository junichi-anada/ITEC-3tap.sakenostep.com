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
        Schema::create('operator_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->integer('priority')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('name', 'idx_col_operator_ranks_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_ranks');
    }
};
