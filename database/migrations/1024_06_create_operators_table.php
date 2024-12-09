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
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('operator_code', 64)->unique();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name', 32);
            $table->foreignId('operator_rank_id')->constrained('operator_ranks')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('operator_code', 'idx_uq_operators_operator_code');
            $table->index('company_id', 'idx_fk_operators_company_id');
            $table->index('operator_rank_id', 'idx_fk_operators_operator_rank_id');
            $table->index('name', 'idx_ft_operators_name')->fulltext();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};
