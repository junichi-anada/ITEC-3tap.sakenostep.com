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
        Schema::create('usable_sites', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 64);
            $table->unsignedBigInteger('entity_id');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->boolean('shared_login_allowed')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_type', 'entity_id'], 'idx_cp_usable_sites');
            $table->index('site_id', 'idx_fk_usable_sites_site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usable_sites');
    }
};
