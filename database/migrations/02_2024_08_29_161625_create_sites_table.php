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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('site_code', 64)->unique();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('url', 255)->unique();
            $table->string('name', 64);
            $table->string('description', 255)->nullable();
            $table->boolean('is_btob')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('site_code', 'idx_uq_sites_site_code');
            $table->unique('url', 'idx_uq_sites_url');
            $table->index('name', 'idx_col_sites_name');
            $table->index('description', 'idx_col_sites_description')->fulltext();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
