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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 64)->unique();
            $table->string('company_name', 32);
            $table->string('name', 64);
            $table->string('postal_code', 10)->nullable();
            $table->string('address', 128)->nullable();
            $table->string('phone', 24)->nullable();
            $table->string('phone2', 24)->nullable();
            $table->string('fax', 24)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('company_code', 'idx_uq_companies_company_code');
            $table->index('company_name', 'idx_col_companies_company_name');
            $table->index('name', 'idx_col_companies_name');
            $table->index('postal_code', 'idx_col_companies_postal_code');
            $table->index('address', 'idx_col_companies_address')->fulltext();
            $table->index('phone', 'idx_col_companies_tel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
